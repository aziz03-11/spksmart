<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Draft Penempatan Prakerin</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; font-size: 12px; color: #555; }
        
        /* Table Layout Fixed agar kolom Keterangan tidak tumpah/melebar berlebihan */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th, td { border: 1px solid #999; padding: 6px; text-align: left; vertical-align: top; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 10px; text-align: center; }
        
        .text-center { text-align: center; }
        .status-diterima { color: green; font-weight: bold; }
        .status-waiting { color: #d97706; font-weight: bold; } /* Warna Amber/Oranye */
        .status-tolak { color: red; font-weight: bold; }
        
        .notes { font-size: 9px; color: #444; line-height: 1.4; }
        
        .footer { margin-top: 30px; text-align: right; font-size: 11px; }
        .ttd { margin-top: 60px; border-bottom: 1px solid #333; width: 180px; display: inline-block; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Draft Penempatan Prakerin (Metode SMART)</h1>
        <p>Tahun Ajaran: {{ $selectedYear->name ?? 'Semua Periode' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="16%">Nama Siswa</th>
                <th width="5%">L/P</th>
                <th width="8%">Jurusan</th>
                <th width="8%">Skor</th>
                <th width="12%">Status</th>
                <th width="15%">Penempatan PT</th>
                <th width="32%">Keterangan / Alasan Sistem</th>
            </tr>
        </thead>
        <tbody>
            @forelse($placements as $index => $placement)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <b>{{ $placement->student->name }}</b><br>
                        <span style="font-size: 9px; color: #666;">NISN: {{ $placement->student->nisn }} | Kls: {{ $placement->student->class_name }}</span>
                    </td>
                    <td class="text-center">{{ $placement->student->gender === 'L' ? 'L' : 'P' }}</td>
                    <td class="text-center">{{ $placement->student->major->code ?? '-' }}</td>
                    <td class="text-center"><b>{{ $placement->final_smart_score }}</b></td>
                    <td class="text-center">
                        @if(in_array($placement->status_pencocokan, ['rekomendasi', 'final']))
                            <span class="status-diterima">{{ strtoupper($placement->status_pencocokan) }}</span>
                        @elseif($placement->status_pencocokan === 'waiting_list')
                            <span class="status-waiting">WAITING LIST</span>
                        @else
                            <span class="status-tolak">PEMBINAAN</span>
                        @endif
                    </td>
                    <td><b>{{ $placement->company->name ?? '-' }}</b></td>
                    <td class="notes">
                        @if(in_array($placement->status_pencocokan, ['rekomendasi', 'final']))
                            Memenuhi standar kualifikasi & kuota industri.
                        @else
                            {!! nl2br(e($placement->notes ?? 'Tidak ada catatan.')) !!}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">Belum ada data penempatan untuk dicetak.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d M Y H:i') }}</p>
        <p>Mengetahui,</p>
        <p>Kepala Hubin / Penanggung Jawab Prakerin</p>
        <div class="ttd"></div>
    </div>

</body>
</html>