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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('tanggal');
            $table->time('jam')->nullable();
            $table->enum('status', ['Hadir','Izin','Cuti','Sakit','Terlambat','Tugas Luar','alpha'])->default('Hadir');
            $table->string('alasan')->nullable();
            $table->string('device_id')->nullable();
            $table->string('berkas')->nullable();
            $table->timestamps();

            // If you have users table, you can add foreign key (optional)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
