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
        Schema::create('utilities', function (Blueprint $table) {
            $table->id('MaTienIch');
            $table->string('TenTienIch')->unique();
        });

        // Bảng trung gian cho quan hệ nhiều nhiều
        Schema::create('house_utility', function (Blueprint $table) {
            $table->foreignId('MaNha')->constrained('houses')->onDelete('cascade');
            $table->foreignId('MaTienIch')->constrained('utilities')->onDelete('cascade');
            $table->primary(['MaNha', 'MaTienIch']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilities');
    }
};
