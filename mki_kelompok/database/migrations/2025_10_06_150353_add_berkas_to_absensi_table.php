<?php

// File: database/migrations/xxxx_xx_xx_xxxxxx_add_berkas_to_absensi_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('absensi')) {
            return;
        }

        if (! Schema::hasColumn('absensi', 'berkas')) {
            Schema::table('absensi', function (Blueprint $table) {
                $table->string('berkas')->nullable()->after('alasan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('absensi')) {
            return;
        }

        if (Schema::hasColumn('absensi', 'berkas')) {
            Schema::table('absensi', function (Blueprint $table) {
                $table->dropColumn('berkas');
            });
        }
    }
};