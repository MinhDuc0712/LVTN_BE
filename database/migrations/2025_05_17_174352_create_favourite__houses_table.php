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
        Schema::create('favourite__houses', function (Blueprint $table) {
            $table->id('MaYeuThich');
            $table->foreignId('MaNguoiDung')->constrained('users')->onDelete('cascade');
            $table->foreignId('MaNha')->constrained('houses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favourite__houses');
    }
};
