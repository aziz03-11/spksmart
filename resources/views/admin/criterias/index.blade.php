@extends('layouts.hubin')

@section('title', 'Kriteria SMART')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    <div class="flex flex-col md:flex-row justify-between items-center border-b border-gray-100 pb-4 gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">🎯 Parameter Kriteria SMART</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola bobot kepentingan dan sifat kriteria untuk kalkulasi otomatis sistem.</p>
        </div>
        <div>
            <button onclick="document.getElementById('createCriteriaModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white shadow-md px-5 py-2.5 rounded-xl font-bold transition flex items-center text-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Kriteria
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-4 rounded-xl shadow-sm font-medium text-sm flex items-center animate-fade-in">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-4 rounded-xl shadow-sm font-medium text-sm animate-fade-in">
            <div class="flex items-center mb-2 font-bold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Gagal Menyimpan Kriteria:
            </div>
            <ul class="list-disc list-inside space-y-1 ml-2 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('createCriteriaModal').classList.remove('hidden');
            });
        </script>
    @endif

    <div class="bg-white shadow-sm overflow-hidden rounded-2xl border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Kriteria</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Sifat / Tipe</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Bobot Statis (W)</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($criterias as $criteria)
                    <tr class="hover:bg-indigo-50/30 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="bg-indigo-50 border border-indigo-100 text-indigo-700 font-extrabold px-3 py-1 rounded-md text-xs tracking-wider">
                                {{ $criteria->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            {{ $criteria->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if(strtolower($criteria->type) === 'benefit')
                                <span class="bg-green-100 border border-green-200 text-green-800 px-3 py-1 rounded-md text-xs font-bold">Benefit</span>
                            @else
                                <span class="bg-red-100 border border-red-200 text-red-800 px-3 py-1 rounded-md text-xs font-bold">Cost</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-extrabold text-indigo-600">
                            {{ $criteria->weight }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center space-x-4">
                                
                                <a href="{{ route('admin.criterias.edit', $criteria->id) }}" class="text-gray-400 hover:text-indigo-600 transition" title="Edit Kriteria">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>

                                <form action="{{ route('admin.criterias.destroy', $criteria->id) }}" method="POST" onsubmit="return confirm('Menghapus kriteria akan berdampak pada perhitungan nilai siswa yang sudah ada! Lanjutkan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition" title="Hapus Kriteria">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 font-medium">
                            Belum ada parameter kriteria yang dibuat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="createCriteriaModal" class="hidden fixed inset-0 bg-gray-900/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-900">Tambah Parameter Baru</h3>
            <button onclick="document.getElementById('createCriteriaModal').classList.add('hidden')" class="text-gray-400 hover:text-red-500 font-bold text-xl">&times;</button>
        </div>
        <form action="{{ route('admin.criterias.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kode</label>
                    <input type="text" name="code" value="{{ old('code') }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-gray-50 focus:bg-white transition" placeholder="Cth: C1">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kriteria</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-gray-50 focus:bg-white transition" placeholder="Cth: Nilai Keaktifan">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Sifat Kriteria</label>
                <select name="type" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-gray-50 focus:bg-white transition">
                    <option value="benefit" {{ old('type') == 'benefit' ? 'selected' : '' }}>Benefit (Makin tinggi makin baik)</option>
                    <option value="cost" {{ old('type') == 'cost' ? 'selected' : '' }}>Cost (Makin rendah makin baik)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Bobot Nilai (Angka)</label>
                <input type="number" step="0.01" name="weight" value="{{ old('weight') }}" required min="1" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-gray-50 focus:bg-white transition" placeholder="Cth: 25">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('createCriteriaModal').classList.add('hidden')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 px-5 rounded-xl transition text-sm">Batal</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-md transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection