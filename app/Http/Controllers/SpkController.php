<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SmartEngineService;
use App\Models\AcademicYear;
use App\Models\Placement;
use App\Models\Company;
use App\Models\CompanySlot;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PlacementsExport;
use Maatwebsite\Excel\Facades\Excel;

class SpkController extends Controller
{
    protected $smartEngine;

    public function __construct(SmartEngineService $smartEngine)
    {
        $this->smartEngine = $smartEngine;
    }

    public function index(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->get('academic_year_id', $activeYear ? $activeYear->id : null);
        $allYears = AcademicYear::all();
        
        $placements = [];

        if ($selectedYearId) {
            // HANYA memuat siswa yang BELUM FINAL / Belum di-ACC
            $placements = Placement::where('academic_year_id', $selectedYearId)
                ->where(function($query) {
                    $query->where('status_pencocokan', '!=', 'final')
                          ->orWhereNull('status_pencocokan');
                })
                ->with(['student.major', 'student.assessment', 'company']) 
                ->paginate(10)
                ->withQueryString();
        }

        return view('admin.placements.index', compact('placements', 'activeYear', 'allYears', 'selectedYearId'));
    }

    public function generate(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        
        if (!$activeYear) {
            return back()->with('error', 'Tidak ada Tahun Ajaran yang sedang aktif.');
        }

        // 1. Ambil ID siswa-siswa yang status penempatannya SUDAH FINAL di periode ini
        $finalizedStudentIds = Placement::where('academic_year_id', $activeYear->id)
            ->where('status_pencocokan', 'final')
            ->pluck('student_id')
            ->toArray();

        // 2. Cek apakah ada siswa (yang BELUM FINAL) tapi belum punya nilai
        $studentsWithoutAssessment = Student::where('academic_year_id', $activeYear->id)
            ->whereNotIn('id', $finalizedStudentIds)
            ->doesntHave('assessment')
            ->count();

        if ($studentsWithoutAssessment > 0) {
            return back()->with('error', "Gagal memproses! Terdapat {$studentsWithoutAssessment} siswa (yang belum final) belum memiliki nilai evaluasi. Harap lengkapi nilai seluruh siswa terlebih dahulu.");
        }

        try {
            DB::beginTransaction();

            // 3. Hapus rekam jejak rekomendasi lama, KECUALI yang sudah final
            Placement::where('academic_year_id', $activeYear->id)
                ->where('status_pencocokan', '!=', 'final')
                ->delete();

            // 4. Panggil Service Engine
            $this->smartEngine->runMatchmaking($activeYear->id, $finalizedStudentIds);

            DB::commit();

            return redirect()->route('admin.placements.index')
                             ->with('success', 'Kalkulasi SPK berhasil diperbarui! Siswa yang berstatus FINAL aman tidak terganggu.');
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function edit(Placement $placement)
    {
        $placement->load(['student.major', 'student.assessment', 'company', 'companySlot']);
        
        $studentGender = $placement->student->gender;

        $allSlots = CompanySlot::with('company')
            ->withCount(['placements as terisi' => function ($query) {
                $query->where('status_pencocokan', 'final');
            }])
            ->where('academic_year_id', $placement->academic_year_id)
            ->where(function($q) use ($studentGender) {
                $q->where('gender_requirement', 'Semua')
                  ->orWhere('gender_requirement', $studentGender);
            })
            ->get()
            ->filter(function($slot) {
                return ($slot->quota - $slot->terisi) > 0;
            });

        return view('admin.placements.edit', compact('placement', 'allSlots'));
    }

    public function update(Request $request, Placement $placement)
    {
        $validated = $request->validate([
            'action_type' => 'required|in:move,cancel',
            'company_slot_id' => 'required_if:action_type,move',
            'notes' => 'required|string'
        ]);

        // ==========================================
        // TINDAKAN 1: BATALKAN PENEMPATAN
        // ==========================================
        if ($validated['action_type'] === 'cancel') {
            
            // Logika Cerdas: Jika sebelumnya siswa punya perusahaan (berarti lolos kualifikasi),
            // kembalikan ke Waiting List. Jika dari awal memang tidak lulus, tetapkan di Pembinaan.
            $newStatus = ($placement->company_id !== null) ? 'waiting_list' : 'pembinaan';

            $placement->update([
                'company_id' => null,
                'company_slot_id' => null,
                'status_pencocokan' => $newStatus,
                'placement_method' => 'MANUAL_OVERRIDE',
                'notes' => "DIBATALKAN KEPALA HUBIN:\n" . $validated['notes'],
            ]);

            if ($placement->student) {
                $placement->student->update(['status' => $newStatus]);
            }

            $pesan = $newStatus === 'waiting_list' 
                     ? 'Penempatan dibatalkan. Karena nilai mencukupi, siswa dialihkan ke status Waiting List.' 
                     : 'Intervensi dibatalkan. Siswa dikembalikan ke daftar Pembinaan.';
                     
            return redirect()->route('admin.placements.index')->with('success', $pesan);
        }

        // ==========================================
        // TINDAKAN 2: PINDAH KE PT / GELOMBANG LAIN
        // ==========================================
        if ($validated['action_type'] === 'move') {
            $slot = CompanySlot::findOrFail($validated['company_slot_id']);
            $studentGender = $placement->student->gender;

            if ($slot->gender_requirement !== 'Semua' && $slot->gender_requirement !== $studentGender) {
                return back()->with('error', 'Gagal: Gender siswa tidak memenuhi syarat perusahaan ini.')->withInput();
            }

            $terisi = \App\Models\Placement::where('company_slot_id', $slot->id)
                ->where('status_pencocokan', 'final')
                ->count();
                
            if (($slot->quota - $terisi) <= 0) {
                return back()->with('error', 'Gagal: Kuota untuk industri ini sudah penuh, silakan pilih industri lain.')->withInput();
            }

            $placement->update([
                'company_id' => $slot->company_id,
                'company_slot_id' => $slot->id,
                'status_pencocokan' => 'rekomendasi',
                'placement_method' => 'MANUAL_OVERRIDE',
                'notes' => "DIPINDAHKAN KEPALA HUBIN:\n" . $validated['notes']
            ]);

            if ($placement->student) {
                $placement->student->update(['status' => 'proses_seleksi']);
            }

            return redirect()->route('admin.placements.index')->with('success', 'Siswa berhasil dipindahkan ke Perusahaan baru. Silakan klik ACC Hubin untuk mem-Final-kan.');
        }
    }

    public function accHubin(Placement $placement)
    {
        if ($placement->status_pencocokan === 'rekomendasi') {
            $placement->update([
                'status_pencocokan' => 'final'
            ]);

            if ($placement->student) {
                $placement->student->update(['status' => 'lolos_prakerin']);
            }

            return redirect()->back()->with('success', 'Penempatan siswa berhasil di-ACC (Final).');
        }

        return redirect()->back()->with('error', 'Hanya status Rekomendasi yang dapat di-ACC.');
    }

    public function exportExcel(Request $request)
    {
        $academicYearId = $request->get('academic_year_id', AcademicYear::where('is_active', true)->first()->id ?? 1);
        $filename = 'Rekap_Draft_Penempatan_Periode_' . date('Y_m_d_His') . '.xlsx';
        return Excel::download(new PlacementsExport($academicYearId), $filename);
    }

    public function printPdf(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->get('academic_year_id', $activeYear ? $activeYear->id : null);

        if (!$selectedYearId) {
            return back()->with('error', 'Tidak ada data Tahun Ajaran untuk dicetak.');
        }

        $placements = Placement::where('academic_year_id', $selectedYearId)
            ->with(['student.major', 'company'])
            ->orderBy('final_smart_score', 'desc')
            ->get();

        $selectedYear = AcademicYear::find($selectedYearId);

        $pdf = Pdf::loadView('admin.placements.pdf', compact('placements', 'selectedYear'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Draft_Penempatan_Prakerin_' . str_replace(['/', '\\'], '-', $selectedYear->name) . '.pdf');
    }
}