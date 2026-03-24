<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hybridization_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_site_id')->constrained('field_sites')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->string('crop_type', 100);
            $table->string('parent_line_a', 100);
            $table->string('parent_line_b', 100);
            $table->string('hybrid_code', 50)->unique();
            $table->date('date_planted');
            $table->string('growth_status', 20)->default('seedling');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('draft');
            $table->text('admin_remarks')->nullable();

            $table->timestamps();

            $table->index(['field_site_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hybridization_records');
    }
};
