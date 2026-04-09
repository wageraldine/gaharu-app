<body style="background:#0b0f1a;color:gold;font-family:tahoma;text-align:center">
<h2>Tambah Produk Gaharu</h2>

<form action="/products" method="POST" enctype="multipart/form-data">
@csrf
<input name="name" placeholder="Nama Produk"><br><br>
<input name="region" placeholder="Asal (Kalimantan/Sumatra/Merauke)"><br><br>
<input type="number" name="price" placeholder="Harga"><br><br>
<textarea name="description" placeholder="Deskripsi"></textarea><br><br>
<input type="file" name="image"><br><br>
<button style="padding:8px 15px;background:gold;border:none;">Simpan</button>
</form>

<br><a href="/products" style="color:gold">Kembali</a>
</body>
