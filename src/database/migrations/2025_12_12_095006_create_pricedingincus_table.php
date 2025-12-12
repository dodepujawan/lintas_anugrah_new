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
        Schema::create('pricedingincus', function (Blueprint $table) {
            $table->id();
            $table->string('KODECUS', 20);
            $table->string('KODEDGN', 20);
            $table->string('KODE', 20);
            $table->string('PERIODE', 50);
            $table->string('PLAT', 50);
            $table->string('JENIS', 50);
            $table->string('ITEM', 100);
            $table->decimal('HARGA', 10, 0)->default(0);
            $table->string('USER', 50);
            $table->string('USEREDIT', 50)->nullable();
            $table->string('KUNCI', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricedingincus');
    }
};
