@extends('layouts.app')
@section('title', 'Laporan Belajar')

@section('content')
<div class="space-y-6" x-data="{
    period: '{{ $period }}',
    changePeriod(val) {
        window.location.href = '{{ route('laporan.index') }}?period=' + val;
    }
}">

  {{-- ── FILTER PERIODE WAKTU ────────────────────────── --}}
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 shadow-sm">
    <div>
      <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Periode Laporan</h2>
      <p class="text-xs text-gray-400 dark:text-gray-400">Pilih periode analisis untuk melihat data perkembangan belajarmu.</p>
    </div>
    <div class="relative w-full sm:w-auto">
      <select x-model="period" @change="changePeriod($event.target.value)"
        class="w-full sm:w-48 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-gray-700 dark:text-gray-200 cursor-pointer">
        <option value="all">📊 Semua Waktu</option>
        <option value="week">📅 7 Hari Terakhir</option>
        <option value="month">📆 30 Hari Terakhir</option>
        <option value="semester">🏫 Semester Terakhir</option>
      </select>
      <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400 text-xs">▼</div>
    </div>
  </div>

  @if($totalSessions === 0)
    {{-- ── EMPTY STATE ──────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-12 shadow-sm text-center max-w-xl mx-auto flex flex-col items-center">
      <div class="text-7xl mb-6 select-none animate-bounce">📈</div>
      <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">Belum Ada Riwayat Belajar</h3>
      <p class="text-gray-500 dark:text-gray-400 text-sm max-w-sm mb-8">
        Kamu belum menyelesaikan latihan soal atau try out pada periode ini. Mulai kerjakan latihan soal pertama kamu sekarang!
      </p>
      <a href="{{ route('latihan.index') }}" 
        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition shadow-sm text-sm hover:scale-[1.02]">
        <span>📝 Mulai Latihan Soal</span>
      </a>
    </div>
  @else
    {{-- ── METRIC CARDS ────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      {{-- Card 1: Rata-rata Skor --}}
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 shadow-sm flex items-center gap-4 hover:scale-[1.01] transition-transform duration-200">
        <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">🎯</div>
        <div>
          <span class="text-xs text-gray-400 dark:text-gray-400 font-medium block">Rata-rata Skor</span>
          <span class="text-xl font-extrabold text-blue-600 dark:text-blue-400 block leading-tight mt-1">{{ $avgScore }}%</span>
        </div>
      </div>

      {{-- Card 2: Latihan Selesai --}}
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 shadow-sm flex items-center gap-4 hover:scale-[1.01] transition-transform duration-200">
        <div class="w-12 h-12 bg-green-50 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">📝</div>
        <div>
          <span class="text-xs text-gray-400 dark:text-gray-400 font-medium block">Latihan Selesai</span>
          <span class="text-xl font-extrabold text-green-600 dark:text-green-400 block leading-tight mt-1">{{ $totalSessions }} Paket</span>
        </div>
      </div>

      {{-- Card 3: Waktu Belajar --}}
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 shadow-sm flex items-center gap-4 hover:scale-[1.01] transition-transform duration-200">
        <div class="w-12 h-12 bg-yellow-50 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">⏱️</div>
        <div>
          <span class="text-xs text-gray-400 dark:text-gray-400 font-medium block">Waktu Belajar</span>
          <span class="text-xl font-extrabold text-yellow-600 dark:text-yellow-400 block leading-tight mt-1">{{ $timeSpentLabel }}</span>
        </div>
      </div>

      {{-- Card 4: Rasio Ketepatan --}}
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 shadow-sm flex items-center gap-4 hover:scale-[1.01] transition-transform duration-200">
        <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">📊</div>
        <div>
          <span class="text-xs text-gray-400 dark:text-gray-400 font-medium block">Total Soal</span>
          <span class="text-xl font-extrabold text-purple-600 dark:text-purple-400 block leading-tight mt-1">{{ $totalCorrect + $totalWrong + $totalEmpty }} Soal</span>
        </div>
      </div>
    </div>

    {{-- ── GRAPHICS SECTION ────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      {{-- Line Chart: Perkembangan Nilai --}}
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm lg:col-span-2 flex flex-col"
        x-data="{
            chartInstance: null,
            init() {
                this.initChart();
            },
            initChart() {
                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }
                const ctx = this.$refs.canvas.getContext('2d');
                this.chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($trendLabels),
                        datasets: [{
                            label: 'Skor Latihan',
                            data: @json($trendScores),
                            borderColor: '#1a56db',
                            backgroundColor: 'rgba(26, 86, 219, 0.05)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 3,
                            pointBackgroundColor: '#1a56db',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                min: 0,
                                max: 100,
                                ticks: { stepSize: 20 }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        }">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="font-bold text-gray-800 dark:text-gray-100 text-base">Grafik Perkembangan Nilai</h3>
            <p class="text-xs text-gray-400">Grafik tren skor dari latihan-latihan terakhirmu.</p>
          </div>
          <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold">TREN SKOR</span>
        </div>
        <div class="relative flex-1 min-h-[260px] h-64">
          <canvas x-ref="canvas"></canvas>
        </div>
      </div>

      {{-- Doughnut Chart: Ketepatan Jawaban --}}
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm flex flex-col"
        x-data="{
            chartInstance: null,
            init() {
                this.initChart();
            },
            initChart() {
                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }
                const ctx = this.$refs.canvas.getContext('2d');
                this.chartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Benar', 'Salah', 'Kosong'],
                        datasets: [{
                            data: [{{ $totalCorrect }}, {{ $totalWrong }}, {{ $totalEmpty }}],
                            backgroundColor: ['#0e9f6e', '#ef4444', '#9ca3af'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                        }
                    }
                });
            }
        }">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="font-bold text-gray-800 dark:text-gray-100 text-base">Rasio Jawaban</h3>
            <p class="text-xs text-gray-400">Komposisi akurasi jawaban Anda.</p>
          </div>
          <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">AKURASI</span>
        </div>
        <div class="relative flex-1 min-h-[220px] h-56">
          <canvas x-ref="canvas"></canvas>
        </div>
      </div>
    </div>

    {{-- ── SUBJECT STRENGTHS & RECOMMENDED AI TOPICS ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      {{-- Subject Strengths --}}
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm">
        <h3 class="font-bold text-gray-800 dark:text-gray-100 text-base mb-1">Performa Per Mata Pelajaran</h3>
        <p class="text-xs text-gray-400 mb-6">Skor rata-rata performamu di tiap mata pelajaran.</p>

        @if(empty($subjectsList))
          <div class="text-center py-10 text-gray-400 text-sm">
            💡 Kerjakan soal materi spesifik untuk melacak nilai mata pelajaran.
          </div>
        @else
          <div class="space-y-4">
            @foreach($subjectsList as $index => $subject)
              @php
                $score = $subjectAverages[$index];
                $color = $score >= 80 ? 'bg-green-500' : ($score >= 60 ? 'bg-blue-500' : 'bg-yellow-500');
                $lightColor = $score >= 80 ? 'bg-green-50' : ($score >= 60 ? 'bg-blue-50' : 'bg-yellow-50');
                $textColor = $score >= 80 ? 'text-green-700' : ($score >= 60 ? 'text-blue-700' : 'text-yellow-700');
              @endphp
              <div class="space-y-1.5">
                <div class="flex justify-between text-sm font-semibold text-gray-700 dark:text-gray-300">
                  <span>{{ $subject }}</span>
                  <span class="{{ $textColor }} dark:opacity-80">{{ $score }}%</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-gray-700 h-2.5 rounded-full overflow-hidden">
                  <div class="h-full {{ $color }} rounded-full transition-all duration-500" style="width: {{ $score }}%"></div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      {{-- Rekomendasi Topik Lemah (AI Smart Tutor) --}}
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm flex flex-col justify-between">
        <div>
          <div class="flex items-center gap-2 mb-2">
            <span class="text-xl">🤖</span>
            <h3 class="font-bold text-gray-800 dark:text-gray-100 text-base">Rekomendasi Tutor AI</h3>
          </div>
          <p class="text-xs text-gray-400 mb-6">Analisis topik lemah Anda dan saran belajar terarah dari Smartka AI.</p>

          @if(empty($weakTopics))
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 rounded-2xl p-5 text-center text-sm text-blue-700 dark:text-blue-300">
              🎉 **Luar biasa!** Belum terdeteksi topik yang lemah. Pertahankan prestasimu dan terus tantang dirimu dengan materi baru!
            </div>
          @else
            <div class="space-y-3">
              <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2">Topik yang perlu kamu tingkatkan:</p>
              <div class="flex flex-wrap gap-2">
                @foreach($weakTopics as $topic)
                  <span class="bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-semibold px-3 py-1.5 rounded-xl border border-red-100 dark:border-red-800 flex items-center gap-1.5 hover:scale-[1.02] transition-transform">
                    ⚠️ {{ $topic }}
                  </span>
                @endforeach
              </div>
            </div>
          @endif
        </div>

        <div class="mt-6 pt-4 border-t border-gray-50 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
          <p class="text-xs text-gray-400 text-center sm:text-left">Tanyakan langsung pembahasannya pada tutor pintar Anda!</p>
          <a href="{{ route('ai.tutor') }}"
            class="w-full sm:w-auto text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl transition text-sm flex justify-center items-center gap-2">
            <span>💬 Diskusi dengan AI Tutor</span>
          </a>
        </div>
      </div>
    </div>
  @endif

</div>

{{-- Script Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
