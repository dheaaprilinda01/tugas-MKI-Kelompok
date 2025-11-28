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
        // Be defensive: only add timestamps if the table exists and columns don't already exist.
        if (! Schema::hasTable('absensi')) {
            // If the base table doesn't exist, skip this migration.
            // A proper create_absensi_table migration should run earlier.
            return;
        }

        if (! Schema::hasColumn('absensi', 'created_at') && ! Schema::hasColumn('absensi', 'updated_at')) {
            Schema::table('absensi', function (Blueprint $table) {
                $table->timestamps(); // create created_at & updated_at
            });
        }
}

public function down(): void
{
        if (! Schema::hasTable('absensi')) {
            return;
        }

        if (Schema::hasColumn('absensi', 'created_at') || Schema::hasColumn('absensi', 'updated_at')) {
            Schema::table('absensi', function (Blueprint $table) {
                // dropTimestamps removes both created_at and updated_at if present
                $table->dropTimestamps();
            });
        }
}

};
