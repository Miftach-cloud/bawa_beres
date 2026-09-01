<div id="booking" class="w-full">
    @if ($isSubmitted && $createdOrder)
        <!-- Booking Success Confirmation Screen -->
        <div class="rounded-3xl bg-white p-8 sm:p-10 border border-slate-200 shadow-xl max-w-3xl mx-auto space-y-6 text-slate-900 animate-fade-in">
            <div class="text-center space-y-3">
                <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 shadow-lg shadow-emerald-500/10">
                    <x-icon name="check" class="w-8 h-8 text-emerald-600" />
                </div>
                <h2 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">
                    Pesanan Berhasil Diterima!
                </h2>
                <p class="text-sm text-slate-600 max-w-md mx-auto">
                    Terima kasih <strong class="text-slate-900">{{ $createdOrder->customer->name }}</strong>, pesanan Anda telah masuk ke sistem operasional BawaBeres.
                </p>
                <div class="inline-block mt-2 font-mono text-sm font-black text-amber-600 bg-amber-50 px-4 py-2 rounded-xl border border-amber-200 shadow-xs">
                    Nomor Order: {{ $createdOrder->order_code }}
                </div>
            </div>

            <!-- Summary Card -->
            <div class="rounded-2xl bg-slate-50 border border-slate-200/80 p-5 space-y-3 text-xs">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <span class="text-slate-500 font-semibold">Layanan:</span>
                    <span class="font-bold text-slate-900">{{ $createdOrder->service->name }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <span class="text-slate-500 font-semibold">Tanggal Diinginkan:</span>
                    <span class="font-bold text-slate-900">{{ $createdOrder->preferred_date ? $createdOrder->preferred_date->translatedFormat('d F Y') : '-' }}</span>
                </div>
                <div class="flex items-start justify-between border-b border-slate-200 pb-2">
                    <span class="text-slate-500 font-semibold">Alamat Penjemputan:</span>
                    <span class="font-medium text-slate-800 text-right max-w-xs">{{ $createdOrder->pickupAddress?->address ?? '-' }}</span>
                </div>
                @if ($createdOrder->destinationAddress)
                    <div class="flex items-start justify-between border-b border-slate-200 pb-2">
                        <span class="text-slate-500 font-semibold">Alamat Tujuan:</span>
                        <span class="font-medium text-slate-800 text-right max-w-xs">{{ $createdOrder->destinationAddress->address }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Jumlah Barang Dideklarasikan:</span>
                    <span class="font-bold text-slate-900">{{ $createdOrder->items->count() }} jenis barang</span>
                </div>
            </div>

            <!-- WhatsApp Direct Confirmation CTA -->
            @php
                $waMessage = rawurlencode("Halo Admin BawaBeres, saya sudah membuat pesanan {$createdOrder->order_code} untuk layanan {$createdOrder->service->name} a.n {$createdOrder->customer->name}. Mohon info estimasi penawaran/jadwalnya.");
            @endphp
            <div class="space-y-3 pt-2">
                <a 
                    href="https://wa.me/6281234567890?text={{ $waMessage }}" 
                    target="_blank"
                    class="w-full flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 hover:bg-emerald-500 px-6 py-4 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 transition cursor-pointer"
                >
                    <x-icon name="chat" class="w-5 h-5 text-white" />
                    <span>Konfirmasi Cepat via WhatsApp</span>
                </a>

                <button 
                    type="button" 
                    wire:click="resetBooking"
                    class="w-full rounded-2xl border border-slate-300 px-5 py-3 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer"
                >
                    Buat Pesanan Lainnya
                </button>
            </div>
        </div>
    @else
        <!-- Public Booking Form Container -->
        <div class="rounded-3xl bg-white p-6 sm:p-10 border border-slate-200 shadow-xl max-w-3xl mx-auto space-y-8 text-slate-900">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-amber-100/80 px-3 py-1 text-xs font-bold text-amber-800 mb-2">
                    <x-icon name="sparkles" class="w-4 h-4 text-amber-600" />
                    <span>Form Pemesanan Instan (Tanpa Ribet Daftar)</span>
                </div>
                <h2 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">
                    Pesan Jasa Pindahan & Penyimpanan
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Isi detail penjemputan dan barang Anda. Tim BawaBeres akan segera menghubungi via WhatsApp untuk estimasi penawaran resmi.
                </p>
            </div>

            <form wire:submit="submit" class="space-y-8 text-xs">
                <!-- 1. Pilih Layanan -->
                <div class="space-y-3">
                    <label class="block text-sm font-bold text-slate-900">1. Pilih Layanan yang Anda Butuhkan *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach ($services as $srv)
                            <label 
                                class="relative flex flex-col p-4 rounded-2xl border cursor-pointer transition-all {{ $serviceId === $srv->id ? 'border-amber-500 bg-amber-50/50 shadow-sm ring-2 ring-amber-500/20' : 'border-slate-200 bg-white hover:border-slate-300' }}"
                            >
                                <input type="radio" wire:model.live="serviceId" value="{{ $srv->id }}" class="sr-only">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm text-slate-900">{{ $srv->name }}</span>
                                    <span class="h-4 w-4 rounded-full border flex items-center justify-center {{ $serviceId === $srv->id ? 'border-amber-500 bg-amber-500 text-white text-[10px]' : 'border-slate-300' }}">
                                        @if ($serviceId === $srv->id)
                                            <x-icon name="check" class="w-2.5 h-2.5 text-slate-950" />
                                        @endif
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed">{{ $srv->description }}</p>
                            </label>
                        @endforeach
                    </div>
                    @error('serviceId') <p class="text-rose-500 text-xs">{{ $message }}</p> @enderror
                </div>

                <!-- 2. Data Kontak Pemesan -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <label class="block text-sm font-bold text-slate-900">2. Informasi Kontak Pemesan *</label>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                            <input 
                                type="text" 
                                wire:model="customerName" 
                                placeholder="Contoh: Budi Santoso" 
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                            @error('customerName') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp / HP Aktif *</label>
                            <input 
                                type="text" 
                                wire:model="customerPhone" 
                                placeholder="Contoh: 081234567890" 
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                            @error('customerPhone') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Email (Opsional untuk bukti invoice)</label>
                        <input 
                            type="email" 
                            wire:model="customerEmail" 
                            placeholder="budi@example.com" 
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                        >
                        @error('customerEmail') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- 3. Alamat Lokasi -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <label class="block text-sm font-bold text-slate-900">3. Lokasi Penjemputan & Tujuan *</label>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap Penjemputan / Asal *</label>
                            <input 
                                type="text" 
                                wire:model="pickupAddress" 
                                placeholder="Contoh: Jl. Soekarno Hatta No. 45, Kost Melati Kamar 12" 
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                            @error('pickupAddress') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kota / Area</label>
                            <input 
                                type="text" 
                                wire:model="pickupCity" 
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                        </div>
                    </div>

                    <div>
                        <input 
                            type="text" 
                            wire:model="pickupNotes" 
                            placeholder="Catatan akses: Lantai 2, tangga sempit, gang masuk mobil pick-up..." 
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-xs text-slate-700"
                        >
                    </div>

                    @if (!$isStorageService)
                        <div class="pt-2 space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Alamat Tujuan Pengantaran *</label>
                                    <input 
                                        type="text" 
                                        wire:model="destinationAddress" 
                                        placeholder="Contoh: Perumahan Sigura-gura Indah Blok C-10" 
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                                    >
                                    @error('destinationAddress') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Kota Tujuan</label>
                                    <input 
                                        type="text" 
                                        wire:model="destinationCity" 
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                                    >
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 4. Deklarasi Daftar Barang -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-bold text-slate-900">4. Daftar Barang yang Akan Diangkut / Dititipkan *</label>
                        <button 
                            type="button" 
                            wire:click="addItem" 
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 hover:text-amber-700 cursor-pointer"
                        >
                            <x-icon name="plus" class="w-3.5 h-3.5 text-amber-600" />
                            <span>Tambah Barang</span>
                        </button>
                    </div>

                    <div class="space-y-2">
                        @foreach ($items as $index => $item)
                            <div class="flex items-center gap-2 bg-slate-50 p-2.5 rounded-2xl border border-slate-200/80">
                                <div class="flex-1">
                                    <input 
                                        type="text" 
                                        wire:model="items.{{ $index }}.name" 
                                        placeholder="Nama barang (e.g. Kasur Springbed No. 2, Kulkas 1 Pintu, 3 Kardus Baju)" 
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900"
                                    >
                                    @error("items.{$index}.name") <p class="text-rose-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                                </div>

                                <div class="w-20">
                                    <input 
                                        type="number" 
                                        min="1" 
                                        wire:model="items.{{ $index }}.quantity" 
                                        placeholder="Qty" 
                                        class="w-full rounded-xl border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-center text-slate-900"
                                    >
                                </div>

                                @if (count($items) > 1)
                                    <button 
                                        type="button" 
                                        wire:click="removeItem({{ $index }})" 
                                        class="text-rose-500 hover:text-rose-700 p-1.5 font-bold cursor-pointer"
                                        title="Hapus baris"
                                    >
                                        <x-icon name="trash" class="w-4 h-4 text-rose-500" />
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 5. Foto Barang (Estimasi) -->
                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <label class="block text-sm font-bold text-slate-900">5. Foto Barang / Ruangan (Opsional - Sangat Membantu Estimasi Armada)</label>
                    <div class="rounded-2xl border-2 border-dashed border-slate-200 p-5 text-center hover:bg-slate-50/50 transition">
                        <input 
                            type="file" 
                            wire:model="photos" 
                            multiple 
                            accept="image/*" 
                            class="hidden" 
                            id="photo-upload"
                        >
                        <label for="photo-upload" class="cursor-pointer block space-y-1">
                            <x-icon name="camera" class="w-8 h-8 text-amber-500 mx-auto" />
                            <span class="font-bold text-amber-600 text-xs block">Klik untuk upload foto barang</span>
                            <span class="text-slate-400 text-[11px] block">Mendukung format JPG, PNG hingga 10MB</span>
                        </label>

                        @if (!empty($photos))
                            <div class="mt-3 flex flex-wrap gap-2 justify-center">
                                @foreach ($photos as $photo)
                                    <div class="h-14 w-14 rounded-lg overflow-hidden border border-slate-200 shadow-xs">
                                        <img src="{{ $photo->temporaryUrl() }}" class="h-full w-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 6. Jadwal & Catatan Tambahan -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-900 mb-1">6. Tanggal Penjemputan yang Diinginkan *</label>
                            <input 
                                type="date" 
                                wire:model="preferredDate" 
                                min="{{ date('Y-m-d') }}"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                            @error('preferredDate') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-slate-900 mb-1">Catatan Tambahan untuk Tim Lapangan</label>
                            <input 
                                type="text" 
                                wire:model="customerNotes" 
                                placeholder="Contoh: Butuh packing kardus tambahan, ada barang pecah belah..." 
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-100">
                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full rounded-2xl bg-amber-500 px-6 py-4 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/25 hover:bg-amber-400 active:scale-98 transition-all cursor-pointer flex items-center justify-center gap-2 disabled:opacity-50"
                    >
                        <span wire:loading.remove class="inline-flex items-center gap-2">
                            <x-icon name="sparkles" class="w-4 h-4 text-slate-950" />
                            <span>Kirim Pesanan Sekarang</span>
                        </span>
                        <span wire:loading class="inline-flex items-center gap-2">
                            <x-icon name="refresh" class="w-4 h-4 animate-spin text-slate-950" />
                            <span>Sedang Memproses Pesanan...</span>
                        </span>
                    </button>
                    <p class="text-center text-slate-400 text-[11px] mt-2">
                        Tanpa perlu bayar sekarang. Tim BawaBeres akan mengecek ketersediaan armada dan mengirim penawaran harga resmi.
                    </p>
                </div>
            </form>
        </div>
    @endif
</div>
