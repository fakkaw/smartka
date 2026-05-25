@extends('layouts.app')
@section('title', 'Daftar Paket Latihan')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6" style="font-family:'Plus Jakarta Sans',sans-serif">Pilih Paket Latihan</h1>

@if($packages->isEmpty())
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-8 text-center">
    <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada paket latihan yang tersedia untuk jenjang kamu.</p>
    <p class="text-6xl">😔</p>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($packages as $package)
    <a href="{{ route('latihan.show', $package->id) }}" class="block bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 transition-all hover:shadow-md hover:border-blue-200 dark:hover:border-blue-500">
        <div class="flex justify-between items-start mb-3">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100" style="font-family:'Plus Jakarta Sans',sans-serif">{{ $package->name }}</h2>
            @if($package->type === 'free')
            <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-500 text-xs font-bold px-2 py-0.5 rounded-full">GRATIS</span>
            @else
            <span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-500 text-xs font-bold px-2 py-0.5 rounded-full">PREMIUM</span>
            @endif
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">{{ Str::limit($package->description, 100) }}</p>
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 gap-4">
            <div class="flex items-center gap-1">
                <span class="text-base">📝</span>
                <span>{{ $package->total_questions }} Soal</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="text-base">⏱️</span>
                <span>@if($package->duration_minutes > 0) {{ $package->duration_minutes }} Menit @else Tanpa Waktu @endif</span>
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif
@endsection
