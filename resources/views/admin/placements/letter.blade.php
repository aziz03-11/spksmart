<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengantar Prakerin - {{ $company->name }}</title>
    <style>
        body { 
            font-family: 'Times-Roman', serif; 
            font-size: 14px; 
            line-height: 1.5; 
            color: #000; 
            margin: 20px 40px; 
        }
        
        /* ... CSS Kop Surat & Info Surat tetap sama ... */
        .kop-surat { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h2, .kop-surat h3, .kop-surat p { margin: 0; }
        .kop-surat h2 { font-size: 18px; text-transform: uppercase; font-weight: bold; }
        .kop-surat h1 { font-size: 22px; text-transform: uppercase; font-weight: bold; margin: 0; }
        .kop-surat p { font-size: 11px; font-style: italic; color: #333; }
        
        .info-surat { margin-bottom: 30px; }
        .info-surat table { width: 100%; border: none; }
        .info-surat td { padding: 2px 0; vertical-align: top; }
        
        .content { text-align: justify; }
        
        /* OPTIMASI UNTUK BANYAK SISWA (30+ SISWA) */
        .content-table { width: 100%; margin: 15px 0; border-collapse: collapse; }
        .content-table th, .content-table td { padding: 6px; border: 1px solid #000; }
        .content-table th { background-color: #f2f2f2; }
        
        /* Trik DomPDF: Mengulang header tabel di setiap halaman baru */
        .content-table thead { display: table-header-group; }
        
        /* Trik DomPDF: Mencegah satu baris data terpotong setengah di ujung halaman */
        .content-table tr { page-break-inside: avoid; }
        
        .footer { margin-top: 40px; width: 100%; page-break-inside: avoid; }
        .footer-table { width: 100%; text-align: center; }
        .ttd-space { height: 70px; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <h2>{{ $settings->instansi_atas ?? 'PEMERINTAH PROVINSI JAWA BARAT' }}</h2>
        <h2>DINAS PENDIDIKAN</h2>
        <h1>{{ $settings->nama_sekolah ?? 'SMK NEGERI 1 SPK' }}</h1>
        <p>{{ $settings->alamat_sekolah ?? 'Jl. Pendidikan No. 123, Alamat Sekolah, Kode Pos 12345' }}</p>
    </div>

    <div class="info-surat">
        <table>
            <tr>
                <td width="15%">Nomor</td>
                <td width="2%">:</td>
                <td width="48%">
                    421.5/123/SMK-SPK/{{ date('Y') }}
                </td>
                <td width="35%" style="text-align: right;">
                    {{ $date_now ?? \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>1 (Satu) Berkas</td>
                <td></td>
            </tr>
            <tr>
                <td>Hal</td>
                <td>:</td>
                <td><b>Permohonan Praktik Kerja Industri (Prakerin)</b></td>
                <td></td>
            </tr>
        </table>
    </div>

    <div class="content">
        <p>Yth. Pimpinan/HRD <b>{{ $company->name }}</b><br>
        {{ $company->address }}</p>

        <p>Dengan hormat,</p>
        
        <p>
            {{ $settings->teks_pengantar_surat ?? 'Dalam rangka pelaksanaan program Pendidikan Sistem Ganda (PSG) dan untuk meningkatkan kompetensi lulusan Sekolah Menengah Kejuruan (SMK), kami memohon kesediaan Bapak/Ibu untuk menerima siswa kami melaksanakan Praktik Kerja Industri (Prakerin) di instansi/perusahaan yang Bapak/Ibu pimpin.' }}
        </p>
        
        <p>
            Adapun siswa yang direkomendasikan adalah sebagai berikut:
        </p>

        <table class="content-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Nama Lengkap</th>
                    <th style="width: 20%;">NISN</th>
                    <th style="width: 25%;">Kelas / Jurusan</th>
                    <th style="width: 15%;">Gelombang</th>
                </tr>
            </thead>
            <tbody>
                @foreach($placements as $index => $placement)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $placement->student->name ?? '-' }}</td>
                    <td style="text-align: center;">{{ $placement->student->nisn ?? '-' }}</td>
                    <td style="text-align: center;">XII / {{ $placement->student->major->name ?? '-' }}</td>
                    <td style="text-align: center;">{{ $placement->companySlot->batch_name ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p>
            Demikian surat permohonan ini kami sampaikan. Besar harapan kami Bapak/Ibu dapat mengabulkan permohonan ini. Atas perhatian dan kerja sama yang baik, kami ucapkan terima kasih.
        </p>
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td width="50%"></td>
                <td width="50%">
                    <p>Kepala {{ $settings->nama_sekolah ?? 'SMK Negeri 1 SPK' }},</p>
                    <div class="ttd-space"></div>
                    <p>
                        <b><u>{{ $settings->nama_kepala_sekolah ?? 'Dr. Nama Kepala Sekolah, M.Pd.' }}</u></b><br>
                        NIP / NUPTK. {{ $settings->nip_kepala_sekolah ?? '19700101 199802 1 001' }}
                    </p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>