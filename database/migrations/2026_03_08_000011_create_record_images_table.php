<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('record_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hybridization_record_id')->constrained('hybridization_records')->cascadeOnDelete();
            $table->string('image');
            $table->string('caption', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_images');
    }
};
