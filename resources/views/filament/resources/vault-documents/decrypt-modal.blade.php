<div 
    x-data="{
        passphrase: '',
        isLoading: false,
        errorMessage: '',
        previewUrl: null,
        mimeType: '{{ $record->file_mime }}',
        async decrypt() {
            if (!this.passphrase) {
                this.errorMessage = 'Please enter your master passphrase.';
                return;
            }
            this.isLoading = true;
            this.errorMessage = '';
            try {
                const response = await fetch('{{ route('vault.download.encrypted', $record) }}');
                if (!response.ok) {
                    throw new Error('Unable to fetch encrypted file.');
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
                this.errorMessage = 'Decryption failed: incorrect passphrase or corrupted file.';
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
            <span class="font-semibold text-zinc-700 dark:text-zinc-200">Document:</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $record->title }}</span>
        </div>
        <div class="flex justify-between items-center text-xs text-zinc-500">
            <span>MIME Type: {{ $record->file_mime }}</span>
            <span>Owner: {{ $record->familyMember?->full_name ?? 'Shared' }}</span>
        </div>
    </div>

    <template x-if="!previewUrl">
        <div class="space-y-3">
            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Master Passphrase</label>
                <input 
                    type="password" 
                    x-model="passphrase"
                    @keydown.enter.prevent="decrypt()"
                    placeholder="Enter passphrase used to encrypt..." 
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
                <span x-show="!isLoading">🔓 Decrypt In-Memory</span>
                <span x-show="isLoading">Decrypting with AES-GCM...</span>
            </button>
            <p class="text-[11px] text-zinc-400 text-center italic">
                Decryption runs entirely inside your browser memory. Your passphrase is never sent to the server.
            </p>
        </div>
    </template>

    <template x-if="previewUrl">
        <div class="space-y-4">
            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 rounded-lg text-xs text-emerald-700 dark:text-emerald-300 flex items-center justify-between">
                <span>Decrypted successfully in RAM!</span>
                <button @click="previewUrl = null; passphrase = ''" class="font-bold underline">Lock again</button>
            </div>

            <!-- Preview content -->
            <div class="w-full max-h-[500px] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-950 flex items-center justify-center p-2">
                <template x-if="mimeType.startsWith('image/')">
                    <img :src="previewUrl" class="max-w-full max-h-[480px] object-contain rounded" alt="Decrypted preview" />
                </template>
                <template x-if="mimeType === 'application/pdf'">
                    <iframe :src="previewUrl" class="w-full h-[450px] rounded border-0"></iframe>
                </template>
                <template x-if="!mimeType.startsWith('image/') && mimeType !== 'application/pdf'">
                    <div class="p-6 text-center">
                        <p class="text-sm text-zinc-600 dark:text-zinc-300 mb-3">Decrypted file is ready for download.</p>
                        <a 
                            :href="previewUrl" 
                            download="{{ $record->title }}"
                            class="px-4 py-2 bg-amber-500 text-white rounded-lg text-xs font-semibold"
                        >
                            Download Decrypted File
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
                    Download to Device
                </a>
            </div>
        </div>
    </template>
</div>

