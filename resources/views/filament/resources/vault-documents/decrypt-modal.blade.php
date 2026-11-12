<div 
    x-data="{
        passphrase: '',
        isLoading: false,
        errorMessage: '',
        previewUrl: null,
        mimeType: '{{ $record->file_mime }}',
        async decrypt() {
            if (!this.passphrase) {
                this.errorMessage = 'Harap masukkan Master Passphrase brankas Anda.';
                return;
            }
            this.isLoading = true;
            this.errorMessage = '';
            try {
                const response = await fetch('{{ route('vault.download.encrypted', $record) }}');
                if (!response.ok) {
                    throw new Error('Gagal mengunduh berkas terenkripsi dari server.');
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
                this.errorMessage = 'Gagal mendekripsi: passphrase salah atau berkas rusak.';
                console.error(err);
            } finally {
                this.isLoading = false;
            }
        }
    }" 
    class="space-y-4"
>
    <div class="rounded-lg bg-zinc-50 dark:bg-zinc-900/60 p-4 border border-zinc-200 dark:border-zinc-800 text-sm">
        <div class="flex justify-between items-center mb-1">
            <span class="font-semibold text-zinc-700 dark:text-zinc-200">Dokumen:</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $record->title }}</span>
        </div>
        <div class="flex justify-between items-center text-xs text-zinc-500">
            <span>Tipe MIME: {{ $record->file_mime }}</span>
            <span>Pemilik: {{ $record->familyMember?->full_name ?? 'Dokumen Bersama' }}</span>
        </div>
    </div>

    <template x-if="!previewUrl">
        <div class="space-y-3">
            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Master Passphrase (Kunci Rahasia)</label>
                <input 
                    type="password" 
                    x-model="passphrase"
                    @keydown.enter.prevent="decrypt()"
                    placeholder="Masukkan passphrase saat dokumen ini dienkripsi..." 
                    class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-500"
                />
            </div>

            <template x-if="errorMessage">
                <p class="text-xs text-rose-500 font-medium" x-text="errorMessage"></p>
            </template>

            <button 
                type="button"
                @click="decrypt()" 
                :disabled="isLoading"
                class="w-full py-2 px-4 rounded-lg bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white font-semibold text-sm transition-colors flex items-center justify-center space-x-2"
            >
                <span x-show="!isLoading">🔓 Dekripsi di Memori RAM</span>
                <span x-show="isLoading">Sedang mendekripsi dengan AES-GCM...</span>
            </button>
            <p class="text-[11px] text-zinc-400 text-center italic">
                Dekripsi berjalan sepenuhnya di memori browser lokal Anda. Passphrase Anda tidak pernah dikirim ke server.
            </p>
        </div>
    </template>

    <template x-if="previewUrl">
        <div class="space-y-4">
            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 rounded-lg text-xs text-emerald-700 dark:text-emerald-300 flex items-center justify-between">
                <span>Dokumen berhasil didekripsi di memori RAM!</span>
                <button @click="previewUrl = null; passphrase = ''" class="font-bold underline">Kunci kembali</button>
            </div>

            <!-- Preview content -->
            <div class="w-full max-h-[500px] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-950 flex items-center justify-center p-2">
                <template x-if="mimeType.startsWith('image/')">
                    <img :src="previewUrl" class="max-w-full max-h-[480px] object-contain rounded" alt="Hasil Dekripsi" />
                </template>
                <template x-if="mimeType === 'application/pdf'">
                    <iframe :src="previewUrl" class="w-full h-[450px] rounded border-0"></iframe>
                </template>
                <template x-if="!mimeType.startsWith('image/') && mimeType !== 'application/pdf'">
                    <div class="p-6 text-center">
                        <p class="text-sm text-zinc-600 dark:text-zinc-300 mb-3">Berkas hasil dekripsi siap diunduh.</p>
                        <a 
                            :href="previewUrl" 
                            download="{{ $record->title }}"
                            class="px-4 py-2 bg-amber-500 text-white rounded-lg text-xs font-semibold"
                        >
                            Unduh Berkas Hasil Dekripsi
                        </a>
                    </div>
                </template>
            </div>

            <div class="flex justify-end">
                <a 
                    :href="previewUrl" 
                    download="{{ $record->title }}"
                    class="px-3 py-1.5 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded text-xs font-medium"
                >
                    Unduh ke Perangkat
                </a>
            </div>
        </div>
    </template>
</div>
