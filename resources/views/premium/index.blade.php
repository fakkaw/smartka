@extends('layouts.app')
@section('title', 'Upgrade Premium')
@section('page-title', 'Upgrade Premium')
@section('page-subtitle', 'Buka semua fitur dan belajar tanpa batas!')

@section('content')

{{-- Hero --}}
<div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-8 mb-8 text-white text-center relative overflow-hidden">
  <div class="absolute inset-0 opacity-10">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
  </div>
  <div class="relative">
    <div class="text-5xl mb-3">⚡</div>
    <h2 class="text-2xl font-extrabold mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">
      Buka Potensi Penuh Belajarmu
    </h2>
    <p class="text-blue-100 max-w-lg mx-auto text-sm">
      Akses ribuan soal, try out tanpa batas, dan AI Tutor personal yang siap membantu 24/7.
    </p>
    @if($user->isPremium())
    <div class="mt-4 inline-flex items-center gap-2 bg-green-500 px-4 py-2 rounded-full text-sm font-semibold">
      ✓ Kamu sudah Premium hingga {{ $user->subscription_ends_at?->format('d M Y') }}
    </div>
    @endif
  </div>
</div>

{{-- Toggle billing period --}}
<div class="flex justify-center mb-8" x-data="{ yearly: false }">
  <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl p-1 flex items-center gap-1">
    <button @click="yearly = false"
      :class="!yearly ? 'bg-white dark:bg-gray-600 shadow-sm text-blue-600 font-semibold' : 'text-gray-500 dark:text-gray-400'"
      class="px-5 py-2.5 rounded-xl text-sm transition">
      Bulanan
    </button>
    <button @click="yearly = true"
      :class="yearly ? 'bg-white dark:bg-gray-600 shadow-sm text-blue-600 font-semibold' : 'text-gray-500 dark:text-gray-400'"
      class="px-5 py-2.5 rounded-xl text-sm transition flex items-center gap-2">
      Tahunan
      <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-bold">Hemat 26%</span>
    </button>
  </div>

  {{-- Pricing Cards --}}
  <div class="hidden"><!-- Alpine scope --></div>
</div>

<div x-data="{ yearly: false }" class="max-w-5xl mx-auto">

  {{-- Toggle ulang di sini karena scope --}}
  <div class="flex justify-center mb-8">
    <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl p-1 flex items-center gap-1">
      <button @click="yearly = false"
        :class="!yearly ? 'bg-white dark:bg-gray-600 shadow-sm text-blue-600 font-semibold' : 'text-gray-500 dark:text-gray-400'"
        class="px-5 py-2.5 rounded-xl text-sm transition">
        Bulanan
      </button>
      <button @click="yearly = true"
        :class="yearly ? 'bg-white dark:bg-gray-600 shadow-sm text-blue-600 font-semibold' : 'text-gray-500 dark:text-gray-400'"
        class="px-5 py-2.5 rounded-xl text-sm transition flex items-center gap-2">
        Tahunan
        <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-bold">Hemat 26%</span>
      </button>
    </div>
  </div>

  {{-- Cards --}}
  <div class="grid md:grid-cols-3 gap-6 items-start">

    {{-- FREE --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 border-2 border-gray-200 dark:border-gray-700 shadow-sm">
      <div class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">Free</div>
      <div class="text-4xl font-extrabold text-gray-800 dark:text-white mb-1">Rp 0</div>
      <div class="text-gray-400 dark:text-gray-500 text-sm mb-6">Selamanya gratis</div>
      <ul class="space-y-3 mb-8">
        @foreach($plans['free']['features'] as $f)
        <li class="flex items-center gap-2.5 text-sm {{ $f['ok'] ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600' }}">
          <span class="{{ $f['ok'] ? 'text-green-500' : 'text-gray-300 dark:text-gray-600' }} text-base">
            {{ $f['ok'] ? '✓' : '✗' }}
          </span>
          {{ $f['text'] }}
        </li>
        @endforeach
      </ul>
      <div class="block text-center border-2 border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 font-semibold py-3 rounded-xl text-sm">
        Paket Saat Ini
      </div>
    </div>

    {{-- PREMIUM --}}
    <div class="bg-blue-600 rounded-3xl p-8 shadow-2xl relative -mt-4">
      <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-yellow-400 text-yellow-900 text-xs font-extrabold px-5 py-1.5 rounded-full shadow-md whitespace-nowrap">
        🔥 PALING POPULER
      </div>
      <div class="text-xs font-bold text-blue-200 uppercase tracking-widest mb-3">Premium</div>

      {{-- Harga bulanan --}}
      <div x-show="!yearly">
        <div class="text-4xl font-extrabold text-white mb-1">Rp 79K</div>
        <div class="text-blue-200 text-sm mb-6">per bulan</div>
      </div>
      {{-- Harga tahunan --}}
      <div x-show="yearly">
        <div class="text-4xl font-extrabold text-white mb-1">Rp 699K</div>
        <div class="text-blue-200 text-sm mb-1">per tahun</div>
        <div class="text-green-300 text-xs mb-5">Hemat Rp 249K dari harga normal!</div>
      </div>

      <ul class="space-y-3 mb-8">
        @foreach($plans['premium']['features'] as $f)
        <li class="flex items-center gap-2.5 text-sm {{ $f['ok'] ? 'text-blue-100' : 'text-blue-300/50' }}">
          <span class="{{ $f['ok'] ? 'text-green-300' : 'text-blue-300/50' }} text-base">
            {{ $f['ok'] ? '✓' : '✗' }}
          </span>
          {{ $f['text'] }}
        </li>
        @endforeach
      </ul>

      @if($user->isPremium() && $user->subscription_status === 'premium')
      <div class="block text-center bg-white/20 text-white font-semibold py-3 rounded-xl text-sm">
        ✓ Paket Aktif
      </div>
      @else
      <div>
        <a x-show="!yearly" href="{{ route('checkout', 'premium') }}"
          class="block text-center bg-white text-blue-700 font-bold py-3.5 rounded-xl hover:bg-yellow-50 transition shadow-md text-sm">
          Upgrade Premium →
        </a>
        <a x-show="yearly" href="{{ route('checkout', 'premium') }}?period=yearly"
          class="block text-center bg-white text-blue-700 font-bold py-3.5 rounded-xl hover:bg-yellow-50 transition shadow-md text-sm">
          Upgrade Premium (Tahunan) →
        </a>
      </div>
      @endif
      <p class="text-center text-blue-200 text-xs mt-3">🛡️ Garansi uang kembali 7 hari</p>
    </div>

    {{-- PREMIUM PLUS --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 border-2 border-yellow-300 dark:border-yellow-600 shadow-lg">
      <div class="text-xs font-bold text-yellow-600 dark:text-yellow-400 uppercase tracking-widest mb-3">Premium Plus</div>

      <div x-show="!yearly">
        <div class="text-4xl font-extrabold text-gray-800 dark:text-white mb-1">Rp 129K</div>
        <div class="text-gray-400 dark:text-gray-500 text-sm mb-6">per bulan</div>
      </div>
      <div x-show="yearly">
        <div class="text-4xl font-extrabold text-gray-800 dark:text-white mb-1">Rp 1.199K</div>
        <div class="text-gray-400 dark:text-gray-500 text-sm mb-1">per tahun</div>
        <div class="text-green-600 dark:text-green-400 text-xs mb-5">Hemat Rp 349K dari harga normal!</div>
      </div>

      <ul class="space-y-3 mb-8">
        @foreach($plans['premium_plus']['features'] as $f)
        <li class="flex items-center gap-2.5 text-sm text-gray-700 dark:text-gray-300">
          <span class="text-yellow-500 dark:text-yellow-400 text-base">★</span>
          {{ $f['text'] }}
        </li>
        @endforeach
      </ul>

      @if($user->subscription_status === 'premium_plus')
      <div class="block text-center border-2 border-yellow-300 dark:border-yellow-600 text-yellow-600 dark:text-yellow-400 font-semibold py-3 rounded-xl text-sm">
        ✓ Paket Aktif
      </div>
      @else
      <div>
        <a x-show="!yearly" href="{{ route('checkout', 'premium_plus') }}"
          class="block text-center border-2 border-yellow-400 dark:border-yellow-500 text-yellow-700 dark:text-yellow-400 font-bold py-3 rounded-xl hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition text-sm">
          Pilih Premium Plus →
        </a>
        <a x-show="yearly" href="{{ route('checkout', 'premium_plus') }}?period=yearly"
          class="block text-center border-2 border-yellow-400 dark:border-yellow-500 text-yellow-700 dark:text-yellow-400 font-bold py-3 rounded-xl hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition text-sm">
          Pilih Plus (Tahunan) →
        </a>
      </div>
      @endif
    </div>
  </div>

  {{-- Comparison table --}}
  <div class="mt-12 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
      <h3 class="font-bold text-gray-800 dark:text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">
        Perbandingan Lengkap Fitur
      </h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="text-left px-6 py-3 text-gray-500 dark:text-gray-400 font-medium">Fitur</th>
            <th class="text-center px-4 py-3 text-gray-500 dark:text-gray-400 font-medium">Free</th>
            <th class="text-center px-4 py-3 text-blue-600 dark:text-blue-400 font-bold">Premium</th>
            <th class="text-center px-4 py-3 text-yellow-600 dark:text-yellow-400 font-bold">Plus</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
          @foreach([
            ['Soal per hari',         '20',           '∞',  '∞'],
            ['Try out per bulan',     '1',            '∞',  '∞'],
            ['Pertanyaan AI / hari',  '5',            '∞',  '∞'],
            ['Pembahasan video',      '✗',            '✓',  '✓'],
            ['Hint bantuan soal',     '✗',            '✓',  '✓'],
            ['Analisis AI kelemahan', 'Dasar',        '✓',  '✓'],
            ['Laporan orang tua',     '✗',            '✗',  '✓'],
            ['Konsultasi guru',       '✗',            '✗',  '2x/bln'],
            ['Prioritas dukungan',    'Standar',      '✓',  'VIP'],
          ] as [$feat, $free, $prem, $plus])
          <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50">
            <td class="px-6 py-3.5 text-gray-700 dark:text-gray-300 font-medium">{{ $feat }}</td>
            <td class="text-center px-4 py-3.5 {{ $free === '✗' ? 'text-gray-300 dark:text-gray-600' : 'text-gray-600 dark:text-gray-400' }}">
              {{ $free }}
            </td>
            <td class="text-center px-4 py-3.5 {{ $prem === '✗' ? 'text-gray-300 dark:text-gray-600' : 'text-green-600 dark:text-green-400 font-semibold' }}">
              {{ $prem }}
            </td>
            <td class="text-center px-4 py-3.5 {{ $plus === '✗' ? 'text-gray-300 dark:text-gray-600' : 'text-yellow-600 dark:text-yellow-400 font-semibold' }}">
              {{ $plus }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- FAQ --}}
  <div class="mt-10 grid md:grid-cols-2 gap-4">
    <h3 class="md:col-span-2 font-bold text-gray-800 dark:text-white text-lg mb-2"
      style="font-family:'Plus Jakarta Sans',sans-serif;">
      Pertanyaan Umum
    </h3>
    @foreach([
      ['Apakah ada garansi uang kembali?',  'Ya! Kami memberikan garansi uang kembali 7 hari tanpa syarat jika kamu tidak puas.'],
      ['Bagaimana cara membatalkan?',       'Kamu bisa batalkan kapan saja dari halaman pengaturan akun, tanpa biaya tambahan.'],
      ['Apakah soal diupdate?',             'Ya, tim kami rutin menambah soal-soal baru setiap minggu mengikuti kurikulum terbaru.'],
      ['Metode pembayaran apa saja?',       'Transfer bank (BCA/BNI/BRI/Mandiri), e-wallet (GoPay/OVO/DANA/ShopeePay), QRIS, dan kartu kredit/debit.'],
    ] as [$q, $a])
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700" x-data="{ open: false }">
      <button class="w-full flex items-center justify-between text-left" @click="open = !open">
        <span class="font-semibold text-gray-800 dark:text-white text-sm">{{ $q }}</span>
        <span class="text-gray-400 dark:text-gray-500 ml-3" x-text="open ? '−' : '+'"></span>
      </button>
      <div x-show="open" x-transition class="mt-3 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
        {{ $a }}
      </div>
    </div>
    @endforeach
  </div>

</div>
@endsection