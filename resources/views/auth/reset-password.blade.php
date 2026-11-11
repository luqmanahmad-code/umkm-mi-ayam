<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi - Warung Mi Ayam Berkah</title>
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
    <div class="absolute inset-0 bg-amber-950/40 backdrop-blur-[1px] z-0"></div>

    <div class="relative z-10 bg-white/95 backdrop-blur-md max-w-sm w-full rounded-2xl shadow-2xl border border-white/30 p-6 space-y-4">
        <div class="text-center mb-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-amber-100 text-amber-700 text-3xl mb-2 shadow-inner">
                🔑
            </div>
            <h1 class="text-xl font-bold text-slate-800">Buat Kata Sandi Baru</h1>
            <p class="text-xs text-slate-500 mt-0.5">Silakan masukkan kata sandi baru untuk akun Anda</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-3 py-2 rounded-lg text-xs">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" required readonly
                       class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 bg-stone-100 text-sm outline-none text-stone-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none text-sm bg-stone-50/50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi baru"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none text-sm bg-stone-50/50">
            </div>

            <button type="submit" 
                    class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2.5 rounded-xl shadow-md transition text-sm mt-1">
                Simpan Kata Sandi Baru
            </button>
        </form>
    </div>
</body>
</html>