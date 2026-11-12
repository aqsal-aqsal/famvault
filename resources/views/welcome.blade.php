<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FamVault - Brankas & Silsilah Keluarga</title>
    
    <!-- PWA Meta Tags -->
    @include('filament.pwa-tags')

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 min-h-screen flex flex-col justify-between antialiased selection:bg-amber-500 selection:text-white">

    <!-- Navbar -->
    <header class="border-b border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 backdrop-blur sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 to-amber-400 flex items-center justify-center text-white shadow-md shadow-amber-500/20 font-black text-xl">
                    🏛️
                </div>
                <div>
                    <span class="text-lg font-black tracking-tight text-zinc-900 dark:text-white">FamVault</span>
                    <span class="hidden sm:inline-block text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 ml-2">Brankas Keluarga</span>
                </div>
            </div>
            <div>
                <a 
                    href="{{ url('/admin') }}" 
                    class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm shadow-sm transition-all transform hover:-translate-y-0.5 active:translate-y-0"
                >
                    <span>Masuk ke Brankas</span>
                    <span>→</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-1 max-w-6xl mx-auto px-4 sm:px-6 py-12 sm:py-20 flex flex-col items-center text-center">
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-semibold mb-6">
            <span>✨ Sederhana, Aman, & Mudah Digunakan Semua Generasi</span>
        </div>

        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-zinc-900 dark:text-white max-w-4xl leading-tight">
            Semua Arsip, Silsilah & Tagihan Keluarga Dalam <span class="text-amber-500">Satu Tempat Aman</span>
        </h1>

        <p class="mt-5 text-base sm:text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl">
            FamVault dirancang agar siapapun di keluarga Anda—mulai dari kakek-nenek, orang tua, hingga anak-cucu—bisa dengan mudah melihat garis keturunan, mengamankan surat berharga, dan mengingat tagihan rumah tangga.
        </p>

        <div class="mt-8 flex flex-col sm:flex-row items-center gap-4">
            <a 
                href="{{ url('/admin') }}" 
                class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-base shadow-lg shadow-amber-500/25 transition-all transform hover:-translate-y-0.5"
            >
                🚀 Buka Dasbor Keluarga
            </a>
            <a 
                href="{{ url('/admin/family-tree') }}" 
                class="w-full sm:w-auto px-6 py-3.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-semibold text-base transition-colors"
            >
                🌳 Lihat Pohon Silsilah
            </a>
        </div>

        <!-- 3 Pillars Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16 text-left w-full">
            <!-- Feature 1 -->
            <div class="p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center text-2xl mb-4 font-bold">
                    👨‍👩‍👧‍👦
                </div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">1. Pohon Silsilah Interaktif</h3>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                    Visualisasi garis keturunan keluarga yang jelas. Geser dan klik untuk melihat profil, tanggal lahir, dan kisah tiap anggota keluarga.
                </p>
                <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 text-xs font-semibold text-sky-600">
                    Mudah ditelusuri &bull; Otomatis terhubung
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-2xl mb-4 font-bold">
                    🔐
                </div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">2. Brankas Dokumen Rahasia</h3>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                    Simpan KTP, Kartu Keluarga, Ijazah, dan Sertifikat Tanah. Dikunci dengan kata sandi rahasia Anda sendiri sehingga aman dari siapapun.
                </p>
                <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 text-xs font-semibold text-amber-600">
                    Terkunci di HP Anda &bull; Pengingat masa berlaku
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-2xl mb-4 font-bold">
                    ⚡
                </div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">3. Rekening & Tagihan Rumah</h3>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                    Catat ID pelanggan meteran PLN, nomor PDAM, WiFi, dan asuransi agar seluruh anggota keluarga mudah membayar tepat waktu tanpa bingung mencari nomor meter.
                </p>
                <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 text-xs font-semibold text-emerald-600">
                    Bebas denda &bull; Siapapun bisa bayar
                </div>
            </div>
        </div>

        <!-- Default Credentials Callout for ease of access -->
        <div class="mt-12 p-4 rounded-xl border border-amber-300 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 text-amber-900 dark:text-amber-200 text-xs max-w-xl">
            <span class="font-bold">🔑 Akun Masuk Bawaan (Default):</span>
            <span class="ml-1">Email: <code class="bg-amber-100 dark:bg-amber-900 px-1.5 py-0.5 rounded font-mono">admin@famvault.local</code></span>
            <span class="ml-2">Kata Sandi: <code class="bg-amber-100 dark:bg-amber-900 px-1.5 py-0.5 rounded font-mono">password</code></span>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-zinc-200 dark:border-zinc-800 py-6 text-center text-xs text-zinc-500">
        <p>&copy; {{ date('Y') }} FamVault &bull; Arsip & Silsilah Keluarga Terpercaya</p>
    </footer>

</body>
</html>
