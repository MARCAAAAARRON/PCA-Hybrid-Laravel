<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harvest_varieties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_harvest_id')->constrained('monthly_harvests')->cascadeOnDelete();
            $table->string('variety', 200);
            $table->string('seednuts_type', 20)->nullable()->comment('OPV or Hybrid');
            $table->unsignedInteger('seednuts_count')->default(0);
            $table->string('remarks', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvest_varieties');
    }
};
