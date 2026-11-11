<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Portal - Warung Mi Ayam Berkah</title>
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

    <!-- Kotak Form Login -->
    <div class="relative z-10 bg-white/95 backdrop-blur-md max-w-md w-full rounded-2xl shadow-2xl border border-white/30 p-8 space-y-5">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-100 text-amber-700 text-3xl mb-3 shadow-inner">
                🍜
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Portal Kelola Usaha</h1>
            
            <!-- Area Subjudul / Notifikasi dengan Tinggi Tetap agar Card Tidak Membesar -->
            <div class="mt-1.5 min-h-[36px] flex items-center justify-center">
                @if (session('sukses') || session('status'))
                    <div class="w-full bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-1.5 rounded-xl text-xs font-medium text-left flex items-center gap-2">
                        <span>✅</span>
                        <span>{{ session('sukses') ?? session('status') }}</span>
                    </div>
                @elseif ($errors->any())
                    <div class="w-full bg-rose-50 border border-rose-200 text-rose-700 px-3 py-1.5 rounded-xl text-xs font-medium text-left flex items-center gap-2">
                        <span>⚠️</span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @else
                    <p class="text-sm text-slate-500">Sistem Stok, Kasir & Keuangan Owner</p>
                @endif
            </div>
        </div>

        <form action="{{ url('/login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email Owner</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Silakan masukkan email Anda..." required autofocus
                       class="w-full px-4 py-3 rounded-xl border border-stone-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none text-base bg-stone-50/50 placeholder:text-slate-400 placeholder:font-normal">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <input type="password" id="passwordInput" name="password" placeholder="Masukkan kata sandi..." required
                           class="w-full px-4 py-3 pr-11 rounded-xl border border-stone-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none text-base bg-stone-50/50 placeholder:text-slate-400 placeholder:font-normal">
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-sm pt-0.5">
                <label class="flex items-center gap-2 text-slate-600 cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500">
                    <span>Ingat saya</span>
                </label>
                <a href="{{ route('password.request') }}" class="font-semibold text-amber-700 hover:text-amber-800 hover:underline">
                    Lupa kata sandi?
                </a>
            </div>

            <button type="submit" 
                    class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3.5 rounded-xl shadow-md transition text-base">
                Masuk ke Beranda Usaha
            </button>
        </form>

        <!-- Tombol Bantuan Admin sebagai Penutup Card yang Proporsional -->
        <div class="pt-3 border-t border-stone-100">
            <a href="https://wa.me/6281234567890" target="_blank"
               class="w-full flex items-center justify-center gap-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-semibold py-2.5 rounded-xl transition text-sm">
                <span>💬</span>
                <span>Bantuan Admin (WhatsApp)</span>
            </a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>