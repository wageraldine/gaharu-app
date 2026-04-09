<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /** Tampilkan daftar pesanan (user: milik sendiri, super admin: semua) */
    public function index()
    {
        $user = Auth::user();
        $orders = $user->isSuperAdmin()
            ? Order::with('user')->latest()->get()
            : Order::where('user_id', $user->id)->latest()->get();

        return view('orders.index', compact('orders'));
    }

    /** Buat pesanan baru dari isi keranjang */
    public function store(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Keranjang masih kosong.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        $items = [];
        $total = 0;
        foreach ($cart as $id => $qty) {
            $p = $products->get((int) $id);
            if (!$p) continue;
            $subtotal = $p->price * $qty;
            $total   += $subtotal;
            $items[]  = [
                'product_id' => $id,
                'name'       => $p->name,
                'price'      => $p->price,
                'qty'        => $qty,
                'subtotal'   => $subtotal,
            ];
        }

        if (empty($items)) {
            return back()->with('error', 'Tidak ada produk valid di keranjang.');
        }

        Order::create([
            'user_id' => Auth::id(),
            'items'   => $items,
            'total'   => $total,
            'status'  => 'pending_payment',
            'notes'   => $request->notes,
        ]);

        // Kosongkan keranjang
        session(['cart' => []]);
        Auth::user()->update(['cart' => []]);

        return redirect()->route('orders.index')
            ->with('success', 'Pesanan berhasil dibuat! Silakan upload bukti pembayaran.');
    }

    /** Upload bukti pembayaran oleh user atau super admin */
    public function uploadProof(Request $request, Order $order)
    {
        // Super admin bisa upload untuk semua pesanan, user hanya milik sendiri
        if (!Auth::user()->isSuperAdmin() && (int) $order->user_id !== (int) Auth::id()) {
            abort(403);
        }
        if (!Auth::user()->isSuperAdmin() && !in_array($order->status, ['pending_payment', 'waiting_confirmation'])) {
            return back()->with('error', 'Status pesanan tidak memungkinkan upload bukti.');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'payment_proof.required' => 'File bukti pembayaran wajib dipilih.',
            'payment_proof.mimes'    => 'Format file harus JPG, PNG, atau PDF.',
            'payment_proof.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with('proof_order_id', $order->id);
        }

        // Hapus file lama jika ada
        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'status'        => 'waiting_confirmation',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu konfirmasi admin.');
    }

    /** Stream file bukti pembayaran (bypass symlink issue pada php artisan serve) */
    public function proofView(Order $order)
    {
        // Super admin bisa lihat semua, user hanya milik sendiri
        if (!Auth::user()->isSuperAdmin() && (int) $order->user_id !== (int) Auth::id()) {
            abort(403);
        }

        if (!$order->payment_proof) {
            abort(404, 'Bukti pembayaran tidak ditemukan.');
        }

        $path = storage_path('app/public/' . $order->payment_proof);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $mimeType = mime_content_type($path);
        return response()->file($path, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    /** Super admin: edit status & catatan pesanan */
    public function update(Request $request, Order $order)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        $request->validate([
            'status' => 'required|in:pending_payment,waiting_confirmation,paid,cancelled',
            'notes'  => 'nullable|string|max:500',
        ]);
        $order->update([
            'status' => $request->status,
            'notes'  => $request->notes,
        ]);
        return back()->with('success', "Pesanan #{$order->id} berhasil diperbarui.");
    }

    /** Super admin: hapus pesanan */
    public function destroy(Order $order)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }
        $orderId = $order->id;
        $order->delete();
        return back()->with('success', "Pesanan #{$orderId} berhasil dihapus.");
    }

    /** Super admin: tandai pesanan sebagai lunas */
    public function markPaid(Order $order)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        if ($order->status !== 'waiting_confirmation') {
            return back()->with('error', 'Pesanan ini belum mengupload bukti pembayaran.');
        }

        $order->update(['status' => 'paid']);

        return back()->with('success', "Pesanan #{$order->id} telah ditandai sebagai Lunas.");
    }

    public function cancel(Order $order)
    {
        $user = Auth::user();

        // User biasa hanya bisa batalkan pesanannya sendiri
        if (!$user->isSuperAdmin() && (int)$order->user_id !== (int)$user->id) {
            abort(403);
        }

        if (in_array($order->status, ['paid', 'cancelled'])) {
            return back()->with('error', 'Pesanan ini tidak dapat dibatalkan.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', "Pesanan #{$order->id} telah dibatalkan.");
    }
}
