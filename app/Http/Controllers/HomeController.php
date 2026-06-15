<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Company;
use App\Models\Placement;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AppSetting;

class HomeController extends Controller
{
    /**
     * Tampilkan Halaman Utama Dashboard dengan Statistik Riil SPK
     */
    public function index(Request $request)
    {
        // Dapatkan Tahun Ajaran Aktif
        $activeYear = AcademicYear::where('is_active', true)->first();
        $activeYearId = $activeYear ? $activeYear->id : null;

        // Hitung Ringkasan Data Statistik Siswa
        $stats = [
            'total_students'   => Student::where('academic_year_id', $activeYearId)->count(),
            'belum_prakerin'   => Student::where('academic_year_id', $activeYearId)->where('status', 'belum_prakerin')->count(),
            'proses_seleksi'   => Student::where('academic_year_id', $activeYearId)->where('status', 'proses_seleksi')->count(),
            'waiting_list'     => Student::where('academic_year_id', $activeYearId)->where('status', 'waiting_list')->count(),
            'pembinaan'        => Student::where('academic_year_id', $activeYearId)->where('status', 'pembinaan')->count(),
            'lolos_prakerin'   => Student::where('academic_year_id', $activeYearId)->where('status', 'lolos_prakerin')->count(),
            'total_companies'  => Company::count(),
        ];

        // Ambil Data Grafik Penempatan per Perusahaan
        $chartData = DB::table('placements')
            ->join('companies', 'placements.company_id', '=', 'companies.id')
            ->where('placements.academic_year_id', $activeYearId)
            ->whereIn('placements.status_pencocokan', ['rekomendasi', 'final'])
            ->whereNull('placements.deleted_at')
            ->select('companies.name as company_name', DB::raw('count(placements.id) as total_students'))
            ->groupBy('companies.id', 'companies.name')
            ->orderBy('total_students', 'desc')
            ->get();

        // Pisahkan data untuk Chart.js
        $chartLabels = $chartData->pluck('company_name')->toArray();
        $chartValues = $chartData->pluck('total_students')->toArray();

        return view('dashboard', compact('stats', 'chartLabels', 'chartValues', 'activeYear'));
    }
    public function welcome(Request $request)
    {
        $nisn = $request->get('nisn');
        $student = null;
        $placement = null;
        $searchPerformed = false;

        // 2. Ambil data pengaturan pertama dari database
        $setting = AppSetting::first();

        // Logika pencarian NISN mandiri siswa
        if ($nisn) {
            $searchPerformed = true;
            $student = Student::where('nisn', $nisn)->first();

            if ($student) {
                $activeYear = AcademicYear::where('is_active', true)->first();
                
                $placement = Placement::where('student_id', $student->id)
                    ->where('status_pencocokan', 'final')
                    ->where('academic_year_id', $activeYear ? $activeYear->id : null)
                    ->with(['company', 'companySlot'])
                    ->first();
            }
        }

        // 3. Tambahkan variabel 'setting' ke dalam fungsi compact() agar terkirim ke view
        return view('welcome', compact('nisn', 'student', 'placement', 'searchPerformed', 'setting'));
    }
}