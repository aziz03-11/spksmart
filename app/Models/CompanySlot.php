<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanySlot extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'academic_year_id',
        // 'major_id' dihapus karena sekarang menggunakan tabel pivot company_slot_major
        'batch_name',
        'gender_requirement',
        'quota',
        'min_total_score',
        'min_absensi_score',
        'start_date',
        'end_date',
        'quota_male',   
        'quota_female', 
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Relasi Many-to-Many ke Major
     * 1 Slot bisa memiliki banyak jurusan
     */
    public function majors()
    {
        return $this->belongsToMany(Major::class, 'company_slot_major', 'company_slot_id', 'major_id');
    }

    /**
     * Relasi One-to-Many ke Placement
     * 1 Slot bisa ditempati oleh banyak penempatan siswa
     */
    public function placements()
    {
        return $this->hasMany(Placement::class);
    }
}