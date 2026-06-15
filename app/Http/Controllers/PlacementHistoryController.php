<?php

namespace App\Http\Controllers;

use App\Models\Placement;
use App\Models\Company;
use App\Models\AcademicYear;
use App\Models\Major;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LolosPrakerinExport;
use Illuminate\Support\Facades\DB;

class PlacementHistoryController extends Controller
{
    /**
     * Tampilkan Riwayat Penempatan yang Sudah FINAL / DI-ACC
     */
    /**
     * Tampilkan Riwayat Penempatan yang Sudah FINAL / DI-ACC
     */
    public function index(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->get('academic_year_id', $activeYear ? $activeYear->id : null);
        $allYears = AcademicYear::all();
        $selectedMajorId = $request->get('major_id');
        $majors = Major::all();

        // Ambil data penempatan final menggunakan paginate(5)
        $companiesWithPlacements = Company::whereHas('placements', function($q) use ($selectedYearId, $selectedMajorId) {
                $q->where('status_pencocokan', 'final')
                  ->where('academic_year_id', $selectedYearId);
                  
                // Filter jurusan langsung di level query jika dipilih
                if ($selectedMajorId) {
                    $q->whereHas('student', function($sq) use ($selectedMajorId) {
                        $sq->where('major_id', $selectedMajorId);
                    });
                }
            })
            ->with(['placements' => function($q) use ($selectedYearId, $selectedMajorId) {
                $q->where('status_pencocokan', 'final')
                  ->where('academic_year_id', $selectedYearId);
                  
                // Terapkan filter jurusan juga pada relasi placement-nya
                if ($selectedMajorId) {
                    $q->whereHas('student', function($sq) use ($selectedMajorId) {
                        $sq->where('major_id', $selectedMajorId);
                    });
                }
                $q->with(['student.major', 'companySlot']);
            }])
            // Paginate 5 perusahaan per halaman, dan pastikan parameter GET dibawa
            ->paginate(5) 
            ->withQueryString(); 

        return view('admin.placements.history', compact(
            'companiesWithPlacements', 
            'allYears', 
            'selectedYearId', 
            'activeYear',
            'majors',
            'selectedMajorId'
        ));
    }

    /**
     * Export Excel Data Seluruh Siswa Lolos Prakerin di Tahun Ajaran Terpilih
     */
    public function exportExcel(Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        $year = AcademicYear::find($academicYearId);
        $filename = 'Data_Siswa_Lolos_Prakerin_' . str_replace('/', '-', $year->name ?? 'Semua') . '.xlsx';

        return Excel::download(new LolosPrakerinExport($academicYearId), $filename);
    }

    /**
     * Export PDF Surat Pengantar Dinamis Per Perusahaan
     */
    public function exportPdfSuratPengantar(Company $company, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        
        // Ambil data setting aplikasi dinamis
        $settings = DB::table('app_settings')->first();

        // Ambil SEMUA siswa yang lolos di PT ini (Tanpa batasan limit)
        // Jumlahnya otomatis menyesuaikan dengan hasil penempatan SMART Engine yang sudah FINAL
        $placements = Placement::where('company_id', $company->id)
            ->where('academic_year_id', $academicYearId)
            ->where('status_pencocokan', 'final')
            ->with(['student.major', 'companySlot'])
            ->get(); // Kita hapus ->limit(10) di sini

        if ($placements->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada siswa yang terdaftar secara FINAL di perusahaan ini.');
        }

        // Generate PDF menggunakan library barryvdh/laravel-dompdf
        $pdf = Pdf::loadView('admin.placements.letter', [
            'company' => $company,
            'placements' => $placements,
            'settings' => $settings,
            'date_now' => \Carbon\Carbon::now()->translatedFormat('d F Y')
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Surat_Pengantar_Prakerin_' . str_replace(' ', '_', $company->name) . '.pdf');
    }
}