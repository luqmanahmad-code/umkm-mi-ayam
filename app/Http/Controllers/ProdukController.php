<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    // BAGIAN KELOLA KATEGORI
    public function simpanKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategoris,nama_kategori'
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()->back()->with('sukses', 'Kategori baru berhasil ditambahkan!');
    }

    public function hapusKategori($id)
    {
        $kategori = Kategori::findOrFail($id);
        
        // Proteksi: Cek apakah masih ada produk yang menggunakan kategori ini
        if (Produk::where('kategori_id', $id)->exists()) {
            return redirect()->back()->with('error', 'Kategori gagal dihapus karena masih ada menu yang menggunakannya. Pindahkan atau hapus menu tersebut terlebih dahulu.');
        }

        $kategori->delete();
        return redirect()->back()->with('sukses', 'Kategori berhasil dihapus permanen!');
    }

    // BAGIAN KELOLA PRODUK / MENU
    public function simpan(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'harga_jual' => 'required|numeric|min:0',
            'stok_tersedia' => 'required|integer|min:0',
            'batas_minimum_stok' => 'nullable|integer'
        ]);

        // Generate Kode Menu Otomatis (Misal: MN-12345)
        $kodeMenu = 'MN-' . strtoupper(Str::random(5));

        Produk::create([
            'kode_menu' => $kodeMenu,
            'nama_produk' => $request->nama_produk,
            'kategori_id' => $request->kategori_id,
            'harga_jual' => $request->harga_jual,
            'stok_tersedia' => $request->stok_tersedia,
            'batas_minimum_stok' => $request->batas_minimum_stok ?? 10,
        ]);

        return redirect()->back()->with('sukses', 'Menu makanan/minuman baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'harga_jual' => 'required|numeric|min:0',
            'stok_tersedia' => 'required|integer|min:0',
        ]);

        $produk = Produk::findOrFail($id);
        $produk->update([
            'nama_produk' => $request->nama_produk,
            'kategori_id' => $request->kategori_id,
            'harga_jual' => $request->harga_jual,
            'stok_tersedia' => $request->stok_tersedia,
        ]);

        return redirect()->back()->with('sukses', 'Data menu berhasil diperbarui!');
    }

    public function hapus($id)
    {
        $produk = Produk::findOrFail($id);
        $namaProduk = $produk->nama_produk;
        $produk->delete();

        return redirect()->back()->with('sukses', "Menu {$namaProduk} berhasil dihapus dari daftar!");
    }

    public function restock(Request $request, $id)
    {
        $request->validate([
            'tambahan_stok' => 'required|integer|min:1'
        ]);

        $produk = Produk::findOrFail($id);
        $produk->increment('stok_tersedia', $request->tambahan_stok);

        return redirect()->back()->with('sukses', "Stok {$produk->nama_produk} berhasil ditambah {$request->tambahan_stok} porsi!");
    }
}