<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🛒 Keranjang Belanja
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800">← Kembali ke Dashboard</a>
        </div>
    </x-slot>

    <div class="py-10" x-data="{ showOrderModal: false }" @keydown.escape.window="showOrderModal = false">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                @if(empty($cart) || !$products)
                    <div class="p-10 text-center text-gray-500">
                        <p class="text-4xl mb-3">🛒</p>
                        <p class="text-lg font-medium">Keranjang masih kosong</p>
                        <a href="{{ route('dashboard') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:underline">Lihat produk</a>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $total = 0; @endphp
                            @foreach($cart as $id => $qty)
                                @php $p = $products->get((int)$id); @endphp
                                @if($p)
                                    @php
                                        $subtotal = $p->price * $qty;
                                        $total += $subtotal;
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $p->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">Rp {{ number_format($p->price) }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $qty }}</td>
                                        <td class="px-6 py-4 text-sm font-semibold text-indigo-600">Rp {{ number_format($subtotal) }}</td>
                                        <td class="px-6 py-4">
                                            <form method="POST" action="/cart/remove/{{ $id }}" onsubmit="return confirm('Hapus item ini dari keranjang?')">
                                                @csrf
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right font-semibold text-gray-700">Total:</td>
                                <td class="px-6 py-4 font-bold text-indigo-700">Rp {{ number_format($total) }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- Tombol Buat Pesanan --}}
                    <div class="px-6 py-4 bg-indigo-50 border-t border-gray-200 flex justify-between items-center">
                        <span class="text-sm text-gray-600">
                            Total: <strong class="text-indigo-700">Rp {{ number_format($total) }}</strong>
                        </span>
                        <button @click="showOrderModal = true"
                            class="px-5 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                            📋 Buat Pesanan
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- ===== MODAL BUAT PESANAN ===== --}}
        <div x-show="showOrderModal"
             style="display:none"
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @click.self="showOrderModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="absolute inset-0 bg-black/50"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden"
                 @click.stop
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800">📋 Konfirmasi Pesanan</h3>
                    <button @click="showOrderModal = false" class="text-gray-400 hover:text-gray-700 text-xl leading-none">&times;</button>
                </div>

                {{-- Ringkasan item --}}
                <div class="px-5 py-3 max-h-56 overflow-y-auto">
                    <table class="w-full text-sm">
                        @php $modalTotal = 0; @endphp
                        @foreach ($cart as $id => $qty)
                            @php $mp = $products->get((int)$id); @endphp
                            @if ($mp)
                                @php
                                    $ms = $mp->price * $qty;
                                    $modalTotal += $ms;
                                @endphp
                                <tr class="border-b border-gray-100 last:border-0">
                                    <td class="py-2 font-medium text-gray-800">{{ $mp->name }}</td>
                                    <td class="py-2 text-center text-gray-500">x{{ $qty }}</td>
                                    <td class="py-2 text-right font-semibold text-indigo-600">Rp {{ number_format($ms) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>

                <div class="px-5 py-3 bg-indigo-50 border-t border-gray-100 flex justify-between text-sm font-bold">
                    <span class="text-gray-700">Total Pesanan</span>
                    <span class="text-indigo-700">Rp {{ number_format($modalTotal) }}</span>
                </div>

                {{-- Form submit --}}
                <form method="POST" action="{{ route('orders.store') }}" class="px-5 py-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            Catatan <span class="text-gray-400 font-normal">(opsional)</span>
                        </label>
                        <textarea name="notes" rows="2"
                            placeholder="Contoh: Tolong kemas dengan aman"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Setelah pesanan dibuat, keranjang akan dikosongkan. Upload bukti pembayaran di menu Pesanan.</p>
                    <div class="flex gap-2 mt-4">
                        <button type="button" @click="showOrderModal = false"
                            class="flex-1 px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                            Buat Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        {{-- ===== END MODAL ===== --}}

    </div>
</x-app-layout>
