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
        Schema::create('tipe_kos', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('kos', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->double('harga_kos');
            $table->string('minimal_sewa');
            $table->string('lokasi_kos');
            $table->integer('kamar_tersedia');
            $table->string('narahubung_kos');
            $table->unsignedBigInteger('id_tipe_kos');
            $table->unsignedBigInteger('id_pemilik');
            $table->text('embed_gmaps');
            $table->double('total_rating');
            $table->timestamps();

            $table->foreign('id_tipe_kos')->references('id')->on('tipe_kos');
            $table->foreign('id_pemilik')->references('id')->on('users');
        });

        Schema::create('kos_ulasan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kos');
            $table->unsignedBigInteger('id_pemberi_ulasan');
            $table->unsignedBigInteger('id_balasan')->nullable();
            $table->double('rating');
            $table->text('ulasan');
            $table->timestamps();

            $table->foreign('id_kos')->references('id')->on('kos');
            $table->foreign('id_pemberi_ulasan')->references('id')->on('users');
            $table->foreign('id_balasan')->references('id')->on('kos_ulasan');
        });

        Schema::create('kos_fotos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kos');
            $table->string('path');
            $table->timestamps();

            $table->foreign('id_kos')->references('id')->on('kos');
        });

        Schema::create('kos_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kos');
            $table->string('nama');
            $table->timestamps();

            $table->foreign('id_kos')->references('id')->on('kos');
        });

        Schema::create('kos_peraturan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kos');
            $table->string('nama');
            $table->timestamps();

            $table->foreign('id_kos')->references('id')->on('kos');
        });

        Schema::create('kos_status', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('kos_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kos');
            $table->unsignedBigInteger('id_status');
            $table->unsignedBigInteger('id_pembuat');
            $table->string('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_kos')->references('id')->on('kos');
            $table->foreign('id_status')->references('id')->on('kos_status');
            $table->foreign('id_pembuat')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kos');
    }
};
