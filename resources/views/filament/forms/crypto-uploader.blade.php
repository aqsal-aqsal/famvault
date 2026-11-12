<div 
    x-data="{
        plainFile: null,
        passphrase: '',
        isEncrypting: false,
        encryptedReady: false,
        statusText: '',

        async handleEncrypt() {
            if (!this.plainFile) {
                alert('Silakan pilih berkas yang ingin dienkripsi terlebih dahulu.');
                return;
            }
            if (!this.passphrase || this.passphrase.length < 6) {
                alert('Harap masukkan passphrase yang aman (minimal 6 karakter).');
                return;
            }

            this.isEncrypting = true;
            this.statusText = 'Menurunkan kunci AES 256-bit via PBKDF2...';

            try {
                const result = await window.VaultCrypto.encryptFile(this.plainFile, this.passphrase);
                this.statusText = 'Berkas berhasil dienkripsi dengan AES-GCM 256!';

                // Create a downloadable/uploadable File object
                const encFilename = this.plainFile.name.replace(/\.[^/.]+$/, '') + '.enc';
                const encFile = new File([result.encryptedBlob], encFilename, { type: 'application/octet-stream' });

                // Fill form fields via Livewire
                const mimeField = document.querySelector('input[wire\\:model*=\"file_mime\"], input[name*=\"file_mime\"]');
                const ivField = document.querySelector('input[wire\\:model*=\"iv_salt\"], input[name*=\"iv_salt\"]');
                const sizeField = document.querySelector('input[wire\\:model*=\"file_size_bytes\"], input[name*=\"file_size_bytes\"]');
                const titleField = document.querySelector('input[wire\\:model*=\"title\"], input[name*=\"title\"]');

                if (ivField) {
                    ivField.value = result.ivSalt;
                    ivField.dispatchEvent(new Event('input', { bubbles: true }));
                    ivField.dispatchEvent(new Event('change', { bubbles: true }));
                }
                if (mimeField) {
                    mimeField.value = this.plainFile.type || 'application/octet-stream';
                    mimeField.dispatchEvent(new Event('input', { bubbles: true }));
                }
                if (sizeField) {
                    sizeField.value = result.encryptedBlob.size;
                    sizeField.dispatchEvent(new Event('input', { bubbles: true }));
                }
                if (titleField && !titleField.value) {
                    titleField.value = this.plainFile.name.replace(/\.[^/.]+$/, '');
                    titleField.dispatchEvent(new Event('input', { bubbles: true }));
                }

                // Trigger direct file download of .enc so user can upload it into the file uploader
                const downloadUrl = URL.createObjectURL(result.encryptedBlob);
                const a = document.createElement('a');
                a.href = downloadUrl;
                a.download = encFilename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);

                this.encryptedReady = true;
                this.statusText = `Berkas terenkripsi ${encFilename} telah siap dan diunduh. Silakan pilih berkas tersebut pada kolom 'Berkas Terenkripsi (.enc)' di bawah!`;
            } catch (err) {
                console.error(err);
                this.statusText = 'Gagal melakukan enkripsi: ' + err.message;
            } finally {
                this.isEncrypting = false;
            }
        }
    }"
    class="p-4 rounded-xl border border-amber-300 dark:border-amber-900/60 bg-amber-50/60 dark:bg-amber-950/20 col-span-full space-y-3"
>
    <div class="flex items-center space-x-2 text-amber-800 dark:text-amber-400 font-semibold text-sm">
        <span>🔐 Enkripsi Zero-Knowledge Sisi Klien</span>
    </div>
    <p class="text-xs text-zinc-600 dark:text-zinc-400">
        Pilih berkas asli dan masukkan Master Passphrase. Berkas akan dienkripsi dengan <strong>AES-GCM 256-bit</strong> secara aman di memori browser Anda. Server hanya menerima dan menyimpan berkas terenkripsi (.enc).
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
        <div>
            <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">1. Pilih Berkas Asli (PDF, Gambar, dll)</label>
            <input 
                type="file" 
                @change="plainFile = $event.target.files[0]"
                class="block w-full text-xs text-zinc-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-500 file:text-white hover:file:bg-amber-600"
            />
        </div>

        <div>
            <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">2. Master Passphrase Brankas</label>
            <input 
                type="password" 
                x-model="passphrase"
                placeholder="Masukkan kata sandi rahasia..."
                class="w-full text-xs rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-1.5 focus:ring-1 focus:ring-amber-500"
            />
        </div>
    </div>

    <div class="flex items-center justify-between pt-2">
        <button 
            type="button" 
            @click="handleEncrypt()" 
            :disabled="isEncrypting"
            class="px-4 py-2 bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors flex items-center space-x-1.5"
        >
            <span x-show="!isEncrypting">⚡ Enkripsi Berkas di Browser</span>
            <span x-show="isEncrypting">Sedang Mengenkripsi...</span>
        </button>

        <span x-text="statusText" class="text-xs text-zinc-600 dark:text-zinc-300 italic"></span>
    </div>
</div>
