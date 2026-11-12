<div class="space-y-6 text-zinc-800 dark:text-zinc-200" id="printable-member-profile">
    <!-- Header Card -->
    <div class="flex items-center space-x-5 p-4 rounded-xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/20">
        <img 
            src="{{ $record->avatar_path ? asset('storage/' . $record->avatar_path) : ('https://ui-avatars.com/api/?name=' . urlencode($record->full_name) . '&background=18181b&color=f59e0b') }}" 
            class="w-20 h-20 rounded-2xl object-cover border-2 border-amber-500 shadow"
            alt="{{ $record->full_name }}"
        />
        <div class="min-w-0 flex-1">
            <h3 class="text-xl font-bold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                <span>{{ $record->full_name }}</span>
                @if($record->nickname)
                    <span class="text-sm font-normal text-zinc-500 dark:text-zinc-400">({{ $record->nickname }})</span>
                @endif
            </h3>
            <p class="text-xs text-amber-600 dark:text-amber-400 font-semibold uppercase tracking-wider mt-0.5">
                {{ $record->gender === 'female' ? 'Perempuan' : 'Laki-laki' }} &bull; ID #{{ $record->id }}
            </p>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                <span>Lahir: {{ $record->birth_date?->format('d F Y') ?? 'Tidak diketahui' }}</span>
                @if($record->death_date)
                    <span class="ml-2 font-semibold text-rose-500">Wafat: {{ $record->death_date->format('d F Y') }}</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Relationships Grid -->
    <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-3">Silsilah & Hubungan Kekerabatan</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
            <div class="p-3 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/40">
                <span class="text-zinc-400 block font-medium mb-1">Ayah</span>
                <span class="font-bold text-zinc-800 dark:text-zinc-200 text-sm">
                    {{ $record->father?->full_name ?? 'Belum tercatat' }}
                </span>
            </div>
            <div class="p-3 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/40">
                <span class="text-zinc-400 block font-medium mb-1">Ibu</span>
                <span class="font-bold text-zinc-800 dark:text-zinc-200 text-sm">
                    {{ $record->mother?->full_name ?? 'Belum tercatat' }}
                </span>
            </div>
            <div class="p-3 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/40">
                <span class="text-zinc-400 block font-medium mb-1">Pasangan</span>
                <span class="font-bold text-zinc-800 dark:text-zinc-200 text-sm">
                    {{ $record->spouse?->full_name ?? 'Tidak ada' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Children List -->
    @php
        $children = $record->children()->get();
    @endphp
    @if($children->isNotEmpty())
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-3">Keturunan / Anak-anak ({{ $children->count() }})</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                @foreach($children as $child)
                    <div class="flex items-center space-x-2.5 p-2 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/40">
                        <span class="w-2 h-2 rounded-full {{ $child->gender === 'female' ? 'bg-rose-400' : 'bg-sky-400' }}"></span>
                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $child->full_name }}</span>
                        @if($child->birth_date)
                            <span class="text-zinc-400 text-[11px]">({{ $child->birth_date->format('Y') }})</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Associated Documents -->
    @php
        $docs = $record->documents;
        $categoryNames = [
            'identity' => 'Identitas',
            'vehicle' => 'Kendaraan',
            'property' => 'Properti',
            'education' => 'Pendidikan',
            'health' => 'Kesehatan',
            'other' => 'Lainnya',
        ];
    @endphp
    @if($docs->isNotEmpty())
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-3">Dokumen Terenkripsi Tersimpan ({{ $docs->count() }})</h4>
            <div class="space-y-2">
                @foreach($docs as $doc)
                    <div class="flex items-center justify-between p-2.5 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/40 text-xs">
                        <div class="flex items-center space-x-2">
                            <span>🔒</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $doc->title }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 uppercase font-bold">{{ $categoryNames[$doc->category] ?? $doc->category }}</span>
                        </div>
                        <span class="text-[11px] {{ $doc->expiry_date && $doc->expiry_date->isPast() ? 'text-rose-500 font-bold' : 'text-zinc-400' }}">
                            {{ $doc->expiry_date ? 'Berlaku s/d: ' . $doc->expiry_date->format('d M Y') : 'Seumur Hidup' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Notes -->
    @if($record->notes)
        <div class="p-3 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/30 text-xs text-zinc-600 dark:text-zinc-400">
            <span class="font-bold block text-zinc-700 dark:text-zinc-300 mb-1">Catatan / Biografi:</span>
            {{ $record->notes }}
        </div>
    @endif

    <!-- Print Action -->
    <div class="flex justify-end pt-2 border-t border-zinc-200 dark:border-zinc-800 print:hidden">
        <button 
            type="button" 
            onclick="window.print()" 
            class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors flex items-center space-x-1.5"
        >
            <span>🖨️ Cetak Kartu Arsip</span>
        </button>
    </div>
</div>
