@extends('layouts.app')
@section('title', 'Status Pembayaran')
@section('page-title', 'Status Pembayaran')

@section('content')
<div class="max-w-lg mx-auto py-8">
  <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm p-10 text-center">

    @if($payment->status === 'success')
    {{-- SUKSES --}}
    <div class="text-7xl mb-5 animate-bounce">🎉</div>
    <h2 class="text-2xl font-extrabold text-gray-800 dark:text-white mb-2"
      style="font-family:'Plus Jakarta Sans',sans-serif;">
      Pembayaran Berhasil!
    </h2>
    <p class="text-gray-500 dark:text-gray-400 mb-6">
      Akun <strong class="dark:text-gray-200">{{ $user->name }}</strong> kini sudah aktif sebagai
      <span class="text-blue-600 dark:text-blue-400 font-bold">
        {{ $user->subscription_status === 'premium_plus' ? 'Premium Plus' : 'Premium' }}
      </span>
    </p>

    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-5 mb-6 text-sm text-left space-y-2">
      <div class="flex justify-between">
        <span class="text-gray-500 dark:text-gray-400">Paket</span>
        <span class="font-semibold dark:text-white capitalize">{{ str_replace('_', ' ', $payment->plan) }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-500 dark:text-gray-400">Jumlah Bayar</span>
        <span class="font-semibold text-blue-600 dark:text-blue-400">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-500 dark:text-gray-400">Aktif Hingga</span>
        <span class="font-semibold dark:text-white">{{ $user->subscription_ends_at?->format('d M Y') }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-500 dark:text-gray-400">Status</span>
        <span class="text-green-600 dark:text-green-400 font-bold">✓ Aktif</span>
      </div>
    </div>

    <a href="{{ route('dashboard') }}"
      class="block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition shadow-md">
      Mulai Belajar Sekarang →
    </a>
    <a href="{{ route('ai.tutor') }}" class="block mt-3 text-sm text-blue-600 dark:text-blue-400 hover:underline">
      Atau coba AI Tutor dulu →
    </a>

    @elseif($payment->status === 'pending')
    {{-- PENDING --}}
    <div class="text-7xl mb-5">⏳</div>
    <h2 class="text-2xl font-extrabold text-gray-800 dark:text-white mb-2">Menunggu Pembayaran</h2>
    <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">
      Selesaikan pembayaran sebelum waktu habis. Akun akan diaktifkan otomatis setelah pembayaran terkonfirmasi.
    </p>

    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-5 mb-6 text-sm text-left space-y-2">
      <div class="flex justify-between">
        <span class="text-gray-500 dark:text-gray-400">Total Bayar</span>
        <span class="font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-500 dark:text-gray-400">Metode</span>
        <span class="font-semibold dark:text-white capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</span>
      </div>
    </div>

    <button onclick="window.location.reload()"
      class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition text-sm mb-3">
      🔄 Cek Status Pembayaran
    </button>
    <a href="{{ route('dashboard') }}" class="block text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
      Kembali ke Dashboard
    </a>

    @else
    {{-- GAGAL --}}
    <div class="text-7xl mb-5">❌</div>
    <h2 class="text-2xl font-extrabold text-gray-800 dark:text-white mb-2">Pembayaran Gagal</h2>
    <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">
      Maaf, pembayaran tidak berhasil. Silakan coba lagi atau gunakan metode pembayaran lain.
    </p>

    <a href="{{ route('checkout', $payment->plan) }}"
      class="block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition mb-3">
      Coba Lagi
    </a>
    <a href="{{ route('premium') }}" class="block text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
      Kembali ke Halaman Premium
    </a>
    @endif

  </div>
</div>
@endsection