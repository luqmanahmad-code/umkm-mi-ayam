<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Warung Mi Ayam Berkah</title>
    <!-- Favicon Mangkuk Mi Ayam -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍜</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .cartoon-pattern {
            background-color: #78350f;
            background-image: radial-gradient(#d97706 1.5px, transparent 1.5px), radial-gradient(#d97706 1.5px, #78350f 1.5px);
            background-size: 60px 60px;
            background-position: 0 0, 30px 30px;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 font-sans text-slate-800 relative overflow-hidden cartoon-pattern">

    <!-- Efek Lapisan Hangat Transparan -->
    <div class="absolute inset-0 bg-amber-950/40 backdrop-blur-[1px] z-0"></div>

    <!-- Kotak Form Lupa Sandi -->
    <div class="relative z-10 bg-white/95 backdrop-blur-md max-w-md w-full rounded-2xl shadow-2xl border border-white/30 p-8 space-y-5">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-100 text-amber-700 text-3xl mb-3 shadow-inner">
                🔒
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Pemulihan Sandi</h1>
            
            <!-- Area Dinamis dengan Tinggi Tetap (Card Tidak Akan Membesar) -->
            <div class="mt-2 min-h-[44px] flex items-center justify-center">
                @if (session('status'))
                    <div class="w-full bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-1.5 rounded-xl text-xs font-medium text-left flex items-center gap-2">
                        <span>✅</span>
                        <span>{{ session('status') }}</span>
                    </div>
                @elseif ($errors->any())
                    <div class="w-full bg-rose-50 border border-rose-200 text-rose-700 px-3 py-1.5 rounded-xl text-xs font-medium text-left flex items-center gap-2">
                        <span>⚠️</span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @else
                    <p class="text-sm text-slate-500">Masukkan email terdaftar untuk menerima tautan pemulihan</p>
                @endif
            </div>
        </div>

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email Terdaftar</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Silakan masukkan email terdaftar Anda..." required autofocus
                       class="w-full px-4 py-3 rounded-xl border border-stone-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none text-base bg-stone-50/50 placeholder:text-slate-400 placeholder:font-normal">
            </div>

            <button type="submit" 
                    class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3.5 rounded-xl shadow-md transition text-base mt-2">
                Kirim Tautan Pemulihan
            </button>
        </form>

        <!-- Tautan Kembali ke Halaman Masuk -->
        <div class="pt-3 border-t border-stone-100 text-center">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-amber-700 hover:text-amber-800 hover:underline">
                ← Kembali ke Halaman Masuk
            </a>
        </div>
    </div>

</body>
</html>