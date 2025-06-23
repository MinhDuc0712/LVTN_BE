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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id('MaDanhGia');
            $table->foreignId('MaNguoiDung')->constrained('users')->onDelete('cascade');
            $table->foreignId('MaNha')->constrained('houses')->onDelete('cascade');
            $table->tinyInteger('SoSao');
            $table->text('NoiDung');
            $table->timestamp('ThoiGian')->useCurrent();
            $table->integer('LuotThich')->default(0);
        });

        Schema::create('like_comment', function (Blueprint $table) {
            $table->id();

            $table->timestamps();

            $table->foreignId('MaDanhGia')->constrained('ratings')->onDelete('cascade');
            $table->foreignId('MaNguoiDung')->constrained('users')->onDelete('cascade');
            $table->unique(['MaDanhGia', 'MaNguoiDung']); // Mỗi người chỉ like 1 lần
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('like_comment');
    }
};
