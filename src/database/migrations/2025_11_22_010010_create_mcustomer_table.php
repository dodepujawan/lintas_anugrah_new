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
        Schema::create('mcustomer', function (Blueprint $table) {
        $table->charset = 'latin1';
        $table->collation = 'latin1_general_ci';

        // PRIMARY KEY BARU LARAVEL
        $table->bigIncrements('id');

        // PRIMARY KEY LAMA FOXPRO
        $table->string('CUSTOMER', 30);
        $table->string('kode_cus', 30)->unique(); // alias modern

        // DATA UTAMA
        $table->string('NAMACUST', 100)->default('');
        $table->string('ALAMAT1', 200)->default('');
        $table->string('ALAMAT2', 200)->default('');
        $table->string('KOTA', 100)->default('');
        $table->string('TELEPON', 100)->default('');
        $table->string('FAX', 100)->default('');
        $table->string('EMAIL', 100)->default('');
        $table->string('KONTAK', 100)->default('');
        $table->string('NPWP', 100)->default('');

        // WILAYAH & STATUS
        $table->string('AREA', 6)->default('');
        $table->string('SUBAREA', 6)->default('');
        $table->string('TYPECUST', 6)->default('');
        $table->string('KOLEKTOR', 6)->default('');
        $table->char('SETATUS', 1)->default('');

        // KEUANGAN
        $table->decimal('SALDO', 10, 0)->default(0);
        $table->decimal('RETURAN', 10, 0)->default(0);
        $table->decimal('TOPKREDIT', 10, 0)->default(0);
        $table->decimal('MAXKREDIT', 10, 0)->default(0);

        // DISKON
        $table->decimal('DISC1', 10, 2)->default(0);
        $table->decimal('DISC2', 10, 2)->default(0);
        $table->decimal('DISC3', 10, 2)->default(0);
        $table->decimal('DISC_REG', 10, 2)->default(0);
        $table->decimal('DISC_CASH', 10, 0)->default(0);

        // INFO UPDATE
        $table->date('TGL_UPDATE')->nullable();
        $table->string('USERID', 20)->default('');

        // DESA
        $table->string('desa', 100)->default('');
        $table->string('camat', 100)->default('');
        $table->string('kabupaten', 100)->default('');

        // PURCHASING
        $table->string('namapur', 100)->default('');
        $table->string('em_pur', 100)->default('');
        $table->string('hp_pur', 100)->default('');

        // STO (tambahan dari schema FoxPro)
        $table->string('nama_sto', 100)->default('');
        $table->string('em_sto', 100)->default('');
        $table->string('hp_sto', 100)->default('');

        // PEMILIK
        $table->string('nama_p', 100)->default('');
        $table->string('ktp_p', 100)->default('');
        $table->string('tempat_l', 100)->default('');
        $table->date('tgll_p')->nullable();
        $table->string('alamat_p', 100)->default('');
        $table->string('desa_p', 100)->default('');
        $table->string('camat_p', 100)->default('');
        $table->string('kab_p', 100)->default('');
        $table->string('tlp_p', 100)->default('');
        $table->string('fax_p', 100)->default('');
        $table->string('email_p', 100)->default('');
        $table->string('npwp_p', 100)->default('');
        $table->string('agama_p', 100)->default('');

        // KONTAK LAIN
        $table->string('kontak_l', 100)->default('');
        $table->string('tlp_kl', 100)->default('');

        // ACCOUNTING
        $table->string('nama_ac', 100)->default('');
        $table->string('em_ac', 100)->default('');
        $table->string('hp_ac', 100)->default('');

        // PAJAK
        $table->string('NM_PAJAK', 100)->default('');
        $table->string('AL_PAJAK', 500)->default('');
        $table->string('AL_PAJAK2', 500)->default('');
        $table->string('NP_PAJAK', 100)->default('');

        // DC
        $table->double('DC', 10, 0)->default(0);

        // Timestamp Laravel optional
        $table->nullableTimestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mcustomer');
    }
};
