@extends('layouts.admin')
@section('title', 'Tambah Topik / Bab')
@section('page-title', 'Tambah Topik / Bab')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<style>
    /* Custom simple prose for markdown preview */
    .prose-preview h1 { font-size: 1.5rem; font-weight: bold; margin-top: 1rem; margin-bottom: 0.5rem; }
    .prose-preview h2 { font-size: 1.25rem; font-weight: bold; margin-top: 1rem; margin-bottom: 0.5rem; }
    .prose-preview h3 { font-size: 1.125rem; font-weight: bold; margin-top: 1rem; margin-bottom: 0.5rem; }
    .prose-preview p { margin-bottom: 0.75rem; }
    .prose-preview ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 0.75rem; }
    .prose-preview ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 0.75rem; }
    .prose-preview strong { font-weight: bold; }
    .prose-preview em { font-style: italic; }
    .prose-preview blockquote { border-left: 4px solid #e5e7eb; padding-left: 1rem; color: #6b7280; font-style: italic; }
    .prose-preview code { background-color: #f3f4f6; padding: 0.2rem 0.4rem; border-radius: 0.25rem; font-family: monospace; font-size: 0.875em; }
    .prose-preview pre { background-color: #1f2937; color: #f9fafb; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin-bottom: 0.75rem; }
    .prose-preview pre code { background-color: transparent; padding: 0; color: inherit; }
</style>
@endpush

@section('content')
<div class="mb-6 flex justify-end">
    <a href="{{ route('admin.topik.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
        &larr; Kembali
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6" x-data="{ 
    content: `{{ old('description') }}`,
    get preview() {
        return this.content ? marked.parse(this.content) : '<p class=\'text-gray-400 italic\'>Preview akan muncul di sini...</p>';
    }
}">
    <form action="{{ route('admin.topik.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
            <!-- Kolom Input -->
            <div class="space-y-6">
                <!-- Mata Pelajaran -->
                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                    <select id="subject_id" name="subject_id" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 @error('subject_id') border-red-500 @enderror">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} (Kelas {{ $s->class_level }})
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Bab -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Judul Topik / Bab</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Aljabar Linier"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Urutan -->
                <div>
                    <label for="order_number" class="block text-sm font-medium text-gray-700 mb-2">Urutan Bab</label>
                    <input type="number" id="order_number" name="order_number" value="{{ old('order_number', 1) }}" required min="1"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 @error('order_number') border-red-500 @enderror">
                    @error('order_number')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi / Materi -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Isi Materi (Dukung Markdown)</label>
                    <textarea id="description" name="description" rows="12" x-model="content"
                        placeholder="Tuliskan materi bab menggunakan format markdown..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 font-mono text-sm @error('description') border-red-500 @enderror"></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kolom Preview -->
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 flex flex-col h-full">
                <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-200">
                    <span class="text-xl">👁️</span>
                    <h3 class="font-bold text-gray-700">Live Preview</h3>
                </div>
                <div class="prose-preview text-gray-800 flex-1 overflow-y-auto max-h-[600px]" x-html="preview">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100">
            <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-colors shadow-sm">
                Simpan Topik
            </button>
        </div>
    </form>
</div>
@endsection
