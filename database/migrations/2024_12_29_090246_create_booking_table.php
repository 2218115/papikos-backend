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
        Schema::create('status_booking', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('booking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pemesan');
            $table->unsignedBigInteger('id_kos');
            $table->unsignedBigInteger('id_status');
            $table->double('nominal');
            $table->integer('waktu_sewa');
            $table->double('total');
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_berakhir');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_pemesan')->references('id')->on('users');
            $table->foreign('id_kos')->references('id')->on('kos');
            $table->foreign('id_status')->references('id')->on('status_booking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
        Schema::dropIfExists('status_booking');
    }
};
