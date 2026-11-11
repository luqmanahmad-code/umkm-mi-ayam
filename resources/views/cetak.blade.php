<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapan Penjualan - Warung Mi Ayam Berkah</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍜</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { margin: 1.5cm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-white text-slate-800 font-sans max-w-4xl mx-auto p-8" onload="window.print()">

    <!-- KOP SURAT LAPORAN -->
    <div class="border-b-2 border-slate-800 pb-4 mb-6 text-center">
        <h1 class="text-3xl font-bold uppercase tracking-wider text-slate-900">Warung Mi Ayam Berkah</h1>
        <p class="text-base font-bold text-amber-700 mt-1 uppercase tracking-wide">📄 Laporan Rekapan Penjualan</p>
        <p class="text-xs font-medium text-slate-500 mt-1">Periode Laporan: <span class="font-semibold text-slate-700">{{ $periode }}</span></p>
    </div>

    <!-- KOTAK RINGKASAN (2 KOLOM SEIMBANG & PROPORSIONAL) -->
    <div class="grid grid-cols-3 bg-stone-50 rounded-2xl border border-stone-200 mb-6 overflow-hidden">
        <div class="p-4 text-center border-r border-stone-200">
            <p class="text-[11px] text-slate-500 uppercase font-bold tracking-wider">Total Transaksi</p>
            <p class="text-xl font-extrabold text-slate-800 mt-1">{{ $riwayatPenjualan->count() }} Nota</p>
        </div>
        <div class="p-4 text-center border-r border-stone-200">
            <p class="text-[11px] text-slate-500 uppercase font-bold tracking-wider">Total Porsi Terjual</p>
            <p class="text-xl font-extrabold text-amber-700 mt-1">{{ $totalPorsi }} Porsi</p>
        </div>
        <div class="p-4 text-center bg-emerald-50/60">
            <p class="text-[11px] text-emerald-700 uppercase font-bold tracking-wider">Total Omzet Pendapatan</p>
            <p class="text-xl font-extrabold text-emerald-700 mt-1">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- TABEL RINCIAN TRANSAKSI -->
    <div class="border border-stone-200 rounded-xl overflow-hidden mb-8">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-stone-100 border-b border-stone-200 text-slate-700 text-xs uppercase tracking-wider">
                    <th class="py-3 px-4 font-bold">No Nota</th>
                    <th class="py-3 px-4 font-bold">Tanggal</th>
                    <th class="py-3 px-4 font-bold">Menu Terjual</th>
                    <th class="py-3 px-4 font-bold text-center">Qty</th>
                    <th class="py-3 px-4 font-bold text-right">Harga Satuan</th>
                    <th class="py-3 px-4 font-bold text-right">Total Masuk</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200">
                @forelse($riwayatPenjualan as $trx)
                <tr>
                    <td class="py-2.5 px-4 font-mono text-xs text-slate-500">{{ $trx->no_nota }}</td>
                    <td class="py-2.5 px-4 text-slate-600">{{ \Carbon\Carbon::parse($trx->tanggal_terjual)->translatedFormat('d M Y') }}</td>
                    <td class="py-2.5 px-4 font-semibold text-slate-800">{{ $trx->produk->nama_produk ?? '-' }}</td>
                    <td class="py-2.5 px-4 text-center font-medium">{{ $trx->jumlah_terjual }} porsi</td>
                    <td class="py-2.5 px-4 text-right text-slate-600">Rp {{ number_format($trx->harga_satuan, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-4 text-right font-bold text-emerald-700">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-500 italic">Tidak ada transaksi pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
            @if($riwayatPenjualan->count() > 0)
            <tfoot>
                <tr class="bg-stone-50 border-t-2 border-stone-300 font-bold text-slate-800">
                    <td colspan="3" class="py-3 px-4 text-right uppercase text-xs tracking-wider text-slate-500">Total Keseluruhan</td>
                    <td class="py-3 px-4 text-center text-amber-700">{{ $totalPorsi }} porsi</td>
                    <td class="py-3 px-4"></td>
                    <td class="py-3 px-4 text-right text-emerald-700 text-base">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <!-- BAGIAN TANDA TANGAN & WAKTU CETAK -->
    <div class="flex justify-between items-end text-xs text-slate-500 mt-10 pt-4 border-t border-stone-200">
        <div>
            <p>Dokumen ini dicetak otomatis dari Sistem Kelola Usaha <strong>Warung Mi Ayam Berkah</strong>.</p>
            <p class="mt-0.5">Waktu Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
        </div>
        <div class="text-center min-w-[160px]">
            <p class="mb-12 text-slate-600">Mengetahui, Owner</p>
            <p class="font-bold text-slate-800 underline">{{ auth()->user()->name }}</p>
        </div>
    </div>

</body>
</html>