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
        Schema::create('giadichvu', function (Blueprint $table) {
            $table->id();
            $table->string('ten');
             $table->decimal('gia_tri', 10, 2);
            $table->date('ngay_ap_dung');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giadichvu');
    }
};
