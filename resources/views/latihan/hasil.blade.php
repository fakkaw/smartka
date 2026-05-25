@extends('layouts.app')
@section('title', 'Hasil Latihan: ' . $package->name)

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2" style="font-family:'Plus Jakarta Sans',sans-serif">
        Hasil Latihan: {{ $package->name }}
    </h1>
    <p class="text-gray-500 dark:text-gray-400">Selesai dikerjakan pada {{ $session->finished_at->format('d M Y, H:i') }}</p>
</div>

<!-- TOP SECTION: SCORE & AI FEEDBACK -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Score Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 flex flex-col justify-center items-center">
        <h3 class="text-gray-500 dark:text-gray-400 font-semibold mb-4">Skor Akhir</h3>
        
        <div class="relative w-32 h-32 mb-6">
            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                <path class="text-gray-100 dark:text-gray-700" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                <path class="{{ $result->total_score >= 75 ? 'text-green-500' : ($result->total_score >= 50 ? 'text-yellow-400' : 'text-red-500') }}" 
                    stroke-dasharray="{{ $result->total_score }}, 100" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" 
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-3xl font-extrabold text-gray-800 dark:text-gray-100" style="font-family:'Plus Jakarta Sans',sans-serif">
                    {{ $result->total_score }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 w-full text-center divide-x divide-gray-100 dark:divide-gray-700">
            <div>
                <p class="text-2xl font-bold text-green-600 mb-1">{{ $result->correct_count }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Benar</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-red-500 mb-1">{{ $result->wrong_count }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Salah</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-400 dark:text-gray-500 mb-1">{{ $result->empty_count }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Kosong</p>
            </div>
        </div>
    </div>

    <!-- AI Tutor Feedback -->
    <div class="lg:col-span-2 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl shadow-md p-6 lg:p-8 text-white relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-32 h-32 bg-blue-400 opacity-20 rounded-full blur-2xl"></div>
        
        <div class="relative z-10 flex items-start gap-4">
            <div class="bg-white/20 p-3 rounded-xl backdrop-blur-sm">
                <span class="text-3xl">🤖</span>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold mb-3" style="font-family:'Plus Jakarta Sans',sans-serif">Evaluasi AI Tutor</h3>
                <div class="prose prose-invert max-w-none text-sm md:text-base leading-relaxed">
                    {!! nl2br(e($aiFeedback)) !!}
                </div>
                
                @if(count($result->weakness_topics ?? []) > 0)
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="text-xs text-blue-200">Fokus perbaikan:</span>
                    @foreach($result->weakness_topics as $wt)
                        <span class="bg-white/10 text-white text-xs px-2 py-1 rounded-md">{{ $wt }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- REVIEW SOAL -->
<h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4" style="font-family:'Plus Jakarta Sans',sans-serif">Pembahasan Soal</h2>

<div class="space-y-6">
    @foreach($questions as $index => $question)
        @php
            $ans = $answers->get($question->id);
            $isCorrect = $ans ? $ans->is_correct : false;
            $isEmpty = !$ans || $ans->selected_answer === null || $ans->selected_answer === '';
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-2xl border {{ $isCorrect ? 'border-green-200 dark:border-green-800' : ($isEmpty ? 'border-gray-200 dark:border-gray-700' : 'border-red-200 dark:border-red-800') }} shadow-sm p-6 relative overflow-hidden">
            
            <!-- Side accent line -->
            <div class="absolute left-0 top-0 bottom-0 w-1 {{ $isCorrect ? 'bg-green-500 dark:bg-green-600' : ($isEmpty ? 'bg-gray-300 dark:bg-gray-600' : 'bg-red-500 dark:bg-red-600') }}"></div>

            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold w-8 h-8 flex items-center justify-center rounded-lg">
                        {{ $index + 1 }}
                    </span>
                    <span class="text-xs font-semibold px-2 py-1 rounded-md {{ $question->type === 'multiple_choice' ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400' : 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400' }}">
                        {{ $question->type === 'multiple_choice' ? 'Pilihan Ganda' : 'Essay' }}
                    </span>
                    <span class="text-xs font-semibold px-2 py-1 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">
                        Topik: {{ $question->topic->name }}
                    </span>
                </div>
                
                @if($isCorrect)
                    <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-500 font-bold px-3 py-1 rounded-full text-xs flex items-center gap-1">✓ BENAR</span>
                @elseif($isEmpty)
                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-bold px-3 py-1 rounded-full text-xs flex items-center gap-1">KOSONG</span>
                @else
                    <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-500 font-bold px-3 py-1 rounded-full text-xs flex items-center gap-1">✕ SALAH</span>
                @endif
            </div>

            <!-- Pertanyaan -->
            <div class="prose max-w-none text-gray-800 dark:text-gray-100 mb-6">
                {!! $question->question_text !!}
            </div>
            
            @if($question->question_image)
                <img src="{{ $question->question_image }}" class="mb-6 rounded-lg max-h-48 border">
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 dark:bg-gray-700/50 p-5 rounded-xl border border-gray-100 dark:border-gray-700 mb-6">
                <!-- Jawaban Kamu -->
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Jawaban Kamu:</h4>
                    @if($isEmpty)
                        <p class="text-gray-400 italic">Tidak dijawab</p>
                    @else
                        @if($question->type === 'multiple_choice')
                            <div class="flex items-center gap-2">
                                <span class="uppercase font-bold w-6 h-6 flex items-center justify-center rounded bg-white dark:bg-gray-800 border shadow-sm {{ $isCorrect ? 'text-green-600 dark:text-green-400 border-green-200 dark:border-green-800' : 'text-red-600 dark:text-red-400 border-red-200 dark:border-red-800' }}">
                                    {{ $ans->selected_answer }}
                                </span>
                                <span class="text-gray-700 dark:text-gray-300">{!! $question->{'option_'.$ans->selected_answer} !!}</span>
                            </div>
                        @else
                            <p class="text-gray-700 dark:text-gray-300 font-medium whitespace-pre-wrap {{ $isCorrect ? 'text-green-700 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ $ans->selected_answer }}</p>
                        @endif
                    @endif
                </div>

                <!-- Kunci Jawaban -->
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Jawaban Benar:</h4>
                    @if($question->type === 'multiple_choice')
                        <div class="flex items-center gap-2">
                            <span class="uppercase font-bold w-6 h-6 flex items-center justify-center rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800 shadow-sm">
                                {{ $question->correct_answer }}
                            </span>
                            <span class="text-gray-700 dark:text-gray-300">{!! $question->{'option_'.$question->correct_answer} !!}</span>
                        </div>
                    @else
                        <p class="text-gray-700 dark:text-gray-300 font-medium whitespace-pre-wrap text-green-700 dark:text-green-400">{{ $question->correct_answer }}</p>
                    @endif
                </div>
            </div>

            <!-- Penjelasan -->
            @if($question->explanation_text)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-xl p-5">
                <h4 class="text-sm font-bold text-blue-800 dark:text-blue-400 mb-2 flex items-center gap-2">
                    <span>💡</span> Pembahasan
                </h4>
                <div class="prose max-w-none text-blue-900 dark:text-blue-300 text-sm">
                    {!! $question->explanation_text !!}
                </div>
            </div>
            @endif

        </div>
    @endforeach
</div>

<div class="mt-10 text-center">
    <a href="{{ route('latihan.index') }}" class="inline-flex items-center justify-center px-8 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm">
        Kembali ke Daftar Latihan
    </a>
</div>

@endsection
