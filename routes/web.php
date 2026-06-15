<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\AppSetting;
use App\Models\Student;

// Import Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\PlacementHistoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanySlotController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\CriterionController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\SettingController;


// ==========================================
// 1. HALAMAN PUBLIK & TRACKER NISN
// ==========================================
Route::get('/', [HomeController::class, 'welcome'])->name('welcome');

Route::post('/track_nisn', function (Request $request) {
    $request->validate(['nisn' => 'required|string']);
    
    // Cari siswa beserta penempatan finalnya
    $student = Student::with(['placements' => function($q) {
        $q->where('status_pencocokan', 'final')->with('company');
    }])->where('nisn', $request->nisn)->first();

    if (!$student) {
        return back()->with('tracker_error', 'NISN tidak ditemukan dalam sistem kami.');
    }

    if ($student->status === 'lolos_prakerin' && $student->placements->isNotEmpty()) {
        $companyName = $student->placements->first()->company->name ?? 'Perusahaan Mitra';
        return back()->with('tracker_success', "Selamat! {$student->name} dinyatakan LOLOS Prakerin di {$companyName}. Silakan hubungi Hubin.");
    }

    return back()->with('tracker_info', "Halo {$student->name}, status penempatan Anda saat ini: Masih dalam proses seleksi / Belum final.");
})->name('track_nisn');


// ==========================================
// 2. AUTENTIKASI (LOGIN/LOGOUT USERNAME)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// ==========================================
// 3. RUTE ADMIN HUBIN (WAJIB LOGIN)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // ------------------------------------------
    // DASHBOARD & MESIN SPK AKTIF
    // ------------------------------------------
    // 1. Dasbor Utama (Memuat Data Statistik Baru via HomeController)
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // 2. Ruang Kerja Proses SPK Aktif
    Route::get('/placements', [SpkController::class, 'index'])->name('admin.placements.index');
    Route::post('/spk/generate', [SpkController::class, 'generate'])->name('admin.spk.generate');
    
    // RUTE CETAK DRAFT EXCEL (SEMUA STATUS / BELUM FINAL)
    Route::get('/spk/export_excel', [SpkController::class, 'exportExcel'])->name('admin.spk.export_excel');

    // Rute Intervensi Manual & ACC
    Route::get('/placements/{placement}/edit', [SpkController::class, 'edit'])->name('admin.placements.edit');
    Route::put('/placements/{placement}', [SpkController::class, 'update'])->name('admin.placements.update');
    Route::post('/placements/{placement}/acc', [SpkController::class, 'accHubin'])->name('admin.placements.acc');

    // Rute Pembaruan Password
    Route::put('/password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');

    // ------------------------------------------
    // RIWAYAT PENEMPATAN FINAL & CETAK BERKAS
    // ------------------------------------------
    Route::get('/placements/history', [PlacementHistoryController::class, 'index'])->name('admin.placements.history');
    Route::get('/placements/export-excel', [PlacementHistoryController::class, 'exportExcel'])->name('admin.placements.export_excel');
    Route::get('/placements/{company}/export-pdf-surat', [PlacementHistoryController::class, 'exportPdfSuratPengantar'])->name('admin.placements.export_pdf_surat');
    
    // Cetak Laporan Global (PDF Draft Sementara)
    Route::get('/spk/print_pdf', [SpkController::class, 'printPdf'])->name('admin.spk.print_pdf');


    // ------------------------------------------
    // MANAJEMEN MASTER DATA (CRUD)
    // ------------------------------------------
    // Master Jurusan
    Route::resource('majors', MajorController::class)->names('admin.majors');
    
    // Master Perusahaan & Slot/Gelombang
    Route::resource('companies', CompanyController::class)->names('admin.companies');
    Route::resource('company_slots', CompanySlotController::class)->names('admin.company_slots');
    
    // Master Kriteria SMART
    Route::resource('criterias', CriterionController::class)->names('admin.criterias');
    
    // Master Tahun Ajaran
    Route::resource('academic_years', AcademicYearController::class)
        ->only(['index', 'store', 'destroy'])
        ->names('admin.academic_years');
    Route::post('/academic_years/{academic_year}/set_active', [AcademicYearController::class, 'setActive'])->name('admin.academic_years.set_active');
    
    // Master Siswa & Import
    Route::get('/students/sample_excel', [StudentController::class, 'downloadSample'])->name('admin.students.sample_excel');
    Route::post('/students/import', [StudentController::class, 'import'])->name('admin.students.import');
    Route::resource('students', StudentController::class)->names('admin.students');

    // Input Nilai / Assessment
    Route::get('/students/{student}/assessment', [AssessmentController::class, 'edit'])->name('admin.students.assessment.edit');
    Route::put('/students/{student}/assessment', [AssessmentController::class, 'update'])->name('admin.students.assessment.update');

    // ------------------------------------------
    // PROFIL & PENGATURAN SISTEM
    // ------------------------------------------
    // Profil Admin
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Pengaturan Aplikasi / Kop Surat
    Route::get('/settings', [SettingController::class, 'edit'])->name('admin.settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

});