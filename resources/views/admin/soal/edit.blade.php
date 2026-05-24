@extends('layouts.admin')
@section('title', 'Edit Soal')
@section('page-title', 'Edit Soal')

@section('content')
<div class="max-w-3xl"
  x-data="{
    type: '{{ old('type', $question->type) }}',
    classLevel: '{{ old('class_level', $question->class_level) }}',
  }">

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

  <form method="POST" action="{{ route('admin.soal.update', $question) }}" enctype="multipart/form-data"
    class="space-y-5">
    @csrf
    @method('PUT')

    {{-- Kategori --}}
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
            <option value="{{ $s->id }}" data-level="{{ $s->class_level }}" @selected($question->subject_id == $s->id)>
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
            {{-- Topics akan diisi via JS, tapi sediakan default untuk edit --}}
            <option value="{{ $question->topic_id }}" selected>{{ $question->topic?->name }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Tingkat Kesulitan</label>
          <select name="difficulty" required
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="easy" @selected($question->difficulty === 'easy')>Mudah</option>
            <option value="medium" @selected($question->difficulty === 'medium')>Sedang</option>
            <option value="hard" @selected($question->difficulty === 'hard')>Sulit</option>
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
            <option value="draft" @selected($question->status === 'draft')>Draft</option>
            <option value="active" @selected($question->status === 'active')>Aktif</option>
            <option value="archived" @selected($question->status === 'archived')>Arsip</option>
          </select>
        </div>
      </div>
    </div>

    {{-- Isi Soal --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
      <h3 class="font-bold text-gray-800 mb-4">Isi Soal</h3>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Teks Soal</label>
        <textarea name="question_text" rows="4" required placeholder="Tulis soal di sini..."
          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('question_text', $question->question_text) }}</textarea>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Gambar Soal (opsional)</label>
        @if($question->question_image)
          <div class="mb-2">
            <img src="{{ asset('storage/' . $question->question_image) }}" alt="Preview" class="h-32 rounded-lg border">
            <p class="text-xs text-gray-500 mt-1">Gambar saat ini. Unggah baru untuk mengganti.</p>
          </div>
        @endif
        <input type="file" name="question_image" accept="image/*"
          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
    </div>

    {{-- Pilihan Jawaban --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6"
      x-show="type === 'multiple_choice'">
      <h3 class="font-bold text-gray-800 mb-4">Pilihan Jawaban</h3>
      <div class="space-y-3">
        @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E'] as $key => $label)
        <div class="flex items-center gap-3">
          <span class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-sm font-bold text-gray-600 flex-shrink-0">
            {{ $label }}
          </span>
          @php $optionField = 'option_' . $key; @endphp
          <input type="text" name="{{ $optionField }}" value="{{ old($optionField, $question->$optionField) }}"
            placeholder="Pilihan {{ $label }}..."
            class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        @endforeach
      </div>
    </div>

    {{-- Jawaban Benar --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
      <h3 class="font-bold text-gray-800 mb-4">Kunci Jawaban & Pembahasan</h3>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Jawaban Benar</label>
        
        {{-- Pilihan Ganda --}}
        <div x-show="type === 'multiple_choice'" class="flex gap-2">
          @foreach(['a','b','c','d','e'] as $opt)
          <label class="flex-1" for="correct_answer_{{ $opt }}">
            <input type="radio" name="correct_answer" value="{{ $opt }}" id="correct_answer_{{ $opt }}" 
              class="sr-only peer" {{ old('correct_answer', $question->correct_answer) === $opt ? 'checked' : '' }}
              :disabled="type !== 'multiple_choice'">
            <div class="text-center border-2 border-gray-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 py-2.5 rounded-xl cursor-pointer font-bold text-sm transition uppercase">
              {{ $opt }}
            </div>
          </label>
          @endforeach
        </div>

        {{-- Benar / Salah --}}
        <div x-show="type === 'true_false'" class="flex gap-4">
          @foreach(['Benar', 'Salah'] as $val)
          <label class="flex-1 flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition peer-checked:border-blue-600">
            <input type="radio" name="correct_answer" value="{{ strtolower($val) }}" 
              class="w-4 h-4 text-blue-600 focus:ring-blue-500"
              {{ strtolower(old('correct_answer', $question->correct_answer)) === strtolower($val) ? 'checked' : '' }}
              :disabled="type !== 'true_false'">
            <span class="text-sm font-medium text-gray-700">{{ $val }}</span>
          </label>
          @endforeach
        </div>

        {{-- Isian Singkat --}}
        <div x-show="type === 'short_answer'">
          <input type="text" name="correct_answer" value="{{ old('correct_answer', $question->correct_answer) }}" placeholder="Ketik jawaban singkat yang benar..."
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            :disabled="type !== 'short_answer'">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Pembahasan</label>
        <textarea name="explanation_text" rows="4" placeholder="Tulis pembahasan langkah per langkah..."
          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('explanation_text', $question->explanation_text) }}</textarea>
      </div>

      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Link Video Pembahasan (opsional)</label>
        <input type="url" name="explanation_video_url" value="{{ old('explanation_video_url', $question->explanation_video_url) }}"
          placeholder="https://youtube.com/..."
          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
    </div>

    {{-- Submit --}}
    <div class="flex gap-3">
      <a href="{{ route('admin.soal.index') }}"
        class="flex-1 text-center border border-gray-300 text-gray-600 font-semibold py-3 rounded-xl hover:bg-gray-50 transition text-sm">
        ← Kembali
      </a>
      <button type="submit"
        class="flex-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition text-sm">
        Simpan Perubahan ✓
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
  const currentTopicId = "{{ $question->topic_id }}";
  select.innerHTML = '<option value="">Pilih Topik</option>';

  const topics = subjectTopics[subjectId] || [];
  topics.forEach(t => {
    const opt = document.createElement('option');
    opt.value       = t.id;
    opt.textContent = t.name;
    if (t.id == currentTopicId) {
        opt.selected = true;
    }
    select.appendChild(opt);
  });
}

// Inisialisasi topik saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    loadTopics(document.getElementById('subject_id').value);
});
</script>
@endsection
