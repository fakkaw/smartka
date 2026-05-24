@extends('layouts.admin')

@section('title', 'Tambah Paket Latihan')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.paket.index') }}" class="text-gray-500 hover:text-gray-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Tambah Paket Try Out / Latihan</h1>
    </div>

    <form action="{{ route('admin.paket.store') }}" method="POST" class="space-y-6" x-data="{ isTryout: {{ old('is_tryout', 0) ? 'true' : 'false' }} }">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Paket</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    @error('name') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (opsional)</label>
                    <textarea name="description" rows="4"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 resize-none">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                        <select name="class_level" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">
                            <option value="6" {{ old('class_level') == '6' ? 'selected' : '' }}>Kelas 6 SD</option>
                            <option value="9" {{ old('class_level', '9') == '9' ? 'selected' : '' }}>Kelas 9 SMP</option>
                            <option value="12" {{ old('class_level') == '12' ? 'selected' : '' }}>Kelas 12 SMA/SMK</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Paket</label>
                        <select name="type" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">
                            <option value="free" {{ old('type') == 'free' ? 'selected' : '' }}>Gratis</option>
                            <option value="premium" {{ old('type') == 'premium' ? 'selected' : '' }}>Premium</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div x-show="isTryout" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Durasi (menit)</label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="10"
                            :required="isTryout" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div x-show="!isTryout" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Durasi (menit)</label>
                        <input type="number" value="0" disabled class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-100 text-gray-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_randomized" value="0">
                        <input type="checkbox" name="is_randomized" value="1" {{ old('is_randomized') ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 rounded border-gray-300">
                        <span class="text-sm text-gray-700">Acak urutan soal saat try out</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_tryout" value="0">
                        <input type="checkbox" name="is_tryout" value="1" {{ old('is_tryout') ? 'checked' : '' }}
                            @change="isTryout = $event.target.checked"
                            class="w-5 h-5 text-blue-600 rounded border-gray-300">
                        <span class="text-sm text-gray-700">Tandai sebagai paket Try Out</span>
                    </label>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition">Buat Paket Try Out</button>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Pilih Soal untuk Try Out</h2>
                        <p class="text-sm text-gray-500">Pilih soal aktif yang ingin dimasukkan ke paket.</p>
                    </div>
                    <div class="text-sm text-gray-500">{{ $questions->count() }} soal tersedia</div>
                </div>

                <div class="bg-gray-50 rounded-2xl border border-gray-200 p-4 max-h-[640px] overflow-y-auto">
                    <div class="space-y-3">
                        @forelse($questions as $question)
                            <label class="flex items-start gap-4 p-4 bg-white rounded-2xl border border-gray-100 hover:border-blue-300 transition cursor-pointer">
                                <div class="pt-1">
                                    <input type="checkbox" name="question_ids[]" value="{{ $question->id }}"
                                        {{ in_array($question->id, old('question_ids', [])) ? 'checked' : '' }}
                                        class="w-5 h-5 text-blue-600 rounded border-gray-300">
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-2 text-xs text-gray-500">
                                        <span class="bg-gray-100 rounded-full px-2 py-1">{{ $question->subject->name }}</span>
                                        <span class="bg-gray-100 rounded-full px-2 py-1">{{ $question->topic->name }}</span>
                                        <span class="bg-blue-50 text-blue-700 rounded-full px-2 py-1">{{ ucfirst($question->difficulty) }}</span>
                                    </div>
                                    <p class="text-sm text-gray-800 line-clamp-2">{!! $question->question_text !!}</p>
                                </div>
                            </label>
                        @empty
                            <div class="text-center py-16 text-gray-500">Tidak ada soal aktif yang tersedia. Tambahkan soal di menu Bank Soal terlebih dahulu.</div>
                        @endforelse
                    </div>
                </div>
                @error('question_ids') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>
    </form>
</div>
@endsection