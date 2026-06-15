<?php

namespace App\Exports;

use App\Models\Placement;
use App\Models\AppSetting;
use App\Models\AcademicYear;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LolosPrakerinExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    protected $academicYearId;
    protected $rowNumber = 0;

    public function __construct($academicYearId)
    {
        $this->academicYearId = $academicYearId;
    }

    /**
     * Ambil data siswa yang HANYA berstatus FINAL (Lolos)
     * Diurutkan berdasarkan Nama Perusahaan agar rapi per kelompok industri
     */
    public function collection()
    {
        return Placement::where('placements.academic_year_id', $this->academicYearId)
            ->where('placements.status_pencocokan', 'final')
            ->join('companies', 'placements.company_id', '=', 'companies.id')
            ->with(['student.major', 'company', 'companySlot'])
            ->orderBy('companies.name', 'asc')
            ->select('placements.*')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NISN',
            'Nama Lengkap Siswa',
            'L/P',
            'Kelas & Jurusan',
            'Perusahaan / Industri Tempat PKL',
            'Alokasi Gelombang',
            'Jalur Penempatan',
            'Catatan Khusus Hubin'
        ];
    }

    public function map($placement): array
    {
        $this->rowNumber++;

        // Format Jalur Penempatan (SMART Engine vs Intervensi Manual)
        $jalur = $placement->placement_method === 'MANUAL_OVERRIDE' 
            ? '✋ Intervensi Manual' 
            : '🤖 SMART Engine';

        // Format Catatan Rekam Jejak
        $catatan = '-';
        if ($placement->placement_method === 'MANUAL_OVERRIDE') {
            // Bersihkan teks "INTERVENSI MANUAL: " jika ada agar rapi di excel
            $catatan = str_replace('INTERVENSI MANUAL: ', '', $placement->notes ?? 'Kebijakan internal Hubin');
        }

        return [
            $this->rowNumber,
            $placement->student->nisn ?? '-',
            $placement->student->name ?? '-',
            $placement->student->gender === 'L' ? 'L' : 'P',
            'XII / ' . ($placement->student->major->code ?? '-'),
            $placement->company->name ?? '-',
            $placement->companySlot->batch_name ?? 'Reguler',
            $jalur,
            $catatan
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Ambil data dinamis Setting & Tahun Ajaran
                $setting = AppSetting::first();
                $academicYear = AcademicYear::find($this->academicYearId);
                $tahun = $academicYear ? $academicYear->name : '-';
                $namaSekolah = $setting->nama_sekolah ?? 'SMK NEGERI 1 SPK';
                
                // 1. Dorong tabel kebawah untuk membuat space Kop Surat (Baris 1-6)
                $sheet->insertNewRowBefore(1, 6);
                
                // 2. Isi Teks Kop Surat Dinas & Sekolah
                $sheet->setCellValue('A1', $setting->instansi_atas ?? 'PEMERINTAH PROVINSI / DINAS PENDIDIKAN');
                $sheet->setCellValue('A2', strtoupper($namaSekolah));
                $sheet->setCellValue('A3', $setting->alamat_sekolah ?? 'Alamat Sekolah Belum Diatur');
                $sheet->setCellValue('A5', 'LAPORAN RESMI RIWAYAT PENEMPATAN PRAKTEK KERJA INDUSTRI (PRAKERIN)');
                $sheet->setCellValue('A6', 'STATUS: VALID / FINAL - TAHUN AJARAN ' . strtoupper($tahun));

                // 3. Satukan kolom (Merge) A sampai I
                $sheet->mergeCells('A1:I1');
                $sheet->mergeCells('A2:I2');
                $sheet->mergeCells('A3:I3');
                $sheet->mergeCells('A5:I5');
                $sheet->mergeCells('A6:I6');

                // 4. Atur posisi text Kop Surat ke Tengah (Center)
                $sheet->getStyle('A1:A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(12);

                $highestRow = $sheet->getHighestRow();

                // 5. Beri Warna Hijau Emerald Sukses khas "Lolos/Final" untuk Header Tabel (Baris 7)
                $sheet->getStyle('A7:I7')->applyFromArray([
                    'font' => [
                        'bold' => true, 
                        'color' => ['argb' => 'FFFFFFFF'] // Teks Putih
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'FF10B981'] // Hijau Emerald Modern (Sukses)
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // 6. Pasang Border Garis Tipis ke seluruh isi tabel
                $sheet->getStyle('A7:I' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // 7. Kunci lebar kolom Catatan (I) agar rapi memanjang kebawah jika teksnya panjang
                $sheet->getStyle('I8:I' . $highestRow)->getAlignment()->setWrapText(true);
                $sheet->getColumnDimension('I')->setAutoSize(false);
                $sheet->getColumnDimension('I')->setWidth(35);
                
                // 8. Atur perataan teks (Alignment) data agar sedap dipandang
                $sheet->getStyle('A7:I' . $highestRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A8:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
                $sheet->getStyle('B8:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // NISN
                $sheet->getStyle('D8:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // L/P
                $sheet->getStyle('F8:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);   // PT & Gelombang
                $sheet->getStyle('H8:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Jalur Penempatan
            },
        ];
    }
}