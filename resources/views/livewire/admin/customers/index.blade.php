<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Data Pelanggan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar pelanggan, riwayat kontak, dan total transaksi</p>
        </div>

        <button 
            type="button" 
            wire:click="openCreateModal"
            class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-bold text-slate-950 shadow-sm shadow-amber-500/20 hover:bg-amber-400 active:scale-95 transition-all cursor-pointer"
        >
            <x-icon name="plus" class="w-4 h-4 text-slate-950" />
            <span>Tambah Pelanggan Baru</span>
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
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="relative w-full max-w-sm">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari (kode pelanggan, nama, no HP, email)..."
                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 pl-9 text-xs text-slate-800 placeholder-slate-400 shadow-xs focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
                <span class="absolute left-3 top-2.5 text-xs text-slate-400">
                    <x-icon name="search" class="w-3.5 h-3.5 text-slate-400" />
                </span>
            </div>

            <div class="text-xs text-slate-500">
                Total: <span class="font-bold text-slate-900">{{ $customers->total() }}</span> Pelanggan Terdaftar
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Kode Pelanggan</th>
                        <th class="px-6 py-3.5">Nama & Kontak</th>
                        <th class="px-6 py-3.5">Email</th>
                        <th class="px-6 py-3.5">Total Pesanan</th>
                        <th class="px-6 py-3.5">Tanggal Registrasi</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-amber-600 text-xs">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="hover:underline">
                                    {{ $customer->customer_code }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $customer->name }}</div>
                                <div class="text-xs text-slate-500 font-mono flex items-center gap-1 mt-0.5">
                                    <x-icon name="phone" class="w-3 h-3 text-slate-400" />
                                    <span>{{ $customer->phone }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $customer->email ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <a 
                                    href="{{ route('admin.customers.show', $customer) }}" 
                                    class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-amber-100 hover:text-amber-800 transition-colors"
                                >
                                    <x-icon name="box" class="w-3.5 h-3.5 text-amber-600" />
                                    <span>{{ $customer->orders_count }} Order</span>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $customer->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a 
                                        href="{{ route('admin.customers.show', $customer) }}"
                                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition-colors"
                                    >
                                        <x-icon name="eye" class="w-3 h-3 text-slate-600" />
                                        <span>Detail</span>
                                    </a>
                                    <button 
                                        type="button" 
                                        wire:click="openEditModal({{ $customer->id }})"
                                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition-colors cursor-pointer"
                                    >
                                        <x-icon name="clipboard" class="w-3 h-3 text-slate-600" />
                                        <span>Edit</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <x-icon name="users" class="w-10 h-10 text-slate-300 mx-auto mb-2" />
                                <p class="text-sm">Tidak ada pelanggan yang cocok dengan pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <!-- Create / Edit Customer Modal -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-5 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-lg">
                        {{ $editingId ? 'Edit Data Pelanggan' : 'Tambah Pelanggan Baru' }}
                    </h3>
                    <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-600 font-bold p-1 cursor-pointer">
                        <x-icon name="x" class="w-5 h-5 text-slate-500" />
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Lengkap Pelanggan
                        </label>
                        <input 
                            type="text" 
                            wire:model="name"
                            placeholder="Contoh: Ahmad Rizki Pratama"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                        >
                        @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Nomor Telepon / WhatsApp
                            </label>
                            <input 
                                type="text" 
                                wire:model="phone"
                                placeholder="081234567890"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                            @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Alamat Email (Opsional)
                            </label>
                            <input 
                                type="email" 
                                wire:model="email"
                                placeholder="ahmad@example.com"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                            @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Catatan Khusus Pelanggan
                        </label>
                        <textarea 
                            wire:model="notes"
                            rows="3"
                            placeholder="Catatan tambahan seperti alamat kost default, preferensi armada, dll..."
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                        ></textarea>
                        @error('notes') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
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
                            {{ $editingId ? 'Simpan Perubahan' : 'Simpan Pelanggan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
