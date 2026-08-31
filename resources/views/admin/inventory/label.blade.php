<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label QR #{{ $item->inventory_code }} - BawaBeres</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-label {
                border: 2px solid #000 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center p-4">
    <!-- Non-print Action Bar -->
    <div class="no-print mb-6 flex items-center gap-3">
        <button onclick="window.print()" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-md cursor-pointer flex items-center gap-2">
            <span>🖨️ Cetak Label (Print)</span>
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl hover:bg-slate-50 cursor-pointer">
            Tutup
        </button>
    </div>

    <!-- Printable Label Container (50x80mm standard custody badge) -->
    <div class="print-label w-[340px] bg-white border-2 border-slate-900 rounded-2xl p-4 shadow-xl text-slate-900 font-sans space-y-3">
        <!-- Label Header -->
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-2">
            <div class="flex items-center gap-2">
                <div class="h-6 w-6 rounded bg-slate-900 flex items-center justify-center text-amber-400 font-black text-xs">
                    BB
                </div>
                <div>
                    <span class="font-black tracking-wider text-xs block leading-none">BAWABERES</span>
                    <span class="text-[8px] tracking-widest uppercase text-slate-500 block">PHYSICAL CUSTODY</span>
                </div>
            </div>
            <div class="text-right">
                <span class="font-mono font-black text-xs bg-slate-100 px-1.5 py-0.5 rounded border border-slate-300">
                    #{{ $item->qr_code }}
                </span>
            </div>
        </div>

        <!-- QR Code & Primary Info Grid -->
        <div class="grid grid-cols-12 gap-3 items-center">
            <div class="col-span-5 flex justify-center bg-white p-1 rounded-lg border border-slate-200">
                <div class="w-full aspect-square flex items-center justify-center">
                    {!! $item->getQrSvg(120) !!}
                </div>
            </div>

            <div class="col-span-7 space-y-1.5 text-xs">
                <div>
                    <span class="text-[9px] uppercase tracking-wider font-bold text-slate-400 block">KODE INVENTARIS</span>
                    <span class="font-mono font-black text-xs text-slate-900 block truncate">
                        {{ $item->inventory_code }}
                    </span>
                </div>

                <div>
                    <span class="text-[9px] uppercase tracking-wider font-bold text-slate-400 block">NAMA BARANG</span>
                    <span class="font-bold text-xs text-slate-900 block leading-tight truncate">
                        {{ $item->name }}
                    </span>
                </div>

                <div>
                    <span class="text-[9px] uppercase tracking-wider font-bold text-slate-400 block">ORDER / CUSTOMER</span>
                    <span class="font-mono font-semibold text-[11px] text-slate-700 block truncate">
                        #{{ $item->order->order_code }} • {{ $item->order->customer->name }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Storage Location Banner -->
        <div class="bg-slate-900 text-white rounded-xl p-2.5 flex items-center justify-between text-xs">
            <div>
                <span class="text-[8px] uppercase tracking-wider text-amber-400 block font-bold">LOKASI RAK GUDANG</span>
                <span class="font-mono font-black text-sm block">
                    📍 {{ $item->storage_location ?: 'BELUM DI RAK' }}
                </span>
            </div>
            <div class="text-right">
                <span class="text-[8px] uppercase tracking-wider text-slate-400 block font-bold">KONDISI</span>
                <span class="font-bold text-xs text-amber-300 block">
                    {{ $item->condition->label() }}
                </span>
            </div>
        </div>

        <!-- Label Footer -->
        <div class="flex items-center justify-between pt-1 border-t border-slate-200 text-[8px] text-slate-400 font-mono">
            <span>Diterima: {{ $item->created_at->format('d/m/Y H:i') }}</span>
            <span>bawaberes.id/i/{{ $item->qr_code }}</span>
        </div>
    </div>
</body>
</html>
