<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Placement extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'student_id', 
        'company_id', 
        'company_slot_id', 
        'academic_year_id', 
        'final_smart_score', 
        'placement_method',
        'status_pencocokan', // rekomendasi, final, waiting_list, pembinaan
        'is_manual_override', // log audit manual (boolean)
        'override_reason', // alasan ubah manual
        'notes' // alasan gagal SPK
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function companySlot()
    {
        return $this->belongsTo(CompanySlot::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}