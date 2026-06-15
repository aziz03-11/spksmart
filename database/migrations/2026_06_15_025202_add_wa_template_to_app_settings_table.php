<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            // Menambahkan kolom template WA setelah kolom teks_pengantar_surat
            $table->text('wa_template_message')->nullable()->after('teks_pengantar_surat');
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn('wa_template_message');
        });
    }
};