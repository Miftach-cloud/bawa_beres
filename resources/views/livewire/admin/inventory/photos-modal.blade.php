<div>
    @if ($show && $item)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs overflow-y-auto">
            <div class="w-full max-w-4xl rounded-2xl bg-white shadow-2xl border border-slate-200 overflow-hidden my-8 max-h-[90vh] flex flex-col">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <x-icon name="camera" class="w-5 h-5 text-blue-600" />
                            <h3 class="font-bold text-slate-900 text-base">
                                Dokumentasi Foto: {{ $item->name }}
                            </h3>
                            <span class="font-mono text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                {{ $item->inventory_code }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Order #{{ $item->order->order_code }} • Customer: {{ $item->order->customer->name }}
                        </p>
                    </div>

                    <button 
                        type="button" 
                        wire:click="close" 
                        class="text-slate-400 hover:text-slate-600 font-bold p-1 rounded-lg hover:bg-slate-200 cursor-pointer"
                    >
                        <x-icon name="x" class="w-5 h-5 text-slate-500" />
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto space-y-6 flex-1">
                    <!-- Flash Message -->
                    @if (session()->has('photo_message'))
                        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 flex items-center gap-2 text-xs font-medium text-emerald-800">
                            <x-icon name="check-circle" class="w-4 h-4 text-emerald-600" />
                            <span>{{ session('photo_message') }}</span>
                        </div>
                    @endif

                    <!-- Upload Section -->
                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 space-y-3">
                        <h4 class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                            <x-icon name="camera" class="w-4 h-4 text-slate-700" />
                            <span>Unggah Foto Dokumentasi Baru (Bisa Banyak Sekaligus)</span>
                        </h4>

                        <form wire:submit="uploadPhotos" class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Foto *</label>
                                    <select wire:model="type" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900">
                                        @foreach ($types as $t)
                                            <option value="{{ $t->value }}">{{ $t->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Catatan Foto</label>
                                    <input type="text" wire:model="caption" placeholder="Contoh: Kondisi lecet sebelum diangkut..." class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs text-slate-900">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih File Foto (JPG/PNG/WebP, maks 10MB) *</label>
                                <input type="file" wire:model="photos" multiple accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 file:cursor-pointer">
                                @error('photos.*') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                @error('photos') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Upload Button with Loading Indicator -->
                            <div class="flex items-center justify-between pt-1">
                                <div wire:loading wire:target="photos" class="text-xs text-amber-600 font-medium">
                                    ⏳ Memproses file foto...
                                </div>
                                <div wire:loading.remove wire:target="photos"></div>

                                <button 
                                    type="submit" 
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 active:scale-95 transition-all cursor-pointer"
                                >
                                    <x-icon name="download" class="w-3.5 h-3.5 text-white" />
                                    <span>Unggah Foto</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Category Filter Tabs -->
                    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
                        <button 
                            type="button" 
                            wire:click="$set('selectedCategoryFilter', '')"
                            class="rounded-lg px-3 py-1 text-xs font-bold transition-all cursor-pointer {{ $selectedCategoryFilter === '' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                        >
                            Semua Foto ({{ $item->photos()->count() }})
                        </button>
                        @foreach ($types as $t)
                            <button 
                                type="button" 
                                wire:click="$set('selectedCategoryFilter', '{{ $t->value }}')"
                                class="rounded-lg px-3 py-1 text-xs font-bold transition-all cursor-pointer {{ $selectedCategoryFilter === $t->value ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                            >
                                {{ $t->name }} ({{ $item->photos()->where('type', $t->value)->count() }})
                            </button>
                        @endforeach
                    </div>

                    <!-- Gallery Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @forelse ($photosList as $p)
                            <div class="group relative rounded-xl border border-slate-200 bg-white overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                                <div class="relative aspect-4/3 bg-slate-100 overflow-hidden cursor-pointer" wire:click="viewPhoto({{ $p->id }})">
                                    <img src="{{ $p->url }}" alt="{{ $p->caption ?: $p->file_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1">
                                        <x-icon name="eye" class="w-4 h-4 text-white" />
                                        <span>Lihat Foto</span>
                                    </div>
                                    <span class="absolute top-2 left-2 inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-bold border shadow-xs {{ $p->type->badgeColor() }}">
                                        {{ $p->type->name }}
                                    </span>
                                </div>

                                <div class="p-2.5 space-y-1 text-xs">
                                    @if ($p->caption)
                                        <p class="font-semibold text-slate-800 line-clamp-1 leading-tight text-[11px]">
                                            {{ $p->caption }}
                                        </p>
                                    @endif
                                    <div class="flex items-center justify-between text-[10px] text-slate-400">
                                        <span>{{ $p->formatted_size }}</span>
                                        <span>{{ $p->created_at->format('d/m/y H:i') }}</span>
                                    </div>
                                    @if ($p->uploader)
                                        <div class="text-[10px] text-slate-400">
                                            Oleh: {{ $p->uploader->name }}
                                        </div>
                                    @endif
                                </div>

                                <div class="p-2 pt-0 flex justify-end">
                                    <button 
                                        type="button" 
                                        wire:click="deletePhoto({{ $p->id }})" 
                                        wire:confirm="Yakin ingin menghapus foto dokumentasi ini?"
                                        class="inline-flex items-center gap-1 text-[11px] text-rose-500 hover:text-rose-700 font-semibold cursor-pointer"
                                    >
                                        <x-icon name="trash" class="w-3 h-3 text-rose-500" />
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full rounded-xl border border-dashed border-slate-200 p-8 text-center text-slate-400 text-xs">
                                <x-icon name="camera" class="w-8 h-8 text-slate-300 mx-auto mb-1.5" />
                                Belum ada foto dokumentasi untuk kategori ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3 border-t border-slate-100 bg-slate-50 flex items-center justify-end">
                    <button 
                        type="button" 
                        wire:click="close" 
                        class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 cursor-pointer"
                    >
                        Tutup Galeri
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- High-res Lightbox Modal -->
    @if ($activePhoto)
        <div class="fixed inset-0 z-60 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md" wire:click="closeLightbox">
            <div class="relative max-w-4xl max-h-[90vh] flex flex-col items-center" wire:click.stop>
                <button 
                    type="button" 
                    wire:click="closeLightbox" 
                    class="absolute -top-10 right-0 text-white hover:text-amber-400 font-bold text-sm inline-flex items-center gap-1 cursor-pointer"
                >
                    <x-icon name="x" class="w-4 h-4 text-white" />
                    <span>Tutup</span>
                </button>
                <img src="{{ $activePhoto->url }}" alt="{{ $activePhoto->caption }}" class="max-w-full max-h-[75vh] object-contain rounded-xl shadow-2xl">
                <div class="mt-3 text-center text-white space-y-1">
                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-bold border {{ $activePhoto->type->badgeColor() }}">
                        {{ $activePhoto->type->label() }}
                    </span>
                    @if ($activePhoto->caption)
                        <p class="text-sm font-medium">{{ $activePhoto->caption }}</p>
                    @endif
                    <p class="text-xs text-slate-400 font-mono">
                        {{ $activePhoto->file_name }} ({{ $activePhoto->formatted_size }}) • {{ $activePhoto->created_at->translatedFormat('d F Y, H:i') }} WIB
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
