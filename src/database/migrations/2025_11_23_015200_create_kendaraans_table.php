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
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id(); // Kolom id auto increment
            $table->char('kode', 20)->nullable(); // Kode diganti jadi 20
            $table->string('nama', 100);
            $table->char('plat', 50);
            $table->char('jens', 50);
            $table->char('fno_prk_b', 20); // Diubah dari 5 menjadi 20
            $table->char('fno_prk_p', 20); // Diubah dari 5 menjadi 20
            $table->char('fno_prk_s', 20); // Diubah dari 5 menjadi 20
            $table->char('fno_prk_o', 20); // Diubah dari 5 menjadi 20
            $table->char('fno_prk_m', 20); // Diubah dari 5 menjadi 20
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
