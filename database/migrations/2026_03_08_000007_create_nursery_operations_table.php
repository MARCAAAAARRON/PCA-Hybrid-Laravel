<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nursery_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_site_id')->constrained('field_sites')->cascadeOnDelete();
            $table->foreignId('upload_id')->nullable()->constrained('excel_uploads')->cascadeOnDelete();
            $table->date('report_month')->comment('First day of the reporting month');
            $table->string('report_type', 20)->default('operation'); // operation or terminal

            $table->string('region_province_district', 100)->nullable();
            $table->string('barangay_municipality', 200)->nullable();
            $table->string('proponent_entity', 200)->nullable();
            $table->string('proponent_representative', 200)->nullable();
            $table->unsignedInteger('target_seednuts')->default(0);

            $table->timestamps();

            $table->index(['field_site_id', 'report_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nursery_operations');
    }
};
