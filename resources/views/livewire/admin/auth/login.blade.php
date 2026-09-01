<div class="min-h-screen bg-slate-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo -->
        <div class="flex justify-center">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500 text-slate-950 font-bold text-xl shadow-lg shadow-amber-500/30">
                    <x-icon name="box" class="w-6 h-6 text-slate-950" />
                </span>
            </a>
        </div>
        <h2 class="mt-4 text-center text-2xl font-bold tracking-tight text-white">
            Bawa Beres Internal
        </h2>
        <p class="mt-1 text-center text-xs text-slate-400">
            Akses Panel Khusus Owner, Admin, dan Tim Operasional
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-slate-800/90 backdrop-blur-xl py-8 px-6 shadow-2xl rounded-2xl border border-slate-700 sm:px-10">
            <form wire:submit="login" class="space-y-5">
                <!-- Email / Username Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">
                        Email / Username
                    </label>
                    <div class="mt-1.5">
                        <input 
                            wire:model="email" 
                            id="email" 
                            type="text" 
                            autocomplete="username" 
                            required 
                            placeholder="adminbawaberes atau admin@bawaberes.id"
                            class="block w-full rounded-xl border border-slate-600 bg-slate-900/80 px-4 py-2.5 text-sm text-white placeholder-slate-500 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">
                        Password
                    </label>
                    <div class="mt-1.5">
                        <input 
                            wire:model="password" 
                            id="password" 
                            type="password" 
                            autocomplete="current-password" 
                            required 
                            placeholder="••••••••"
                            class="block w-full rounded-xl border border-slate-600 bg-slate-900/80 px-4 py-2.5 text-sm text-white placeholder-slate-500 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30"
                        >
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input 
                            wire:model="remember" 
                            type="checkbox" 
                            class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-amber-500 focus:ring-amber-500"
                        >
                        <span class="text-xs text-slate-300">Ingat sesi saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl shadow-lg shadow-amber-500/20 text-sm font-bold text-slate-950 bg-amber-500 hover:bg-amber-400 active:scale-98 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all cursor-pointer disabled:opacity-60"
                    >
                        <span wire:loading.remove>Masuk ke Dashboard</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-slate-950" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Back to website link -->
        <div class="text-center mt-6">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-amber-400 transition-colors">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Kembali ke Halaman Utama</span>
            </a>
        </div>
    </div>
</div>
