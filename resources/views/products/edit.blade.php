<body style="background:#0b0f1a;color:gold;font-family:tahoma;text-align:center">
<h2>Edit Produk</h2>

<form action="/products/{{ $product->id }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')
<input name="name" value="{{ $product->name }}"><br><br>
<input name="region" value="{{ $product->region }}"><br><br>
<input type="number" name="price" value="{{ $product->price }}"><br><br>
<textarea name="description">{{ $product->description }}</textarea><br><br>
<input type="file" name="image"><br><br>
<button style="padding:8px 15px;background:gold;border:none;">Update</button>
</form>

<br><a href="/products" style="color:gold">Kembali</a>
</body>
