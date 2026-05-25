@extends('layouts.app')
@section('title', $package->name)

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100" style="font-family:'Plus Jakarta Sans',sans-serif">{{ $package->name }}</h1>
    @if($package->type === 'free')
    <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-500 text-sm font-bold px-3 py-1 rounded-full">GRATIS</span>
    @else
    <span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-500 text-sm font-bold px-3 py-1 rounded-full">PREMIUM</span>
    @endif
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 mb-6">
    <p class="text-gray-600 dark:text-gray-300 mb-4">{{ $package->description }}</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-300">
        <div class="flex items-center gap-2">
            <span class="text-lg">📝</span>
            <span>Jumlah Soal: <span class="font-semibold">{{ $package->total_questions }}</span></span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-lg">⏱️</span>
            <span>Durasi: <span class="font-semibold">@if($package->duration_minutes > 0) {{ $package->duration_minutes }} Menit @else Tanpa Waktu @endif</span></span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-lg">✅</span>
            <span>Tipe: <span class="font-semibold">{{ $package->type === 'free' ? 'Gratis' : 'Premium' }}</span></span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-lg">📊</span>
            <span>Level: <span class="font-semibold">{{ $package->class_level }}</span></span>
        </div>
    </div>
</div>

@if($package->type === 'premium' && !Auth::user()->isPremium())
<div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-yellow-700 dark:text-yellow-500 rounded-xl px-4 py-3 mb-6 text-sm flex items-center gap-3">
    <span>👑</span>
    <span>Paket ini hanya tersedia untuk pengguna Premium. <a href="{{ route('premium') }}" class="font-semibold text-yellow-800 dark:text-yellow-400 hover:underline">Upgrade sekarang!</a></span>
</div>
<button class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 dark:disabled:bg-gray-700 disabled:cursor-not-allowed text-white font-semibold py-3 rounded-xl transition text-sm" disabled>
    Mulai Latihan
</button>
@else
<a href="{{ route('latihan.start', $package->id) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition text-sm flex items-center justify-center">
    <span class="text-lg mr-2">▶️</span> Mulai Latihan
</a>
@endif

<div class="mt-8">
    <a href="{{ route('latihan.index') }}" class="text-blue-600 hover:underline text-sm">
        ← Kembali ke Daftar Paket
    </a>
</div>
@endsection
