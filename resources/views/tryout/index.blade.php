@extends('layouts.app')
@section('title', 'Daftar Try Out')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2" style="font-family:'Plus Jakarta Sans',sans-serif">Try Out Nasional</h1>
    <p class="text-gray-500 dark:text-gray-400">Uji kemampuanmu dengan simulasi ujian sesungguhnya yang dilengkapi waktu ketat.</p>
</div>

@if($packages->isEmpty())
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-8 text-center mt-6">
    <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada jadwal Try Out yang tersedia untuk jenjang kamu saat ini.</p>
    <p class="text-6xl">🗓️</p>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
    @foreach($packages as $package)
    <a href="{{ route('latihan.show', $package->id) }}" class="block bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl border border-red-100 dark:border-red-900/50 shadow-sm p-6 transition-all hover:shadow-md hover:border-red-300 dark:hover:border-red-700 relative overflow-hidden">
        
        <!-- Decoration -->
        <div class="absolute -right-4 -top-4 w-16 h-16 bg-red-500 rounded-full opacity-10"></div>
        
        <div class="flex justify-between items-start mb-3">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 relative z-10" style="font-family:'Plus Jakarta Sans',sans-serif">{{ $package->name }}</h2>
            @if($package->type === 'free')
            <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-500 text-xs font-bold px-2 py-0.5 rounded-full relative z-10">GRATIS</span>
            @else
            <span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-500 text-xs font-bold px-2 py-0.5 rounded-full relative z-10">PREMIUM</span>
            @endif
        </div>
        
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-5 relative z-10">{{ Str::limit($package->description, 100) }}</p>
        
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 gap-4 mt-auto">
            <div class="flex items-center gap-1 bg-white dark:bg-gray-700 px-2 py-1 rounded border border-gray-100 dark:border-gray-600 shadow-sm">
                <span class="text-base">📝</span>
                <span class="font-semibold">{{ $package->total_questions }} Soal</span>
            </div>
            <div class="flex items-center gap-1 bg-red-50 dark:bg-red-900/30 px-2 py-1 rounded border border-red-100 dark:border-red-800 shadow-sm text-red-700 dark:text-red-400">
                <span class="text-base">⏱️</span>
                @if($package->duration_minutes > 0)
                    <span class="font-bold">{{ $package->duration_minutes }} Menit</span>
                @else
                    <span class="font-bold">Tanpa Waktu</span>
                @endif
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif
@endsection
