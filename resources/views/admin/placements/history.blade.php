@extends('layouts.hubin')

@section('title', 'Riwayat Penempatan Final')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-200 pb-4 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900">🎓 Riwayat Penempatan Final (DI-ACC)</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar siswa yang telah divalidasi lolos Prakerin beserta cetak berkas pengantar.</p>
        </div>
        <div class="w-full md:w-auto">
            <a href="{{ route('admin.placements.export_excel', ['academic_year_id' => $selectedYearId]) }}" class="w-full md:w-auto bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm px-5 py-2.5 rounded-xl font-bold transition flex items-center justify-center text-sm gap-2">
                📊 Export Excel (Semua Lolos)
            </a>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row gap-4 items-end">
        <form method="GET" action="{{ route('admin.placements.history') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Tahun Ajaran:</label>
                <select name="academic_year_id" onchange="this.form.submit()" class="block w-full rounded-xl border-gray-300 bg-gray-50 font-bold text-sm px-4 py-2.5 text-gray-800 focus:bg-white transition">
                    @foreach($allYears as $year)
                        <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                            {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Jurusan / Program Keahlian:</label>
                <select name="major_id" onchange="this.form.submit()" class="block w-full rounded-xl border-gray-300 bg-gray-50 font-bold text-sm px-4 py-2.5 text-gray-800 focus:bg-white transition">
                    <option value="">-- Semua Jurusan --</option>
                    @foreach($majors as $m)
                        <option value="{{ $m->id }}" {{ $selectedMajorId == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->code }})</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <div class="space-y-6">
        @forelse($companiesWithPlacements as $company)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden transition hover:shadow-md">
                <div class="bg-gray-50/70 px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-base font-black text-gray-900">🏢 {{ $company->name }}</h2>
                        <p class="text-xs text-gray-500 mt-0.5">📍 {{ $company->address ?: 'Alamat belum diatur' }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.placements.export_pdf_surat', [$company->id, 'academic_year_id' => $selectedYearId]) }}" target="_blank" class="inline-flex items-center gap-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold px-4 py-2 rounded-xl transition shadow-sm">
                            📄 Cetak Surat Pengantar (PDF)
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[300px] overflow-y-auto relative">
                    <table class="min-w-full divide-y divide-gray-100 text-sm relative">
                        <thead class="bg-gray-50/90 text-gray-500 font-bold text-xs uppercase tracking-wider sticky top-0 z-10 backdrop-blur-sm">
                            <tr>
                                <th class="px-6 py-3 text-left">Nama Siswa</th>
                                <th class="px-6 py-3 text-left">NISN / Kelas</th>
                                <th class="px-6 py-3 text-left">Jurusan</th>
                                <th class="px-6 py-3 text-left">Gelombang</th>
                                <th class="px-6 py-3 text-left">Gender</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 font-medium text-gray-700">
                            @foreach($company->placements as $placement)
                                @if($placement->student)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-3.5 font-bold text-gray-900">{{ $placement->student->name }}</td>
                                        <td class="px-6 py-3.5 text-xs text-gray-600">
                                            {{ $placement->student->nisn ?? '-' }} <br>
                                            <span class="text-[11px] text-gray-400 font-normal">Kelas: XII</span>
                                        </td>
                                        <td class="px-6 py-3.5">
                                            <span class="text-[10px] font-black text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded">
                                                {{ $placement->student->major->code }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3.5 text-xs text-gray-600">{{ $placement->companySlot->batch_name ?? 'Reguler' }}</td>
                                        <td class="px-6 py-3.5">
                                            @if($placement->student->gender === 'L')
                                                <span class="text-[10px] font-bold text-blue-600">Laki-laki</span>
                                            @else
                                                <span class="text-[10px] font-bold text-pink-600">Perempuan</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="bg-gray-50/50 px-6 py-3 border-t border-gray-100 text-xs font-bold text-gray-500 text-right">
                    Total: {{ $company->placements->count() }} Siswa Lolos
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm">
                <div class="text-gray-300 text-5xl mb-3">📭</div>
                <h3 class="text-base font-bold text-gray-700">Belum Ada Riwayat Penempatan</h3>
                <p class="text-xs text-gray-400 mt-1">Belum ada siswa yang berstatus FINAL / Lolos Prakerin pada filter terpilih.</p>
            </div>
        @endforelse

        @if($companiesWithPlacements->hasPages())
            <div class="mt-8 pt-4">
                {{ $companiesWithPlacements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection