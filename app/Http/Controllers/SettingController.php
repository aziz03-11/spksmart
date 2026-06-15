<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        // Ambil data setting pertama (karena kita hanya punya 1 baris setting dari seeder)
        $setting = AppSetting::firstOrCreate(['id' => 1]);
        return view('admin.settings.edit', compact('setting'));
    }

   public function update(Request $request)
    {
        // Validasi disesuaikan dengan 'name' yang ada pada tag form HTML
        $request->validate([
            'instansi_atas' => 'nullable|string|max:255',
            'nama_sekolah' => 'required|string|max:255',
            'alamat_sekolah' => 'nullable|string',
            'kontak_sekolah' => 'nullable|string|max:255',
            'kepala_sekolah' => 'nullable|string|max:255', // Memakai kepala_sekolah
            'nip_kepala_sekolah' => 'nullable|string|max:255',
            'teks_pengantar' => 'nullable|string', // Memakai teks_pengantar
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        $setting = AppSetting::first();

        // Handle Upload Logo
        if ($request->hasFile('logo')) {
            if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $setting->logo_path = $path;
        }

        // Mapping agar masuk ke kolom Database yang benar
        $setting->update([
            'instansi_atas' => $request->instansi_atas,
            'nama_sekolah' => $request->nama_sekolah,
            'alamat_sekolah' => $request->alamat_sekolah,
            'kontak_sekolah' => $request->kontak_sekolah,
            'nama_kepala_sekolah' => $request->kepala_sekolah, // disinkronkan ke nama_kepala_sekolah
            'nip_kepala_sekolah' => $request->nip_kepala_sekolah,
            'teks_pengantar_surat' => $request->teks_pengantar, // disinkronkan ke teks_pengantar_surat
        ]);

        return redirect()->route('admin.settings.edit')->with('success', 'Pengaturan Aplikasi dan Kop Surat berhasil diperbarui!');
    }
}