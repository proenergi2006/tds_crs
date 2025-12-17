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
            $table->string('dp_persen')->nullable();
            $table->string('dp_keterangan')->nullable();
            $table->string('repayment_persen')->nullable();
            $table->string('repayment_hari')->nullable();
        });
    }
    
    public function down(): void
    {
        Schema::table('penawarans', function (Blueprint $table) {
            $table->dropColumn(['dp_persen', 'dp_keterangan', 'repayment_persen', 'repayment_hari']);
        });
    }
};
