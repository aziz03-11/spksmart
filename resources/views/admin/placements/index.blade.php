@extends('layouts.hubin')

@section('title', 'Proses SPK Aktif')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-4 rounded-xl shadow-sm font-medium text-sm flex items-center animate-fade-in">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-4 rounded-xl shadow-sm font-medium text-sm flex items-center animate-fade-in">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <form id="filterForm" method="GET" action="{{ url()->current() }}" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <label for="academicYearSelect" class="text-sm font-extrabold text-gray-700 whitespace-nowrap">Tinjau Data Periode:</label>
            <select name="academic_year_id" id="academicYearSelect" onchange="document.getElementById('filterForm').submit()" class="block w-full md:w-64 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 bg-gray-50 focus:bg-white font-bold transition">
                @foreach($allYears as $year)
                    <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                        {{ $year->name }} @if($year->is_active) • (Periode Saat Ini) @endif
                    </option>
                @endforeach
            </select>
        </div>
        <div class="text-xs font-medium text-gray-400">
            *Daftar siswa di bawah terikat secara spesifik pada periode tahun ajaran yang dipilih.
        </div>
    </form>

    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center border-b border-gray-100 pb-5 gap-5">
        <div>
            <h1 class="text-2xl font-black text-gray-900">Manajemen Proses SPK Aktif</h1>
            <p class="text-sm text-gray-500 mt-1">Jalankan algoritma SMART, tinjau hasil kalkulasi, dan berikan persetujuan (ACC) penempatan.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2.5 w-full xl:w-auto">
            
            <a href="{{ route('admin.spk.print_pdf', ['academic_year_id' => $selectedYearId]) }}" target="_blank" class="w-full sm:w-auto bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:text-red-700 px-4 py-2.5 rounded-xl font-bold transition flex items-center justify-center text-sm shadow-sm gap-2">
                📄 Draft (PDF)
            </a>
            
            <a href="{{ route('admin.spk.export_excel', ['academic_year_id' => $selectedYearId]) }}" class="w-full sm:w-auto bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 px-4 py-2.5 rounded-xl font-bold transition flex items-center justify-center text-sm shadow-sm gap-2">
                📊 Draft (Excel)
            </a>
            
            <form action="{{ route('admin.spk.generate') }}" method="POST" id="generateSpkForm" class="w-full sm:w-auto flex-1">
                @csrf
                <button type="button" id="generateBtn" onclick="confirmGenerate()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white shadow-md px-6 py-2.5 rounded-xl font-bold transition flex items-center justify-center text-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Proses Algoritma SMART SPK
                </button>
            </form>
            
        </div>
    </div>

    <div class="bg-white shadow-sm overflow-hidden rounded-2xl border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">NISN / Nama Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas / Jurusan</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Skor SMART</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Penempatan Industri</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Status & Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($placements as $placement)
                    <tr class="hover:bg-indigo-50/20 transition duration-150">
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-extrabold text-gray-900">{{ $placement->student->name }}</div>
                            <div class="text-xs font-bold text-gray-400 mt-0.5 tracking-wide">NISN: {{ $placement->student->nisn }}</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-800">{{ $placement->student->class_name ?? '-' }}</div>
                            <div class="text-xs font-extrabold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100 inline-block mt-1">
                                {{ $placement->student->major->code ?? '-' }}
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="bg-indigo-50 border border-indigo-100 text-indigo-700 px-3.5 py-1.5 rounded-xl text-sm font-black shadow-sm">
                                {{ number_format($placement->final_smart_score, 2) }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 max-w-xs">
                            @if($placement->company)
                                <div class="font-extrabold text-green-800 truncate text-sm">{{ $placement->company->name }}</div>
                                @if($placement->companySlot)
                                    <div class="mt-1">
                                        <span class="bg-gray-50 text-gray-600 text-[10px] font-black px-2.5 py-0.5 rounded-lg border border-gray-200 inline-block tracking-wider">
                                            Gelombang: {{ $placement->companySlot->batch_name }}
                                        </span>
                                    </div>
                                @endif
                            @else
                                @if($placement->status_pencocokan === 'waiting_list')
                                    <span class="text-amber-600 font-black text-xs tracking-tight italic bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 inline-block">⏳ Waiting List (Menunggu Kuota)</span>
                                @else
                                    <span class="text-red-500 font-black text-xs tracking-tight italic bg-red-50 px-2.5 py-1 rounded-lg border border-red-100 inline-block">⚠️ Sistem Pembinaan</span>
                                @endif
                                
                                @if($placement->notes && $placement->placement_method === 'SYSTEM')
                                    <div class="mt-1.5">
                                        <button type="button" onclick="openJustifikasiModal({{ $placement->id }})" class="text-[10px] font-black text-red-600 hover:text-red-800 hover:underline bg-red-50 px-2 py-0.5 rounded-lg border border-red-100 mt-0.5 shadow-sm">
                                            👁️ Lihat Alasan Detail
                                        </button>
                                    </div>
                                @endif
                            @endif

                            @if($placement->placement_method === 'MANUAL_OVERRIDE')
                                <div class="mt-1.5">
                                    <span class="bg-amber-50 text-amber-800 text-[10px] font-black px-2.5 py-0.5 rounded-lg border border-amber-200 inline-block">✋ Intervensi Kepala Hubin</span>
                                </div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex flex-col gap-1.5 items-end">
                                
                                <div class="flex gap-2 items-center">
                                    @if($placement->company_id)
                                        @if($placement->status_pencocokan === 'final')
                                            <span class="px-3 py-1 text-[10px] font-black rounded-lg bg-green-50 text-green-800 border border-green-200 tracking-wider">✅ Final / Di-ACC</span>
                                        @else
                                            <span class="px-3 py-1 text-[10px] font-black rounded-lg bg-blue-50 text-blue-800 border border-blue-200 tracking-wider">⏳ Menunggu ACC</span>
                                        @endif
                                    @else
                                        @if($placement->status_pencocokan === 'waiting_list')
                                            <span class="px-3 py-1 text-[10px] font-black rounded-lg bg-amber-50 text-amber-800 border border-amber-200 tracking-wider">Menunggu Slot Industri</span>
                                        @else
                                            <span class="px-3 py-1 text-[10px] font-black rounded-lg bg-red-50 text-red-800 border border-red-200 tracking-wider">Butuh Pembinaan</span>
                                        @endif
                                    @endif
                                    
                                    <button type="button" onclick="openCalcModal({{ $placement->id }})" class="text-[11px] font-extrabold text-blue-600 hover:text-blue-800 hover:underline bg-blue-50/50 px-2.5 py-1 rounded-lg border border-blue-100 transition">
                                        🧮 Transparansi Hitung
                                    </button>
                                </div>
                        
                                <div class="flex items-center gap-2 mt-0.5">
                                    @if($placement->status_pencocokan === 'rekomendasi')
                                        <form action="{{ route('admin.placements.acc', $placement->id) }}" method="POST" onsubmit="return confirm('ACC siswa ini untuk Prakerin di perusahaan tersebut?');">
                                            @csrf
                                            <button type="submit" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-100 font-bold px-3 py-1.5 rounded-lg text-xs transition border border-emerald-200">
                                                ✅ ACC Hubin
                                            </button>
                                        </form>
                                    @endif

                                    @if($placement->status_pencocokan !== 'final')
                                        <a href="{{ route('admin.placements.edit', $placement->id) }}" class="bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold px-3 py-1.5 rounded-lg text-xs transition border border-amber-200">
                                            ⚙️ Intervensi Manual
                                        </a>
                                    @endif
                                </div>
                        
                                @if($placement->placement_method === 'MANUAL_OVERRIDE')
                                    <button type="button" onclick="openJustifikasiModal({{ $placement->id }})" class="text-[10px] font-black text-red-600 hover:text-red-800 hover:underline bg-red-50 px-2 py-0.5 rounded-lg border border-red-100 mt-0.5">
                                        👁️ Lihat Alasan Kronologi
                                    </button>
                                @endif
                                
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="font-black text-gray-600 text-base">Belum ada data rekap penempatan.</p>
                                <p class="text-xs font-bold mt-1">Jalankan algoritma pemetaan (*Matchmaking*) sistem untuk memproses data.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($placements instanceof \Illuminate\Pagination\LengthAwarePaginator && $placements->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $placements->links() }}
            </div>
        @endif
    </div>
</div>

@foreach($placements as $placement)
    @php
        $assessment = $placement->student->assessment;
        $scoresData = $assessment ? $assessment->scores_data : [];
        $allCriterias = \App\Models\Criterion::orderBy('id', 'asc')->get();
        $totalWeight = $allCriterias->sum('weight') > 0 ? $allCriterias->sum('weight') : 1;
        $totalScoreAccumulation = 0;
    @endphp
    
    <div id="calc-modal-{{ $placement->id }}" class="fixed inset-0 bg-gray-900/60 hidden flex items-center justify-center z-50 p-4 backdrop-blur-sm animate-fade-in">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full border-t-4 border-indigo-600 flex flex-col max-h-[85vh]">
            <div class="p-6 border-b border-gray-100 flex justify-between items-start flex-shrink-0 bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-black text-gray-900">🧮 Transparansi Kalkulasi Metode SMART</h3>
                    <p class="text-xs font-bold text-gray-500 mt-1">Siswa Dinilai: <span class="text-indigo-700">{{ $placement->student->name }}</span> (NISN: {{ $placement->student->nisn }})</p>
                </div>
                <button type="button" onclick="closeCalcModal({{ $placement->id }})" class="text-gray-400 hover:text-red-500 font-extrabold text-2xl">&times;</button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1 text-xs text-gray-700">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-xl overflow-hidden text-center">
                    <thead class="bg-gray-100/70 font-black text-gray-600 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-4 py-3 text-left">Kriteria Evaluasi</th>
                            <th class="px-4 py-3">Sifat/Tipe</th>
                            <th class="px-4 py-3">Bobot (W)</th>
                            <th class="px-4 py-3">Nilai Input (C)</th>
                            <th class="px-4 py-3">Utilitas (U)</th>
                            <th class="px-4 py-3 bg-indigo-50 border-l border-indigo-100">Nilai Akhir (U x W)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 font-bold bg-white text-gray-800">
                        @forelse($allCriterias as $crit)
                            @php
                                $val = $scoresData[$crit->id] ?? 0;
                                $normWeight = $crit->weight / $totalWeight;
                                $isCost = strtolower($crit->type) === 'cost';
                                
                                // Kalkulasi Utility
                                if ($isCost) {
                                    $utility = (100 - $val) / 100;
                                } else {
                                    $utility = $val / 100;
                                }
                                $scoreResult = $utility * $normWeight;
                                $totalScoreAccumulation += ($scoreResult * 100);
                            @endphp
                            <tr class="{{ $isCost ? 'bg-red-50/30' : 'hover:bg-gray-50' }}">
                                <td class="px-4 py-3 text-left {{ $isCost ? 'text-red-900' : 'text-gray-900' }}">{{ $crit->name }}</td>
                                <td class="px-4 py-3 uppercase text-[9px] tracking-widest {{ $isCost ? 'text-red-600' : 'text-green-600' }}">
                                    <span class="px-2 py-1 rounded border {{ $isCost ? 'bg-red-100 border-red-200' : 'bg-green-100 border-green-200' }}">{{ $crit->type }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ number_format($normWeight, 4) }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $val }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ number_format($utility, 2) }}</td>
                                <td class="px-4 py-3 bg-indigo-50/30 border-l border-indigo-100 text-indigo-700 tracking-wide">{{ number_format($scoreResult, 4) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 font-extrabold">Parameter kriteria belum diatur di sistem.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-indigo-50 font-black text-indigo-900 text-sm">
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-right">AKUMULASI TOTAL NILAI SMART (x100):</td>
                            <td class="px-4 py-4 text-center text-base bg-indigo-100 border border-indigo-200 text-indigo-800 tracking-wide">
                                {{ round($totalScoreAccumulation, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="p-6 border-t border-gray-100 flex justify-end flex-shrink-0 bg-gray-50/50 rounded-b-2xl">
                <button type="button" onclick="closeCalcModal({{ $placement->id }})" class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-3 rounded-xl font-extrabold shadow-sm text-xs transition">
                    Tutup Detail Kalkulasi
                </button>
            </div>
        </div>
    </div>

    @if($placement->placement_method === 'MANUAL_OVERRIDE' || (!$placement->company_id && $placement->notes))
        <div id="justifikasi-modal-{{ $placement->id }}" class="fixed inset-0 bg-gray-900/60 hidden flex items-center justify-center z-50 p-4 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8 border-t-4 border-amber-500">
                <div class="flex justify-between items-start mb-5">
                    <div>
                        <h3 class="text-base font-black text-gray-900">
                            📄 {{ $placement->placement_method === 'MANUAL_OVERRIDE' ? 'Berita Acara Intervensi Manual' : 'Detail Alasan Penempatan' }}
                        </h3>
                        <p class="text-xs font-bold text-gray-400 mt-0.5">Siswa: {{ $placement->student->name }}</p>
                    </div>
                    <button type="button" onclick="closeJustifikasiModal({{ $placement->id }})" class="text-gray-400 hover:text-red-600 font-extrabold text-2xl">&times;</button>
                </div>
                
                <div class="bg-amber-50 p-5 rounded-xl border border-amber-200 text-xs text-amber-900 leading-relaxed font-bold shadow-inner whitespace-pre-wrap">
                    {{ $placement->notes }}
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="button" onclick="closeJustifikasiModal({{ $placement->id }})" class="bg-gray-800 hover:bg-gray-900 text-white font-extrabold text-xs px-6 py-3 rounded-xl shadow-sm transition">
                        Tutup Modal
                    </button>
                </div>
            </div>
        </div>
    @endif
@endforeach

<script>
    // JS Mencegah Double Click / Spam Click di Tombol Generate
    function confirmGenerate() {
        if(confirm('Proses ulang kalkulasi SMART? Data yang sudah FINAL tidak akan terganggu.')) {
            const btn = document.getElementById('generateBtn');
            btn.innerHTML = '⏳ Sedang Memproses...'; // Ubah teks tombol
            btn.classList.add('opacity-70', 'cursor-not-allowed'); // Beri efek mati
            btn.disabled = true; // Kunci tombolnya
            document.getElementById('generateSpkForm').submit(); // Eksekusi form
        }
    }

    // Modal Script
    function openCalcModal(id) { 
        document.getElementById('calc-modal-' + id).classList.remove('hidden'); 
    }
    function closeCalcModal(id) { 
        document.getElementById('calc-modal-' + id).classList.add('hidden'); 
    }
    
    function openJustifikasiModal(id) { 
        document.getElementById('justifikasi-modal-' + id).classList.remove('hidden'); 
    }
    function closeJustifikasiModal(id) { 
        document.getElementById('justifikasi-modal-' + id).classList.add('hidden'); 
    }
</script>
@endsection