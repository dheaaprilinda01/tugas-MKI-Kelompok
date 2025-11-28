<?php

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
            // Table doesn't exist yet; skip. The create_absensi migration should create it.
            return;
        }

        if (! Schema::hasColumn('absensi', 'device_id')) {
            Schema::table('absensi', function (Blueprint $table) {
                $table->string('device_id')->nullable()->after('user_id');
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

        if (Schema::hasColumn('absensi', 'device_id')) {
            Schema::table('absensi', function (Blueprint $table) {
                $table->dropColumn('device_id');
            });
        }
    }
};
