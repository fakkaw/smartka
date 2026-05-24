@extends('layouts.admin')
@section('title', 'Edit Paket Latihan')
@section('page-title', 'Edit Paket Latihan')

@section('content')
<div class="mb-6 flex justify-end">
    <a href="{{ route('admin.paket.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
        &larr; Kembali
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6" x-data="{ selectedQuestions: {{ json_encode($package->questions->pluck('id')) }}, isTryout: {{ preg_match('/try\\s*out/i', $package->name) ? 'true' : 'false' }} }">
    <form action="{{ route('admin.paket.update', $package->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Kiri: Detail Paket -->
            <div class="lg:col-span-1 space-y-6">
                <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-2">Informasi Paket</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Paket</label>
                    <input type="text" name="name" value="{{ old('name', $package->name) }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500">{{ old('description', $package->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                        <select name="class_level" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                            <option value="6" {{ old('class_level', $package->class_level) == '6' ? 'selected' : '' }}>Kelas 6 SD</option>
                            <option value="9" {{ old('class_level', $package->class_level) == '9' ? 'selected' : '' }}>Kelas 9 SMP</option>
                            <option value="12" {{ old('class_level', $package->class_level) == '12' ? 'selected' : '' }}>Kelas 12 SMA/SMK</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                        <select name="type" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                            <option value="free" {{ old('type', $package->type) == 'free' ? 'selected' : '' }}>Gratis</option>
                            <option value="premium" {{ old('type', $package->type) == 'premium' ? 'selected' : '' }}>Premium</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div x-show="isTryout" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Durasi (Menit)</label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $package->duration_minutes) }}" min="10"
                            :required="isTryout"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div x-show="!isTryout" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Durasi (Menit)</label>
                        <input type="number" value="0" disabled class="w-full border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-100 text-gray-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                            <option value="draft" {{ old('status', $package->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $package->status) == 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_randomized" value="0">
                        <input type="checkbox" name="is_randomized" value="1" {{ old('is_randomized', $package->is_randomized) ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Acak Urutan Soal saat Dikerjakan</span>
                    </label>
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_tryout" value="0">
                        <input type="checkbox" name="is_tryout" value="1" @change="isTryout = $event.target.checked" {{ preg_match('/try\\s*out/i', $package->name) ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Tandai sebagai paket Try Out</span>
                    </label>
                </div>
            </div>

            <!-- Kolom Kanan: Pemilihan Soal -->
            <div class="lg:col-span-2 space-y-4 flex flex-col h-full">
                <div class="flex justify-between items-end border-b border-gray-100 pb-2">
                    <h3 class="font-bold text-gray-800">Pilih Soal</h3>
                    <span class="text-sm text-blue-600 font-semibold" x-text="selectedQuestions.length + ' soal terpilih'"></span>
                </div>
                
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 flex-1 max-h-[600px] overflow-y-auto">
                    <div class="space-y-3">
                        @forelse($questions as $q)
                        <label class="flex items-start gap-4 p-4 bg-white rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:border-blue-300 transition">
                            <div class="pt-1">
                                <input type="checkbox" name="question_ids[]" value="{{ $q->id }}" x-model="selectedQuestions"
                                    class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start mb-1">
                                    <div class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                        {{ $q->subject->name }} &bull; {{ $q->topic->name }}
                                    </div>
                                    <span class="text-xs px-2 py-0.5 rounded font-semibold 
                                        {{ $q->difficulty == 'easy' ? 'bg-green-100 text-green-700' : ($q->difficulty == 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ ucfirst($q->difficulty) }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-800 line-clamp-2">{{ $q->question_text }}</div>
                            </div>
                        </label>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            Tidak ada soal aktif yang tersedia.
                        </div>
                        @endforelse
                    </div>
                </div>
                @error('question_ids') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100">
            <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-colors shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
