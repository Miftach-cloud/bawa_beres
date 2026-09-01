<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Katalog Layanan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola daftar layanan platform moving, storage, dan delivery</p>
        </div>

        <button 
            type="button" 
            wire:click="openCreateModal"
            class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-bold text-slate-950 shadow-sm shadow-amber-500/20 hover:bg-amber-400 active:scale-95 transition-all cursor-pointer"
        >
            <x-icon name="plus" class="w-4 h-4 text-slate-950" />
            <span>Tambah Layanan Baru</span>
        </button>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-xs font-medium text-emerald-800">
            <x-icon name="check-circle" class="w-5 h-5 text-emerald-600" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Table Container -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-xs overflow-hidden">
        <!-- Filter / Search Toolbar -->
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between gap-4">
            <div class="relative w-full max-w-sm">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari layanan (nama, slug, deskripsi)..."
                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 pl-9 text-xs text-slate-800 placeholder-slate-400 shadow-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
                <span class="absolute left-3 top-2.5 text-xs text-slate-400">
                    <x-icon name="search" class="w-3.5 h-3.5 text-slate-400" />
                </span>
            </div>

            <div class="text-xs text-slate-500">
                Total: <span class="font-bold text-slate-900">{{ $services->total() }}</span> Layanan
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Nama Layanan</th>
                        <th class="px-6 py-3.5">Tipe Penetapan Harga</th>
                        <th class="px-6 py-3.5">Tarif Dasar</th>
                        <th class="px-6 py-3.5">Total Order</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($services as $service)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $service->name }}</div>
                                <div class="text-xs text-slate-400 font-mono">/{{ $service->slug }}</div>
                                @if($service->description)
                                    <p class="text-xs text-slate-500 mt-1 max-w-md line-clamp-1">{{ $service->description }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 border border-slate-200">
                                    {{ $service->pricing_type->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono font-medium text-slate-800">
                                Rp {{ number_format($service->base_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-600">
                                {{ $service->orders_count }} order
                            </td>
                            <td class="px-6 py-4">
                                <button 
                                    type="button"
                                    wire:click="toggleStatus({{ $service->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold border transition-all cursor-pointer {{ $service->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200' }}"
                                >
                                    <span class="h-2 w-2 rounded-full {{ $service->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    <span>{{ $service->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button 
                                    type="button" 
                                    wire:click="openEditModal({{ $service->id }})"
                                    class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition-colors cursor-pointer"
                                >
                                    <x-icon name="clipboard" class="w-3 h-3 text-slate-600" />
                                    <span>Edit</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <x-icon name="cog" class="w-10 h-10 text-slate-300 mx-auto mb-2" />
                                <p class="text-sm">Tidak ada layanan yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($services->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $services->links() }}
            </div>
        @endif
    </div>

    <!-- Create / Edit Modal -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-5 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-lg">
                        {{ $editingId ? 'Edit Layanan' : 'Tambah Layanan Baru' }}
                    </h3>
                    <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-600 font-bold p-1 cursor-pointer">
                        <x-icon name="x" class="w-5 h-5 text-slate-500" />
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Layanan
                        </label>
                        <input 
                            type="text" 
                            wire:model.live="name"
                            placeholder="Contoh: Jasa Pindahan Kost & Rumah"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                        >
                        @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Slug URL
                        </label>
                        <input 
                            type="text" 
                            wire:model="slug"
                            placeholder="jasa-pindahan-kost-rumah"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-mono text-slate-700 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                        >
                        @error('slug') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Tipe Penetapan Harga
                            </label>
                            <select 
                                wire:model="pricing_type"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                                @foreach ($pricingTypes as $type)
                                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                @endforeach
                            </select>
                            @error('pricing_type') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Tarif Dasar (Rp)
                            </label>
                            <input 
                                type="number" 
                                wire:model="base_price"
                                min="0"
                                step="1000"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                            @error('base_price') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Deskripsi Layanan
                        </label>
                        <textarea 
                            wire:model="description"
                            rows="3"
                            placeholder="Penjelasan ringkas cakupan layanan, fasilitas armada, dan tenaga angkut..."
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                        ></textarea>
                        @error('description') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input 
                            type="checkbox" 
                            id="is_active"
                            wire:model="is_active"
                            class="h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500"
                        >
                        <label for="is_active" class="text-xs font-semibold text-slate-700 cursor-pointer">
                            Aktifkan Layanan (Dapat dipesan pelanggan)
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button 
                            type="button" 
                            wire:click="closeModal"
                            class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="rounded-xl bg-amber-500 px-5 py-2 text-xs font-bold text-slate-950 shadow-sm shadow-amber-500/20 hover:bg-amber-400 active:scale-95 transition-all cursor-pointer"
                        >
                            {{ $editingId ? 'Simpan Perubahan' : 'Tambah Layanan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
