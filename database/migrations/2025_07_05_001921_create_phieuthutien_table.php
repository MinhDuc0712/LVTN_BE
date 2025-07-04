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
        Schema::create('phieuthutien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hopdong_id')->constrained()->onDelete('cascade');
            $table->decimal('so_tien', 10, 2);
            $table->date('ngay_thu');
            $table->string('noi_dung')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieuthutien');
    }
};
