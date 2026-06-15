@extends('layouts.hubin')

@section('title', 'Pengaturan Aplikasi')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 py-4">
    
    <div class="border-b border-gray-100 pb-4">
        <h1 class="text-2xl font-extrabold text-gray-900">⚙️ Pengaturan Aplikasi & Kop Surat</h1>
        <p class="text-sm text-gray-500 mt-1">Sesuaikan identitas sekolah, logo, dan data kepala sekolah untuk keperluan cetak dokumen SPK.</p>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-4 rounded-xl shadow-sm font-bold text-sm flex items-center animate-fade-in">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Berhasil: {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <div class="bg-white p-8 shadow-sm rounded-2xl border border-gray-100 h-fit">
                <header class="mb-6 border-b border-gray-50 pb-4">
                    <h2 class="text-lg font-bold text-gray-900">Identitas Sekolah (Untuk Kop PDF)</h2>
                </header>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Logo Sekolah</label>
                        @if(isset($setting) && $setting->logo_path)
                            <div class="mb-3 bg-gray-50 border border-gray-200 rounded-xl p-2 w-fit">
                                <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="Logo" class="h-16 object-contain">
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/jpeg,image/png" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-200 rounded-xl cursor-pointer transition">
                        <p class="text-[11px] text-gray-400 mt-1.5 font-medium">Format: JPG/PNG, Maks: 2MB.</p>
                        @error('logo') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Instansi Atas (Opsional)</label>
                        <input type="text" name="instansi_atas" value="{{ old('instansi_atas', $setting->instansi_atas ?? '') }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-3 bg-gray-50 focus:bg-white transition" placeholder="Cth: PEMERINTAH PROVINSI JAWA BARAT">
                        @error('instansi_atas') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Sekolah</label>
                        <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $setting->nama_sekolah ?? '') }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-3 bg-gray-50 focus:bg-white transition" placeholder="Cth: SMK Negeri 1 SPK">
                        @error('nama_sekolah') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Lengkap & Kontak</label>
                        <textarea name="alamat_sekolah" rows="3" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-3 bg-gray-50 focus:bg-white transition" placeholder="Cth: Jl. Pendidikan No. 1, Kota Belajar. Telp: (021) 123456">{{ old('alamat_sekolah', $setting->alamat_sekolah ?? '') }}</textarea>
                        @error('alamat_sekolah') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 shadow-sm rounded-2xl border border-gray-100 h-fit">
                <header class="mb-6 border-b border-gray-50 pb-4">
                    <h2 class="text-lg font-bold text-gray-900">Data Kepala Sekolah & Surat</h2>
                </header>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kepala Sekolah</label>
                        <input type="text" name="kepala_sekolah" value="{{ old('kepala_sekolah', $setting->nama_kepala_sekolah ?? '') }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-3 bg-gray-50 focus:bg-white transition" placeholder="Cth: Drs. H. Pendidik Utama, M.Pd.">
                        @error('kepala_sekolah') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                   <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">NIP / NUPTK Kepala Sekolah</label>
                        <input type="text" name="nip_kepala_sekolah" value="{{ old('nip_kepala_sekolah', $setting->nip_kepala_sekolah ?? '') }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-3 bg-gray-50 focus:bg-white transition" placeholder="Cth: 19800101 200501 1 001 (NIP) atau 123456789 (NUPTK)">
                        @error('nip_kepala_sekolah') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Teks Paragraf Pengantar Prakerin (Cetak PDF)</label>
                        <textarea name="teks_pengantar" rows="6" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-3 bg-gray-50 focus:bg-white transition leading-relaxed">{{ old('teks_pengantar', $setting->teks_pengantar_surat ?? 'Dalam rangka pelaksanaan program Pendidikan Sistem Ganda (PSG) dan untuk meningkatkan kompetensi lulusan Sekolah Menengah Kejuruan (SMK), kami memohon kesediaan Bapak/Ibu untuk menerima siswa kami melaksanakan Praktik Kerja Industri (Prakerin) di instansi/perusahaan yang Bapak/Ibu pimpin.') }}</textarea>
                        @error('teks_pengantar') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-xl shadow-lg font-extrabold transition-all hover:scale-[1.02] flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Simpan Konfigurasi
            </button>
        </div>
    </form>
</div>
@endsection