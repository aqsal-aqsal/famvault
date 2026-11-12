<div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm p-5 sm:p-6 mb-2">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-5 border-b border-zinc-100 dark:border-zinc-800">
        <div>
            <div class="flex items-center space-x-2">
                <span class="text-2xl">👋</span>
                <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Selamat Datang di FamVault</h2>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 font-semibold">Panduan Mudah</span>
            </div>
            <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                Ikuti 3 langkah sederhana di bawah untuk mulai menyusun silsilah dan mengamankan arsip penting keluarga Anda:
            </p>
        </div>
    </div>

    <!-- 3 Action Steps Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-5">
        <!-- Step 1 -->
        <div class="p-4 rounded-xl border border-sky-200 dark:border-sky-950 bg-sky-50/50 dark:bg-sky-950/20 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400">Langkah 1</span>
                    <span class="text-xl">👨‍👩‍👧‍👦</span>
                </div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-1">Daftarkan Anggota Keluarga</h3>
                <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed mb-4">
                    Masukkan data kakek, nenek, orang tua, hingga anak-anak. Pohon silsilah keluarga akan terbentuk secara otomatis.
                </p>
            </div>
            <div class="flex items-center gap-2 pt-2 border-t border-sky-100 dark:border-sky-900/40">
                <a 
                    href="{{ url('/admin/family-members/create') }}" 
                    class="flex-1 py-1.5 px-3 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold text-center shadow-sm transition-colors"
                >
                    + Tambah Anggota
                </a>
                <a 
                    href="{{ url('/admin/family-tree') }}" 
                    class="py-1.5 px-3 rounded-lg border border-sky-300 dark:border-sky-800 text-sky-700 dark:text-sky-300 bg-white dark:bg-zinc-900 text-xs font-semibold hover:bg-sky-50 dark:hover:bg-zinc-800 transition-colors"
                    title="Buka Pohon Silsilah"
                >
                    🌳 Pohon
                </a>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="p-4 rounded-xl border border-amber-200 dark:border-amber-950 bg-amber-50/50 dark:bg-amber-950/20 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Langkah 2</span>
                    <span class="text-xl">🔐</span>
                </div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-1">Amankan Dokumen Berharga</h3>
                <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed mb-4">
                    Simpan scan KTP, Kartu Keluarga, Ijazah, atau Akta Rumah. Kunci dengan kata sandi rahasia agar aman dari siapapun.
                </p>
            </div>
            <div class="flex items-center gap-2 pt-2 border-t border-amber-100 dark:border-amber-900/40">
                <a 
                    href="{{ url('/admin/vault-documents/create') }}" 
                    class="flex-1 py-1.5 px-3 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold text-center shadow-sm transition-colors"
                >
                    + Simpan Dokumen
                </a>
                <a 
                    href="{{ url('/admin/vault-documents') }}" 
                    class="py-1.5 px-3 rounded-lg border border-amber-300 dark:border-amber-800 text-amber-700 dark:text-amber-300 bg-white dark:bg-zinc-900 text-xs font-semibold hover:bg-amber-50 dark:hover:bg-zinc-800 transition-colors"
                    title="Buka Brankas"
                >
                    📂 Brankas
                </a>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="p-4 rounded-xl border border-emerald-200 dark:border-emerald-950 bg-emerald-50/50 dark:bg-emerald-950/20 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Langkah 3</span>
                    <span class="text-xl">⚡</span>
                </div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-1">Catat Rekening & Tagihan</h3>
                <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed mb-4">
                    Simpan nomor meteran PLN, PDAM, atau internet WiFi rumah agar siapapun di keluarga bisa membantu membayar tepat waktu.
                </p>
            </div>
            <div class="flex items-center gap-2 pt-2 border-t border-emerald-100 dark:border-emerald-900/40">
                <a 
                    href="{{ url('/admin/utility-records/create') }}" 
                    class="flex-1 py-1.5 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold text-center shadow-sm transition-colors"
                >
                    + Catat Tagihan
                </a>
                <a 
                    href="{{ url('/admin/utility-records') }}" 
                    class="py-1.5 px-3 rounded-lg border border-emerald-300 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 bg-white dark:bg-zinc-900 text-xs font-semibold hover:bg-emerald-50 dark:hover:bg-zinc-800 transition-colors"
                    title="Daftar Tagihan"
                >
                    📋 Daftar
                </a>
            </div>
        </div>
    </div>

    <!-- Security Reassurance Box in Everyday Words -->
    <div class="mt-4 p-3 rounded-xl bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 flex items-start space-x-3 text-xs text-zinc-600 dark:text-zinc-400">
        <span class="text-base shrink-0">💡</span>
        <div>
            <span class="font-bold text-zinc-800 dark:text-zinc-200">Bagaimana privasi dokumen keluarga dijaga?</span>
            Dokumen Anda dikunci secara otomatis di perangkat Anda menggunakan kata sandi rahasia sebelum dikirim ke server. Bahkan pengelola sistem tidak dapat membaca isi dokumen Anda.
        </div>
    </div>
</div>
