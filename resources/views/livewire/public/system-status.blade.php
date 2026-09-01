<div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
        <div>
            <h3 class="font-bold text-slate-900 text-lg">System Foundation Check</h3>
            <p class="text-xs text-slate-500">Livewire Reactivity & Database Connectivity</p>
        </div>
        <div class="flex items-center gap-2">
            @if ($dbStatus === 'connected')
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    MySQL Active ({{ $dbName }})
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 border border-rose-200">
                    <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                    Database Issue: {{ $dbStatus }}
                </span>
            @endif
        </div>
    </div>

    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-slate-600">
            <span>Uji Reaktivitas Livewire: </span>
            <span class="font-bold text-amber-600 text-base" id="counter-value">{{ $counter }} klik</span>
        </div>

        <button 
            type="button" 
            wire:click="increment" 
            class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow-md shadow-amber-500/20 hover:bg-amber-400 active:scale-95 transition-all cursor-pointer"
        >
            <x-icon name="sparkles" class="w-4 h-4 text-slate-950" />
            <span>Klik Test Livewire</span>
        </button>
    </div>
</div>
