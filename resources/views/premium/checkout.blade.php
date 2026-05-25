@extends('layouts.app')
@section('title', 'Checkout')
@section('page-title', 'Checkout')
@section('page-subtitle', 'Selesaikan pembayaran untuk mengaktifkan Premium')

@section('content')
<div class="max-w-4xl mx-auto"
  x-data="{
    step: 1,
    method: '',
    bank: '',
    wallet: '',
    promo: '',
    promoApplied: false,
    promoDiscount: 0,
    period: '{{ request('period', 'monthly') }}',
    basePrice: {{ $planData['price'] }},
    yearlyPrice: {{ $planData['price_year'] ?? $planData['price'] }},
    get finalPrice() {
      const p = this.period === 'yearly' ? this.yearlyPrice : this.basePrice;
      return p - this.promoDiscount;
    },
    applyPromo() {
      if (this.promo.toUpperCase() === 'SMARTKA10') {
        const p = this.period === 'yearly' ? this.yearlyPrice : this.basePrice;
        this.promoDiscount = Math.floor(p * 0.1);
        this.promoApplied  = true;
      } else {
        this.promoDiscount = 0;
        this.promoApplied  = false;
        alert('Kode promo tidak valid.');
      }
    },
    formatRp(n) {
      return 'Rp ' + n.toLocaleString('id-ID');
    }
  }">

  {{-- Stepper --}}
  <div class="flex items-center justify-center gap-2 mb-8">
    @foreach(['Pilih Periode', 'Metode Bayar', 'Konfirmasi'] as $i => $label)
    <div class="flex items-center">
      <div class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold transition-all"
        :class="{{ $i + 1 }} <= step ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'">
        <span x-show="{{ $i + 1 }} < step">✓</span>
        <span x-show="{{ $i + 1 }} >= step">{{ $i + 1 }}</span>
      </div>
      <span class="ml-2 text-xs font-medium hidden md:inline"
        :class="{{ $i + 1 }} <= step ? 'text-blue-600' : 'text-gray-400'">
        {{ $label }}
      </span>
      @if($i < 2)
      <div class="w-8 md:w-16 h-0.5 mx-2"
        :class="{{ $i + 2 }} <= step ? 'bg-blue-600' : 'bg-gray-200'"></div>
      @endif
    </div>
    @endforeach
  </div>

  <div class="grid md:grid-cols-3 gap-6">

    {{-- Form utama --}}
    <div class="md:col-span-2 space-y-4">

      {{-- STEP 1: Pilih Periode --}}
      <div x-show="step === 1" x-transition class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 dark:text-white mb-5" style="font-family:'Plus Jakarta Sans',sans-serif;">
          Pilih Periode Berlangganan
        </h3>

        <div class="grid gap-4">
          {{-- Bulanan --}}
          <div @click="period = 'monthly'"
            :class="period === 'monthly' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700'"
            class="border-2 rounded-2xl p-5 cursor-pointer transition-all">
            <div class="flex items-center justify-between">
              <div>
                <div class="font-bold text-gray-800 dark:text-white">Bulanan</div>
                <div class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">Bayar setiap bulan, batalkan kapan saja</div>
              </div>
              <div class="text-right">
                <div class="font-extrabold text-blue-600 dark:text-blue-400 text-xl">
                  Rp {{ number_format($planData['price'], 0, ',', '.') }}
                </div>
                <div class="text-gray-400 dark:text-gray-500 text-xs">per bulan</div>
              </div>
            </div>
          </div>

          {{-- Tahunan --}}
          @if(isset($planData['price_year']))
          <div @click="period = 'yearly'"
            :class="period === 'yearly' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700'"
            class="border-2 rounded-2xl p-5 cursor-pointer transition-all relative">
            <div class="absolute -top-3 right-4 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
              HEMAT 26%
            </div>
            <div class="flex items-center justify-between">
              <div>
                <div class="font-bold text-gray-800 dark:text-white">Tahunan</div>
                <div class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">
                  Hemat Rp {{ number_format($planData['price'] * 12 - $planData['price_year'], 0, ',', '.') }} dari harga normal
                </div>
              </div>
              <div class="text-right">
                <div class="font-extrabold text-blue-600 dark:text-blue-400 text-xl">
                  Rp {{ number_format($planData['price_year'], 0, ',', '.') }}
                </div>
                <div class="text-gray-400 dark:text-gray-500 text-xs">per tahun</div>
              </div>
            </div>
          </div>
          @endif
        </div>

        <button @click="step = 2"
          class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition text-sm">
          Lanjut — Pilih Metode Bayar →
        </button>
      </div>

      {{-- STEP 2: Metode Pembayaran --}}
      <div x-show="step === 2" x-transition class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 dark:text-white mb-5" style="font-family:'Plus Jakarta Sans',sans-serif;">
          Pilih Metode Pembayaran
        </h3>

        <div class="space-y-3">
          {{-- Transfer Bank --}}
          <div>
            <div @click="method = 'bank_transfer'"
              :class="method === 'bank_transfer' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700'"
              class="border-2 rounded-xl p-4 cursor-pointer transition-all">
              <div class="flex items-center gap-3">
                <span class="text-2xl">🏦</span>
                <div class="flex-1">
                  <div class="font-semibold text-gray-800 dark:text-white text-sm">Transfer Bank</div>
                  <div class="text-gray-400 dark:text-gray-500 text-xs">Virtual Account BCA, BNI, BRI, Mandiri</div>
                </div>
                <div class="w-5 h-5 rounded-full border-2 transition-all"
                  :class="method === 'bank_transfer' ? 'border-blue-600 bg-blue-600' : 'border-gray-300 dark:border-gray-600'">
                </div>
              </div>

              {{-- Pilih bank --}}
              <div x-show="method === 'bank_transfer'" x-transition class="mt-4 flex flex-wrap gap-2">
                @foreach(['BCA', 'BNI', 'BRI', 'Mandiri'] as $b)
                <button type="button" @click.stop="bank = '{{ $b }}'"
                  :class="bank === '{{ $b }}' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 font-semibold' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400'"
                  class="border-2 px-4 py-2 rounded-xl text-sm transition">
                  {{ $b }}
                </button>
                @endforeach
              </div>
            </div>
          </div>

          {{-- E-Wallet --}}
          <div>
            <div @click="method = 'ewallet'"
              :class="method === 'ewallet' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700'"
              class="border-2 rounded-xl p-4 cursor-pointer transition-all">
              <div class="flex items-center gap-3">
                <span class="text-2xl">📱</span>
                <div class="flex-1">
                  <div class="font-semibold text-gray-800 dark:text-white text-sm">E-Wallet</div>
                  <div class="text-gray-400 dark:text-gray-500 text-xs">GoPay, OVO, DANA, ShopeePay</div>
                </div>
                <div class="w-5 h-5 rounded-full border-2 transition-all"
                  :class="method === 'ewallet' ? 'border-blue-600 bg-blue-600' : 'border-gray-300 dark:border-gray-600'">
                </div>
              </div>

              <div x-show="method === 'ewallet'" x-transition class="mt-4 flex flex-wrap gap-2">
                @foreach(['GoPay', 'OVO', 'DANA', 'ShopeePay'] as $w)
                <button type="button" @click.stop="wallet = '{{ $w }}'"
                  :class="wallet === '{{ $w }}' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 font-semibold' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400'"
                  class="border-2 px-4 py-2 rounded-xl text-sm transition">
                  {{ $w }}
                </button>
                @endforeach
              </div>
            </div>
          </div>

          {{-- QRIS --}}
          <div @click="method = 'qris'"
            :class="method === 'qris' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700'"
            class="border-2 rounded-xl p-4 cursor-pointer transition-all">
            <div class="flex items-center gap-3">
              <span class="text-2xl">📷</span>
              <div class="flex-1">
                <div class="font-semibold text-gray-800 dark:text-white text-sm">QRIS</div>
                <div class="text-gray-400 dark:text-gray-500 text-xs">Scan QR dari semua aplikasi dompet digital</div>
              </div>
              <div class="w-5 h-5 rounded-full border-2 transition-all"
                :class="method === 'qris' ? 'border-blue-600 bg-blue-600' : 'border-gray-300 dark:border-gray-600'">
              </div>
            </div>
          </div>

          {{-- Kartu Kredit --}}
          <div @click="method = 'credit_card'"
            :class="method === 'credit_card' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700'"
            class="border-2 rounded-xl p-4 cursor-pointer transition-all">
            <div class="flex items-center gap-3">
              <span class="text-2xl">💳</span>
              <div class="flex-1">
                <div class="font-semibold text-gray-800 dark:text-white text-sm">Kartu Kredit / Debit</div>
                <div class="text-gray-400 dark:text-gray-500 text-xs">Visa, Mastercard, semua bank</div>
              </div>
              <div class="w-5 h-5 rounded-full border-2 transition-all"
                :class="method === 'credit_card' ? 'border-blue-600 bg-blue-600' : 'border-gray-300 dark:border-gray-600'">
              </div>
            </div>
          </div>
        </div>

        <div class="flex gap-3 mt-6">
          <button @click="step = 1"
            class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 font-semibold py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm">
            ← Kembali
          </button>
          <button @click="method ? step = 3 : null"
            :disabled="!method"
            class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 dark:disabled:bg-gray-700 text-white font-semibold py-3 rounded-xl transition text-sm">
            Lanjut — Konfirmasi →
          </button>
        </div>
      </div>

      {{-- STEP 3: Konfirmasi --}}
      <div x-show="step === 3" x-transition class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 dark:text-white mb-5" style="font-family:'Plus Jakarta Sans',sans-serif;">
          Konfirmasi Pesanan
        </h3>

        {{-- Detail --}}
        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 space-y-3 mb-5 text-sm">
          <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400">Paket</span>
            <span class="font-semibold dark:text-white">{{ $planData['name'] }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400">Periode</span>
            <span class="font-semibold dark:text-white" x-text="period === 'yearly' ? 'Tahunan' : 'Bulanan'"></span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400">Metode</span>
            <span class="font-semibold dark:text-white" x-text="method === 'bank_transfer' ? '🏦 Transfer Bank (' + bank + ')' : method === 'ewallet' ? '📱 ' + wallet : method === 'qris' ? '📷 QRIS' : '💳 Kartu Kredit'"></span>
          </div>
          <div x-show="promoApplied" class="flex justify-between text-green-600 dark:text-green-400">
            <span>Diskon Promo (SMARTKA10)</span>
            <span class="font-semibold" x-text="'- ' + formatRp(promoDiscount)"></span>
          </div>
          <div class="border-t border-gray-200 dark:border-gray-600 pt-3 flex justify-between">
            <span class="font-bold text-gray-800 dark:text-white">Total Bayar</span>
            <span class="font-extrabold text-blue-600 dark:text-blue-400 text-lg" x-text="formatRp(finalPrice)"></span>
          </div>
        </div>

        {{-- Kode promo --}}
        <div class="flex gap-2 mb-5">
          <input type="text" x-model="promo" placeholder="Kode promo (cth: SMARTKA10)"
            class="flex-1 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <button @click="applyPromo()"
            class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold px-4 py-2.5 rounded-xl text-sm transition">
            Pakai
          </button>
        </div>

        {{-- Form submit --}}
        <form method="POST" action="{{ route('payment.process') }}">
          @csrf
          <input type="hidden" name="plan"   value="{{ $plan }}">
          <input type="hidden" name="promo_code"      x-bind:value="promo">
          <input type="hidden" name="payment_method"  x-bind:value="method === 'bank_transfer' ? 'bank_' + bank.toLowerCase() : method === 'ewallet' ? 'ewallet_' + wallet.toLowerCase() : method">
          <input type="hidden" name="period"          x-bind:value="period">

          <div class="flex gap-3">
            <button type="button" @click="step = 2"
              class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 font-semibold py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm">
              ← Kembali
            </button>
            <button type="submit"
              class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition text-sm shadow-md">
              Bayar Sekarang 🔒
            </button>
          </div>
        </form>

        <p class="text-center text-gray-400 dark:text-gray-500 text-xs mt-3">
          🛡️ Transaksi dilindungi enkripsi SSL · Garansi 7 hari
        </p>
      </div>
    </div>

    {{-- Order Summary --}}
    <div class="space-y-4">
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 sticky top-24">
        <h4 class="font-bold text-gray-800 dark:text-white mb-4 text-sm">Ringkasan Pesanan</h4>

        {{-- Paket info --}}
        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
          <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">⭐</div>
          <div>
            <div class="font-semibold text-gray-800 dark:text-white text-sm">{{ $planData['name'] }}</div>
            <div class="text-gray-400 dark:text-gray-500 text-xs" x-text="period === 'yearly' ? 'Tahunan' : 'Bulanan'"></div>
          </div>
        </div>

        {{-- Harga --}}
        <div class="space-y-2 text-sm mb-4">
          <div class="flex justify-between text-gray-600 dark:text-gray-400">
            <span>Harga</span>
            <span x-text="period === 'yearly' ? formatRp(yearlyPrice) : formatRp(basePrice)"></span>
          </div>
          <div x-show="promoApplied" class="flex justify-between text-green-600 dark:text-green-400">
            <span>Diskon</span>
            <span x-text="'- ' + formatRp(promoDiscount)"></span>
          </div>
          <div class="flex justify-between font-bold text-gray-800 dark:text-white pt-2 border-t border-gray-100 dark:border-gray-700">
            <span>Total</span>
            <span class="text-blue-600 dark:text-blue-400" x-text="formatRp(finalPrice)"></span>
          </div>
        </div>

        {{-- Benefits --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-3 space-y-2">
          @foreach(['Aktif langsung setelah bayar', 'Garansi uang kembali 7 hari', 'Batalkan kapan saja'] as $b)
          <div class="flex items-center gap-2 text-xs text-blue-700 dark:text-blue-300">
            <span class="text-green-500 dark:text-green-400">✓</span> {{ $b }}
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endsection