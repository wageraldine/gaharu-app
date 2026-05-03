<!DOCTYPE html>
<html>
<head>
    <title>Produk Gaharu</title>
    <style>
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: gold;
            font-family: Arial;
            text-align: center;
        }
        .card {
            background: rgba(0,0,0,0.7);
            padding: 20px;
            margin: 20px;
            border-radius: 15px;
            display: inline-block;
            width: 250px;
        }
        img {
            width: 200px;
            height: 150px;
            border-radius: 10px;
        }
        button {
            background: gold;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h1>🕌 Produk Kayu Gaharu</h1>

@foreach($products as $p)
<div class="card">
    <img src="{{ asset($p['image']) }}">
    <h3>{{ $p['name'] }}</h3>
    <p>{{ $p['desc'] }}</p>
    <p><b>{{ $p['price'] }}</b></p>
    <button>Beli</button>
</div>
@endforeach

<br><br>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>

</body>
</html>
