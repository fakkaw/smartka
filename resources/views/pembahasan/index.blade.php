@extends('layouts.app')
@section('title', 'Pembahasan & Materi')
@section('page-title', 'Pembahasan & Silabus')
@section('page-subtitle', 'Pelajari modul pembelajaran lengkap berdasarkan bab & topik pelajaranmu.')

@section('content')
<div class="space-y-6" x-data="{
    search: '',
    expanded: null,
    toggle(id) {
        this.expanded = this.expanded === id ? null : id;
    },
    matchesSearch(subjectName, subjectDesc) {
        if (!this.search) return true;
        const q = this.search.toLowerCase();
        return subjectName.toLowerCase().includes(q) || (subjectDesc && subjectDesc.toLowerCase().includes(q));
    }
}">

  {{-- ── CARD SEARCH & BANNER ────────────────────────── --}}
  <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-900 rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
    <div class="space-y-2">
      <div class="flex items-center gap-2">
        <span class="text-2xl">📖</span>
        <h2 class="text-xl font-bold font-sans">Pusat Belajar SMARTKA</h2>
      </div>
      <p class="text-blue-100 text-sm max-w-xl">
        Di sini kamu bisa mempelajari bab secara menyeluruh. Klik mata pelajaran di bawah untuk melihat daftar bab dan materi yang diujikan!
      </p>
    </div>
    
    {{-- Search Bar --}}
    <div class="relative w-full md:w-80">
      <input type="text" x-model="search" placeholder="Cari mata pelajaran atau topik..."
        class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-xl px-4 py-3 pl-11 text-white placeholder-white/70 text-sm focus:outline-none focus:ring-2 focus:ring-white transition">
      <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg opacity-85">🔍</span>
      <button x-show="search.length > 0" @click="search = ''" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white text-xs">✕</button>
    </div>
  </div>

  @if($subjects->isEmpty())
    {{-- ── EMPTY STATE SYSTEM ────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-12 text-center max-w-xl mx-auto">
      <div class="text-7xl mb-6 select-none animate-pulse">📚</div>
      <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">Mata Pelajaran Belum Tersedia</h3>
      <p class="text-gray-500 dark:text-gray-400 text-sm max-w-sm mx-auto mb-6">
        Administrator belum merilis mata pelajaran untuk tingkat jenjang kelas {{ $classLevel }} saat ini.
      </p>
    </div>
  @else
    {{-- ── ACCORDION LIST ────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-4">
      
      @php $visibleCount = 0; @endphp
      @foreach($subjects as $subject)
        @php
          $subjectColor = $subject->color_hex ?? '#1a56db';
          $topicCount = $subject->topics->count();
        @endphp
        <div x-show="matchesSearch('{{ addslashes($subject->name) }}', '{{ addslashes($subject->description) }}')"
          class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden transition-all duration-300 hover:border-gray-200 dark:hover:border-gray-600"
          :class="expanded === {{ $subject->id }} ? 'ring-1 ring-blue-500/20 shadow-md' : ''"
          x-transition>

          {{-- Header Accordion --}}
          <div @click="toggle({{ $subject->id }})"
            class="p-5 flex items-center justify-between cursor-pointer select-none hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors">
            <div class="flex items-center gap-4">
              {{-- Icon box with dynamic color --}}
              <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0"
                style="background-color: {{ $subjectColor }}15; color: {{ $subjectColor }}">
                @php
                  $icon = match($subject->icon) {
                    'calculator' => '🧮',
                    'flask' => '🧪',
                    'book-open' => '📖',
                    'globe' => '🌐',
                    'languages' => '🗣️',
                    'brain' => '🧠',
                    default => '📚'
                  };
                @endphp
                {{ $icon }}
              </div>
              <div>
                <h3 class="font-bold text-gray-800 dark:text-gray-100 text-base leading-tight">{{ $subject->name }}</h3>
                <span class="text-xs text-gray-400 dark:text-gray-400 font-medium block mt-1">
                  {{ $topicCount }} Bab Pelajaran • {{ $subject->questions_count }} Bank Soal
                </span>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <span x-show="expanded !== {{ $subject->id }}" class="text-xs text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 font-semibold px-2.5 py-1 rounded-lg">Buka Bab ▼</span>
              <span x-show="expanded === {{ $subject->id }}" class="text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 font-semibold px-2.5 py-1 rounded-lg">Tutup ▲</span>
            </div>
          </div>

          {{-- Expandable Content (Topics) --}}
          <div x-show="expanded === {{ $subject->id }}" x-collapse
            class="border-t border-gray-50 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-800/30 p-6 space-y-4">
            
            @if($subject->description)
              <div class="bg-blue-50/30 dark:bg-blue-900/20 border border-blue-100/30 dark:border-blue-800/30 rounded-xl p-4 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                ℹ️ **Ringkasan Materi**: {{ $subject->description }}
              </div>
            @endif

            @if($subject->topics->isEmpty())
              <div class="text-center py-6 text-gray-400 text-sm">
                📭 Belum ada materi bab terperinci untuk mata pelajaran ini.
              </div>
            @else
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($subject->topics as $topic)
                  <div class="bg-white dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-600 p-4 shadow-sm hover:border-blue-100 dark:hover:border-blue-800/50 transition-all flex flex-col justify-between hover:scale-[1.01]">
                    <div>
                      <div class="flex justify-between items-start gap-2 mb-2">
                        <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-bold px-2 py-0.5 rounded-full">
                          BAB {{ $topic->order_number }}
                        </span>
                      </div>
                      <h4 class="font-bold text-gray-800 dark:text-gray-100 text-sm mb-1">{{ $topic->name }}</h4>
                      <p class="text-xs text-gray-400 line-clamp-2">{{ $topic->description ?? 'Pelajari kisi-kisi dan pembahasan lengkap materi pada bab pembelajaran ini.' }}</p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-50 dark:border-gray-600 flex justify-between items-center gap-2">
                      <span class="text-[10px] text-gray-400 font-medium">Topik Terstruktur</span>
                      <a href="{{ route('latihan.index') }}" 
                        class="text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-1">
                        <span>Latihan Bab →</span>
                      </a>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>

        </div>
      @endforeach

      {{-- Search No Match State --}}
      <div x-show="search.length > 0 && !document.querySelectorAll('.bg-white[x-show*=\'matchesSearch\']').length && !document.querySelectorAll('.dark\\:bg-gray-800[x-show*=\'matchesSearch\']').length"
        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-12 text-center max-w-xl mx-auto flex flex-col items-center">
        <div class="text-6xl mb-4 select-none">🔍</div>
        <h4 class="font-bold text-gray-800 dark:text-gray-100 mb-1">Mata Pelajaran Tidak Ditemukan</h4>
        <p class="text-gray-500 dark:text-gray-400 text-xs max-w-xs mb-4">
          Tidak ada hasil pencarian yang cocok dengan kata kunci **"<span x-text="search"></span>"**.
        </p>
        <button @click="search = ''" class="text-xs font-bold text-blue-600 hover:underline">Hapus Pencarian</button>
      </div>

    </div>
  @endif

</div>
@endsection
