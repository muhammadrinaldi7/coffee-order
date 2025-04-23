<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Meja {{ $table->table_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }
        .qr-container {
            border: 2px solid #000;
            padding: 10px;
            display: inline-block;
            margin-top: 20px;
        }
        img {
            width: 200px;
            height: 200px;
        }
    </style>
</head>
<body>
    <h2>Meja No. {{ $table->table_number }}</h2>
    <div class="qr-container">
        <img src="{{ $filePath }}" alt="QR Code">
    </div>
    <p>Scan QR Code ini untuk melakukan pemesanan</p>
</body>
</html>
