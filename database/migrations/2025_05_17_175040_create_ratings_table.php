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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id('MaDanhGia');
            $table->foreignId('MaNguoiDung')->constrained('users')->onDelete('cascade');
            $table->foreignId('MaNha')->constrained('houses')->onDelete('cascade');
            $table->tinyInteger('SoSao');
            $table->text('NoiDung');
            $table->timestamps('ThoiGian')->useCurrent();
            $table->interger('LuotThich')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
