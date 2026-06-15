<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\Criterion;
use App\Models\Company;
use App\Models\CompanySlot;
use App\Models\Student;
use App\Models\Assessment;

class DummyPrakerinSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT TAHUN AJARAN
        $year1 = AcademicYear::firstOrCreate(['name' => '2025/2026'], ['is_active' => false]);
        $year2 = AcademicYear::firstOrCreate(['name' => '2026/2027'], ['is_active' => true]);
        $activeYearId = $year2->id;

        // 2. BUAT JURUSAN
        $majors = [
            ['name' => 'Rekayasa Perangkat Lunak', 'code' => 'RPL'],
            ['name' => 'Teknik Komputer & Jaringan', 'code' => 'TKJ'],
            ['name' => 'Akuntansi & Keuangan Lembaga', 'code' => 'AKL'],
            ['name' => 'Bisnis Daring & Pemasaran', 'code' => 'BDP'],
        ];
        
        $majorIds = [];
        foreach ($majors as $m) {
            $major = Major::firstOrCreate(['code' => $m['code']], ['name' => $m['name']]);
            $majorIds[$m['code']] = $major->id;
        }

        // 3. BUAT KRITERIA SMART
        $criteria = [
            ['code' => 'C1', 'name' => 'Nilai Rapor (Akademik)', 'weight' => 40, 'type' => 'Benefit'],
            ['code' => 'C2', 'name' => 'Tes Wawancara Industri', 'weight' => 30, 'type' => 'Benefit'],
            ['code' => 'C3', 'name' => 'Tingkat Kehadiran', 'weight' => 20, 'type' => 'Benefit'],
            ['code' => 'C4', 'name' => 'Riwayat Pelanggaran (Poin Tatib)', 'weight' => 10, 'type' => 'Cost'],
        ];

        $criteriaIds = [];
        foreach ($criteria as $c) {
            $crit = Criterion::firstOrCreate(
                ['code' => $c['code']], 
                ['name' => $c['name'], 'weight' => $c['weight'], 'type' => $c['type']]
            );
            $criteriaIds[] = $crit->id;
        }

        // 4. BUAT PERUSAHAAN DENGAN KUOTA GENDER & AMBANG BATAS NILAI YANG BERAGAM
        $companiesData = [
            [
                'name' => 'PT Telkom Indonesia (Witel)', 
                'address' => 'Jl. Pahlawan No. 1, Pusat Kota', 
                'phone' => '021-111111',
                'majors' => ['RPL', 'TKJ'],
                'slot' => [
                    'batch' => 'Gelombang 1', 
                    'quota' => 4, 'quota_male' => 2, 'quota_female' => 2, 'gender' => 'Semua',
                    'min_total' => 80, 'min_absensi' => 85
                ]
            ],
            [
                'name' => 'Bank BCA Cabang Utama', 
                'address' => 'Jl. Sudirman Kav. 22', 
                'phone' => '021-222222',
                'majors' => ['AKL', 'BDP'],
                'slot' => [
                    'batch' => 'Reguler 2026', 
                    'quota' => 5, 'quota_male' => 1, 'quota_female' => 4, 'gender' => 'Semua',
                    'min_total' => 75, 'min_absensi' => 80
                ]
            ],
            [
                'name' => 'PT Indofood Sukses Makmur', 
                'address' => 'Kawasan Industri Terpadu Blok A', 
                'phone' => '021-333333',
                'majors' => ['AKL', 'BDP', 'TKJ'],
                'slot' => [
                    'batch' => 'Batch 1 (Juli-Okt)', 
                    'quota' => 6, 'quota_male' => 4, 'quota_female' => 2, 'gender' => 'Semua',
                    'min_total' => 70, 'min_absensi' => 75
                ]
            ],
            [
                'name' => 'CV Media Kreatif Digital', 
                'address' => 'Ruko Tech Park No. 8', 
                'phone' => '021-444444',
                'majors' => ['RPL'],
                'slot' => [
                    'batch' => 'Kelas Industri', 
                    'quota' => 3, 'quota_male' => 3, 'quota_female' => 0, 'gender' => 'L', // KHUSUS COWO
                    'min_total' => 85, 'min_absensi' => 85
                ]
            ],
            [
                'name' => 'PT Astra Honda Motor', 
                'address' => 'Kawasan Perakitan Jalur 2', 
                'phone' => '021-555555',
                'majors' => ['TKJ'],
                'slot' => [
                    'batch' => 'Mekanik Ganjil', 
                    'quota' => 4, 'quota_male' => 4, 'quota_female' => 0, 'gender' => 'L', // KHUSUS COWO
                    'min_total' => 75, 'min_absensi' => 75
                ]
            ]
        ];

        foreach ($companiesData as $cData) {
            $company = Company::firstOrCreate(
                ['name' => $cData['name']],
                ['address' => $cData['address'], 'phone' => $cData['phone']]
            );

            $slot = CompanySlot::firstOrCreate([
                'company_id' => $company->id,
                'academic_year_id' => $activeYearId,
                'batch_name' => $cData['slot']['batch'],
            ], [
                'quota' => $cData['slot']['quota'],
                'quota_male' => $cData['slot']['quota_male'],
                'quota_female' => $cData['slot']['quota_female'],
                'gender_requirement' => $cData['slot']['gender'],
                'min_total_score' => $cData['slot']['min_total'],
                'min_absensi_score' => $cData['slot']['min_absensi'],
                'start_date' => Carbon::parse('2026-07-15')->format('Y-m-d'),
                'end_date' => Carbon::parse('2026-10-15')->format('Y-m-d'),
            ]);

            $syncMajors = [];
            foreach ($cData['majors'] as $mc) {
                $syncMajors[] = $majorIds[$mc];
            }
            $slot->majors()->sync($syncMajors);
        }

        // 5. DATA 35 SISWA DENGAN PILIHAN JURUSAN & GENDER BERIMBANG
        $studentsData = [
            // KELOMPOK SISWA ELITE (Nilai Bagus, Pasti Lolos Rekomendasi)
            ['Budi Santoso', 'L', 'RPL', 'elite'], ['Siti Aminah', 'P', 'AKL', 'elite'], 
            ['Ahmad Fauzi', 'L', 'TKJ', 'elite'], ['Dewi Lestari', 'P', 'BDP', 'elite'], 
            ['Rizki Pratama', 'L', 'RPL', 'elite'], ['Putri Wahyuni', 'P', 'AKL', 'elite'],
            ['Eko Susanto', 'L', 'TKJ', 'elite'], ['Ayu Wandira', 'P', 'BDP', 'elite'],
            
            // KELOMPOK SISWA RATA-RATA (Nilai Menengah, bersaing ketat, berpotensi WAITING LIST karena kalah kuota)
            ['Dimas Anggara', 'L', 'RPL', 'average'], ['Nisa Sabyan', 'P', 'AKL', 'average'], 
            ['Fajar Nugraha', 'L', 'TKJ', 'average'], ['Rina Nose', 'P', 'BDP', 'average'],
            ['Gilang Dirga', 'L', 'RPL', 'average'], ['Maudy Ayunda', 'P', 'AKL', 'average'], 
            ['Reza Rahadian', 'L', 'TKJ', 'average'], ['Chelsea Islan', 'P', 'BDP', 'average'],
            ['Iqbaal Ramadhan', 'L', 'RPL', 'average'], ['Tara Basro', 'P', 'AKL', 'average'], 
            ['Vino Bastian', 'L', 'TKJ', 'average'], ['Dian Sastro', 'P', 'BDP', 'average'],
            ['Bambang Pamungkas', 'L', 'TKJ', 'average'], ['Susi Susanti', 'P', 'AKL', 'average'],
            ['Taufik Hidayat', 'L', 'RPL', 'average'], ['Sri Mulyani', 'P', 'AKL', 'average'],

            // KELOMPOK SISWA DI BAWAH STANDAR (Nilai Rendah / Absensi Buruk / Pelanggaran Banyak, masuk PEMBINAAN)
            ['Andika Kangen', 'L', 'RPL', 'low'], ['Rosa Meldianti', 'P', 'AKL', 'low'],
            ['Lucinta Luna', 'P', 'TKJ', 'low'], ['Vicky Prasetyo', 'L', 'BDP', 'low'],
            ['Doddy Sudrajat', 'L', 'RPL', 'low'], ['Mayang Lucyana', 'P', 'AKL', 'low'],
            ['Rangga Sasana', 'L', 'TKJ', 'low'], ['Barbie Kumalasari', 'P', 'BDP', 'low'],
            ['Zumi Zola', 'L', 'AKL', 'low'], ['Lutfi Agizal', 'L', 'RPL', 'low'],
            ['Gaston Castano', 'L', 'TKJ', 'low']
        ];

        $nisnCounter = 100200300;
        
        foreach ($studentsData as $index => $sData) {
            $student = Student::firstOrCreate(
                ['nisn' => (string)($nisnCounter + $index)],
                [
                    'name' => $sData[0],
                    'gender' => $sData[1],
                    'class_name' => 'XII ' . $sData[2] . ' 1',
                    'major_id' => $majorIds[$sData[2]],
                    'academic_year_id' => $activeYearId,
                    'status' => 'belum_prakerin',
                    'parent_phone' => '0812' . rand(10000000, 99999999)
                ]
            );

            // 6. GENERATE STRUKTUR NILAI BERDASARKAN KELOMPOK (PROFIL REALISTIS)
            $scores = [];
            
            if ($sData[3] === 'elite') {
                // Siswa Pintar: Nilai 88-98, Absensi Tinggi (90-100), Pelanggaran Kecil/Tidak Ada (0-3)
                $scores[$criteriaIds[0]] = rand(88, 98); // Rapor
                $scores[$criteriaIds[1]] = rand(85, 95); // Wawancara
                $scores[$criteriaIds[2]] = rand(92, 100); // Absensi
                $scores[$criteriaIds[3]] = rand(0, 3);   // Pelanggaran (Cost)
            } elseif ($sData[3] === 'average') {
                // Siswa Menengah: Nilai 75-85, Absensi Pas-pasan (76-88), Pelanggaran Normal (2-10)
                $scores[$criteriaIds[0]] = rand(75, 85);
                $scores[$criteriaIds[1]] = rand(72, 84);
                $scores[$criteriaIds[2]] = rand(76, 88);
                $scores[$criteriaIds[3]] = rand(2, 10);
            } else {
                // Siswa Bermasalah: Nilai Jatuh (50-72), Absensi Hancur (50-74), Pelanggaran Banyak (15-45)
                $scores[$criteriaIds[0]] = rand(55, 72);
                $scores[$criteriaIds[1]] = rand(50, 70);
                $scores[$criteriaIds[2]] = rand(50, 74); // Ini akan memicu gagal 'min_absensi_score'
                $scores[$criteriaIds[3]] = rand(15, 45); // Cost tinggi memperkecil utility
            }

            Assessment::updateOrCreate(
                ['student_id' => $student->id],
                ['scores_data' => $scores]
            );
        }
        
        $this->command->info('🎉 Sukses! 35 Data Dummy Riil (Elite, Average, Low) Berhasil Disuntikkan.');
    }
}