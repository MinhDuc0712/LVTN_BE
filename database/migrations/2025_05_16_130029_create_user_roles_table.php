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
        Schema::create('user_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('MaNguoiDung');
            $table->unsignedBigInteger('MaQuyen');
            $table->foreign('MaNguoiDung')->references('MaNguoiDung')->on('users')->onDelete('cascade');
            $table->foreign('MaQuyen')->references('MaQuyen')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
