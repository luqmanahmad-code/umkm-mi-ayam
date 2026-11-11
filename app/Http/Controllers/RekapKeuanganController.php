<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapKeuanganController extends Controller
{
    public function exportCsv(Request $request): StreamedResponse
    {
        // Filter rentang waktu sesuai pilihan owner
        $riwayatPenjualan = Penjualan::with('produk')
            ->when($request->filter_tanggal === 'hari_ini', fn($q) => $q->whereDate('tanggal_terjual', today()))
            ->when($request->filter_tanggal === 'minggu_ini', fn($q) => $q->whereBetween('tanggal_terjual', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($request->filter_tanggal === 'bulan_ini', fn($q) => $q->whereMonth('tanggal_terjual', now()->month)->whereYear('tanggal_terjual', now()->year))
            ->latest('tanggal_terjual')
            ->get();

        // Ubah ekstensi file menjadi .csv dengan separator titik koma (;) agar otomatis terpecah di Excel Indonesia
        $fileName = 'rekap-penjualan-' . ($request->filter_tanggal ?? 'semua-waktu') . '-' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No Nota', 'Tanggal', 'Nama Menu', 'Jumlah Terjual (Qty)', 'Harga Satuan', 'Total Harga'];

        $callback = function() use($riwayatPenjualan, $columns) {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8 agar karakter tidak berantakan
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Menggunakan pemisah titik koma (;) agar Excel otomatis memecah kolom
            fputcsv($file, $columns, ';');

            $totalQty = 0;
            $totalOmzet = 0;

            foreach ($riwayatPenjualan as $trx) {
                $totalQty += $trx->jumlah_terjual;
                $totalOmzet += $trx->total_harga;

                fputcsv($file, [
                    $trx->no_nota,
                    \Carbon\Carbon::parse($trx->tanggal_terjual)->format('d-m-Y'),
                    $trx->produk->nama_produk ?? '-',
                    $trx->jumlah_terjual,
                    'Rp ' . number_format($trx->harga_satuan, 0, ',', '.'),
                    'Rp ' . number_format($trx->total_harga, 0, ',', '.')
                ], ';');
            }

            // Baris kosong
            fputcsv($file, ['', '', '', '', '', ''], ';');

            // Baris Total di bagian bawah
            fputcsv($file, [
                'TOTAL KESELURUHAN',
                '',
                '',
                $totalQty,
                '',
                'Rp ' . number_format($totalOmzet, 0, ',', '.')
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}