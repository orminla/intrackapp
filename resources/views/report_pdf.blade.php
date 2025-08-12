<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Bukti Laporan Inspeksi</title>
        <style>
            body {
                font-family:
                    DejaVu Sans,
                    sans-serif;
                font-size: 13px;
                margin: 0;
                padding: 30px;
                background-color: #fff;
                color: #000;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
            }
            td {
                border: 1px solid #ccc;
                padding: 6px 8px;
                vertical-align: top;
            }
            .table-title {
                background-color: #004a91;
                color: #fff;
                font-weight: bold;
                text-transform: uppercase;
            }
            ul {
                margin: 5px 0;
                padding-left: 20px;
            }
            .footer {
                font-size: 11px;
                text-align: center;
                margin-top: 20px;
                color: #555;
            }
            .lampiran-img {
                max-width: 100%;
                margin-top: 5px;
                page-break-inside: avoid;
            }
            .lampiran-item {
                margin-bottom: 15px;
                page-break-inside: avoid;
            }
        </style>
    </head>
    <body>
        {{-- Judul --}}
        <h2
            style="
                text-align: center;
                font-size: 24px;
                color: #004a91;
                margin-bottom: 0px;
            "
        >
            INSPECTION REPORT VALIDATION
        </h2>
        <p
            style="
                text-align: center;
                font-size: 18px;
                margin-top: 0;
                margin-bottom: 36px;
                color: #004a91;
            "
        >
            PT SUCOFINDO Cabang Pontianak
        </p>

        {{-- Informasi Inspeksi --}}
        <table>
            <tr>
                <td class="table-title" colspan="2">Informasi Inspeksi</td>
            </tr>
            <tr>
                <td>Mitra</td>
                <td>{{ $partner->name ?? "-" }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>{{ $partner->address ?? "-" }}</td>
            </tr>
            <tr>
                <td>Petugas</td>
                <td>{{ $inspector->name ?? "-" }}</td>
            </tr>
            <tr>
                <td>Bidang</td>
                <td>{{ $department ?? "-" }}</td>
            </tr>
            <tr>
                <td>Portofolio</td>
                <td>{{ $portofolio ?? "-" }}</td>
            </tr>
            <tr>
                <td>Tanggal Mulai</td>
                <td>
                    {{ $started_date ? \Carbon\Carbon::parse($started_date)->translatedFormat("l, d-m-Y") : "-" }}
                </td>
            </tr>
            <tr>
                <td>Tanggal Selesai</td>
                <td>
                    {{ $finished_date ? \Carbon\Carbon::parse($finished_date)->translatedFormat("l, d-m-Y") : "-" }}
                </td>
            </tr>
            <tr>
                <td>Produk</td>
                <td>{{ $product->name ?? "-" }}</td>
            </tr>
        </table>

        {{-- Detail Produk --}}
        <table>
            <tr>
                <td class="table-title">Detail Produk</td>
            </tr>
            @if (! empty($details) && count($details) > 0)
                @foreach ($details as $detail)
                    <tr>
                        <td>{{ $detail->name ?? "-" }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td><em>Tidak ada detail produk</em></td>
                </tr>
            @endif
        </table>

        {{-- Lampiran --}}
        @if (count($documents) > 0)
            <table>
                <tr>
                    <td class="table-title">Lampiran Dokumen</td>
                </tr>
                @foreach ($documents as $doc)
                    <tr>
                        <td>{{ $doc["name"] }}</td>
                    </tr>
                @endforeach
            </table>
        @endif

        {{-- Keterangan --}}
        <div
            style="
                margin-top: 15px;
                padding: 10px;
                background: #e8f5e9;
                font-size: 12px;
                color: #2e7d32;
            "
        >
            Bukti ini telah terverifikasi oleh Admin PT SUCOFINDO Cabang
            Pontianak dan dinyatakan sah.
        </div>

        {{-- Footer --}}
        <div class="footer">
            Dicetak pada tanggal
            {{ \Carbon\Carbon::now()->format("d-m-Y H:i") }} | InTrack App by
            Nirmala &copy; {{ date("Y") }} All rights reserved.
        </div>
    </body>
</html>
