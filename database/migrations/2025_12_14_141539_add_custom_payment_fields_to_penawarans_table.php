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
        Schema::table('penawarans', function (Blueprint $table) {
            $table->string('dp_persen', 10)->nullable()->after('tipe_pembayaran');
            $table->string('dp_keterangan', 100)->nullable()->after('dp_persen');
            $table->string('repayment_persen', 10)->nullable()->after('dp_keterangan');
            $table->string('repayment_hari', 10)->nullable()->after('repayment_persen');
        });
    }

    public function down(): void
    {
        Schema::table('penawarans', function (Blueprint $table) {
            $table->dropColumn(['dp_persen', 'dp_keterangan', 'repayment_persen', 'repayment_hari']);
        });
    }
};
