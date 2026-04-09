<h1>Detail Produk Gaharu</h1>

<p><b>Nama:</b> {{ $product->name }}</p>
<p><b>Asal:</b> {{ $product->origin }}</p>
<p><b>Harga:</b> Rp {{ number_format($product->price) }}</p>
<p><b>Deskripsi:</b><br>{{ $product->description }}</p>

<a href="{{ route('products.index') }}">Kembali</a>
