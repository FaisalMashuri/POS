<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Barcode</title>

    <style>
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <table width="100%">
        <tr>
            @foreach ($dataproduk as $produk)
                @for ($i = 0; $i <$produk->quantity; $i++)
                    <td class="text-center" style="border: 1px solid #333;">
                        <p>{{ $produk->product->nama_produk }} - Rp. {{ format_uang($produk->product->harga_jual) }}</p>
                        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($produk->product->kode_produk, 'C39') }}"
                            alt="{{ $produk->product->kode_produk }}"
                            width="180"
                            height="60">
                        <br>
                        {{ $produk->product->kode_produk }}
                    </td>
                    <br>

                    @if ($no++ % 3 == 0)
                        </tr>
                        <tr>
                    @endif
                @endfor
                
            @endforeach
        </tr>
    </table>
</body>
</html>