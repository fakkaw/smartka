@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Beranda')
@section('page-subtitle', 'Halo, ' . auth()->user()->name . '! Semangat belajar hari ini 💪')

@section('content')

{{-- ═══ GREETING + STREAK ════════════════════════════ --}}
<div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
  <div class="absolute right-0 top-0 bottom-0 w-48 opacity-10">
    <div class="text-9xl absolute right-4 top-1/2 -translate-y-1/2">📚</div>
  </div>
  <div class="relative">
    <div class="flex items-center gap-2 mb-2">
      @if($streak > 0)
      <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-semibold flex items-center gap-1">
        🔥 {{ $streak }} hari berturut-turut!
      </span>
      @endif
      @if(auth()->user()->isPremium())
      <span class="bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold">⭐ PREMIUM</span>
      @endif
    </div>
    <h2 class="text-2xl font-extrabold mb-1" style="font-family:'Plus Jakarta Sans',sans-serif;">
      Selamat datang, {{ auth()->user()->name }}! 👋
    </h2>
    <p class="text-blue-100 text-sm">
      @if($streak > 0)
        Kamu sudah belajar {{ $streak }} hari berturut-turut. Pertahankan semangatmu!
      @else
        Yuk mulai belajar hari ini dan bangun streakmu! 🚀
      @endif
    </p>
  </div>
</div>

{{-- ═══ METRIC CARDS ══════════════════════════════════ --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
  @foreach([
    ['📝', 'Soal Dikerjakan',   number_format($totalSoal), 'text-blue-600 dark:text-blue-400',   'bg-blue-50 dark:bg-blue-900/30'],
    ['📊', 'Rata-rata Skor',    round($avgScore) . '%',    'text-green-600 dark:text-green-400',  'bg-green-50 dark:bg-green-900/30'],
    ['⏱️', 'Try Out Selesai',   $totalTryout,              'text-purple-600 dark:text-purple-400', 'bg-purple-50 dark:bg-purple-900/30'],
    ['🔥', 'Hari Streak',       $streak . ' hari',         'text-orange-500 dark:text-orange-400', 'bg-orange-50 dark:bg-orange-900/30'],
  ] as [$icon, $label, $value, $textColor, $bgColor])
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition">
    <div class="w-10 h-10 {{ $bgColor }} rounded-xl flex items-center justify-center text-xl mb-3">
      {{ $icon }}
    </div>
    <div class="text-2xl font-extrabold {{ $textColor }} mb-0.5">{{ $value }}</div>
    <div class="text-gray-400 text-xs">{{ $label }}</div>
  </div>
  @endforeach
</div>

{{-- ═══ PROGRESS + AI WIDGET ══════════════════════════ --}}
<div class="grid md:grid-cols-3 gap-6 mb-6">

  {{-- Progress mingguan --}}
  <div class="md:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
    <div class="flex items-center justify-between mb-5">
      <div>
        <h3 class="font-bold text-gray-800 dark:text-gray-100" style="font-family:'Plus Jakarta Sans',sans-serif;">
          Progress 7 Hari Terakhir
        </h3>
        <p class="text-gray-400 text-xs mt-0.5">Rata-rata skor harian kamu</p>
      </div>
      <span class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-medium">Minggu Ini</span>
    </div>

    {{-- Chart bar manual --}}
    <div class="flex items-end gap-3 h-36"
      x-data="{
        bars: {{ json_encode($weeklyProgress->pluck('score')->toArray()) }},
        days: {{ json_encode($weeklyProgress->pluck('date')->toArray()) }},
        hovered: null
      }">
      <template x-for="(score, i) in bars" :key="i">
        <div class="flex-1 flex flex-col items-center gap-1.5 group relative">
          {{-- Tooltip --}}
          <div x-show="hovered === i"
            class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap z-10">
            <span x-text="score + '%'"></span>
          </div>
          {{-- Bar --}}
          <div class="w-full rounded-t-lg transition-all duration-500 cursor-pointer"
            :style="'height: ' + (score > 0 ? Math.max(score * 1.3, 8) : 8) + 'px; background: ' + (score >= 80 ? '#0e9f6e' : score >= 60 ? '#1a56db' : score > 0 ? '#f59e0b' : '#e5e7eb')"
            @mouseenter="hovered = i" @mouseleave="hovered = null">
          </div>
          {{-- Label hari --}}
          <span class="text-xs text-gray-400" x-text="days[i]"></span>
        </div>
      </template>
    </div>

    {{-- Legend --}}
    <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
      <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-500 rounded-sm inline-block"></span> ≥80%</span>
      <span class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-600 rounded-sm inline-block"></span> 60-79%</span>
      <span class="flex items-center gap-1"><span class="w-3 h-3 bg-yellow-400 rounded-sm inline-block"></span> &lt;60%</span>
    </div>
  </div>

  {{-- AI Tutor widget --}}
  <div class="bg-gray-900 rounded-2xl p-5 text-white flex flex-col">
    <div class="flex items-center gap-2 mb-4">
      <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center">🤖</div>
      <div>
        <div class="font-bold text-sm">Smartka AI</div>
        <div class="text-green-400 text-xs flex items-center gap-1">
          <span class="w-1.5 h-1.5 bg-green-400 rounded-full inline-block"></span> Online
        </div>
      </div>
      <span class="ml-auto text-xs bg-blue-600/30 text-blue-300 px-2 py-0.5 rounded-full">Gemini</span>
    </div>

    <p class="text-gray-300 text-sm mb-4 leading-relaxed flex-1">
      Punya soal sulit atau materi yang belum dipahami? Tanya Smartka AI sekarang!
    </p>

    {{-- Kuota --}}
    @if(!auth()->user()->isPremium())
    <div class="mb-4">
      <div class="flex justify-between text-xs text-gray-400 mb-1.5">
        <span>Kuota gratis hari ini</span>
        <span class="{{ $aiQuota <= 1 ? 'text-red-400' : 'text-green-400' }} font-semibold">
          {{ $aiQuota }}/5 tersisa
        </span>
      </div>
      <div class="w-full bg-gray-700 rounded-full h-1.5">
        <div class="h-1.5 rounded-full transition-all {{ $aiQuota <= 1 ? 'bg-red-500' : 'bg-green-500' }}"
          style="width: {{ ($aiQuota / 5) * 100 }}%"></div>
      </div>
    </div>
    @else
    <div class="mb-4 text-xs text-green-400 flex items-center gap-1">
      <span>✓</span> Pertanyaan tanpa batas (Premium)
    </div>
    @endif

    <a href="#"
      class="block text-center bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold py-2.5 rounded-xl transition">
      Buka AI Tutor →
    </a>
  </div>
</div>

{{-- ═══ REKOMENDASI AI ════════════════════════════════ --}}
@if(count($weakTopics) > 0)
<div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-5 mb-6">
  <div class="flex items-center gap-2 mb-4">
    <span class="text-xl">🎯</span>
    <h3 class="font-bold text-amber-800 dark:text-amber-500" style="font-family:'Plus Jakarta Sans',sans-serif;">
      Fokus Belajar Hari Ini — Rekomendasi AI
    </h3>
  </div>
  <div class="grid md:grid-cols-3 gap-3">
    @foreach(array_slice($weakTopics, 0, 3) as $topic)
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-amber-100 dark:border-gray-700">
      <div class="flex items-center gap-2 mb-2">
        <span class="text-red-500">📌</span>
        <span class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $topic }}</span>
      </div>
      <div class="text-xs text-gray-500">Perlu lebih banyak latihan di topik ini</div>
      <a href="#" class="mt-2 inline-block text-xs text-blue-600 font-semibold hover:underline">
        Latihan sekarang →
      </a>
    </div>
    @endforeach
  </div>
</div>
@else
<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-5 mb-6 flex items-center gap-4">
  <div class="text-3xl">🎉</div>
  <div>
    <div class="font-bold text-green-800 dark:text-green-500">Belum ada data analisis</div>
    <div class="text-green-600 dark:text-green-400 text-sm">Selesaikan latihan pertamamu untuk mendapatkan rekomendasi AI personal!</div>
  </div>
</div>
@endif

{{-- ═══ PAKET LATIHAN ══════════════════════════════════ --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
  <div class="flex items-center justify-between mb-5">
    <div>
      <h3 class="font-bold text-gray-800 dark:text-gray-100 text-lg" style="font-family:'Plus Jakarta Sans',sans-serif;">
        Paket Latihan Tersedia
      </h3>
      <p class="text-gray-400 text-xs mt-0.5">Untuk kelas {{ auth()->user()->class_level }}</p>
    </div>
    <a href="#" class="text-sm text-blue-600 font-semibold hover:underline">Lihat semua →</a>
  </div>

  @if($packages->isEmpty())
  {{-- Empty state --}}
  <div class="text-center py-12 text-gray-400">
    <div class="text-5xl mb-3">📭</div>
    <p class="font-medium">Belum ada paket latihan tersedia</p>
    <p class="text-sm mt-1">Admin sedang menyiapkan soal untuk kelasmu.</p>
  </div>
  @else
  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($packages as $pkg)
    <div class="border border-gray-100 dark:border-gray-700 rounded-2xl p-5 hover:border-blue-300 dark:hover:border-blue-500 hover:shadow-md transition group">
      {{-- Header --}}
      <div class="flex items-start justify-between mb-3">
        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-xl">📝</div>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full
          {{ $pkg->type === 'premium' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-500' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-500' }}">
          {{ $pkg->type === 'premium' ? '⭐ PREMIUM' : '✓ FREE' }}
        </span>
      </div>

      {{-- Info --}}
      <h4 class="font-bold text-gray-800 dark:text-gray-100 text-sm mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition line-clamp-2">
        {{ $pkg->name }}
      </h4>
      <p class="text-gray-400 text-xs mb-3 line-clamp-2">
        {{ $pkg->description ?? 'Latihan soal pilihan untuk meningkatkan kemampuanmu.' }}
      </p>

      {{-- Meta --}}
      <div class="flex items-center gap-3 text-xs text-gray-400 mb-4">
        <span class="flex items-center gap-1">📝 {{ $pkg->questions_count }} soal</span>
        <span class="flex items-center gap-1">⏱️ @if($pkg->duration_minutes > 0) {{ $pkg->duration_minutes }} mnt @else Tanpa Waktu @endif</span>
        <span class="flex items-center gap-1">
          {{ $pkg->class_level == '6' ? '🏫' : ($pkg->class_level == '9' ? '🏢' : '🎓') }}
          Kelas {{ $pkg->class_level }}
        </span>
      </div>

      {{-- CTA --}}
      @if($pkg->type === 'premium' && !auth()->user()->isPremium())
      <button
        class="w-full bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-sm font-semibold py-2.5 rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
        🔒 Khusus Premium
      </button>
      @else
      <a href="#"
        class="block text-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition">
        Mulai Latihan →
      </a>
      @endif
    </div>
    @endforeach
  </div>
  @endif
</div>

@endsection