@extends('layouts.admin')

@section('content')
<div class="rounded-2xl bg-white p-8 border border-slate-200 shadow-xs text-center max-w-xl mx-auto my-12">
    <div class="h-16 w-16 rounded-2xl bg-amber-100 flex items-center justify-center mx-auto mb-4">
        <x-icon name="cog" class="w-8 h-8 text-amber-600 animate-spin" />
    </div>
    <h2 class="text-xl font-bold text-slate-900">{{ $title ?? 'Modul Sedang Disiapkan' }}</h2>
    <p class="text-sm text-slate-500 mt-2">
        Fitur ini akan diimplementasikan pada fase berikutnya sesuai roadmap MVP platform.
    </p>
    <div class="mt-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition-colors">
            <x-icon name="arrow-left" class="w-4 h-4 text-white" />
            <span>Kembali ke Dashboard</span>
        </a>
    </div>
</div>
@endsection
