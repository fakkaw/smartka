@extends('layouts.admin')
@section('title', 'Tambah Soal')
@section('page-title', 'Tambah Soal Baru')

@section('content')
<div class="max-w-3xl"
  x-data="{
    type: 'multiple_choice',
    classLevel: '',
    inputMethod: 'manual',
    topics: [],
    async loadTopics(subjectId) {
      if (!subjectId) { this.topics = []; return; }
    }
  }">

  {{-- Alert Success --}}
  @if(session('success'))
  <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mb-5 text-sm flex items-center gap-3">
    <span class="text-xl">✅</span>
    <span>{{ session('success') }}</span>
  </div>
  @endif

  {{-- Alert Warning (Partial Failures) --}}
  @if(session('warning'))
  <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl px-4 py-3 mb-5 text-sm">
    <div class="flex items-center gap-3 mb-2">
      <span class="text-xl">⚠️</span>
      <span class="font-bold">{{ session('warning') }}</span>
    </div>
    @if(session('import_failures'))
    <ul class="list-disc list-inside text-xs space-y-1">
      @foreach(session('import_failures') as $failure)
      <li>Baris {{ $failure->row() }}: {{ implode(', ', $failure->errors()) }}</li>
      @endforeach
    </ul>
    @endif
  </div>
  @endif

  {{-- Alert Error --}}
  @if(session('error'))
  <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm flex items-center gap-3">
    <span class="text-xl">❌</span>
    <span>{{ session('error') }}</span>
  </div>
  @endif

  @if($errors->any())
  <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
    <strong>Terdapat kesalahan:</strong>
    <ul class="mt-1 list-disc list-inside">
      @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  {{-- Tab Toggle --}}
  <div class="flex bg-gray-100 p-1 rounded-xl mb-6">
    <button @click="inputMethod = 'manual'" 
      :class="inputMethod === 'manual' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700'"
      class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">
      ✍️ Input Manual
    </button>
    <button @click="inputMethod = 'excel'" 
      :class="inputMethod === 'excel' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700'"
      class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">
      📊 Import Excel
    </button>
  </div>

  <form method="POST" :action="inputMethod === 'manual' ? '{{ route('admin.soal.store') }}' : '{{ route('admin.soal.import') }}'" enctype="multipart/form-data"
    class="space-y-5">
    @csrf

    {{-- Kategori (Shared) --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
      <h3 class="font-bold text-gray-800 mb-4">Kategori Soal</h3>
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenjang</label>
          <select name="class_level" x-model="classLevel" required
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Pilih Jenjang</option>
            <option value="6">Kelas 6 SD</option>
            <option value="9">Kelas 9 SMP</option>
            <option value="12">Kelas 12 SMA</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Mata Pelajaran</label>
          <select name="subject_id" id="subject_id" required
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            onchange="loadTopics(this.value)">
            <option value="">Pilih Mapel</option>
            @foreach($subjects as $s)
            <option value="{{ $s->id }}" data-level="{{ $s->class_level }}">
              {{ $s->name }} (Kelas {{ $s->class_level }})
            </option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Bab / Topik</label>
          <select name="topic_id" id="topic_id" required
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Pilih Topik</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Tingkat Kesulitan</label>
          <select name="difficulty" required
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="easy">Mudah</option>
            <option value="medium" selected>Sedang</option>
            <option value="hard">Sulit</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe Soal</label>
          <select name="type" x-model="type" required
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="multiple_choice">Pilihan Ganda</option>
            <option value="true_false">Benar / Salah</option>
            <option value="short_answer">Isian Singkat</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
          <select name="status" required
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="draft">Draft</option>
            <option value="active">Aktif</option>
            <option value="archived">Arsip</option>
          </select>
        </div>
      </div>
    </div>

    {{-- Section Khusus Excel --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6" x-show="inputMethod === 'excel'" x-cloak>
      <div class="flex items-start gap-4">
        <div class="bg-blue-600 p-3 rounded-xl text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <div class="flex-1">
          <h3 class="font-bold text-blue-900 mb-1">Upload File Excel</h3>
          <p class="text-xs text-blue-700 mb-4">Pastikan format kolom: <span class="font-mono bg-blue-100 px-1 italic">teks_soal, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, jawaban_benar, pembahasan, link_pembahasan</span></p>
          
          <input type="file" name="excel_file" accept=".xlsx,.xls,.csv"
            :required="inputMethod === 'excel'"
            class="w-full bg-white border border-blue-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <p class="mt-2 text-[10px] text-blue-500">*Mendukung format .xlsx, .xls, dan .csv</p>
        </div>
      </div>
    </div>

    {{-- Section Manual --}}
    <div x-show="inputMethod === 'manual'" class="space-y-5">
      {{-- Isi Soal --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">Isi Soal</h3>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Teks Soal</label>
          <textarea name="question_text" rows="4" :required="inputMethod === 'manual'" placeholder="Tulis soal di sini..."
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('question_text') }}</textarea>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Gambar Soal (opsional)</label>
          <input type="file" name="question_image" accept="image/*"
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
      </div>

      {{-- Pilihan Jawaban --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6" x-show="type === 'multiple_choice'">
        <h3 class="font-bold text-gray-800 mb-4">Pilihan Jawaban</h3>
        <div class="space-y-3">
          @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E'] as $key => $label)
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-sm font-bold text-gray-600 flex-shrink-0">
              {{ $label }}
            </span>
            <input type="text" name="option_{{ $key }}" value="{{ old('option_' . $key) }}"
              placeholder="Pilihan {{ $label }}..."
              class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          @endforeach
        </div>
      </div>

      {{-- Kunci Jawaban & Pembahasan --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">Kunci Jawaban & Pembahasan</h3>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Jawaban Benar</label>
          
          {{-- Pilihan Ganda --}}
          <div x-show="type === 'multiple_choice'" class="flex gap-2">
            @foreach(['a','b','c','d','e'] as $opt)
            <label class="flex-1" for="correct_answer_{{ $opt }}">
              <input type="radio" name="correct_answer" value="{{ $opt }}" id="correct_answer_{{ $opt }}" 
                class="sr-only peer" {{ old('correct_answer') === $opt ? 'checked' : '' }}
                :disabled="type !== 'multiple_choice' || inputMethod !== 'manual'">
              <div class="text-center border-2 border-gray-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 py-2.5 rounded-xl cursor-pointer font-bold text-sm transition uppercase">
                {{ $opt }}
              </div>
            </label>
            @endforeach
          </div>

          {{-- Benar / Salah --}}
          <div x-show="type === 'true_false'" class="flex gap-4">
            @foreach(['Benar', 'Salah'] as $val)
            <label class="flex-1 flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition">
              <input type="radio" name="correct_answer" value="{{ strtolower($val) }}" 
                class="w-4 h-4 text-blue-600 focus:ring-blue-500"
                {{ strtolower(old('correct_answer')) === strtolower($val) ? 'checked' : '' }}
                :disabled="type !== 'true_false' || inputMethod !== 'manual'">
              <span class="text-sm font-medium text-gray-700">{{ $val }}</span>
            </label>
            @endforeach
          </div>

          {{-- Isian Singkat --}}
          <div x-show="type === 'short_answer'">
            <input type="text" name="correct_answer" value="{{ old('correct_answer') }}" placeholder="Ketik jawaban singkat yang benar..."
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              :disabled="type !== 'short_answer' || inputMethod !== 'manual'">
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Pembahasan</label>
          <textarea name="explanation_text" rows="4" placeholder="Tulis pembahasan langkah per langkah..."
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('explanation_text') }}</textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Link Video Pembahasan (opsional)</label>
          <input type="url" name="explanation_video_url" value="{{ old('explanation_video_url') }}"
            placeholder="https://youtube.com/..."
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
      </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex gap-3 mt-8">
      <a href="{{ route('admin.soal.index') }}"
        class="flex-1 text-center border border-gray-300 text-gray-600 font-semibold py-3 rounded-xl hover:bg-gray-50 transition text-sm">
        ← Kembali
      </a>
      <button type="submit" name="status_action" value="draft" x-show="inputMethod === 'manual'"
        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 rounded-xl transition text-sm">
        Simpan Draft
      </button>
      <button type="submit"
        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition text-sm">
        <span x-text="inputMethod === 'manual' ? 'Simpan & Publikasikan ✓' : 'Mulai Import Soal 🚀'"></span>
      </button>
    </div>
  </form>
</div>

<script>
// Data topik per subject (dari PHP)
const subjectTopics = {
  @foreach($subjects as $s)
  "{{ $s->id }}": [
    @foreach($s->topics as $t)
    { id: "{{ $t->id }}", name: "{{ $t->name }}" },
    @endforeach
  ],
  @endforeach
};

function loadTopics(subjectId) {
  const select = document.getElementById('topic_id');
  select.innerHTML = '<option value="">Pilih Topik</option>';

  const topics = subjectTopics[subjectId] || [];
  topics.forEach(t => {
    const opt = document.createElement('option');
    opt.value       = t.id;
    opt.textContent = t.name;
    select.appendChild(opt);
  });
}
</script>
@endsection
