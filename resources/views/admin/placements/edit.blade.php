@extends('layouts.hubin')

@section('title', 'Intervensi Manual Penempatan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
        <a href="{{ route('admin.placements.index') }}" class="text-gray-400 hover:text-indigo-600 transition bg-gray-50 hover:bg-indigo-50 p-2 rounded-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-black text-gray-900">⚙️ Intervensi Manual</h1>
    </div>

    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100">
        
        <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Data Siswa</p>
                <p class="text-lg font-black text-indigo-900">{{ $placement->student->name }} <span class="text-xs font-bold text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded ml-1">{{ $placement->student->major->code ?? '-' }}</span></p>
            </div>
            <div class="md:text-right">
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Rekomendasi PT Saat Ini</p>
                <p class="text-sm font-bold text-gray-700">{{ $placement->company->name ?? 'Tidak Ada / Masuk Pembinaan' }}</p>
            </div>
        </div>

        @if($placement->notes && $placement->placement_method === 'MANUAL_OVERRIDE')
            <div class="mb-8">
                <label class="block text-sm font-extrabold text-gray-700 mb-2">📜 Jejak Intervensi (Log Trail) Sebelumnya:</label>
                <div class="bg-slate-900 p-4 rounded-xl text-xs text-emerald-400 font-mono leading-relaxed shadow-inner whitespace-pre-wrap overflow-y-auto max-h-48 border border-slate-700">
{{ $placement->notes }}
                </div>
            </div>
        @endif

        <form action="{{ route('admin.placements.update', $placement->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                
                <div>
                    <label class="block text-sm font-extrabold text-gray-700 mb-3">Tindakan Intervensi Baru:</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <label class="border-2 border-gray-100 rounded-xl p-4 cursor-pointer hover:bg-indigo-50/50 transition flex items-start gap-3 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                            <input type="radio" name="action_type" value="move" class="mt-1 w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" checked onchange="toggleInterventionForm()">
                            <div>
                                <div class="font-black text-sm text-indigo-900">Pindahkan ke PT Lain</div>
                                <div class="text-xs text-gray-500 mt-1 leading-relaxed">Siswa tetap diberangkatkan, tetapi dialihkan ke perusahaan atau gelombang yang berbeda.</div>
                            </div>
                        </label>
                        
                        <label class="border-2 border-gray-100 rounded-xl p-4 cursor-pointer hover:bg-amber-50/50 transition flex items-start gap-3 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
                            <input type="radio" name="action_type" value="cancel" class="mt-1 w-4 h-4 text-amber-600 border-gray-300 focus:ring-amber-500" onchange="toggleInterventionForm()">
                            <div>
                                <div class="font-black text-sm text-amber-700">Batalkan Penempatan</div>
                                <div class="text-xs text-gray-500 mt-1 leading-relaxed">Penempatan dibatalkan (Wali tidak setuju, sakit, dll). Jika nilai mencukupi, siswa dialihkan ke <b class="text-amber-700">Waiting List</b>.</div>
                            </div>
                        </label>

                    </div>
                </div>

                <div id="moveCompanySection" class="animate-fade-in p-5 bg-gray-50 border border-gray-100 rounded-xl">
                    <label class="block text-sm font-extrabold text-gray-700 mb-2">Tujuan Perusahaan & Gelombang Baru:</label>
                    <select name="company_slot_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-white font-bold text-gray-700 cursor-pointer">
                        <option value="">-- Pilih Tujuan Industri --</option>
                        @foreach($allSlots as $slot)
                            @php
                                $used = \App\Models\Placement::where('company_slot_id', $slot->id)->where('status_pencocokan', 'final')->count();
                                $available = max(0, $slot->quota - $used);
                            @endphp
                            <option value="{{ $slot->id }}">
                                {{ $slot->company->name }} - Batch: {{ $slot->batch_name }} (Sisa Kuota: {{ $available }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-extrabold text-gray-700 mb-2">Alasan / Catatan Intervensi (Wajib):</label>
                    <textarea name="notes" rows="3" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-gray-50 focus:bg-white transition" placeholder="Jelaskan alasan spesifik... (Contoh: Penempatan dibatalkan karena wali siswa tidak mengizinkan penempatan di luar kota)"></textarea>
                    <p class="text-xs text-gray-400 font-medium mt-2">*Catatan ini akan ditambahkan ke log trail untuk transparansi kepada sekolah dan wali.</p>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('admin.placements.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl text-sm hover:bg-gray-200 transition">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl text-sm shadow-md hover:bg-indigo-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Intervensi
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    function toggleInterventionForm() {
        const actionType = document.querySelector('input[name="action_type"]:checked').value;
        const moveSection = document.getElementById('moveCompanySection');
        const companySelect = document.querySelector('select[name="company_slot_id"]');
        
        if(actionType === 'cancel') {
            moveSection.style.display = 'none';
            companySelect.removeAttribute('required');
        } else {
            moveSection.style.display = 'block';
            companySelect.setAttribute('required', 'required');
        }
    }
    document.addEventListener("DOMContentLoaded", toggleInterventionForm);
</script>
@endsection