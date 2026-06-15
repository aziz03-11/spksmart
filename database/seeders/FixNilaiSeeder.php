<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Assessment;

class FixNilaiSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua siswa yang ada di database
        $students = Student::orderBy('id', 'asc')->get();
        $count = 0;

        foreach ($students as $student) {
            // Kita buat variasi nilai agar datanya terlihat natural (tidak bohong)
            if ($student->id <= 60) {
                // KELOMPOK ELITE (Nilai Tinggi, Pelanggaran Rendah)
                $scores = [
                    "1" => rand(88, 98), // C1: Rapor
                    "2" => rand(85, 95), // C2: Wawancara
                    "3" => rand(90, 100), // C3: Absensi
                    "4" => rand(88, 96), // C4: Praktik
                    "5" => rand(0, 3),   // C5: Pelanggaran (Cost)
                ];
            } elseif ($student->id <= 150) {
                // KELOMPOK MENENGAH (Nilai Pas-pasan)
                $scores = [
                    "1" => rand(75, 85),
                    "2" => rand(72, 84),
                    "3" => rand(76, 88),
                    "4" => rand(75, 85),
                    "5" => rand(2, 10),
                ];
            } else {
                // KELOMPOK BAWAH / PEMBINAAN (Nilai Hancur, Pelanggaran Tinggi)
                $scores = [
                    "1" => rand(55, 72),
                    "2" => rand(50, 70),
                    "3" => rand(50, 74),
                    "4" => rand(55, 70),
                    "5" => rand(20, 45),
                ];
            }

            // Simpan atau perbarui nilai siswa
            Assessment::updateOrCreate(
                ['student_id' => $student->id],
                ['scores_data' => $scores]
            );
            
            $count++;
        }

        $this->command->info('🎉 Mantap! Nilai evaluasi untuk ' . $count . ' siswa telah sukses diisi 100%!');
    }
}