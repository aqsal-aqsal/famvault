<div 
    x-data="{
        plainFile: null,
        passphrase: '',
        showPass: false,
        isEncrypting: false,
        isUploaded: false,
        uploadedName: '',
        uploadedSize: '',
        errorMessage: '',

        formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        async handleSecureUpload() {
            if (!this.plainFile) {
                this.errorMessage = 'Silakan pilih berkas (PDF, Foto KTP, KK, Sertifikat, dll) terlebih dahulu.';
                return;
            }
            if (!this.passphrase || this.passphrase.length < 6) {
                this.errorMessage = 'Kata sandi rahasia minimal 6 karakter demi keamanan dokumen keluarga Anda.';
                return;
            }

            this.errorMessage = '';
            this.isEncrypting = true;

            try {
                // 1. Encrypt file in client browser memory (Zero Knowledge)
                const result = await window.VaultCrypto.encryptFile(this.plainFile, this.passphrase);

                // 2. Upload encrypted blob directly to server
                const formData = new FormData();
                const encFilename = (this.plainFile.name.replace(/\.[^/.]+$/, '') || 'dokumen') + '.enc';
                formData.append('encrypted_blob', result.encryptedBlob, encFilename);

                const csrfToken = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') 
                    || '{{ csrf_token() }}';

                const response = await fetch('{{ route('vault.upload.encrypted') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                if (!response.ok) {
                    const errData = await response.json().catch(() => ({}));
                    throw new Error(errData.message || 'Gagal mengunggah berkas ke server.');
                }

                const data = await response.json();

                // 3. Fill Livewire form state
                const mimeType = this.plainFile.type || 'application/octet-stream';
                const fileSize = result.encryptedBlob.size;
                const docTitle = this.plainFile.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' ');

                if (typeof $wire !== 'undefined') {
                    $wire.set('data.encrypted_file_path', data.path);
                    $wire.set('data.iv_salt', result.ivSalt);
                    $wire.set('data.file_mime', mimeType);
                    $wire.set('data.file_size_bytes', fileSize);
                    
                    if (!$wire.get('data.title')) {
                        $wire.set('data.title', docTitle);
                    }
                }

                // Also populate hidden DOM inputs if present
                const setInputValue = (name, val) => {
                    const el = document.querySelector(`input[name=\"${name}\"], input[name*=\"${name}\"]`);
                    if (el) {
                        el.value = val;
                        el.dispatchEvent(new Event('input', { bubbles: true }));
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                };

                setInputValue('encrypted_file_path', data.path);
                setInputValue('iv_salt', result.ivSalt);
                setInputValue('file_mime', mimeType);
                setInputValue('file_size_bytes', fileSize);

                this.uploadedName = this.plainFile.name;
                this.uploadedSize = this.formatBytes(this.plainFile.size);
                this.isUploaded = true;
            } catch (err) {
                console.error(err);
                this.errorMessage = 'Terjadi kendala: ' + err.message;
            } finally {
                this.isEncrypting = false;
            }
        },

        resetFile() {
            this.plainFile = null;
            this.isUploaded = false;
            this.uploadedName = '';
            this.uploadedSize = '';
            this.errorMessage = '';
            if (typeof $wire !== 'undefined') {
                $wire.set('data.encrypted_file_path', '');
                $wire.set('data.iv_salt', '');
            }
        }
    }"
    class="space-y-4"
>
    <!-- Before Upload State -->
    <template x-if="!isUploaded">
        <div class="space-y-4 p-5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Step 1: Choose File -->
                <div>
                    <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">
                        1. Pilih Berkas Asli (PDF atau Foto) <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="file" 
                        @change="plainFile = $event.target.files[0]; errorMessage = ''"
                        class="block w-full text-xs text-zinc-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-white hover:file:bg-amber-600 cursor-pointer"
                    />
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1">
                        Format yang didukung: PDF, Foto KTP/KK (JPG, PNG), Dokumen.
                    </p>
                </div>

                <!-- Step 2: Passphrase -->
                <div>
                    <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">
                        2. Buat Kata Sandi Rahasia Dokumen <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            :type="showPass ? 'text' : 'password'" 
                            x-model="passphrase"
                            placeholder="Contoh: KeluargaBahagia2026"
                            class="w-full text-xs rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 pr-9 text-zinc-900 dark:text-zinc-100 focus:ring-1 focus:ring-amber-500"
                        />
                        <button 
                            type="button" 
                            @click="showPass = !showPass"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-zinc-400 hover:text-zinc-600"
                            tabindex="-1"
                        >
                            <span x-show="!showPass">👁️</span>
                            <span x-show="showPass">🙈</span>
                        </button>
                    </div>
                    <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-1">
                        ⚠️ <strong>Penting:</strong> Ingat kata sandi ini. Kata sandi ini diperlukan saat ingin melihat dokumen kembali.
                    </p>
                </div>
            </div>

            <!-- Error message if any -->
            <template x-if="errorMessage">
                <div class="p-3 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-900 text-xs text-rose-600 dark:text-rose-300 font-medium">
                    ⚠️ <span x-text="errorMessage"></span>
                </div>
            </template>

            <!-- Action Button -->
            <div class="pt-2">
                <button 
                    type="button"
                    @click="handleSecureUpload()"
                    :disabled="isEncrypting"
                    class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white font-bold text-xs shadow-sm transition-all flex items-center justify-center space-x-2"
                >
                    <span x-show="!isEncrypting">🔒 Kunci & Amankan Berkas Sekarang</span>
                    <span x-show="isEncrypting">⏳ Sedang mengunci berkas dengan aman...</span>
                </button>
            </div>
        </div>
    </template>

    <!-- Success / Uploaded State -->
    <template x-if="isUploaded">
        <div class="p-4 rounded-xl border border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-between gap-4">
            <div class="flex items-center space-x-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg font-bold shrink-0">
                    ✓
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-emerald-900 dark:text-emerald-200 flex items-center space-x-1.5">
                        <span>Berkas Berhasil Terkunci & Diamankan!</span>
                    </h4>
                    <p class="text-xs text-emerald-800 dark:text-emerald-300 truncate">
                        <span x-text="uploadedName" class="font-semibold"></span>
                        <span x-text="'(' + uploadedSize + ')'" class="text-emerald-600 dark:text-emerald-400 ml-1"></span>
                    </p>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                        👉 Silakan lengkapi Judul dan Kategori dokumen di bawah, lalu klik tombol <strong>"Simpan"</strong> di paling bawah.
                    </p>
                </div>
            </div>
            <button 
                type="button" 
                @click="resetFile()"
                class="px-3 py-1.5 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 text-xs font-semibold shrink-0"
            >
                Ganti Berkas
            </button>
        </div>
    </template>
</div>
