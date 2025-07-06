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
        Schema::create('phong_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phong_id')
                ->constrained('phong')
                ->onDelete('cascade');
            $table->string('image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phong_images');
    }
};
