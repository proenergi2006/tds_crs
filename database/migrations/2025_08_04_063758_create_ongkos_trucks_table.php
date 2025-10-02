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
        Schema::create('ongkos_trucks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_transportir');
            $table->unsignedBigInteger('id_angkut_wilayah');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        
            $table->foreign('id_transportir')->references('id')->on('transportirs')->onDelete('cascade');
            $table->foreign('id_angkut_wilayah')->references('id')->on('wilayah_angkuts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ongkos_trucks');
    }
};
