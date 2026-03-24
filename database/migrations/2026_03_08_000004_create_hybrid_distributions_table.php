<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hybrid_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_site_id')->constrained('field_sites')->cascadeOnDelete();
            $table->foreignId('upload_id')->nullable()->constrained('excel_uploads')->cascadeOnDelete();
            $table->date('report_month')->comment('First day of the reporting month');

            // Location
            $table->string('region', 20)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('district', 20)->nullable();
            $table->string('municipality', 100)->nullable();
            $table->string('barangay', 100)->nullable();

            // Farmer name
            $table->string('farmer_last_name', 100)->nullable();
            $table->string('farmer_first_name', 100)->nullable();
            $table->string('farmer_middle_initial', 10)->nullable();

            // Gender
            $table->boolean('is_male')->default(false);
            $table->boolean('is_female')->default(false);

            // Farm location
            $table->string('farm_barangay', 100)->nullable();
            $table->string('farm_municipality', 100)->nullable();
            $table->string('farm_province', 100)->nullable();

            // Distribution data
            $table->string('seedlings_received', 50)->nullable();
            $table->date('date_received')->nullable();
            $table->string('variety', 100)->nullable();
            $table->unsignedInteger('seedlings_planted')->default(0);
            $table->date('date_planted')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['field_site_id', 'report_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hybrid_distributions');
    }
};
