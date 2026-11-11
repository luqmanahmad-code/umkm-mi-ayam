<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class OwnerPortalController extends Controller
{

    // AUTHENTICATION
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('beranda');
        }
        return view('auth.login');
    }

    public function prosesLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/beranda');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi kurang tepat, Pak/Bu.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // FITUR LUPA & RESET KATA SANDI
    public function formLupaPassword()
    {
        return view('auth.forgot-password');
    }

    public function kirimLinkReset(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', 'Tautan pemulihan kata sandi telah dikirim ke email Anda.')
                    : back()->withErrors(['email' => __($status)]);
    }

    public function formResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function prosesResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->password = Hash::make($request->password);
                $user->save();
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('sukses', 'Kata sandi berhasil diubah! Silakan masuk kembali.')
                    : back()->withErrors(['email' => [__($status)]]);
    }

    // BERANDA & DASHBOARD
    public function beranda(Request $request)
    {
        // 🌟 FITUR AUTO-HEALING: Otomatis ubah semua stok minus menjadi 0 di database
        Produk::where('stok_tersedia', '<', 0)->update(['stok_tersedia' => 0]);

        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $semuaProduk = Produk::orderBy('nama_produk')->get();

        $produks = Produk::with('kategori')
            ->when($request->cari, function ($q) use ($request) {
                $q->where('nama_produk', 'like', '%' . $request->cari . '%')
                  ->orWhere('kode_menu', 'like', '%' . $request->cari . '%');
            })
            ->when($request->status === 'habis', fn($q) => $q->where('stok_tersedia', '<=', 0))
            ->when($request->status === 'menipis', fn($q) => $q->where('stok_tersedia', '>', 0)->whereColumn('stok_tersedia', '<=', 'batas_minimum_stok'))
            ->when($request->status === 'tersedia', fn($q) => $q->whereColumn('stok_tersedia', '>', 'batas_minimum_stok'))
            ->orderBy('stok_tersedia', 'asc')
            ->paginate(5, ['*'], 'page_stok')
            ->withQueryString();

        $uangMasukHariIni = Penjualan::whereDate('tanggal_terjual', today())->sum('total_harga');
        $porsiTerjualHariIni = Penjualan::whereDate('tanggal_terjual', today())->sum('jumlah_terjual');
        $uangMasukBulanIni = Penjualan::whereMonth('tanggal_terjual', now()->month)
            ->whereYear('tanggal_terjual', now()->year)
            ->sum('total_harga');

        $jumlahTersedia = Produk::whereColumn('stok_tersedia', '>', 'batas_minimum_stok')->count();
        $jumlahPerluRestock = Produk::whereColumn('stok_tersedia', '<=', 'batas_minimum_stok')->count();

        // Riwayat Penjualan
        $riwayatPenjualan = Penjualan::with('produk.kategori')
            ->when($request->filter_tanggal === 'hari_ini', fn($q) => $q->whereDate('tanggal_terjual', today()))
            ->when($request->filter_tanggal === 'minggu_ini', fn($q) => $q->whereBetween('tanggal_terjual', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($request->filter_tanggal === 'bulan_ini', fn($q) => $q->whereMonth('tanggal_terjual', now()->month)->whereYear('tanggal_terjual', now()->year))
            ->latest('tanggal_terjual')
            ->latest('id')
            ->paginate(5, ['*'], 'page_riwayat')
            ->withQueryString();

        return view('beranda', compact(
            'kategoris', 'semuaProduk', 'produks',
            'uangMasukHariIni', 'porsiTerjualHariIni', 'uangMasukBulanIni',
            'jumlahTersedia', 'jumlahPerluRestock', 'riwayatPenjualan'
        ));
    }

    // KELOLA PRODUK (MENU)
    public function simpanProduk(Request $request)
    {
        $data = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'nama_produk' => 'required|string|max:100',
            'harga_jual' => 'required|integer|min:500',
            'stok_tersedia' => 'required|integer|min:0',
            'batas_minimum_stok' => 'required|integer|min:1',
        ]);

        $urutTerakhir = (Produk::max('id') ?? 0) + 1;
        $data['kode_menu'] = 'MNU-' . str_pad($urutTerakhir, 3, '0', STR_PAD_LEFT);

        Produk::create($data);
        return back()->with([
            'sukses' => "Menu baru '{$request->nama_produk}' berhasil ditambahkan!",
            'active_tab' => 'tab-kasir'
        ]);
    }

    public function updateProduk(Request $request, Produk $produk)
    {
        $data = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'nama_produk' => 'required|string|max:100',
            'harga_jual' => 'required|integer|min:500',
            'stok_tersedia' => 'required|integer|min:0',
        ]);

        $produk->update($data);
        return back()->with([
            'sukses' => "Menu '{$produk->nama_produk}' berhasil diperbarui!",
            'active_tab' => 'tab-stok'
        ]);
    }

    public function hapusProduk(Produk $produk)
    {
        $nama = $produk->nama_produk;
        $produk->delete();
        return back()->with([
            'sukses' => "Menu '{$nama}' berhasil dihapus dari daftar!",
            'active_tab' => 'tab-stok'
        ]);
    }

    public function tambahStok(Request $request, Produk $produk)
    {
        $request->validate([
            'tambahan_stok' => 'required|integer|min:1',
        ]);

        $produk->increment('stok_tersedia', $request->tambahan_stok);
        return back()->with([
            'sukses' => "Stok {$produk->nama_produk} berhasil ditambah (+{$request->tambahan_stok})!",
            'active_tab' => 'tab-stok'
        ]);
    }

    // KELOLA KATEGORI
    public function simpanKategori(Request $request)
    {
        $data = $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategoris,nama_kategori',
        ]);

        $data['deskripsi'] = 'Kategori makanan/minuman tambahan';
        Kategori::create($data);

        return back()->with([
            'sukses' => "Kategori jenis makanan '{$request->nama_kategori}' berhasil dibuat!",
            'active_tab' => 'tab-kasir'
        ]);
    }

    public function hapusKategori($id)
    {
        $kategori = Kategori::findOrFail($id);
        
        if (Produk::where('kategori_id', $id)->exists()) {
            return back()->with([
                'error' => 'Kategori gagal dihapus karena masih dipakai pada suatu menu.',
                'active_tab' => 'tab-kasir'
            ]);
        }

        $kategori->delete();
        return back()->with([
            'sukses' => 'Kategori berhasil dihapus!',
            'active_tab' => 'tab-kasir'
        ]);
    }

    // PENJUALAN (KASIR - MENDUKUNG 1 BARANG MAUPUN BANYAK BARANG SEKALIGUS)
    public function simpanPenjualan(Request $request)
    {
        // Mendukung input banyak baris (items array) maupun 1 baris biasa
        $items = $request->input('items', [
            [
                'produk_id' => $request->produk_id,
                'jumlah_terjual' => $request->jumlah_terjual,
            ]
        ]);

        $request->merge(['items' => $items]);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.jumlah_terjual' => 'required|integer|min:1',
            'catatan' => 'nullable|string|max:100',
        ]);

        // Gabungkan jumlah jika owner memilih menu yang sama di lebih dari 1 baris
        $rekapItems = [];
        foreach ($items as $item) {
            $id = $item['produk_id'];
            $qty = (int) $item['jumlah_terjual'];
            $rekapItems[$id] = ($rekapItems[$id] ?? 0) + $qty;
        }

        // Validasi kecukupan stok untuk seluruh menu sebelum disimpan
        foreach ($rekapItems as $produkId => $totalQty) {
            $produk = Produk::findOrFail($produkId);
            if ($produk->stok_tersedia < $totalQty) {
                return back()->withErrors([
                    'stok' => "Stok {$produk->nama_produk} tidak mencukupi! Sisa stok: {$produk->stok_tersedia}."
                ])->with('active_tab', 'tab-kasir');
            }
        }

        // Simpan seluruh item penjualan & kurangi stok
        $kodeNotaDasar = 'NOTA-' . now()->format('ymdHis');
        $urutan = 1;
        $jumlahJenis = count($rekapItems);
        $ringkasanTeks = [];

        foreach ($rekapItems as $produkId => $totalQty) {
            $produk = Produk::findOrFail($produkId);

            Penjualan::create([
                'no_nota' => $jumlahJenis > 1 ? "{$kodeNotaDasar}-{$urutan}" : $kodeNotaDasar,
                'produk_id' => $produk->id,
                'tanggal_terjual' => today(),
                'jumlah_terjual' => $totalQty,
                'harga_satuan' => $produk->harga_jual,
                'total_harga' => $produk->harga_jual * $totalQty,
                'catatan' => $request->catatan ?? 'Penjualan kasir',
            ]);

            $produk->decrement('stok_tersedia', $totalQty);
            $ringkasanTeks[] = "{$totalQty}x {$produk->nama_produk}";
            $urutan++;
        }

        return back()->with([
            'sukses' => 'Penjualan (' . implode(', ', $ringkasanTeks) . ') berhasil dicatat!',
            'active_tab' => 'tab-kasir'
        ]);
    }

    // CETAK LAPORAN PDF
    public function cetakPdf(Request $request)
    {
        $riwayatPenjualan = Penjualan::with('produk.kategori')
            ->when($request->filter_tanggal === 'hari_ini', fn($q) => $q->whereDate('tanggal_terjual', today()))
            ->when($request->filter_tanggal === 'minggu_ini', fn($q) => $q->whereBetween('tanggal_terjual', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($request->filter_tanggal === 'bulan_ini', fn($q) => $q->whereMonth('tanggal_terjual', now()->month)->whereYear('tanggal_terjual', now()->year))
            ->latest('tanggal_terjual')
            ->latest('id')
            ->get(); 

        $totalOmzet = $riwayatPenjualan->sum('total_harga');
        $totalPorsi = $riwayatPenjualan->sum('jumlah_terjual');

        $periode = 'Semua Waktu';
        if ($request->filter_tanggal == 'hari_ini') $periode = 'Hari Ini (' . today()->translatedFormat('d F Y') . ')';
        if ($request->filter_tanggal == 'minggu_ini') $periode = 'Seminggu Terakhir';
        if ($request->filter_tanggal == 'bulan_ini') $periode = 'Bulan ' . now()->translatedFormat('F Y');

        return view('cetak', compact('riwayatPenjualan', 'totalOmzet', 'totalPorsi', 'periode'));
    }
}