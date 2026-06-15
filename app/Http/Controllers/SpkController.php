<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SmartEngineService;
use App\Models\AcademicYear;
use App\Models\Placement;
use App\Models\Company;
use App\Models\CompanySlot;
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

        $studentsWithoutAssessment = \App\Models\Student::where('academic_year_id', $activeYear->id)
                                        ->doesntHave('assessment')
                                        ->count();

        if ($studentsWithoutAssessment > 0) {
            return back()->with('error', "Gagal memproses! Terdapat {$studentsWithoutAssessment} siswa yang belum memiliki nilai. Harap lengkapi nilai seluruh siswa terlebih dahulu.");
        }

        try {
            $this->smartEngine->runMatchmaking($activeYear->id);

            return redirect()->route('admin.placements.index')
                             ->with('success', 'Kalkulasi SPK dan pencocokan industri berhasil diselesaikan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // ========================================================
    // FITUR: MANUAL OVERRIDE (INTERVENSI MANUAL)
    // ========================================================

    // ========================================================
    // FITUR: MANUAL OVERRIDE (INTERVENSI MANUAL)
    // ========================================================

    public function edit(Placement $placement)
    {
        $placement->load(['student.major', 'student.assessment', 'company', 'companySlot']);
        
        $studentGender = $placement->student->gender;

        // Load slot beserta jumlah yang terisi, lalu langsung FILTER di level query dan koleksi
        $companySlots = CompanySlot::with('company')
            ->withCount(['placements as terisi' => function ($query) {
                $query->where('status_pencocokan', 'final');
            }])
            ->where('academic_year_id', $placement->academic_year_id)
            // 1. FILTER GENDER: Tampilkan yang 'Semua' atau yang sesuai dengan gender siswa
            ->where(function($q) use ($studentGender) {
                $q->where('gender_requirement', 'Semua')
                  ->orWhere('gender_requirement', $studentGender);
            })
            ->get()
            // 2. FILTER KUOTA: Sembunyikan (*hide*) jika sisa kuota sudah habis/minus
            ->filter(function($slot) {
                return ($slot->quota - $slot->terisi) > 0;
            });

        return view('admin.placements.edit', compact('placement', 'companySlots'));
    }

    public function update(Request $request, Placement $placement)
    {
        $validated = $request->validate([
            'company_slot_id' => 'required|exists:company_slots,id',
            'notes' => 'nullable|string'
        ]);

        $slot = CompanySlot::findOrFail($validated['company_slot_id']);
        $studentGender = $placement->student->gender;

        // --- VALIDASI KEAMANAN BACK-END ---

        // 1. Cegah paksaan jika gender tidak sesuai
        if ($slot->gender_requirement !== 'Semua' && $slot->gender_requirement !== $studentGender) {
            return back()->with('error', 'Gagal: Gender siswa tidak memenuhi syarat perusahaan ini.')->withInput();
        }

        // 2. Cegah paksaan jika kuota benar-benar sudah penuh
        $terisi = \App\Models\Placement::where('company_slot_id', $slot->id)
            ->where('status_pencocokan', 'final')
            ->count();
            
        if (($slot->quota - $terisi) <= 0) {
            return back()->with('error', 'Gagal: Kuota untuk industri ini sudah penuh, silakan pilih industri lain.')->withInput();
        }

        // --- JIKA AMAN, LANJUTKAN PROSES ---

        $placement->update([
            'company_id' => $slot->company_id,
            'company_slot_id' => $slot->id,
            'status_pencocokan' => 'final',
            'placement_method' => 'MANUAL_OVERRIDE',
            'notes' => $validated['notes'] ? "INTERVENSI MANUAL: " . $validated['notes'] : "Diintervensi secara manual oleh Hubin."
        ]);

        if ($placement->student) {
            $placement->student->update(['status' => 'lolos_prakerin']);
        }

        return redirect()->route('admin.placements.index')->with('success', 'Intervensi manual berhasil disimpan dan status menjadi Final.');
    }

    // ========================================================
    // FITUR: ACC HUBIN & EXPORT
    // ========================================================

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
        $filename = 'Rekap_Penempatan_Prakerin_Periode_' . date('Y_m_d_His') . '.xlsx';
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

        return $pdf->stream('Laporan_Penempatan_Prakerin_' . str_replace(['/', '\\'], '-', $selectedYear->name) . '.pdf');
    }
}