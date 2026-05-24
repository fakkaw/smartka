@extends('layouts.admin')

@section('title', 'Import Soal via Excel')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ fileSelected: null, subjectId: '', isTryout: {{ old('is_tryout', 0) ? 'true' : 'false' }} }">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.paket.index') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-50 shadow-sm transition">
                ←
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Import Paket via Excel 📊</h1>
                <p class="text-sm text-gray-500">Upload file Excel (.csv, .xlsx), sistem akan otomatis mengekstrak soal ke database.</p>
            </div>
        </div>
        <a href="{{ route('admin.paket.import.template') }}" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-semibold px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
            <span>📥</span> Unduh Template Excel
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
        <form method="POST" action="{{ route('admin.paket.import.process') }}" enctype="multipart/form-data" class="p-8">
            @csrf

            {{-- 1. Upload File --}}
            <div class="mb-8">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Dokumen Excel <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:bg-gray-50 transition cursor-pointer relative"
                    :class="fileSelected ? 'border-blue-500 bg-blue-50' : ''">
                    <input type="file" name="file" accept=".csv,.xls,.xlsx" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        @change="fileSelected = $event.target.files[0].name">
                    <div class="text-4xl mb-3">📊</div>
                    <div x-show="!fileSelected" class="text-gray-600 font-medium text-sm">Klik atau seret file Excel kesini</div>
                    <div x-show="!fileSelected" class="text-gray-400 text-xs mt-1">Maksimal 5MB (.csv, .xlsx)</div>
                    <div x-show="fileSelected" class="text-blue-600 font-bold text-sm" x-text="fileSelected"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                {{-- Nama Paket --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Paket <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Try Out Matematika UTBK 2026"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Jenjang Kelas --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenjang Kelas <span class="text-red-500">*</span></label>
                    <select name="class_level" required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Pilih Jenjang</option>
                        <option value="6">Kelas 6 SD</option>
                        <option value="9">Kelas 9 SMP</option>
                        <option value="12">Kelas 12 SMA/SMK</option>
                    </select>
                </div>

                {{-- Mata Pelajaran --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mata Pelajaran <span class="text-red-500">*</span></label>
                    <select name="subject_id" x-model="subjectId" required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }} (Kelas {{ $subject->class_level }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Topik --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Topik <span class="text-red-500">*</span></label>
                    <input type="text" name="topic_name" required placeholder="Contoh: Aljabar Dasar"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white disabled:bg-gray-100 disabled:text-gray-400" :disabled="!subjectId">
                </div>

                {{-- Durasi --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu Pengerjaan (Menit)
                        <span class="text-red-500" x-show="isTryout">*</span>
                    </label>
                    <input type="number" name="duration_minutes" :required="isTryout" min="10" value="60"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :disabled="!isTryout">
                </div>

                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_tryout" value="0">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_tryout" value="1" @change="isTryout = $event.target.checked"
                            class="w-4 h-4 text-blue-600 rounded border-gray-300">
                        <span class="text-sm text-gray-700">Ini paket Try Out (gunakan durasi)</span>
                    </label>
                </div>

                {{-- Tipe --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Aksesibilitas <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="free">Gratis (Semua User)</option>
                        <option value="premium">Premium Saja</option>
                    </select>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-8 flex gap-3 text-sm text-blue-800">
                <div class="text-xl">ℹ️</div>
                <div>
                    <strong>Catatan:</strong><br>
                    - Baris pertama di file Excel akan otomatis diabaikan karena dianggap sebagai header/judul kolom.<br>
                    - Semua soal yang masuk akan otomatis berstatus <strong>Active</strong> (aktif) dan paketnya berstatus <strong>Published</strong> (tayang).
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition flex items-center gap-2">
                    <span>Mulai Proses Import</span> <span>🚀</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
