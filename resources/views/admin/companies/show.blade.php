@extends('layouts.hubin')

@section('title', 'Detail Perusahaan & Daftar Gelombang')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.companies.index') }}" class="text-gray-400 hover:text-indigo-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Manajemen Kuota Industri</h1>
            </div>
        </div>
        <div>
            <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full border border-indigo-100">Mitra ID: #{{ $company->id }}</span>
        </div>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row gap-6 items-start md:items-center relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-50 rounded-full opacity-50 blur-2xl pointer-events-none"></div>
        
        <div class="p-4 bg-indigo-100/50 rounded-2xl flex-shrink-0 border border-indigo-50 relative z-10">
            <div class="w-14 h-14 flex items-center justify-center text-indigo-600">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
        </div>
        
        <div class="space-y-4 flex-grow relative z-10 w-full">
            <div>
                <h2 class="text-2xl font-black text-gray-900 leading-tight">{{ $company->name }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Mitra Aktif SPK</span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-gray-50">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        Kontak / Telepon
                    </span>
                    <p class="text-sm font-bold text-gray-800">{{ $company->phone ?: 'Belum diatur' }}</p>
                </div>
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Alamat Email
                    </span>
                    <p class="text-sm font-bold text-gray-800">{{ $company->email ?: 'Belum diatur' }}</p>
                </div>
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Lokasi / Alamat Lengkap
                    </span>
                    <p class="text-sm font-bold text-gray-800 line-clamp-2" title="{{ $company->address }}">{{ $company->address ?: 'Belum diatur' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/50 p-3 rounded-2xl border border-gray-100">
        <form method="GET" action="{{ route('admin.companies.show', $company->id) }}" class="flex items-center gap-3 w-full sm:w-auto">
            <label class="font-bold text-gray-500 text-xs uppercase tracking-wider whitespace-nowrap pl-2">Filter Kuota Tahun:</label>
            <select name="academic_year_id" onchange="this.form.submit()" class="block w-full sm:w-64 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 bg-white font-bold transition cursor-pointer hover:border-indigo-300">
                @foreach($allYears as $year)
                    <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                        {{ $year->name }} {{ $year->is_active ? '(Periode Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </form>

        <button onclick="document.getElementById('createSlotModal').classList.remove('hidden')" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm px-6 py-2.5 rounded-xl font-bold transition flex items-center justify-center text-sm gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Buka Gelombang Baru
        </button>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-4 rounded-xl shadow-sm font-medium text-sm flex items-center animate-fade-in">
            <span class="mr-2 text-xl">✅</span> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-sm overflow-hidden rounded-2xl border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Gelombang</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jurusan Diterima</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Syarat Khusus</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kuota (Siswa)</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jadwal Prakerin</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($slots as $slot)
                    <tr class="hover:bg-indigo-50/20 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-extrabold text-gray-900 text-sm flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                {{ $slot->batch_name }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1 max-w-[150px]">
                                @forelse($slot->majors as $major)
                                    <span class="text-[10px] font-black text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-md border border-indigo-100">
                                        {{ $major->code }}
                                    </span>
                                @empty
                                    <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200">Tidak ada jurusan</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-4 mb-2">
                                <div class="text-xs text-gray-500 font-medium">SMART: <strong class="text-gray-900 ml-1">&ge; {{ $slot->min_total_score }}</strong></div>
                                <div class="text-xs text-gray-500 font-medium">Absensi: <strong class="text-gray-900 ml-1">&ge; {{ $slot->min_absensi_score }}</strong></div>
                            </div>
                            
                            @if($slot->gender_requirement === 'P')
                                <span class="text-[10px] font-black text-pink-700 bg-pink-50 border border-pink-200 px-2 py-0.5 rounded">Khusus Perempuan</span>
                            @elseif($slot->gender_requirement === 'L')
                                <span class="text-[10px] font-black text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded">Khusus Laki-laki</span>
                            @else
                                <span class="text-[10px] font-black text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">Semua Gender</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-end gap-1">
                                <span class="font-black text-xl text-indigo-600">{{ $slot->quota }}</span> 
                                <span class="text-xs font-bold text-gray-400 mb-1">Orang</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $start = \Carbon\Carbon::parse($slot->start_date);
                                $end = \Carbon\Carbon::parse($slot->end_date);
                                $diffMonths = $start->diffInMonths($end) ?: 1;
                            @endphp
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg text-[10px] font-black border border-amber-100 mb-2">
                                ⏳ Durasi: {{ $diffMonths }} Bulan
                            </div>
                            <div class="text-[11px] font-bold text-gray-700 flex items-center gap-2 mb-1">
                                <span class="text-gray-400">🛫 Berangkat:</span> {{ $start->format('d M Y') }}
                            </div>
                            <div class="text-[11px] font-bold text-gray-700 flex items-center gap-2">
                                <span class="text-gray-400">🛬 Penarikan:</span> {{ $end->format('d M Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <form action="{{ route('admin.company_slots.destroy', $slot->id) }}" method="POST" onsubmit="return confirm('Hapus gelombang ini? Semua data terkait yang belum ACC mungkin terpengaruh.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 bg-white hover:bg-red-50 border border-transparent hover:border-red-100 p-2 rounded-xl transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center bg-gray-50/30">
                            <div class="flex flex-col items-center justify-center text-gray-400 space-y-3">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-700">Belum ada Gelombang</h4>
                                    <p class="text-xs text-gray-500 mt-1">Silakan klik "Buka Gelombang Baru" untuk menambahkan kuota.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="createSlotModal" class="hidden fixed inset-0 bg-gray-900/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full border border-gray-100 overflow-hidden my-8 animate-fade-in">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-900">Buka Kuota / Gelombang Baru</h3>
            <button onclick="document.getElementById('createSlotModal').classList.add('hidden')" class="text-gray-400 hover:text-red-500 font-bold text-xl transition transform hover:scale-110">&times;</button>
        </div>
        
        <form action="{{ route('admin.company_slots.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="company_id" value="{{ $company->id }}">
            <input type="hidden" name="academic_year_id" value="{{ $selectedYearId }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Gelombang (Batch) <span class="text-red-500">*</span></label>
                    <input type="text" name="batch_name" required placeholder="Cth: Gelombang 1 - 2026" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-white focus:bg-indigo-50/30 transition">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-3">Pilih Jurusan yang Diterima <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-gray-50/80 p-5 rounded-2xl border border-gray-200">
                        @foreach($majors as $major)
                            <label class="flex items-center p-3 bg-white rounded-xl border border-gray-200 cursor-pointer hover:border-indigo-400 hover:shadow-sm transition group">
                                <input type="checkbox" name="major_ids[]" value="{{ $major->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 transition">
                                <span class="ml-3 text-xs font-black text-gray-700 group-hover:text-indigo-700">{{ $major->code }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Persyaratan Gender <span class="text-red-500">*</span></label>
                    <select name="gender_requirement" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-white transition">
                        <option value="Semua">Semua Gender (L & P)</option>
                        <option value="L">Hanya Laki-laki (L)</option>
                        <option value="P">Hanya Perempuan (P)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-indigo-700 mb-2">Total Kuota (Siswa) <span class="text-red-500">*</span></label>
                    <input type="number" name="quota" required min="1" placeholder="Cth: 10" class="block w-full rounded-xl border-indigo-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-indigo-50/50 focus:bg-white transition font-bold text-indigo-900">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Min. Skor Kualitas <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="min_total_score" required min="0" value="0" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-white transition">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Min. Skor Kehadiran <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="min_absensi_score" required min="0" value="0" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-white transition">
                </div>

                <div class="md:col-span-2 pt-4 border-t border-gray-100 mt-2 grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pemberangkatan <span class="text-red-500">*</span></label>
                        <input type="date" id="input_start_date" name="start_date" required onchange="calculateEndDate()" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-white transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-amber-600 mb-2">Durasi (Bulan) <span class="text-red-500">*</span></label>
                        <input type="number" id="input_duration" min="1" max="12" placeholder="Cth: 3" oninput="calculateEndDate()" class="block w-full rounded-xl border-amber-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm px-4 py-3 bg-amber-50/50 focus:bg-white transition font-bold text-amber-900">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Penarikan <span class="font-normal italic">(Otomatis)</span></label>
                        <input type="date" id="input_end_date" name="end_date" required readonly class="block w-full rounded-xl border-gray-200 shadow-none text-sm px-4 py-3 bg-gray-100 text-gray-500 cursor-not-allowed font-medium outline-none pointer-events-none">
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-100 mt-2">
                <button type="button" onclick="document.getElementById('createSlotModal').classList.add('hidden')" class="w-full sm:w-auto bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-2.5 px-6 rounded-xl transition text-sm text-center">Batal</button>
                <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 text-sm text-center">Simpan Gelombang Baru</button>
            </div>
        </form>
    </div>
</div>

<script>
    function calculateEndDate() {
        const startDateInput = document.getElementById('input_start_date').value;
        const durationInput = document.getElementById('input_duration').value;
        const endDateField = document.getElementById('input_end_date');

        if (startDateInput && durationInput) {
            let date = new Date(startDateInput);
            date.setMonth(date.getMonth() + parseInt(durationInput));
            
            let year = date.getFullYear();
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let day = String(date.getDate()).padStart(2, '0');
            
            endDateField.value = `${year}-${month}-${day}`;
        } else {
            endDateField.value = '';
        }
    }
</script>
@endsection