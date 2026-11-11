<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Penjualan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Login Owner
        User::create([
            'name' => 'Pak Budi (Owner Mi Ayam)',
            'email' => 'owner@miayam.com',
            'password' => Hash::make('password123'),
        ]);

        // 2. Buat Kategori Menu (Mendukung ekspansi makanan baru)
        $katMi = Kategori::create([
            'nama_kategori' => 'Mi Ayam Utama',
            'deskripsi' => 'Menu porsi utama mi ayam'
        ]);
        $katMakananLain = Kategori::create([
            'nama_kategori' => 'Makanan & Camilan',
            'deskripsi' => 'Menu makanan tambahan selain mi ayam'
        ]);
        $katTopping = Kategori::create([
            'nama_kategori' => 'Topping Tambahan',
            'deskripsi' => 'Lauk pendamping mi ayam'
        ]);
        $katMinuman = Kategori::create([
            'nama_kategori' => 'Minuman',
            'deskripsi' => 'Minuman dingin dan hangat'
        ]);

        // 3. Buat Daftar Produk / Menu (Ada yang Tersedia, Menipis, dan Habis)
        $daftarProduk = [
            ['kategori_id' => $katMi->id, 'kode_menu' => 'MA-01', 'nama_produk' => 'Mi Ayam Original', 'harga_jual' => 12000, 'stok_tersedia' => 85, 'batas_minimum_stok' => 15],
            ['kategori_id' => $katMi->id, 'kode_menu' => 'MA-02', 'nama_produk' => 'Mi Ayam Bakso Urat', 'harga_jual' => 16000, 'stok_tersedia' => 50, 'batas_minimum_stok' => 10],
            ['kategori_id' => $katMi->id, 'kode_menu' => 'MA-03', 'nama_produk' => 'Mi Ayam Ceker Lunak', 'harga_jual' => 15000, 'stok_tersedia' => 8, 'batas_minimum_stok' => 10], // Menipis
            ['kategori_id' => $katMi->id, 'kode_menu' => 'MA-04', 'nama_produk' => 'Mi Ayam Komplit Jumbo', 'harga_jual' => 20000, 'stok_tersedia' => 35, 'batas_minimum_stok' => 10],
            ['kategori_id' => $katMakananLain->id, 'kode_menu' => 'MK-01', 'nama_produk' => 'Bakso Kuah Spesial', 'harga_jual' => 15000, 'stok_tersedia' => 40, 'batas_minimum_stok' => 10],
            ['kategori_id' => $katMakananLain->id, 'kode_menu' => 'MK-02', 'nama_produk' => 'Pangsit Goreng Isi Ayam (5 pcs)', 'harga_jual' => 8000, 'stok_tersedia' => 4, 'batas_minimum_stok' => 8], // Menipis
            ['kategori_id' => $katTopping->id, 'kode_menu' => 'TP-01', 'nama_produk' => 'Extra Ceker Pedas', 'harga_jual' => 5000, 'stok_tersedia' => 0, 'batas_minimum_stok' => 5], // Habis
            ['kategori_id' => $katTopping->id, 'kode_menu' => 'TP-02', 'nama_produk' => 'Kerupuk Pangsit Rebus', 'harga_jual' => 3000, 'stok_tersedia' => 30, 'batas_minimum_stok' => 5],
            ['kategori_id' => $katMinuman->id, 'kode_menu' => 'MN-01', 'nama_produk' => 'Es Teh Manis Jumbo', 'harga_jual' => 4000, 'stok_tersedia' => 90, 'batas_minimum_stok' => 15],
            ['kategori_id' => $katMinuman->id, 'kode_menu' => 'MN-02', 'nama_produk' => 'Es Jeruk Peras Murni', 'harga_jual' => 6000, 'stok_tersedia' => 0, 'batas_minimum_stok' => 10], // Habis
        ];

        $produkModels = [];
        foreach ($daftarProduk as $item) {
            $produkModels[] = Produk::create($item);
        }

        // 4. Buat 32 Data Transaksi Penjualan Contoh (Sesuai syarat >= 30 data di rubrik)
        for ($i = 1; $i <= 32; $i++) {
            // Pilih produk yang stoknya masih aman agar tidak minus saat seeding
            $produkPilihan = collect([$produkModels[0], $produkModels[1], $produkModels[3], $produkModels[4], $produkModels[8]])->random();
            $qty = rand(1, 3);
            $tanggal = $i <= 10 ? today() : now()->subDays(rand(1, 6));

            Penjualan::create([
                'no_nota' => 'NOTA-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'produk_id' => $produkPilihan->id,
                'tanggal_terjual' => $tanggal,
                'jumlah_terjual' => $qty,
                'harga_satuan' => $produkPilihan->harga_jual,
                'total_harga' => $produkPilihan->harga_jual * $qty,
                'catatan' => $i <= 10 ? 'Penjualan hari ini' : 'Transaksi minggu ini',
            ]);
        }
    }
}