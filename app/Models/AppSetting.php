<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'instansi_atas', 
        'nama_sekolah', 
        'alamat_sekolah', 
        'kontak_sekolah', 
        'logo_path', 
        'nama_kepala_sekolah', 
        'nip_kepala_sekolah', 
        'teks_pengantar_surat',
        'wa_template_message',
    ];
}