@extends('layouts.hubin')

@section('title', 'Beranda Utama Dasbor')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 space-y-2">
            <h1 class="text-2xl sm:text-3xl font-black">Selamat Datang di Portal Hubin SMART 👋</h1>
            <p class="text-indigo-100 text-sm sm:text-base font-medium max-w-2xl leading-relaxed">
                Periode Aktif: <span class="bg-white/20 px-3 py-1 rounded-lg text-xs font-bold shadow-sm inline-block mt-1 sm:mt-0">{{ $activeYear->name ?? 'Belum Diatur' }}</span><br class="hidden sm:block">
                Sistem pencocokan otomatis (*SMART Engine*) siap membantu mengalokasikan penempatan industri siswa secara objektif dan transparan.
            </p>
        </div>
        <div class="absolute -right-10 -bottom-10 text-white/5 font-black text-9xl pointer-events-none select-none">SMK</div>
        <div class="absolute top-0 right-1/4 w-32 h-32 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
            <div class="space-y-1">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Total Siswa Terdaftar</span>
                <span class="text-3xl font-black text-gray-900 block">{{ $stats['total_students'] ?? 0 }}</span>
                <span class="text-xs text-gray-500 font-medium block">Tahun ajaran berjalan</span>
            </div>
            <div class="w-12 h-12 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center text-2xl font-bold border border-gray-100 shadow-sm">👥</div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
            <div class="space-y-1">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Siswa Belum Proses</span>
                <span class="text-3xl font-black text-gray-500 block">{{ $stats['belum_prakerin'] ?? 0 }}</span>
                <span class="text-xs text-amber-600 font-bold block flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Belum dinilai</span>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center text-2xl font-bold border border-amber-100 shadow-sm">⏳</div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
            <div class="space-y-1">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Siswa Waiting List</span>
                <span class="text-3xl font-black text-blue-600 block">{{ $stats['waiting_list'] ?? 0 }}</span>
                <span class="text-[11px] text-blue-500 font-bold block leading-tight">Nilai cukup, kuota industri penuh</span>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center text-2xl font-bold border border-blue-100 shadow-sm">📋</div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
            <div class="space-y-1">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">Butuh Pembinaan</span>
                <span class="text-3xl font-black text-red-600 block">{{ $stats['pembinaan'] ?? 0 }}</span>
                <span class="text-xs text-red-500 font-bold block flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Skor di bawah standar</span>
            </div>
            <div class="w-12 h-12 bg-red-50 text-red-500 rounded-xl flex items-center justify-center text-2xl font-bold border border-red-100 shadow-sm">🎯</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-indigo-50 border border-indigo-100 p-5 rounded-2xl flex items-center gap-4 hover:bg-indigo-100/50 transition">
            <div class="text-3xl drop-shadow-sm">🏭</div>
            <div>
                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-wider block">Perusahaan Mitra Active</span>
                <span class="text-lg font-black text-indigo-900 block">{{ $stats['total_companies'] ?? 0 }} Industri Terdaftar</span>
            </div>
        </div>
        <div class="bg-purple-50 border border-purple-100 p-5 rounded-2xl flex items-center gap-4 hover:bg-purple-100/50 transition">
            <div class="text-3xl drop-shadow-sm">🔍</div>
            <div>
                <span class="text-[10px] font-black text-purple-500 uppercase tracking-wider block">Proses Seleksi / Rekomendasi</span>
                <span class="text-lg font-black text-purple-900 block">{{ $stats['proses_seleksi'] ?? 0 }} Siswa Menunggu ACC</span>
            </div>
        </div>
        <div class="bg-emerald-50 border border-emerald-100 p-5 rounded-2xl flex items-center gap-4 hover:bg-emerald-100/50 transition">
            <div class="text-3xl drop-shadow-sm">🚀</div>
            <div>
                <span class="text-[10px] font-black text-emerald-600 uppercase tracking-wider block">Lolos Final & Berangkat</span>
                <span class="text-lg font-black text-emerald-900 block">{{ $stats['lolos_prakerin'] ?? 0 }} Siswa Telah Divalidasi</span>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-100 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-50 pb-4">
            <div>
                <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                    📊 Distribusi Penempatan Industri
                </h2>
                <p class="text-xs text-gray-500 mt-1">Menampilkan jumlah siswa (Rekomendasi & Final) yang dialokasikan per perusahaan mitra.</p>
            </div>
            <a href="{{ route('admin.placements.index') }}" class="text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-lg transition border border-indigo-100">
                Lihat Detail Proses &rarr;
            </a>
        </div>
        
        <div class="relative w-full overflow-hidden min-h-[350px] flex items-center justify-center pt-2">
            @if(count($chartLabels) > 0)
                <canvas id="placementBarChart" class="w-full max-h-[400px]"></canvas>
            @else
                <div class="text-center space-y-2 text-gray-400 py-12">
                    <span class="text-5xl block drop-shadow-sm">📈</span>
                    <span class="text-sm font-bold block text-gray-500">Data Grafik Masih Kosong</span>
                    <span class="text-xs block max-w-xs mx-auto">Sistem belum memproses penempatan apa pun pada periode ini. Silakan jalankan Algoritma SMART.</span>
                </div>
            @endif
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil data array dari PHP ke Javascript
        const labels = {!! json_encode($chartLabels) !!};
        const values = {!! json_encode($chartValues) !!};

        if (labels.length > 0) {
            const ctx = document.getElementById('placementBarChart').getContext('2d');
            
            // Konfigurasi Gradien Warna untuk Batang Grafik
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.9)'); // Indigo 600
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0.4)'); // Indigo 500 Transparan

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: ' Jumlah Siswa Dialokasikan',
                        data: values,
                        backgroundColor: gradient,
                        borderColor: 'rgba(67, 56, 202, 1)', // Indigo 700
                        borderWidth: 1.5,
                        borderRadius: 6, // Ujung batang melengkung modern
                        borderSkipped: false,
                        hoverBackgroundColor: 'rgba(67, 56, 202, 0.9)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: { weight: 'bold', family: "'Inter', sans-serif" },
                                color: '#374151',
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)', // Gray 900
                            titleFont: { family: "'Inter', sans-serif", size: 13 },
                            bodyFont: { family: "'Inter', sans-serif", size: 12, weight: 'bold' },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' Siswa';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1, // Pastikan sumbu Y tidak menampilkan koma/desimal
                                font: { weight: '600', family: "'Inter', sans-serif" },
                                color: '#6B7280'
                            },
                            grid: { color: 'rgba(243, 244, 246, 1)', drawBorder: false }
                        },
                        x: {
                            ticks: {
                                font: { weight: 'bold', size: 11, family: "'Inter', sans-serif" },
                                color: '#4B5563',
                                maxRotation: 45, // Miringkan teks jika nama PT terlalu panjang
                                minRotation: 0
                            },
                            grid: { display: false, drawBorder: false }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection