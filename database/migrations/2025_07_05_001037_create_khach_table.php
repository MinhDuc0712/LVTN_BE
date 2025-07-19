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
        Schema::create('khach', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten');
            $table->string('cmnd')->unique();
            $table->string('sdt');
            $table->string('email')->nullable();
            $table->string('dia_chi')->nullable();
            $table->unsignedBigInteger('MaNguoiDung')->nullable();
            $table->foreign('MaNguoiDung')->references('MaNguoiDung')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khach');
    }
};
