<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SPK Prakerin - {{ $setting->nama_sekolah ?? 'SMK' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-800">
    <nav class="bg-white shadow-sm border-b px-6 py-4 flex justify-between items-center">
        <div class="font-bold text-xl text-indigo-700 flex items-center gap-3">
            @if(!empty($setting->logo_path))
                <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="Logo {{ $setting->nama_sekolah }}" class="h-10 w-auto object-contain">
            @else
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            @endif
            <span class="tracking-tight">{{ $setting->nama_sekolah ?? 'Sistem SPK Prakerin' }}</span>
        </div>
        
        <div>
            @auth
                <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-indigo-600 px-4">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md transition duration-150">Masuk (Hubin)</a>
            @endauth
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900 mb-4">
            Sistem Pendukung Keputusan <br> <span class="text-indigo-600">Penempatan Prakerin</span>
        </h1>
        <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
            Membantu Hubungan Industri (Hubin) dalam mendistribusikan siswa secara transparan, akurat, dan efisien menggunakan perhitungan Metode SMART.
        </p>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 mb-20">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Cek Penempatan Mandiri Siswa</h2>
            <p class="text-center text-gray-500 mb-6 text-sm">Masukkan NISN Anda untuk mengecek status penempatan Praktik Kerja Lapangan (PKL).</p>
            
            <form action="{{ route('welcome') }}" method="GET" class="flex gap-3">
                <input type="text" name="nisn" value="{{ request('nisn') }}" placeholder="Ketik NISN di sini..." required class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg py-3 px-4">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-md transition duration-150 shadow-md">
                    Lacak
                </button>
            </form>

            <div class="mt-6">
                @if(isset($searchPerformed) && $searchPerformed)
                    <div id="hasil-pencarian" class="animate-fade-in scroll-mt-24">
                        @if($student)
                            @if($placement)
                                <div class="bg-white rounded-2xl border-2 border-emerald-500 shadow-md overflow-hidden text-left relative transition-all hover:shadow-lg mt-4">
                                    <div class="bg-emerald-500 px-6 py-4 flex items-center gap-4">
                                        <div class="bg-white/20 p-2 rounded-full">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-black text-white leading-tight">Selamat! Kamu Lolos Penempatan</h3>
                                            <p class="text-emerald-100 text-xs mt-0.5">Data Prakerin kamu sudah berstatus FINAL.</p>
                                        </div>
                                    </div>
                                    <div class="p-6 space-y-5">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-1">Nama Lengkap</p>
                                                <p class="text-lg font-black text-gray-900">{{ $student->name }}</p>
                                                <p class="text-sm text-gray-600 font-medium">NISN: {{ $student->nisn }}</p>
                                            </div>
                                            <div class="bg-indigo-50 p-3 rounded-xl border border-indigo-100">
                                                <p class="text-[11px] font-bold text-indigo-500 uppercase tracking-widest mb-1">Ditempatkan Di</p>
                                                <p class="text-base font-black text-indigo-700 leading-tight">{{ $placement->company->name ?? '-' }}</p>
                                                <p class="text-xs text-indigo-600 mt-1 font-semibold border-t border-indigo-200/50 pt-1">Gelombang: {{ $placement->companySlot->batch_name ?? 'Reguler' }}</p>
                                            </div>
                                        </div>
                                        <div class="pt-4 border-t border-gray-100 flex items-start gap-3">
                                            <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="text-xs text-gray-500 font-medium leading-relaxed">Silakan tunggu arahan dari pihak Hubin atau admin sekolah untuk pengambilan Surat Pengantar Prakerin kamu ke pihak industri.</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-white rounded-2xl border border-yellow-300 shadow-sm p-6 text-center transition-all hover:shadow-md relative overflow-hidden mt-4">
                                    <div class="absolute top-0 left-0 w-full h-1 bg-yellow-400"></div>
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 mb-4 ring-4 ring-yellow-50">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <h3 class="text-lg font-black text-gray-900 mb-1">Halo, {{ $student->name }}!</h3>
                                    <p class="text-gray-600 text-sm font-medium leading-relaxed max-w-xl mx-auto">Data kamu ditemukan, namun saat ini <span class="font-bold text-yellow-600 bg-yellow-50 px-1 py-0.5 rounded">belum ada penempatan FINAL</span> untukmu. Silakan cek kembali secara berkala.</p>
                                </div>
                            @endif
                        @else
                            <div class="bg-white rounded-2xl border border-red-200 shadow-sm p-6 text-center transition-all hover:shadow-md relative overflow-hidden mt-4">
                                <div class="absolute top-0 left-0 w-full h-1 bg-red-500"></div>
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-100 text-red-500 mb-4 ring-4 ring-red-50">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <h3 class="text-lg font-black text-gray-900 mb-1">Data Tidak Ditemukan</h3>
                                <p class="text-gray-600 text-sm font-medium leading-relaxed max-w-xl mx-auto">Kami tidak menemukan siswa dengan NISN <span class="font-bold text-red-500 border-b border-red-500">{{ $nisn }}</span>. Pastikan angka yang kamu masukkan sudah benar.</p>
                            </div>
                        @endif
                    </div>
                    
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const resultElement = document.getElementById('hasil-pencarian');
                            if(resultElement) {
                                resultElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        });
                    </script>
                @endif
            </div>
        </div>
    </div>
</body>
</html>