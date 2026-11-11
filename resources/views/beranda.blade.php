<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="page-title">Beranda - Warung Mi Ayam Berkah</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍜</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none; 
            scrollbar-width: none; 
        }
    </style>
</head>
<body class="bg-stone-50 text-slate-800 font-sans min-h-screen lg:h-screen lg:overflow-hidden flex flex-col pb-20 xl:pb-0">

    <!-- NAVBAR RESPONSIP -->
    <nav class="bg-white border-b border-stone-200 sticky top-0 z-20 shadow-xs print:hidden shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3.5 flex flex-wrap justify-between items-center gap-3">
            
            <!-- Bagian Kiri: Logo & Info -->
            <div class="flex items-center gap-3">
                <span class="text-2xl bg-amber-100 p-2 rounded-xl">🍜</span>
                <div>
                    <h1 class="text-sm sm:text-base font-bold text-slate-800 leading-tight">Warung Mi Ayam Berkah</h1>
                    <p class="text-xs text-slate-500">Owner: {{ auth()->user()->name }}</p>
                </div>
            </div>

            <!-- Bagian Tengah: Menu Tab (Tampil di Atas pada Layar PC/Laptop >= 1280px) -->
            <div class="hidden xl:block">
                <div class="flex items-center gap-1 bg-stone-100 p-1 rounded-xl border border-stone-200 text-sm font-medium">
                    <button onclick="bukaTab(event, 'tab-beranda')" data-tab="tab-beranda" class="tab-btn whitespace-nowrap px-4 py-2 rounded-lg bg-white text-amber-700 shadow-sm transition">
                        📊 Beranda
                    </button>
                    <button onclick="bukaTab(event, 'tab-kasir')" data-tab="tab-kasir" class="tab-btn whitespace-nowrap px-4 py-2 rounded-lg text-slate-600 hover:text-slate-900 transition">
                        🛒 Kasir & Menu Baru
                    </button>
                    <button onclick="bukaTab(event, 'tab-stok')" data-tab="tab-stok" class="tab-btn whitespace-nowrap px-4 py-2 rounded-lg text-slate-600 hover:text-slate-900 transition">
                        📦 Stok Barang
                    </button>
                    <button onclick="bukaTab(event, 'tab-ai')" data-tab="tab-ai" class="tab-btn whitespace-nowrap px-4 py-2 rounded-lg text-slate-600 hover:text-slate-900 transition">
                        🤖 Asisten AI
                    </button>
                </div>
            </div>

            <!-- Bagian Kanan: Pilihan Rentang & Tombol Cetak/Keluar -->
            <div class="flex items-center gap-2 ml-auto xl:ml-0">
                <div class="flex items-center gap-1 bg-stone-100 p-1 rounded-xl border border-stone-200">
                    <select id="pilihan-rentang" class="px-2.5 py-1.5 rounded-lg text-xs bg-white font-medium text-slate-700 outline-none border border-stone-300">
                        <option value="">📅 Semua Waktu</option>
                        <option value="hari_ini">Hari Ini</option>
                        <option value="minggu_ini">Seminggu Terakhir</option>
                        <option value="bulan_ini">Bulan Ini</option>
                    </select>

                    <button onclick="cetakLaporan('pdf')" class="bg-slate-700 hover:bg-slate-800 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition shadow-sm">
                        🖨️ PDF
                    </button>
                    <button onclick="cetakLaporan('csv')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition shadow-sm">
                        📥 Excel
                    </button>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="inline" onsubmit="return confirm('⚠️ Apakah Anda yakin ingin keluar dari sistem Warung Mi Ayam Berkah?')">
                    @csrf
                    <button type="submit" class="border border-rose-200 text-rose-600 hover:bg-rose-50 px-3.5 py-2 rounded-xl text-xs font-semibold transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- NOTIFIKASI OTOMATIS HILANG DALAM BEBERAPA DETIK (FLOATING TOAST) -->
    @if(session('sukses') || session('error') || $errors->any())
        <div id="notifikasi-otomatis" class="fixed top-20 left-1/2 -translate-x-1/2 z-50 w-[92%] max-w-lg transition-all duration-500 ease-in-out print:hidden">
            @if(session('sukses'))
                <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl text-sm font-semibold shadow-lg flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span>✅</span>
                        <span>{{ session('sukses') }}</span>
                    </div>
                    <button type="button" onclick="tutupNotifikasi()" class="text-emerald-600 hover:text-emerald-900 font-bold text-lg leading-none">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-xl text-sm font-semibold shadow-lg flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span>⚠️</span>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="tutupNotifikasi()" class="text-rose-600 hover:text-rose-900 font-bold text-lg leading-none">&times;</button>
                </div>
            @endif
            @if($errors->any())
                <div class="bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-xl text-sm font-semibold shadow-lg flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span>⚠️</span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                    <button type="button" onclick="tutupNotifikasi()" class="text-rose-600 hover:text-rose-900 font-bold text-lg leading-none">&times;</button>
                </div>
            @endif
        </div>
    @endif

    <!-- KONTEN UTAMA (JARAK ATAS LEGA & PROPORSIONAL TANPA SCROLL DI PC/LAPTOP) -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 pt-5 pb-6 flex flex-col justify-start overflow-visible lg:overflow-hidden">

        <!-- ================= TAB 1: BERANDA & KEUANGAN ================= -->
        <div id="tab-beranda" class="tab-content space-y-5">
            <!-- 4 Kartu Ringkasan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-stone-200 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Uang Masuk Hari Ini</p>
                    <p class="text-xl sm:text-2xl font-bold text-emerald-600 mt-1">Rp {{ number_format($uangMasukHariIni, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-500 mt-1">Dari <strong>{{ $porsiTerjualHariIni }}</strong> porsi/item terjual</p>
                </div>
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-stone-200 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Omzet Bulan Ini</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($uangMasukBulanIni, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-500 mt-1">Akumulasi pendapatan bulan ini</p>
                </div>
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-stone-200 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Menu Stok Aman</p>
                    <p class="text-xl sm:text-2xl font-bold text-blue-600 mt-1">{{ $jumlahTersedia }} Menu</p>
                    <p class="text-xs text-slate-500 mt-1">Siap dijual tanpa kendala stok</p>
                </div>
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-stone-200 shadow-xs">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Stok Menipis / Habis</p>
                    <p class="text-xl sm:text-2xl font-bold text-rose-600 mt-1">{{ $jumlahPerluRestock }} Menu</p>
                    <p class="text-xs text-slate-500 mt-1">Segera siapkan bahan tambahan</p>
                </div>
            </div>

            <!-- Card Tabel Rekap Penjualan -->
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200 shadow-xs">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-800 mb-0.5">🧾 Rekap Transaksi Penjualan</h2>
                        <p class="text-xs text-slate-500">Daftar penjualan yang otomatis tercatat ke dalam laporan.</p>
                    </div>

                    <form method="GET" action="{{ route('beranda') }}" class="flex items-center gap-2 print:hidden w-full sm:w-auto">
                        <select name="filter_tanggal" onchange="this.form.submit()" class="flex-1 sm:flex-none px-3 py-1.5 rounded-xl border border-stone-300 text-xs sm:text-sm bg-white font-medium text-slate-700">
                            <option value="">📅 Semua Waktu</option>
                            <option value="hari_ini" {{ request('filter_tanggal') == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="minggu_ini" {{ request('filter_tanggal') == 'minggu_ini' ? 'selected' : '' }}>Seminggu Terakhir</option>
                            <option value="bulan_ini" {{ request('filter_tanggal') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                        </select>
                        @if(request('filter_tanggal'))
                            <a href="{{ route('beranda') }}" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 rounded-xl text-xs font-semibold text-slate-600 flex items-center transition">Reset</a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto lg:overflow-visible">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-stone-200 text-slate-500 text-xs uppercase tracking-wider">
                                <th class="py-2.5">No Nota</th>
                                <th class="py-2.5">Tanggal</th>
                                <th class="py-2.5">Menu Terjual</th>
                                <th class="py-2.5">Jumlah (Qty)</th>
                                <th class="py-2.5">Harga Satuan</th>
                                <th class="py-2.5 text-right">Total Masuk</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($riwayatPenjualan as $trx)
                            <tr class="hover:bg-stone-50/70">
                                <td class="py-2.5 font-mono text-xs text-slate-500">{{ $trx->no_nota }}</td>
                                <td class="py-2.5">{{ \Carbon\Carbon::parse($trx->tanggal_terjual)->translatedFormat('d M Y') }}</td>
                                <td class="py-2.5 font-semibold text-slate-800">{{ $trx->produk->nama_produk ?? '-' }}</td>
                                <td class="py-2.5">{{ $trx->jumlah_terjual }} porsi</td>
                                <td class="py-2.5">Rp {{ number_format($trx->harga_satuan, 0, ',', '.') }}</td>
                                <td class="py-2.5 font-bold text-emerald-700 text-right">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400">Tidak ada data transaksi pada rentang waktu ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-3 border-t border-stone-100">
                    {{ $riwayatPenjualan->links() }}
                </div>
            </div>
        </div>

        <!-- ================= TAB 2: KASIR & TAMBAH MENU ================= -->
        <div id="tab-kasir" class="tab-content hidden">
            <!-- Menggunakan items-start agar Card Kanan (Tambah Menu) TIDAK ikut memanjang/bergeser saat Card Kiri (Kasir) ditambah barisnya -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                
                <!-- KASIR AREA (KIRI - RAPAT & PROPORSIONAL) -->
                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200 shadow-xs h-fit">
                    <div class="flex justify-between items-start gap-2 mb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-800 mb-1">🛒 Catat Hasil Penjualan (Kasir)</h2>
                            <p class="text-xs text-slate-500">Pilih satu menu atau klik <strong>+ Baris Menu</strong> jika pembeli memesan banyak jenis.</p>
                        </div>
                        <button type="button" onclick="tambahBarisKasir()" class="bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition shrink-0">
                            + Baris Menu
                        </button>
                    </div>

                    <form id="form-kasir" action="{{ route('penjualan.simpan') }}" method="POST" class="space-y-3">
                        @csrf
                        <div id="wadah-baris-kasir" class="space-y-2.5">
                            <div class="baris-kasir grid grid-cols-1 sm:grid-cols-12 gap-3 items-end bg-stone-50/80 p-3.5 rounded-xl border border-stone-200/80">
                                <div class="sm:col-span-8">
                                    <label class="text-xs font-semibold text-slate-600">Pilih Menu Makanan / Minuman</label>
                                    <select name="items[0][produk_id]" required onchange="hitungUlangKasir()" class="select-produk-kasir w-full mt-1.5 px-3 py-2.5 rounded-xl border border-stone-300 text-sm bg-white outline-none focus:ring-2 focus:ring-amber-500">
                                        <option value="" disabled selected>-- Pilih Menu Terjual --</option>
                                        @foreach($semuaProduk as $item)
                                            <option value="{{ $item->id }}" data-stok="{{ $item->stok_tersedia }}" data-harga="{{ $item->harga_jual }}" {{ $item->stok_tersedia <= 0 ? 'disabled' : '' }}>
                                                {{ $item->nama_produk }} (Sisa: {{ $item->stok_tersedia }}) — Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="sm:col-span-4 flex items-end gap-1.5">
                                    <div class="flex-1">
                                        <label class="text-xs font-semibold text-slate-600">Jumlah Laku (Qty)</label>
                                        <input type="number" name="items[0][jumlah_terjual]" value="1" min="1" required oninput="hitungUlangKasir()" class="input-qty-kasir w-full mt-1.5 px-3 py-2.5 rounded-xl border border-stone-300 text-sm bg-white outline-none focus:ring-2 focus:ring-amber-500">
                                    </div>
                                    <button type="button" onclick="hapusBarisKasir(this)" class="btn-hapus-baris hidden bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 px-3 py-2.5 rounded-xl text-sm font-bold transition" title="Hapus baris">
                                        &times;
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Kotak Estimasi Total & Tombol Simpan dengan Jarak Rapat yang Proporsional -->
                        <div class="pt-1 space-y-2.5">
                            <div class="flex justify-between items-center bg-emerald-50/60 border border-emerald-200/80 px-4 py-2.5 rounded-xl text-xs">
                                <span class="text-slate-600 font-semibold">Estimasi Total Pembayaran</span>
                                <strong id="estimasi-total-kasir" class="text-emerald-700 text-sm font-bold">Rp 0</strong>
                            </div>

                            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 rounded-xl text-sm transition shadow-sm">
                                + Simpan Transaksi Penjualan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAMBAH MENU & KATEGORI AREA (KANAN - TINGGI TETAP STABIL / TIDAK IKUT GESER) -->
                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200 shadow-xs h-fit">
                    <div>
                        <h2 class="text-base font-bold text-slate-800 mb-1">🍲 Tambah Produk Makanan / Minuman Baru</h2>
                        <p class="text-xs text-slate-500 mb-4">Kode barang dibuat otomatis. Cukup ketik nama, harga, dan stok awal.</p>
                        
                        <form action="{{ route('produk.simpan') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @csrf
                            <div class="sm:col-span-3">
                                <label class="text-xs font-semibold text-slate-600">Nama Makanan / Minuman</label>
                                <input type="text" name="nama_produk" placeholder="Contoh: Nasi Goreng Spesial" required class="w-full mt-1 px-3 py-2 rounded-xl border border-stone-300 text-sm">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Pilih Kategori</label>
                                <select name="kategori_id" required class="w-full mt-1 px-3 py-2 rounded-xl border border-stone-300 text-sm bg-white">
                                    @foreach($kategoris as $kat)
                                        <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Harga Jual (Rp)</label>
                                <input type="number" name="harga_jual" placeholder="15000" min="500" required class="w-full mt-1 px-3 py-2 rounded-xl border border-stone-300 text-sm">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Stok Awal (Qty)</label>
                                <input type="number" name="stok_tersedia" placeholder="30" min="0" required class="w-full mt-1 px-3 py-2 rounded-xl border border-stone-300 text-sm">
                                <input type="hidden" name="batas_minimum_stok" value="10">
                            </div>
                            <div class="sm:col-span-3 pt-1">
                                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-semibold py-2.5 rounded-xl text-sm transition shadow-sm">
                                    + Tambah Menu Baru
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Form & Daftar Kategori -->
                    <div class="mt-4 pt-4 border-t border-stone-200">
                        <form action="{{ route('kategori.simpan') }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                            @csrf
                            <input type="text" id="input-kategori" name="nama_kategori" placeholder="Buat kategori baru (Mis: Aneka Nasi)..." required 
                                   oninput="cekInputKategori()"
                                   class="flex-1 px-3 py-2 rounded-xl border border-stone-300 text-xs focus:ring-2 focus:ring-slate-400 outline-none transition">
                                   
                            <button type="submit" id="btn-kategori" disabled 
                                    class="bg-stone-100 text-slate-400 px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap cursor-not-allowed transition">
                                + Kategori Baru
                            </button>
                        </form>

                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach($kategoris as $kat)
                                <div class="inline-flex items-center gap-1.5 bg-stone-50 border border-stone-200 px-2.5 py-1 rounded-lg text-xs">
                                    <span class="text-slate-600 font-medium">{{ $kat->nama_kategori }}</span>
                                    <form action="{{ route('kategori.hapus', $kat->id) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Yakin ingin menghapus kategori {{ $kat->nama_kategori }}? Pastikan tidak ada menu yang terhubung.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-400 hover:text-rose-600 text-sm font-bold leading-none">&times;</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ================= TAB 3: STOK BARANG ================= -->
        <div id="tab-stok" class="tab-content hidden">
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200 shadow-xs">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-800 mb-0.5">📦 Daftar Stok Barang / Menu (Qty)</h2>
                        <p class="text-xs text-slate-500">Pantau ketersediaan porsi, ubah data menu, atau hapus menu.</p>
                    </div>
                    
                    <form method="GET" action="{{ route('beranda') }}" class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                        <input type="hidden" name="tab" value="stok">
                        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari menu..." class="flex-1 sm:flex-none px-3.5 py-1.5 rounded-xl border border-stone-300 text-sm">
                        <select name="status" onchange="this.form.submit()" class="flex-1 sm:flex-none px-3.5 py-1.5 rounded-xl border border-stone-300 text-sm bg-white">
                            <option value="">Semua Status</option>
                            <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="menipis" {{ request('status') == 'menipis' ? 'selected' : '' }}>Hampir Habis</option>
                            <option value="habis" {{ request('status') == 'habis' ? 'selected' : '' }}>Habis</option>
                        </select>
                    </form>
                </div>

                <div class="overflow-x-auto lg:overflow-visible">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-stone-200 text-slate-500 text-xs uppercase tracking-wider">
                                <th class="py-3">Kode</th>
                                <th class="py-3">Nama Menu</th>
                                <th class="py-3">Kategori</th>
                                <th class="py-3">Harga</th>
                                <th class="py-3">Sisa Qty</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Tambah Stok</th>
                                <th class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($produks as $produk)
                            <tr class="hover:bg-stone-50/70">
                                <td class="py-3 text-xs font-mono text-slate-500">{{ $produk->kode_menu }}</td>
                                <td class="py-3 font-semibold text-slate-800">{{ $produk->nama_produk }}</td>
                                <td class="py-3 text-slate-500 text-xs">{{ $produk->kategori->nama_kategori }}</td>
                                <td class="py-3">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                                <td class="py-3 font-bold text-base">{{ $produk->stok_tersedia }}</td>
                                <td class="py-3">
                                    @if($produk->status_stok === 'Tersedia')
                                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-semibold whitespace-nowrap">Tersedia</span>
                                    @elseif($produk->status_stok === 'Menipis')
                                        <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-semibold whitespace-nowrap">Hampir Habis</span>
                                    @else
                                        <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full text-xs font-semibold whitespace-nowrap">Habis</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <form action="{{ route('produk.restock', $produk->id) }}" method="POST" class="flex items-center gap-1.5">
                                        @csrf
                                        <input type="number" name="tambahan_stok" placeholder="+Qty" min="1" required class="w-16 px-2.5 py-1 border border-stone-300 rounded-lg text-xs">
                                        <button type="submit" class="bg-stone-200 hover:bg-stone-300 text-slate-700 px-3 py-1 rounded-lg text-xs font-semibold transition">+ Isi</button>
                                    </form>
                                </td>
                                <td class="py-3 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button onclick="bukaModalEdit('{{ $produk->id }}', '{{ $produk->nama_produk }}', '{{ $produk->kategori_id }}', '{{ $produk->harga_jual }}', '{{ $produk->stok_tersedia }}')" 
                                                class="bg-blue-50 hover:bg-blue-100 text-blue-700 px-2.5 py-1 rounded-lg text-xs font-semibold transition">✏️ Edit</button>
                                        <form action="{{ route('produk.hapus', $produk->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus menu {{ $produk->nama_produk }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-700 px-2.5 py-1 rounded-lg text-xs font-semibold transition">🗑️ Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">Tidak ada menu yang sesuai.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-3 border-t border-stone-100">
                    {{ $produks->links() }}
                </div>
            </div>
        </div>

        <!-- ================= TAB 4: ASISTEN AI ================= -->
        <div id="tab-ai" class="tab-content hidden">
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200 shadow-xs max-w-3xl mx-auto flex flex-col h-[500px] lg:h-[calc(100vh-140px)]">
                <div class="mb-3 text-center shrink-0">
                    <h2 class="text-base font-bold text-slate-800">🤖 Asisten Pintar Owner (AI)</h2>
                    <p class="text-xs text-slate-500">Tanyakan performa jualan atau ide promosi.</p>
                </div>

                <div class="flex flex-wrap justify-center gap-2 mb-3 shrink-0">
                    <button type="button" onclick="kirimCepat('Berapa total porsi yang terjual hari ini dan berapa uang masuknya?')" 
                            class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 px-3 py-1.5 rounded-lg font-medium transition">
                        🏆 Rekapan Hari Ini?
                    </button>
                    <button type="button" onclick="kirimCepat('Menu apa saja yang stoknya habis atau hampir habis?')" 
                            class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 px-3 py-1.5 rounded-lg font-medium transition">
                        ⚠️ Cek Stok Kritis
                    </button>
                    <button type="button" onclick="kirimCepat('Beri saya 3 ide promosi untuk meningkatkan penjualan hari ini.')" 
                            class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 px-3 py-1.5 rounded-lg font-medium transition">
                        💡 Ide Promosi
                    </button>
                </div>

                <div id="chat-box" class="flex-1 overflow-y-auto no-scrollbar bg-stone-50 p-4 rounded-xl space-y-3 text-sm border border-stone-200 mb-3">
                    <div class="bg-white border border-stone-200 p-3 rounded-xl text-slate-700 shadow-xs w-[90%] sm:w-4/5">
                        Halo Pak/Bu! Saya asisten pintar usaha Anda. Silahkan tanya apapun tentang usaha Anda
                    </div>
                </div>

                <form id="chat-form" class="flex flex-col sm:flex-row gap-2 shrink-0">
                    <input type="text" id="pesan-input" placeholder="Tanya seputar jualan..." required
                           class="flex-1 px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold w-full sm:w-auto transition">
                        Tanya AI
                    </button>
                </form>
            </div>
        </div>

    </main>

    <!-- MENU BAR BAWAH KHUSUS LAYAR TABLET & HP (< 1280px) -->
    <nav class="xl:hidden fixed bottom-0 inset-x-0 bg-white border-t border-stone-200 z-30 shadow-[0_-4px_12px_rgba(0,0,0,0.06)] print:hidden">
        <div class="grid grid-cols-4 max-w-lg mx-auto p-1.5 gap-1">
            <button onclick="bukaTab(event, 'tab-beranda')" data-tab="tab-beranda" class="tab-btn flex flex-col items-center justify-center py-1.5 px-1 rounded-xl bg-white text-amber-700 shadow-sm transition">
                <span class="text-base leading-none">📊</span>
                <span class="text-[11px] font-semibold mt-1">Beranda</span>
            </button>
            <button onclick="bukaTab(event, 'tab-kasir')" data-tab="tab-kasir" class="tab-btn flex flex-col items-center justify-center py-1.5 px-1 rounded-xl text-slate-600 hover:text-slate-900 transition">
                <span class="text-base leading-none">🛒</span>
                <span class="text-[11px] font-semibold mt-1">Kasir & Menu</span>
            </button>
            <button onclick="bukaTab(event, 'tab-stok')" data-tab="tab-stok" class="tab-btn flex flex-col items-center justify-center py-1.5 px-1 rounded-xl text-slate-600 hover:text-slate-900 transition">
                <span class="text-base leading-none">📦</span>
                <span class="text-[11px] font-semibold mt-1">Stok Barang</span>
            </button>
            <button onclick="bukaTab(event, 'tab-ai')" data-tab="tab-ai" class="tab-btn flex flex-col items-center justify-center py-1.5 px-1 rounded-xl text-slate-600 hover:text-slate-900 transition">
                <span class="text-base leading-none">🤖</span>
                <span class="text-[11px] font-semibold mt-1">Asisten AI</span>
            </button>
        </div>
    </nav>

    <!-- MODAL EDIT MENU -->
    <div id="modal-edit" class="fixed inset-0 bg-slate-950/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-5 sm:p-6 shadow-2xl border border-stone-200 space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-bold text-slate-800">✏️ Edit Menu</h3>
                <button onclick="tutupModalEdit()" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>

            <form id="form-edit" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-xs font-semibold text-slate-600">Nama Makanan / Minuman</label>
                    <input type="text" id="edit-nama" name="nama_produk" required class="w-full mt-1 px-3 py-2 rounded-xl border border-stone-300 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">Kategori</label>
                    <select id="edit-kategori" name="kategori_id" required class="w-full mt-1 px-3 py-2 rounded-xl border border-stone-300 text-sm bg-white">
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">Harga Jual (Rp)</label>
                    <input type="number" id="edit-harga" name="harga_jual" min="500" required class="w-full mt-1 px-3 py-2 rounded-xl border border-stone-300 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">Sisa Stok (Qty)</label>
                    <input type="number" id="edit-stok" name="stok_tersedia" min="0" required class="w-full mt-1 px-3 py-2 rounded-xl border border-stone-300 text-sm">
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="tutupModalEdit()" class="flex-1 bg-stone-200 hover:bg-stone-300 text-slate-700 py-2.5 rounded-xl text-sm font-semibold">Batal</button>
                    <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white py-2.5 rounded-xl text-sm font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Hilangkan Semua Notifikasi Secara Otomatis Setelah 3.5 Detik
        function tutupNotifikasi() {
            const notif = document.getElementById('notifikasi-otomatis');
            if (notif) {
                notif.classList.add('opacity-0', '-translate-y-3');
                setTimeout(() => notif.remove(), 500);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const notif = document.getElementById('notifikasi-otomatis');
            if (notif) {
                setTimeout(tutupNotifikasi, 3500);
            }
        });

        // Fungsi Ekspor Cetak Laporan (PDF / CSV) Berdasarkan Rentang Waktu
        function cetakLaporan(jenis) {
            const rentang = document.getElementById('pilihan-rentang').value;
            let url = '';

            if (jenis === 'pdf') {
                url = "{{ route('rekap.pdf') }}?filter_tanggal=" + rentang;
                window.open(url, '_blank');
            } else if (jenis === 'csv') {
                url = "{{ route('rekap.csv') }}?filter_tanggal=" + rentang;
                window.location.href = url;
            }
        }

        // ================= LOGIKA MULTI-ITEM KASIR =================
        let indexBarisKasir = 1;

        function tambahBarisKasir() {
            const wadah = document.getElementById('wadah-baris-kasir');
            const semuaBarisSaatIni = wadah.querySelectorAll('.baris-kasir');

            // Batasi maksimal 3 baris sekaligus agar card tetap proporsional tanpa scroll di layar PC
            if (semuaBarisSaatIni.length >= 3) {
                alert('Maksimal 3 baris menu sekaligus agar tampilan tetap proporsional tanpa scroll. Silakan simpan terlebih dahulu.');
                return;
            }

            const barisPertama = semuaBarisSaatIni[0];
            const barisBaru = barisPertama.cloneNode(true);

            const selectProduk = barisBaru.querySelector('.select-produk-kasir');
            const inputQty = barisBaru.querySelector('.input-qty-kasir');

            selectProduk.name = `items[${indexBarisKasir}][produk_id]`;
            selectProduk.selectedIndex = 0;

            inputQty.name = `items[${indexBarisKasir}][jumlah_terjual]`;
            inputQty.value = 1;
            inputQty.removeAttribute('max');

            wadah.appendChild(barisBaru);
            indexBarisKasir++;

            perbaruiTombolHapusKasir();
            hitungUlangKasir();
        }

        function hapusBarisKasir(btn) {
            const wadah = document.getElementById('wadah-baris-kasir');
            if (wadah.querySelectorAll('.baris-kasir').length > 1) {
                btn.closest('.baris-kasir').remove();
                perbaruiTombolHapusKasir();
                hitungUlangKasir();
            }
        }

        function perbaruiTombolHapusKasir() {
            const semuaBaris = document.querySelectorAll('#wadah-baris-kasir .baris-kasir');
            semuaBaris.forEach((baris) => {
                const btnHapus = baris.querySelector('.btn-hapus-baris');
                if (semuaBaris.length > 1) {
                    btnHapus.classList.remove('hidden');
                } else {
                    btnHapus.classList.add('hidden');
                }
            });
        }

        function hitungUlangKasir() {
            const semuaBaris = document.querySelectorAll('#wadah-baris-kasir .baris-kasir');
            let estimasiTotal = 0;

            semuaBaris.forEach((baris) => {
                const select = baris.querySelector('.select-produk-kasir');
                const inputQty = baris.querySelector('.input-qty-kasir');
                const opsi = select.options[select.selectedIndex];

                if (opsi && opsi.dataset.stok) {
                    const maxStok = parseInt(opsi.dataset.stok);
                    const harga = parseInt(opsi.dataset.harga || 0);
                    inputQty.max = maxStok;

                    let qty = parseInt(inputQty.value || 0);
                    if (qty > maxStok) {
                        qty = maxStok;
                        inputQty.value = maxStok;
                        alert('Tidak bisa melebihi batas. Sisa stok menu ini hanya ' + maxStok + ' porsi.');
                    }

                    if (qty > 0) {
                        estimasiTotal += (harga * qty);
                    }
                }
            });

            document.getElementById('estimasi-total-kasir').textContent = 'Rp ' + estimasiTotal.toLocaleString('id-ID');
        }

        // Variabel untuk menampung interval animasi judul berjalan
        let intervalJudul = null;

        // Logika Tab Navigasi, Update URL Address Bar & Judul Halaman Berjalan
        function bukaTab(evt, tabName, updateUrl = true) {
            const contents = document.getElementsByClassName("tab-content");
            for (let c of contents) {
                c.classList.add("hidden");
            }

            const buttons = document.getElementsByClassName("tab-btn");
            for (let b of buttons) {
                if (b.dataset.tab === tabName) {
                    b.classList.add("bg-white", "text-amber-700", "shadow-sm");
                    b.classList.remove("text-slate-600");
                } else {
                    b.classList.remove("bg-white", "text-amber-700", "shadow-sm");
                    b.classList.add("text-slate-600");
                }
            }

            document.getElementById(tabName).classList.remove("hidden");
            localStorage.setItem('activeTab', tabName);

            // Perbarui URL di address bar browser secara dinamis tanpa reload
            if (updateUrl) {
                const slugMap = {
                    'tab-beranda': 'beranda',
                    'tab-kasir': 'kasir',
                    'tab-stok': 'stok',
                    'tab-ai': 'ai'
                };
                const slug = slugMap[tabName] || 'beranda';
                const urlSekarang = new URL(window.location.href);

                if (slug === 'beranda') {
                    urlSekarang.searchParams.delete('tab');
                } else {
                    urlSekarang.searchParams.set('tab', slug);
                }

                window.history.pushState({ tab: tabName }, '', urlSekarang.toString());
            }

            // Hentikan animasi sebelumnya agar tidak bertumpuk
            if (intervalJudul) clearInterval(intervalJudul);

            // Tentukan teks dasar secara mutlak (tidak digabung string lama)
            let teksDasar = "Warung Mi Ayam Berkah — ";
            if (tabName === 'tab-beranda') {
                teksDasar = " Beranda — Warung Mi Ayam Berkah — ";
            } else if (tabName === 'tab-kasir') {
                teksDasar = " Kasir & Menu Baru — Warung Mi Ayam Berkah — ";
            } else if (tabName === 'tab-stok') {
                teksDasar = " Kelola Stok Barang — Warung Mi Ayam Berkah — ";
            } else if (tabName === 'tab-ai') {
                teksDasar = " Asisten AI Owner — Warung Mi Ayam Berkah — ";
            }

            // Jalankan animasi teks bergerak pada judul tab browser
            let i = 0;
            intervalJudul = setInterval(() => {
                document.title = teksDasar.substring(i) + teksDasar.substring(0, i);
                i = (i + 1) % teksDasar.length;
            }, 300);
        }

        // Tangani tombol Back / Forward pada browser agar tab ikut berpindah
        window.addEventListener('popstate', function(event) {
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            const reverseMap = {
                'beranda': 'tab-beranda',
                'kasir': 'tab-kasir',
                'stok': 'tab-stok',
                'ai': 'tab-ai'
            };
            const targetTab = (event.state && event.state.tab) || reverseMap[tabParam] || 'tab-beranda';
            bukaTab(null, targetTab, false);
        });

        // Logika Modal Edit
        function bukaModalEdit(id, nama, kategoriId, harga, stok) {
            const modal = document.getElementById('modal-edit');
            const form = document.getElementById('form-edit');
            
            form.action = `/produk/${id}`;
            document.getElementById('edit-nama').value = nama;
            document.getElementById('edit-kategori').value = kategoriId;
            document.getElementById('edit-harga').value = harga;
            document.getElementById('edit-stok').value = stok;

            modal.classList.remove('hidden');
        }

        function tutupModalEdit() {
            document.getElementById('modal-edit').classList.add('hidden');
        }

        // Penanganan Tab Otomatis Saat Halaman Dimuat
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            let targetTab = 'tab-beranda';

            const sessionTab = "{{ session('active_tab') }}";
            const tabFromUrl = urlParams.get('tab');
            const reverseMap = {
                'beranda': 'tab-beranda',
                'kasir': 'tab-kasir',
                'stok': 'tab-stok',
                'ai': 'tab-ai'
            };
            
            if (sessionTab && sessionTab !== '') {
                targetTab = sessionTab;
                localStorage.setItem('activeTab', sessionTab);
            } 
            else if (tabFromUrl && reverseMap[tabFromUrl]) {
                targetTab = reverseMap[tabFromUrl];
            }
            else if (urlParams.has('page_stok') || urlParams.has('cari') || urlParams.has('status')) {
                targetTab = 'tab-stok';
            } 
            else if (urlParams.has('page_riwayat') || urlParams.has('filter_tanggal')) {
                targetTab = 'tab-beranda';
            } 
            else {
                const savedTab = localStorage.getItem('activeTab');
                if (savedTab) {
                    targetTab = savedTab;
                }
            }

            bukaTab(null, targetTab);
        });

        // Logika Chat AI
        const chatForm = document.getElementById('chat-form');
        const pesanInput = document.getElementById('pesan-input');
        const chatBox = document.getElementById('chat-box');

        function kirimCepat(teks) {
            pesanInput.value = teks;
            chatForm.dispatchEvent(new Event('submit'));
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const pesan = pesanInput.value.trim();
            if (!pesan) return;

            chatBox.innerHTML += `<div class="bg-amber-600 text-white p-3 rounded-xl ml-auto text-right shadow-sm w-fit max-w-[90%] sm:max-w-[80%]">${pesan}</div>`;
            pesanInput.value = '';
            chatBox.scrollTop = chatBox.scrollHeight;

            try {
                const res = await fetch("{{ route('ai.tanya') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ pesan })
                });
                const data = await res.json();

                let formattedJawaban = data.jawaban
                    .replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-slate-900">$1</strong>')
                    .replace(/\*(.*?)\*/g, '<em>$1</em>');

                chatBox.innerHTML += `<div class="bg-white border border-stone-200 p-3 rounded-xl mr-auto text-slate-700 shadow-sm whitespace-pre-line w-fit max-w-[90%] sm:max-w-[80%]">${formattedJawaban}</div>`;
            } catch (err) {
                chatBox.innerHTML += `<div class="bg-rose-50 border border-rose-200 p-3 rounded-xl mr-auto text-rose-700 w-fit max-w-[90%]">Gagal memuat jawaban asisten.</div>`;
            }
            chatBox.scrollTop = chatBox.scrollHeight;
        });

        // Logika Interaktif Tombol Kategori
        function cekInputKategori() {
            const input = document.getElementById('input-kategori');
            const btn = document.getElementById('btn-kategori');
            
            if (input.value.trim() !== '') {
                btn.disabled = false;
                btn.classList.remove('bg-stone-100', 'text-slate-400', 'cursor-not-allowed');
                btn.classList.add('bg-stone-200', 'hover:bg-stone-300', 'text-slate-800', 'cursor-pointer');
            } else {
                btn.disabled = true;
                btn.classList.add('bg-stone-100', 'text-slate-400', 'cursor-not-allowed');
                btn.classList.remove('bg-stone-200', 'hover:bg-stone-300', 'text-slate-800', 'cursor-pointer');
            }
        }
    </script>
</body>
</html>