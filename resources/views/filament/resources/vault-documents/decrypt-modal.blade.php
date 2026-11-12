<div 
    x-data="{
        passphrase: '',
        showPass: false,
        isLoading: false,
        errorMessage: '',
        previewUrl: null,
        mimeType: '{{ $record->file_mime }}',
        async decrypt() {
            if (!this.passphrase) {
                this.errorMessage = 'Silakan masukkan kata sandi rahasia dokumen ini.';
                return;
            }
            this.isLoading = true;
            this.errorMessage = '';
            try {
                const response = await fetch('{{ route('vault.download.encrypted', $record) }}');
                if (!response.ok) {
                    throw new Error('Gagal mengunduh berkas aman dari server.');
                }
                const buffer = await response.arrayBuffer();
                const decryptedBlob = await window.VaultCrypto.decryptFile(
                    buffer,
                    '{{ $record->iv_salt }}',
                    this.passphrase,
                    this.mimeType
                );
                this.previewUrl = URL.createObjectURL(decryptedBlob);
            } catch (err) {
                this.errorMessage = 'Kata sandi rahasia salah atau berkas tidak cocok. Silakan coba lagi.';
                console.error(err);
            } finally {
                this.isLoading = false;
            }
        }
    }" 
    class="space-y-4"
>
    <!-- Document Info Card -->
    <div class="rounded-xl bg-zinc-50 dark:bg-zinc-900/60 p-4 border border-zinc-200 dark:border-zinc-800 text-sm">
        <div class="flex justify-between items-center mb-1">
            <span class="text-xs text-zinc-500 dark:text-zinc-400">Nama Dokumen:</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $record->title }}</span>
        </div>
        <div class="flex justify-between items-center text-xs text-zinc-500">
            <span>Pemilik: <strong class="text-zinc-700 dark:text-zinc-300">{{ $record->familyMember?->full_name ?? 'Dokumen Bersama Keluarga' }}</strong></span>
            <span>Masa Berlaku: <strong class="text-zinc-700 dark:text-zinc-300">{{ $record->expiry_date?->format('d M Y') ?? 'Seumur Hidup' }}</strong></span>
        </div>
    </div>

    <!-- Password Input State -->
    <template x-if="!previewUrl">
        <div class="space-y-3">
            <div>
                <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">
                    Masukkan Kata Sandi Rahasia Dokumen
                </label>
                <div class="relative">
                    <input 
                        :type="showPass ? 'text' : 'password'" 
                        x-model="passphrase"
                        @keydown.enter.prevent="decrypt()"
                        placeholder="Ketik kata sandi yang Anda buat saat menyimpan dokumen..." 
                        class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3.5 py-2.5 pr-10 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-500"
                    />
                    <button 
                        type="button" 
                        @click="showPass = !showPass"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-zinc-400 hover:text-zinc-600"
                        tabindex="-1"
                    >
                        <span x-show="!showPass">👁️</span>
                        <span x-show="showPass">🙈</span>
                    </button>
                </div>
            </div>

            <!-- Error message -->
            <template x-if="errorMessage">
                <div class="p-3 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-900 text-xs text-rose-600 dark:text-rose-300 font-semibold">
                    ⚠️ <span x-text="errorMessage"></span>
                </div>
            </template>

            <button 
                type="button"
                @click="decrypt()" 
                :disabled="isLoading"
                class="w-full py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white font-bold text-sm shadow-sm transition-all flex items-center justify-center space-x-2"
            >
                <span x-show="!isLoading">🔓 Buka & Tampilkan Dokumen Sekarang</span>
                <span x-show="isLoading">⏳ Membuka dokumen dengan aman...</span>
            </button>
            <p class="text-[11px] text-zinc-500 text-center">
                🔒 Dokumen Anda dibuka langsung di perangkat Anda secara aman. Kata sandi tidak pernah dikirim ke pihak manapun.
            </p>
        </div>
    </template>

    <!-- Document Preview State -->
    <template x-if="previewUrl">
        <div class="space-y-4">
            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 rounded-xl text-xs text-emerald-800 dark:text-emerald-300 flex items-center justify-between">
                <span class="font-semibold">✓ Dokumen berhasil dibuka!</span>
                <button 
                    type="button"
                    @click="previewUrl = null; passphrase = ''" 
                    class="font-bold underline hover:text-emerald-950 dark:hover:text-white"
                >
                    🔒 Kunci Kembali
                </button>
            </div>

            <!-- Preview Frame -->
            <div class="w-full max-h-[500px] overflow-auto rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-950 flex items-center justify-center p-2">
                <template x-if="mimeType.startsWith('image/')">
                    <img :src="previewUrl" class="max-w-full max-h-[480px] object-contain rounded-lg" alt="Hasil Dokumen" />
                </template>
                <template x-if="mimeType === 'application/pdf'">
                    <iframe :src="previewUrl" class="w-full h-[450px] rounded-lg border-0"></iframe>
                </template>
                <template x-if="!mimeType.startsWith('image/') && mimeType !== 'application/pdf'">
                    <div class="p-6 text-center">
                        <p class="text-sm text-zinc-600 dark:text-zinc-300 mb-3">Dokumen telah berhasil dibuka dan siap diunduh:</p>
                        <a 
                            :href="previewUrl" 
                            download="{{ $record->title }}"
                            class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs"
                        >
                            <span>📥 Unduh Berkas Asli</span>
                        </a>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>
