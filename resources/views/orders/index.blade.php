<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📋 {{ Auth::user()->isSuperAdmin() ? 'Semua Pesanan' : 'Pesanan Saya' }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Info Rekening Bank --}}
            <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-4">
                <p class="text-sm font-bold text-green-800 mb-2">🏦 Informasi Pembayaran</p>
                <div class="flex flex-wrap gap-x-8 gap-y-1 text-sm text-green-900">
                    <div><span class="font-medium">Bank</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <span class="font-semibold">Bank Central Asia (BCA)</span></div>
                    <div><span class="font-medium">No. Rekening</span> : <span class="font-semibold tracking-widest">400011223344</span></div>
                    <div><span class="font-medium">Atas Nama</span>&nbsp;&nbsp; : <span class="font-semibold">PT Gaharu Indonesia</span></div>
                </div>
            </div>

            @if ($orders->isEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-16 text-center text-gray-500">
                    <p class="text-4xl mb-3">📋</p>
                    <p class="text-lg font-medium">Belum ada pesanan</p>
                    <a href="{{ route('dashboard') }}" class="mt-3 inline-block text-sm text-indigo-600 hover:underline">Lihat Produk</a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($orders as $order)
                    @php
                        $color = $order->statusColor();
                        $colorMap = [
                            'yellow' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-200'],
                            'blue'   => ['bg' => 'bg-blue-100',   'text' => 'text-blue-800',   'border' => 'border-blue-200'],
                            'green'  => ['bg' => 'bg-green-100',  'text' => 'text-green-800',  'border' => 'border-green-200'],
                            'red'    => ['bg' => 'bg-red-100',    'text' => 'text-red-800',    'border' => 'border-red-200'],
                            'gray'   => ['bg' => 'bg-gray-100',   'text' => 'text-gray-800',   'border' => 'border-gray-200'],
                        ];
                        $c = $colorMap[$color] ?? $colorMap['gray'];
                    @endphp

                    <div x-data="{ showProof: {{ session('proof_order_id') == $order->id ? 'true' : 'false' }}, showItems: false, showPreview: false, showEdit: false }"
                         class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                        {{-- Header pesanan --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-gray-700">#{{ $order->id }}</span>
                                <span class="text-xs {{ $c['bg'] }} {{ $c['text'] }} {{ $c['border'] }} border rounded-full px-3 py-1 font-semibold">
                                    {{ $order->statusLabel() }}
                                </span>
                                @if (Auth::user()->isSuperAdmin())
                                    <span class="text-xs text-gray-500">
                                        {{ $order->user->name ?: $order->user->username }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 text-xs text-gray-400">
                                <span class="order-time" data-utc="{{ $order->created_at->toIso8601String() }}"
                                     title="{{ $order->created_at->toIso8601String() }}">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                <button @click="showItems = !showItems"
                                    class="text-indigo-600 hover:text-indigo-800 font-medium"
                                    x-text="showItems ? 'Sembunyikan Detail' : 'Lihat Detail'">
                                </button>
                            </div>
                        </div>

                        {{-- Tabel item (collapsible) --}}
                        <div x-show="showItems"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             style="display:none">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-5 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                        <th class="px-5 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                        <th class="px-5 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                        <th class="px-5 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($order->items as $item)
                                    <tr>
                                        <td class="px-5 py-3 font-medium text-gray-800">{{ $item['name'] }}</td>
                                        <td class="px-5 py-3 text-gray-500">Rp {{ number_format($item['price']) }}</td>
                                        <td class="px-5 py-3 text-gray-500">{{ $item['qty'] }}</td>
                                        <td class="px-5 py-3 font-semibold text-indigo-600">Rp {{ number_format($item['subtotal']) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if ($order->notes)
                                <div class="px-5 py-3 text-sm text-gray-500 bg-gray-50 border-t border-gray-100">
                                    <span class="font-medium text-gray-700">Catatan:</span> {{ $order->notes }}
                                </div>
                            @endif
                        </div>

                        {{-- Footer: total + aksi --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 bg-gray-50 border-t border-gray-100">
                            <span class="font-bold text-indigo-700">Total: Rp {{ number_format($order->total) }}</span>

                            <div class="flex flex-wrap gap-2 items-center">

                                {{-- Bukti bayar: lihat (semua role) --}}
                                @if ($order->payment_proof)
                                    @php
                                        $proofUrl  = route('orders.proofView', $order->id);
                                        $ext       = strtolower(pathinfo($order->payment_proof, PATHINFO_EXTENSION));
                                        $isImage   = in_array($ext, ['jpg','jpeg','png','webp']);
                                    @endphp

                                    @if ($isImage)
                                        {{-- Preview inline untuk gambar --}}
                                        <button @click="showPreview = true"
                                            class="px-3 py-1.5 text-xs bg-gray-100 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-200 transition">
                                            🧾 Lihat Bukti Bayar
                                        </button>

                                        {{-- Modal preview gambar --}}
                                        <div x-show="showPreview"
                                             style="display:none"
                                             class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                             @click.self="showPreview = false"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100"
                                             x-transition:leave-end="opacity-0">
                                            <div class="absolute inset-0 bg-black/70"></div>
                                            <div class="relative z-10 bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden"
                                                 @click.stop
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100">
                                                <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100">
                                                    <span class="text-sm font-semibold text-gray-800">🧾 Bukti Pembayaran — Pesanan #{{ $order->id }}</span>
                                                    <div class="flex items-center gap-2">
                                                        <a href="{{ $proofUrl }}" target="_blank"
                                                           class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                                            Buka di tab baru ↗
                                                        </a>
                                                        <button @click="showPreview = false" class="text-gray-400 hover:text-gray-700 text-xl leading-none ml-2">&times;</button>
                                                    </div>
                                                </div>
                                                <div class="p-4 bg-gray-50 flex items-center justify-center min-h-48">
                                                    <img src="{{ $proofUrl }}"
                                                         alt="Bukti Pembayaran #{{ $order->id }}"
                                                         class="max-w-full max-h-[60vh] rounded-lg object-contain shadow">
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{-- PDF: buka di tab baru --}}
                                        <a href="{{ $proofUrl }}" target="_blank"
                                           class="px-3 py-1.5 text-xs bg-gray-100 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-200 transition">
                                            🧾 Lihat Bukti Bayar (PDF)
                                        </a>
                                    @endif
                                @endif

                                {{-- Upload/ganti bukti bayar (semua user jika status memungkinkan; super admin tanpa batasan status) --}}
                                @if (Auth::user()->isSuperAdmin() || in_array($order->status, ['pending_payment', 'waiting_confirmation']))
                                    <button @click="showProof = !showProof; showEdit = false"
                                        class="px-3 py-1.5 text-xs bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                        {{ $order->payment_proof ? '🔄 Ganti Bukti' : '📤 Upload Bukti Bayar' }}
                                    </button>
                                @endif

                                {{-- Tandai lunas (super admin, status waiting_confirmation) --}}
                                @if (Auth::user()->isSuperAdmin() && $order->status === 'waiting_confirmation')
                                    <form method="POST" action="{{ route('orders.markPaid', $order->id) }}"
                                          onsubmit="return confirm('Tandai pesanan #{{ $order->id }} sebagai Lunas?')">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1.5 text-xs bg-green-600 text-white rounded-md hover:bg-green-700 transition font-semibold">
                                            ✅ Tandai Lunas
                                        </button>
                                    </form>
                                @endif

                                {{-- Hapus (super admin only) --}}
                                @if (Auth::user()->isSuperAdmin())
                                    <form method="POST" action="{{ route('orders.destroy', $order->id) }}"
                                          onsubmit="return confirm('Hapus pesanan #{{ $order->id }}? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1.5 text-xs bg-red-500 text-white rounded-md hover:bg-red-600 transition">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                @endif

                                {{-- Batalkan Pesanan (user & super admin, selama belum lunas/dibatalkan) --}}
                                @if (!in_array($order->status, ['paid', 'cancelled']))
                                    <form method="POST" action="{{ route('orders.cancel', $order->id) }}"
                                          onsubmit="return confirm('Batalkan pesanan #{{ $order->id }}?')">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1.5 text-xs bg-orange-500 text-white rounded-md hover:bg-orange-600 transition font-medium">
                                            ❌ Batalkan Pesanan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- Form upload bukti bayar (inline, collapsible) --}}
                        @if (Auth::user()->isSuperAdmin() || in_array($order->status, ['pending_payment', 'waiting_confirmation']))
                        <div x-show="showProof" style="display:none"
                             class="px-5 py-4 border-t border-gray-200 bg-indigo-50">
                            <form method="POST" action="{{ route('orders.proof', $order->id) }}"
                                  enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
                                @csrf
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        File Bukti Pembayaran
                                        <span class="text-gray-400 font-normal">(JPG, PNG, PDF · Maks 2MB)</span>
                                    </label>
                                    <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" required
                                        class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200">
                                    @error('payment_proof')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" @click="showProof = false"
                                        class="px-3 py-1.5 text-xs border border-gray-300 rounded-md text-gray-600 hover:bg-gray-50 transition">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="px-3 py-1.5 text-xs bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition font-semibold">
                                        Kirim Bukti
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif



                    </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

    <script>
        document.querySelectorAll('.order-time').forEach(function (el) {
            const utc = el.getAttribute('data-utc');
            if (!utc) return;
            try {
                const date = new Date(utc);
                const formatted = date.toLocaleString('id-ID', {
                    day:    '2-digit',
                    month:  'long',
                    year:   'numeric',
                    hour:   '2-digit',
                    minute: '2-digit',
                    hour12: false,
                });
                el.textContent = formatted;
                el.title = 'Waktu lokal Anda: ' + formatted;
            } catch (e) {}
        });
    </script>
</x-app-layout>
