<?php

namespace App\Services;

use App\Models\Placement;
use App\Models\Student;
use App\Models\Criterion;
use App\Models\CompanySlot;
use Illuminate\Support\Facades\DB;

class SmartEngineService
{
    private $bounds = ['min' => 0, 'max' => 100];

    private function getNormalizedWeights(): array
    {
        $criterias = Criterion::all();
        $totalWeight = $criterias->sum('weight');
        $totalWeight = $totalWeight > 0 ? $totalWeight : 1.0;

        $normalized = [];
        foreach ($criterias as $criterion) {
            $normalized[$criterion->id] = $criterion->weight / $totalWeight;
        }
        return $normalized;
    }

    public function calculateScore($assessment): float
    {
        $weights = $this->getNormalizedWeights();
        $criterias = Criterion::all(); 
        $score = 0;
        $scoresData = $assessment->scores_data ?? [];

        foreach ($criterias as $crit) {
            $id = $crit->id;
            $type = strtolower($crit->type);
            $weight = $weights[$id] ?? 0;
            $val = $scoresData[$id] ?? 0;

            if ($type === 'benefit') {
                $utility = ($val - $this->bounds['min']) / ($this->bounds['max'] - $this->bounds['min']);
                $score += $utility * $weight;
            } elseif ($type === 'cost') {
                $utility = ($this->bounds['max'] - $val) / ($this->bounds['max'] - $this->bounds['min']);
                $score += $utility * $weight;
            }
        }
        return round($score * 100, 2);
    }

    private function getAbsensiScore($assessment): float
    {
        $scoresData = $assessment->scores_data ?? [];
        $absensiCriterion = Criterion::where('name', 'like', '%absen%')
                                     ->orWhere('name', 'like', '%hadir%')
                                     ->first();

        if ($absensiCriterion) {
            return (float) ($scoresData[$absensiCriterion->id] ?? 0);
        }
        return 0;
    }

    public function runMatchmaking($academicYearId)
    {
        DB::beginTransaction();
        try {
            // 1. CLEANUP: Hapus rekaman sistem lama (kecuali yang FINAL / DI-ACC)
            Placement::where('academic_year_id', $academicYearId)
                     ->where(function($query) {
                         $query->where('status_pencocokan', '!=', 'final')
                               ->orWhereNull('status_pencocokan');
                     })
                     ->where('placement_method', 'SYSTEM') 
                     ->delete();

            Student::where('academic_year_id', $academicYearId)
                   ->whereDoesntHave('placement', function($query) {
                       $query->where('placement_method', 'MANUAL_OVERRIDE')
                             ->orWhere('status_pencocokan', 'final');
                   })
                   ->update(['status' => 'belum_prakerin']); 
                   
            $students = Student::where('academic_year_id', $academicYearId)
                ->where('status', '!=', 'lolos_prakerin')
                ->with(['assessment', 'major'])
                ->get();

            // Load Slot dengan relasi Majors (Many-to-Many)
            $companySlots = CompanySlot::with(['company', 'majors'])->where('academic_year_id', $academicYearId)->get();
            
            foreach ($companySlots as $slot) {
                $used = Placement::where('company_slot_id', $slot->id)
                                 ->where('status_pencocokan', 'final')
                                 ->count();
                $slot->available_quota = max(0, $slot->quota - $used);
            }

            foreach ($students as $student) {
                if (!$student->assessment) {
                    $student->final_score = 0;
                    $student->absensi_score = 0;
                    continue; 
                }
                $student->final_score = $this->calculateScore($student->assessment);
                $student->absensi_score = $this->getAbsensiScore($student->assessment);
            }

            $students = $students->sortByDesc('final_score');

            // 2. PROSES PENEMPATAN
            foreach ($students as $student) {
                $isPlaced = false;
                $failReasons = [];
                $passedScoreAtLeastOnce = false; // Flag untuk Waiting List

                // FILTER SUPER KETAT: Menyaring Jurusan DAN Gender di Awal!
                $relevantSlots = $companySlots->filter(function($slot) use ($student) {
                    // 1. Filter Jurusan
                    $matchMajor = $slot->majors->contains($student->major_id);
                    // 2. Filter Gender secara ketat (Abaikan slot lawan jenis)
                    $matchGender = ($slot->gender_requirement === 'Semua' || $slot->gender_requirement === $student->gender);
                    
                    return $matchMajor && $matchGender;
                });

                if ($relevantSlots->isEmpty()) {
                    $failReasons[] = "- Tidak ada mitra industri yang membuka kuota untuk Jurusan dan Gender Anda saat ini.";
                    $passedScoreAtLeastOnce = false;
                } else {
                    $companyReasons = [];

                    foreach ($relevantSlots as $slot) {
                        $res = $this->tryPlaceStudentWithReason($student, $slot, $academicYearId);
                        
                        if ($res['status'] === true) {
                            $isPlaced = true;
                            break; 
                        } else {
                            if ($res['type'] === 'kuota') {
                                $passedScoreAtLeastOnce = true; // Skor oke, tapi kuota habis
                            }
                            
                            $companyReasons[$slot->company_id][] = [
                                'company_name' => $slot->company->name,
                                'batch_name'   => $slot->batch_name ?? 'Utama',
                                'type'         => $res['type'],
                                'message'      => $res['message']
                            ];
                        }
                    }

                    if (!$isPlaced) {
                        // Saring alasan yang terkumpul (Tanpa alasan gender karena sudah difilter di awal)
                        foreach ($companyReasons as $compId => $reasons) {
                            foreach ($reasons as $r) {
                                $failReasons[] = "- {$r['company_name']} ({$r['batch_name']}): {$r['message']}";
                            }
                        }
                    }
                }

                if (!$isPlaced) {
                    // Logic: Lolos nilai tapi kuota habis = Waiting List. Tidak lolos nilai = Pembinaan.
                    $placementStatus = $passedScoreAtLeastOnce ? 'waiting_list' : 'pembinaan';

                    Placement::create([
                        'student_id'        => $student->id,
                        'company_id'        => null,
                        'company_slot_id'   => null,
                        'final_smart_score' => $student->final_score,
                        'placement_method'  => 'SYSTEM',
                        'status_pencocokan' => $placementStatus,
                        'notes'             => "ALASAN DETAIL SISTEM:\n" . implode("\n", $failReasons),
                        'academic_year_id'  => $academicYearId
                    ]);
                    
                    \App\Models\Student::where('id', $student->id)->update(['status' => $placementStatus]);
                    $student->status = $placementStatus;
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function tryPlaceStudentWithReason($student, $companySlot, $academicYearId)
    {
        // 1. CEK SKOR KUALITAS
        // (Catatan: Pengecekan Gender dihapus dari sini karena sudah difilter di atas)
        if ($student->final_score < $companySlot->min_total_score) {
            return ['status' => false, 'type' => 'score', 'message' => "Skor SMART Anda (" . number_format($student->final_score, 2) . ") belum memenuhi standar industri (Min: " . number_format($companySlot->min_total_score, 2) . ")."];
        }
        if ($student->absensi_score < $companySlot->min_absensi_score) {
            return ['status' => false, 'type' => 'score', 'message' => "Skor Absensi/Kehadiran Anda ({$student->absensi_score}) kurang dari standar (Min: {$companySlot->min_absensi_score})."];
        }

        // 2. CEK KUOTA
        if ($companySlot->available_quota <= 0) {
            return ['status' => false, 'type' => 'kuota', 'message' => "Nilai memenuhi syarat, tetapi kuota penuh."];
        }

        // LOLOS
        Placement::create([
            'student_id'        => $student->id,
            'company_id'        => $companySlot->company_id, 
            'company_slot_id'   => $companySlot->id,
            'final_smart_score' => $student->final_score,
            'placement_method'  => 'SYSTEM',
            'status_pencocokan' => 'rekomendasi',
            'academic_year_id'  => $academicYearId
        ]);

        $companySlot->available_quota--; 
        \App\Models\Student::where('id', $student->id)->update(['status' => 'proses_seleksi']);
        $student->status = 'proses_seleksi';

        return ['status' => true];
    }
}