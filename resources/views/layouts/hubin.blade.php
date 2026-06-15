<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Hubin') - SPK Prakerin</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900 flex flex-col min-h-screen selection:bg-indigo-100 selection:text-indigo-900">

    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                
                <div class="flex items-center gap-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group transition">
                        @if(isset($appSetting) && $appSetting->logo_path)
                            <img src="{{ asset('storage/' . $appSetting->logo_path) }}" alt="Logo Sekolah" class="w-10 h-10 object-contain group-hover:scale-105 transition-transform duration-200">
                        @else
                            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200 group-hover:scale-105 transition-transform duration-200">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                        @endif
                        <span class="font-extrabold text-xl tracking-tight bg-gradient-to-r from-indigo-700 to-blue-600 bg-clip-text text-transparent hidden sm:block">
                            {{ isset($appSetting) && $appSetting->nama_sekolah ? $appSetting->nama_sekolah : 'HubinSmart' }}
                        </span>
                    </a>

                    <div class="hidden xl:flex items-center gap-1">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2.5 rounded-xl text-sm font-bold flex items-center gap-1.5 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 shadow-inner' : 'text-gray-500 hover:bg-gray-50 hover:text-indigo-600' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Dasbor
                        </a>
                        
                        <div class="relative">
                            <button id="spkDataBtn" class="px-3 py-2.5 rounded-xl text-sm font-bold flex items-center gap-1.5 transition-all duration-200 {{ request()->routeIs('admin.placements.*') ? 'bg-indigo-50 text-indigo-700 shadow-inner' : 'text-gray-500 hover:bg-gray-50 hover:text-indigo-600' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                Manajemen SPK
                                <svg id="spkDataArrow" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div id="spkDataMenu" class="hidden absolute left-0 mt-2 w-56 bg-white border border-gray-100 rounded-xl shadow-xl z-50 overflow-hidden transform origin-top-left transition-all">
                                <a href="{{ route('admin.placements.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold {{ request()->routeIs('admin.placements.index') ? 'bg-indigo-50 text-indigo-700 border-l-2 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Proses SPK Aktif
                                </a>
                                <a href="{{ route('admin.placements.history') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold {{ request()->routeIs('admin.placements.history') ? 'bg-indigo-50 text-indigo-700 border-l-2 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                    Riwayat Penempatan
                                </a>
                            </div>
                        </div>

                        <div class="relative">
                            <button id="masterDataBtn" class="px-3 py-2.5 rounded-xl text-sm font-bold flex items-center gap-1.5 transition-all duration-200 {{ request()->routeIs('admin.academic_years.*', 'admin.majors.*', 'admin.criterias.*', 'admin.students.*') ? 'bg-indigo-50 text-indigo-700 shadow-inner' : 'text-gray-500 hover:bg-gray-50 hover:text-indigo-600' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                                Master Data
                                <svg id="masterDataArrow" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div id="masterDataMenu" class="hidden absolute left-0 mt-2 w-52 bg-white border border-gray-100 rounded-xl shadow-xl z-50 overflow-hidden transform origin-top-left transition-all">
                                <a href="{{ route('admin.academic_years.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold {{ request()->routeIs('admin.academic_years.*') ? 'bg-indigo-50 text-indigo-700 border-l-2 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Periode / Tahun
                                </a>
                                <a href="{{ route('admin.majors.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold {{ request()->routeIs('admin.majors.*') ? 'bg-indigo-50 text-indigo-700 border-l-2 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"></path><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                                    Jurusan Sekolah
                                </a>
                                <a href="{{ route('admin.students.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold {{ request()->routeIs('admin.students.*') ? 'bg-indigo-50 text-indigo-700 border-l-2 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    Siswa & Nilai
                                </a>
                                <a href="{{ route('admin.criterias.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold {{ request()->routeIs('admin.criterias.*') ? 'bg-indigo-50 text-indigo-700 border-l-2 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                                    Kriteria SMART
                                </a>
                            </div>
                        </div>

                        <a href="{{ route('admin.companies.index') }}" class="px-3 py-2.5 rounded-xl text-sm font-bold flex items-center gap-1.5 transition-all duration-200 {{ request()->routeIs('admin.companies.*', 'admin.company_slots.*') ? 'bg-indigo-50 text-indigo-700 shadow-inner' : 'text-gray-500 hover:bg-gray-50 hover:text-indigo-600' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Industri Mitra
                        </a>
                    </div>
                </div>

                <div class="relative ml-4">
                    <button id="profileDropdownBtn" class="flex items-center gap-3 focus:outline-none hover:bg-indigo-50/70 p-1.5 pr-3 rounded-2xl transition-all border border-transparent hover:border-indigo-100">
                        <div class="text-right hidden lg:block">
                            <div class="text-sm font-extrabold text-gray-900">{{ Auth::user()->name ?? 'Administrator' }}</div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-100 to-blue-50 flex items-center justify-center text-indigo-700 font-black text-lg border border-indigo-200 shadow-sm">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" id="dropdownArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="profileDropdownMenu" class="hidden absolute right-0 mt-2 w-64 bg-white border border-gray-100 rounded-xl shadow-xl z-50 overflow-hidden transform origin-top-right transition-all">
                        <div class="px-4 py-3 border-b border-gray-100 lg:hidden">
                            <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name ?? 'Administrator' }}</p>
                        </div>

                        <div class="xl:hidden border-b border-gray-100 py-2 bg-gray-50/50 space-y-1">
                             <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold {{ request()->routeIs('dashboard') ? 'text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-700' }}">
                                 <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                 Dasbor Utama
                             </a>
                             
                             <div class="px-4 py-2 mt-1 text-[10px] font-black text-gray-400 uppercase tracking-widest border-t border-gray-100 pt-3">Manajemen SPK</div>
                             <a href="{{ route('admin.placements.index') }}" class="flex items-center gap-2 px-6 py-2 text-sm font-semibold {{ request()->routeIs('admin.placements.index') ? 'text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-700' }}">
                                 <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                 Proses SPK Aktif
                             </a>
                             <a href="{{ route('admin.placements.history') }}" class="flex items-center gap-2 px-6 py-2 text-sm font-semibold {{ request()->routeIs('admin.placements.history') ? 'text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-700' }}">
                                 <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                 Riwayat Penempatan
                             </a>

                             <div class="px-4 py-2 mt-2 text-[10px] font-black text-gray-400 uppercase tracking-widest border-t border-gray-100 pt-3">Master Data</div>
                             <a href="{{ route('admin.academic_years.index') }}" class="flex items-center gap-2 px-6 py-2 text-sm font-semibold {{ request()->routeIs('admin.academic_years.*') ? 'text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-700' }}">
                                 <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                 Periode Tahun
                             </a>
                             <a href="{{ route('admin.majors.index') }}" class="flex items-center gap-2 px-6 py-2 text-sm font-semibold {{ request()->routeIs('admin.majors.*') ? 'text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-700' }}">
                                 <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"></path><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                                 Jurusan
                             </a>
                             <a href="{{ route('admin.students.index') }}" class="flex items-center gap-2 px-6 py-2 text-sm font-semibold {{ request()->routeIs('admin.students.*') ? 'text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-700' }}">
                                 <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                 Siswa & Nilai
                             </a>
                             <a href="{{ route('admin.criterias.index') }}" class="flex items-center gap-2 px-6 py-2 text-sm font-semibold {{ request()->routeIs('admin.criterias.*') ? 'text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-700' }}">
                                 <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                                 Kriteria SMART
                             </a>

                             <div class="px-4 py-2 mt-2 text-[10px] font-black text-gray-400 uppercase tracking-widest border-t border-gray-100 pt-3">Lainnya</div>
                             <a href="{{ route('admin.companies.index') }}" class="flex items-center gap-2 px-6 py-2 text-sm font-semibold {{ request()->routeIs('admin.companies.*', 'admin.company_slots.*') ? 'text-indigo-700' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-700' }} pb-3">
                                 <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                 Industri Mitra
                             </a>
                        </div>
                        
                        <div class="border-t border-gray-100"></div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 font-semibold transition">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Profil Akun
                        </a>

                        <a href="{{ route('admin.settings.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 font-semibold transition">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Pengaturan Aplikasi
                        </a>
                        
                        <div class="border-t border-gray-100"></div>
                        
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 font-bold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Keluar Sistem
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex-grow w-full">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-indigo-100 rounded flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <p class="text-sm text-gray-600 font-medium">© {{ date('Y') }} SPK Prakerin Metode SMART.</p>
            </div>
            <div class="text-sm font-semibold text-gray-400">
                Sistem Pendukung Keputusan Penempatan Industri
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Script Profil
            const profileBtn = document.getElementById('profileDropdownBtn');
            const profileMenu = document.getElementById('profileDropdownMenu');
            const profileArrow = document.getElementById('dropdownArrow');

            // Script Master Data
            const masterBtn = document.getElementById('masterDataBtn');
            const masterMenu = document.getElementById('masterDataMenu');
            const masterArrow = document.getElementById('masterDataArrow');
            
            // Script Manajemen SPK
            const spkBtn = document.getElementById('spkDataBtn');
            const spkMenu = document.getElementById('spkDataMenu');
            const spkArrow = document.getElementById('spkDataArrow');

            function closeAllDropdowns(except = null) {
                if(except !== 'profile' && profileMenu && !profileMenu.classList.contains('hidden')) {
                    profileMenu.classList.add('hidden');
                    if(profileArrow) profileArrow.style.transform = 'rotate(0deg)';
                }
                if(except !== 'master' && masterMenu && !masterMenu.classList.contains('hidden')) {
                    masterMenu.classList.add('hidden');
                    if(masterArrow) masterArrow.style.transform = 'rotate(0deg)';
                }
                if(except !== 'spk' && spkMenu && !spkMenu.classList.contains('hidden')) {
                    spkMenu.classList.add('hidden');
                    if(spkArrow) spkArrow.style.transform = 'rotate(0deg)';
                }
            }

            // Toggle Profil
            if (profileBtn && profileMenu) {
                profileBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    closeAllDropdowns('profile');
                    const isHidden = profileMenu.classList.contains('hidden');
                    if (isHidden) {
                        profileMenu.classList.remove('hidden');
                        if(profileArrow) profileArrow.style.transform = 'rotate(180deg)';
                    } else {
                        profileMenu.classList.add('hidden');
                        if(profileArrow) profileArrow.style.transform = 'rotate(0deg)';
                    }
                });
            }

            // Toggle Master Data
            if (masterBtn && masterMenu) {
                masterBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    closeAllDropdowns('master');
                    const isHidden = masterMenu.classList.contains('hidden');
                    if (isHidden) {
                        masterMenu.classList.remove('hidden');
                        if(masterArrow) masterArrow.style.transform = 'rotate(180deg)';
                    } else {
                        masterMenu.classList.add('hidden');
                        if(masterArrow) masterArrow.style.transform = 'rotate(0deg)';
                    }
                });
            }
            
            // Toggle Manajemen SPK
            if (spkBtn && spkMenu) {
                spkBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    closeAllDropdowns('spk');
                    const isHidden = spkMenu.classList.contains('hidden');
                    if (isHidden) {
                        spkMenu.classList.remove('hidden');
                        if(spkArrow) spkArrow.style.transform = 'rotate(180deg)';
                    } else {
                        spkMenu.classList.add('hidden');
                        if(spkArrow) spkArrow.style.transform = 'rotate(0deg)';
                    }
                });
            }

            // Tutup semua saat klik area luar
            document.addEventListener('click', function (e) {
                if (
                    (!profileBtn || !profileBtn.contains(e.target)) && (!profileMenu || !profileMenu.contains(e.target)) &&
                    (!masterBtn || !masterBtn.contains(e.target)) && (!masterMenu || !masterMenu.contains(e.target)) &&
                    (!spkBtn || !spkBtn.contains(e.target)) && (!spkMenu || !spkMenu.contains(e.target))
                ) {
                    closeAllDropdowns();
                }
            });
        });
    </script>
</body>
</html>