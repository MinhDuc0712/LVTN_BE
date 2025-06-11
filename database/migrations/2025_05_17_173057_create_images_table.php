<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id('MaHinhAnh');
            $table->unsignedBigInteger('MaNha');
            $table->longText('DuongDanHinh')->nullable();
            $table->boolean('LaAnhDaiDien')->default(false);
            $table->timestamps();

            $table->foreign('MaNha')->references('MaNha')->on('houses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
