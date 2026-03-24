<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generated_by')->constrained('users')->cascadeOnDelete();
            $table->string('report_type', 10); // pdf, csv, excel
            $table->foreignId('field_site_id')->nullable()->constrained('field_sites')->nullOnDelete();
            $table->string('title', 200);
            $table->string('file');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
