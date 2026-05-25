@extends('layouts.app')
@section('title', 'Mengerjakan Latihan: ' . $package->name)

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100" style="font-family:'Plus Jakarta Sans',sans-serif">
                {{ $package->name }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Kerjakan dengan jujur dan teliti!</p>
        </div>
        @if($package->duration_minutes > 0)
        <div class="bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-4 py-2 rounded-xl font-bold border border-blue-200 dark:border-blue-800">
            Sisa Waktu: <span id="timerDisplay">--:--</span>
        </div>
        @else
        <div class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-4 py-2 rounded-xl font-semibold border border-gray-100 dark:border-gray-700">
            Tanpa Waktu
        </div>
        @endif
    </div>
</div>

<div x-data="latihanSoal()" class="grid grid-cols-1 lg:grid-cols-4 gap-6 relative">
    
    <!-- Main Content: Active Question -->
    <div class="lg:col-span-3">
        <template x-for="(question, index) in questions" :key="question.id">
            <div x-show="currentIndex === index" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 lg:p-8">
                
                <div class="flex justify-between items-start mb-6">
                    <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 text-xs font-bold px-3 py-1 rounded-full">
                        Soal No. <span x-text="index + 1"></span>
                    </span>
                    <span class="text-xs font-semibold text-gray-400 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded border dark:border-gray-600">
                        <span x-text="question.type === 'multiple_choice' ? 'Pilihan Ganda' : 'Isian Singkat / Essay'"></span>
                    </span>
                </div>

                <!-- Question Text -->
                <div class="prose max-w-none text-gray-800 dark:text-gray-100 mb-8" x-html="question.question_text"></div>

                <!-- Question Image (if any) -->
                <template x-if="question.question_image">
                    <img :src="question.question_image" alt="Soal Image" class="mb-8 rounded-xl max-h-64 object-contain border">
                </template>

                <!-- Options / Input Area -->
                <div class="space-y-4">
                    <!-- Multiple Choice -->
                    <template x-if="question.type === 'multiple_choice'">
                        <div class="space-y-3">
                            <template x-for="optionKey in ['a', 'b', 'c', 'd', 'e']" :key="optionKey">
                                <label x-show="question['option_' + optionKey]" 
                                    class="flex items-center p-4 border rounded-xl cursor-pointer transition-all"
                                    :class="getAnswer(question.id) === optionKey ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 dark:border-blue-500' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                                    
                                    <input type="radio" :name="'question_' + question.id" :value="optionKey" class="w-4 h-4 text-blue-600 border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-blue-500"
                                        :checked="getAnswer(question.id) === optionKey"
                                        @change="saveAnswer(question.id, optionKey)">
                                    
                                    <span class="ml-3 font-semibold text-gray-700 dark:text-gray-300 uppercase" x-text="optionKey + '.'"></span>
                                    <span class="ml-2 text-gray-600 dark:text-gray-400" x-html="question['option_' + optionKey]"></span>
                                </label>
                            </template>
                        </div>
                    </template>

                    <!-- Essay / Short Answer -->
                    <template x-if="question.type === 'short_answer'">
                        <div>
                            <textarea 
                                class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-xl p-4 focus:ring-2 focus:ring-blue-500 min-h-[150px] transition-all"
                                placeholder="Ketik jawabanmu di sini..."
                                :value="getAnswer(question.id)"
                                @change="saveAnswer(question.id, $event.target.value)"
                            ></textarea>
                            <p class="text-xs text-gray-400 mt-2">Jawaban akan otomatis tersimpan saat kamu berpindah soal atau klik di luar kotak teks.</p>
                        </div>
                    </template>
                </div>

                <!-- Footer Navigation -->
                <div class="mt-10 flex justify-between items-center pt-6 border-t border-gray-100 dark:border-gray-700">
                    <button @click="prevQuestion" :disabled="currentIndex === 0" 
                        class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 transition-all font-medium">
                        &larr; Sebelumnya
                    </button>
                    
                    <span x-show="saveStatus" class="text-sm font-semibold text-green-600 flex items-center gap-1 transition-opacity" x-transition>
                        <span>✓</span> Tersimpan
                    </span>

                    <button @click="nextQuestion" x-show="currentIndex < questions.length - 1" 
                        class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition-all font-medium">
                        Selanjutnya &rarr;
                    </button>
                    
                    <button x-show="currentIndex === questions.length - 1" @click="finishTest" 
                        class="px-6 py-2.5 rounded-xl bg-green-600 text-white hover:bg-green-700 transition-all font-bold shadow-sm">
                        Selesai
                    </button>
                </div>

            </div>
        </template>
    </div>

    <!-- Sidebar: Navigasi Soal -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 sticky top-24">
            <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4" style="font-family:'Plus Jakarta Sans',sans-serif">Navigasi Soal</h3>
            
            <div class="grid grid-cols-5 gap-2 mb-6">
                <template x-for="(question, index) in questions" :key="question.id">
                    <button @click="goToQuestion(index)"
                        class="w-full aspect-square flex items-center justify-center rounded-lg font-bold text-sm transition-all"
                        :class="{
                            'bg-blue-600 text-white shadow-md': currentIndex === index,
                            'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-500 border border-green-200 dark:border-green-800': currentIndex !== index && getAnswer(question.id) !== null && getAnswer(question.id) !== '',
                            'bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600': currentIndex !== index && (getAnswer(question.id) === null || getAnswer(question.id) === '')
                        }">
                        <span x-text="index + 1"></span>
                    </button>
                </template>
            </div>

            <button @click="finishTest" class="w-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 border border-red-200 dark:border-red-800 py-3 rounded-xl font-bold transition-all">
                Akhiri Latihan
            </button>
        </div>
    </div>

</div>

<!-- Modal Konfirmasi -->
<div id="finishModal" class="hidden fixed inset-0 z-50 bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">🏁</div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Yakin Ingin Mengakhiri?</h3>
            <p class="text-gray-500 dark:text-gray-400">Pastikan semua soal sudah terjawab dengan benar. Jawaban tidak dapat diubah setelah latihan diakhiri.</p>
        </div>
        
        <form action="{{ route('latihan.finish', $session->id) }}" method="POST" class="flex gap-4">
            @csrf
            <button type="button" onclick="document.getElementById('finishModal').classList.add('hidden')" 
                class="flex-1 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-700">
                Batal
            </button>
            <button type="submit" 
                class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">
                Ya, Akhiri
            </button>
        </form>
    </div>
</div>

<script>
    // Inisialisasi Timer
    let durationSeconds = {{ $package->duration_minutes * 60 }};
    let timeSpent = {{ $session->time_spent_seconds }};

    if (durationSeconds > 0) {
        let remainingTime = Math.max(0, durationSeconds - timeSpent);

        function updateTimer() {
            let min = Math.floor(remainingTime / 60);
            let sec = remainingTime % 60;
            document.getElementById('timerDisplay').innerText = 
                (min < 10 ? '0' : '') + min + ':' + (sec < 10 ? '0' : '') + sec;

            if (remainingTime > 0) {
                remainingTime--;
                timeSpent++;
            } else {
                // Waktu habis, submit form otomatis
                document.querySelector('#finishModal form').submit();
            }
        }

        setInterval(updateTimer, 1000);
        updateTimer();
    }

    // Data AlpineJS
    function latihanSoal() {
        return {
            questions: @json($questions),
            answers: @json($userAnswers),
            currentIndex: 0,
            saveStatus: false,
            saveTimeout: null,
            timeSpentSinceLastSave: 0,

            getAnswer(questionId) {
                if (this.answers[questionId] && this.answers[questionId].selected_answer !== null) {
                    return this.answers[questionId].selected_answer;
                }
                return '';
            },

            saveAnswer(questionId, value) {
                // Update local state instantly
                if (!this.answers[questionId]) {
                    this.answers[questionId] = {};
                }
                this.answers[questionId].selected_answer = value;

                // Send AJAX
                fetch("{{ route('latihan.submit', $session->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        selected_answer: value,
                        time_spent: 10 // Simplifikasi waktu pengerjaan per soal
                    })
                })
                .then(response => {
                    if (response.ok) {
                        this.showSaveStatus();
                    }
                })
                .catch(err => console.error("Gagal menyimpan jawaban", err));
            },

            showSaveStatus() {
                this.saveStatus = true;
                clearTimeout(this.saveTimeout);
                this.saveTimeout = setTimeout(() => {
                    this.saveStatus = false;
                }, 2000);
            },

            nextQuestion() {
                if (this.currentIndex < this.questions.length - 1) {
                    this.currentIndex++;
                    window.scrollTo({top: 0, behavior: 'smooth'});
                }
            },

            prevQuestion() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                    window.scrollTo({top: 0, behavior: 'smooth'});
                }
            },

            goToQuestion(index) {
                this.currentIndex = index;
                window.scrollTo({top: 0, behavior: 'smooth'});
            },

            finishTest() {
                document.getElementById('finishModal').classList.remove('hidden');
            }
        }
    }
</script>
@endsection
