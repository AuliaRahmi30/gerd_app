<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_makans', function (Blueprint $table) {
            $table->id();

            // USER (WAJIB untuk multi-user system)
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            // JAM MAKAN
            $table->time('jam');

            // KETERANGAN (Sarapan, Siang, Malam)
            $table->string('keterangan');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_makans');
    }
};