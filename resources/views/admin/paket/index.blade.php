@extends('layouts.admin')

@section('title', 'Paket Latihan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h1 class="text-xl font-bold text-gray-800">Paket Latihan</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.paket.import') }}"
               class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition flex items-center gap-2">
                <span>📊</span> Import via Excel
            </a>
            <a href="{{ route('admin.paket.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
                + Tambah Paket
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-left bg-gray-50">
                    <th class="px-5 py-3 text-gray-600 font-semibold">Judul Paket</th>
                    <th class="px-5 py-3 text-gray-600 font-semibold">Kelas</th>
                    <th class="px-5 py-3 text-gray-600 font-semibold">Jumlah Soal</th>
                    <th class="px-5 py-3 text-gray-600 font-semibold">Tipe</th>
                    <th class="px-5 py-3 text-gray-600 font-semibold">Status</th>
                    <th class="px-5 py-3 text-gray-600 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($packages as $package)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4 text-gray-800 font-semibold">{{ $package->name }}</td>
                        <td class="px-5 py-4 text-gray-600">Kelas {{ $package->class_level }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $package->total_questions }} soal</td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-bold
                                {{ $package->type === 'premium' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                {{ strtoupper($package->type) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-bold
                                {{ $package->status === 'published' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ strtoupper($package->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.paket.edit', $package) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                            Belum ada paket latihan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="text-gray-500 text-xs">Total: {{ $packages->total() }} paket</div>
    {{ $packages->links() }}
</div>
@endsection