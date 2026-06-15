<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Karena mengubah ENUM di Laravel menggunakan Schema::table kadang bermasalah,
        // cara paling aman adalah mengeksekusi raw query SQL.
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('belum_prakerin', 'proses_seleksi', 'waiting_list', 'pembinaan', 'lolos_prakerin') DEFAULT 'belum_prakerin'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum awal (sesuaikan dengan nilai awal Anda jika berbeda)
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('belum_prakerin', 'lolos_prakerin') DEFAULT 'belum_prakerin'");
    }
};