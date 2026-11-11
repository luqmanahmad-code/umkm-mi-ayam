<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AiAssistantController extends Controller
{
    public function tanyaAi(Request $request)
    {
        $request->validate([
            'pesan' => 'required|string|max:300'
        ]);

        // 1. Kumpulkan Data Real-Time Warung
        $produkTerlaris = Penjualan::select(
                'produk_id',
                DB::raw('SUM(jumlah_terjual) as total_qty'),
                DB::raw('SUM(total_harga) as total_omzet')
            )
            ->with('produk:id,nama_produk,harga_jual,stok_tersedia')
            ->groupBy('produk_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $stokKritis = Produk::whereColumn('stok_tersedia', '<=', 'batas_minimum_stok')
            ->get(['nama_produk', 'stok_tersedia', 'harga_jual']);

        $omzetHariIni = Penjualan::whereDate('tanggal_terjual', today())->sum('total_harga') ?? 0;
        $porsiHariIni = Penjualan::whereDate('tanggal_terjual', today())->sum('jumlah_terjual') ?? 0;
        $omzetBulanIni = Penjualan::whereMonth('tanggal_terjual', now()->month)->sum('total_harga') ?? 0;

        $apiKey = env('GEMINI_API_KEY');

        // Jika API Key tidak ada, langsung pakai AI Lokal
        if (empty($apiKey)) {
            return response()->json([
                'jawaban' => $this->jawabanLokalPintar($request->pesan, $produkTerlaris, $stokKritis, $omzetHariIni, $porsiHariIni, $omzetBulanIni)
            ]);
        }

        try {
            $konteks = json_encode([
                'omzet_hari_ini_rp' => $omzetHariIni,
                'porsi_terjual_hari_ini' => $porsiHariIni,
                'total_omzet_bulan_ini_rp' => $omzetBulanIni,
                'top_menu_laris' => $produkTerlaris,
                'stok_hampir_habis' => $stokKritis,
            ]);

            $prompt = "Kamu adalah asisten bisnis untuk warung Mi Ayam. Data saat ini: {$konteks}. Jawab pertanyaan owner berikut dengan ramah, singkat, dan berikan ide kreatif jika diminta: '{$request->pesan}'";

            // PERUBAHAN FINAL: Menggunakan nama model presisi 'gemini-1.5-flash-latest' di jalur v1beta
            $response = Http::withoutVerifying()
                ->timeout(20) 
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $jawabanAi = $response->json('candidates.0.content.parts.0.text');
                if (!empty($jawabanAi)) {
                    return response()->json(['jawaban' => $jawabanAi]);
                }
            }

            // Jika API Google sedang gangguan ringan, pakai AI Lokal tanpa memunculkan error merah
            return response()->json([
                'jawaban' => $this->jawabanLokalPintar($request->pesan, $produkTerlaris, $stokKritis, $omzetHariIni, $porsiHariIni, $omzetBulanIni)
            ]);

        } catch (\Exception $e) {
            // Jika koneksi terputus sama sekali, otomatis ditangani oleh AI Lokal
            return response()->json([
                'jawaban' => $this->jawabanLokalPintar($request->pesan, $produkTerlaris, $stokKritis, $omzetHariIni, $porsiHariIni, $omzetBulanIni)
            ]);
        }
    }

    // FUNGSI AI LOKAL SUPER PINTAR (Sistem Cadangan)
    private function jawabanLokalPintar($pesan, $produkTerlaris, $stokKritis, $omzetHariIni, $porsiHariIni, $omzetBulanIni)
    {
        $pesan = strtolower($pesan);
        $top1 = $produkTerlaris->first();
        $namaTop1 = $top1 && $top1->produk ? $top1->produk->nama_produk : 'Menu Spesial';

        if (str_contains($pesan, 'promo') || str_contains($pesan, 'ide') || str_contains($pesan, 'strategi') || str_contains($pesan, 'meningkatkan')) {
            return "💡 **Ide Promosi Hari Ini:**\n1. **Paket Bundling:** Tawarkan *{$namaTop1}* gratis minum untuk pembelian di jam sepi (14.00-16.00).\n2. **Loyalty Program:** Kumpulkan 5 stempel nota gratis 1 porsi pangsit.\n3. **Promo Sosmed:** Diskon Rp2.000 jika pelanggan menandai warung kita di story IG/WhatsApp mereka!";
        }
        
        elseif (str_contains($pesan, 'stok') || str_contains($pesan, 'habis') || str_contains($pesan, 'kritis') || str_contains($pesan, 'sisa')) {
            $daftarHabis = $stokKritis->map(fn($p) => "• {$p->nama_produk} (Sisa: {$p->stok_tersedia})")->implode("\n");
            if (empty($daftarHabis)) {
                return "✅ **Info Stok:** Semua persediaan menu aman, tidak ada bahan yang kritis hari ini.";
            }
            return "⚠️ **Perhatian! Menu Berikut Hampir Habis:**\n" . $daftarHabis;
        }
        
        elseif (str_contains($pesan, 'laris') || str_contains($pesan, 'terbaik') || str_contains($pesan, 'populer')) {
            return "🏆 **Menu Terlaris:**\nSaat ini, menu yang paling diminati pelanggan adalah **{$namaTop1}**. Pastikan bahan bakunya tidak sampai kehabisan!";
        }
        
        elseif (str_contains($pesan, 'porsi') || str_contains($pesan, 'uang masuk') || str_contains($pesan, 'hari ini') || str_contains($pesan, 'rekap')) {
            return "📊 **Rekap Omzet Hari Ini:**\nKita sudah menjual **{$porsiHariIni} porsi**, dengan total uang masuk sebesar **Rp " . number_format($omzetHariIni, 0, ',', '.') . "**.";
        }

        return "Halo! Uang masuk kita hari ini **Rp " . number_format($omzetHariIni, 0, ',', '.') . "** dari **{$porsiHariIni} porsi** terjual. Ada pertanyaan lain soal stok, promo, atau menu unggulan?";
    }
}