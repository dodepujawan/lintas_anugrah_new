<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
body { font-size: 10px; }
table { width: 100%; border-collapse: collapse; }
td, th { border: 1px solid #000; padding: 4px; }
.no-border { border: none; }
.text-center { text-align: center; }
.text-right { text-align: right; }
</style>
<body>
    {{-- Header --}}
    <table class="no-border">
        <tr>
            {{-- <td class="no-border" width="20%">
                <img src="{{ public_path('logo.png') }}" width="80">
            </td> --}}
            <td class="no-border">
                <strong>PT. LINTAS MITRA ANUGERAH SEJATI</strong><br>
                COLD CHAIN DISTRIBUTION & STORAGE<br>
                Denpasar - Bali
            </td>
            <td class="no-border text-right">
                <strong>SURAT JALAN EKSPEDISI</strong><br>
                NO SJ: {{ $expedisi->NOSJ }}
            </td>
        </tr>
    </table>
    {{-- Identitas --}}
    <table>
        <tr>
            <td>Tanggal SJ</td>
            <td>{{ $expedisi->tglsj }}</td>
            <td>Nama Pengirim</td>
            <td>{{ $expedisi->CUSTOMER }}</td>
        </tr>
        <tr>
            <td>Nama Penerima</td>
            <td>{{ $expedisi->P_NAMA }}</td>
            <td>No Telp</td>
            <td>{{ $expedisi->P_PHONE }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td colspan="3">{{ $expedisi->P_ALAMAT }}</td>
        </tr>
    </table>
    {{-- Item Barang --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Item Barang</th>
                <th>F/C</th>
                <th>Suhu</th>
                <th>Koli</th>
                <th>Kg / Ton</th>
                <th>Kondisi Barang</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 1; $i <= 6; $i++)
            <tr>
                <td class="text-center">{{ $i }}</td>
                <td>&nbsp;</td>
                <td class="text-center">☐</td>
                <td class="text-center">&nbsp;</td>
                <td class="text-center">&nbsp;</td>
                <td class="text-center">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @endfor
        </tbody>
    </table>
    {{-- Total --}}
    <table>
        <tr>
            <td width="70%">Catatan Khusus</td>
            <td class="text-right">Subtotal</td>
            <td class="text-right">
                {{ number_format($expedisi->TOTAL,0,',','.') }}
            </td>
        </tr>
        <tr>
            <td rowspan="3"></td>
            <td class="text-right">PPN</td>
            <td class="text-right">
                {{ number_format($expedisi->PPN,0,',','.') }}
            </td>
        </tr>
        <tr>
            <td class="text-right">Grand Total</td>
            <td class="text-right">
                {{ number_format($expedisi->GRAND,0,',','.') }}
            </td>
        </tr>
    </table>
    {{-- Tanda Tangan --}}
    <table class="no-border" style="margin-top:30px;">
        <tr>
            <td class="text-center no-border">Pengirim</td>
            <td class="text-center no-border">Driver</td>
            <td class="text-center no-border">Penerima</td>
        </tr>
        <tr>
            <td height="60" class="no-border"></td>
            <td class="no-border"></td>
            <td class="no-border"></td>
        </tr>
    </table>
</body>
</html>
