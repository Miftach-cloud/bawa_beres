@extends('layouts.admin')

@section('content')
<div class="rounded-2xl bg-white p-8 border border-slate-200 shadow-xs text-center max-w-xl mx-auto my-12">
    <div class="h-16 w-16 rounded-2xl bg-amber-100 flex items-center justify-center text-3xl mx-auto mb-4">
        🚧
    </div>
    <h2 class="text-xl font-bold text-slate-900">{{ $title ?? 'Modul Sedang Disiapkan' }}</h2>
    <p class="text-sm text-slate-500 mt-2">
        Fitur ini akan diimplementasikan pada fase berikutnya sesuai roadmap MVP platform.
    </p>
    <div class="mt-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition-colors">
            ← Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
