<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pollen_productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_site_id')->constrained('field_sites')->cascadeOnDelete();
            $table->foreignId('upload_id')->nullable()->constrained('excel_uploads')->cascadeOnDelete();
            $table->date('report_month')->comment('First day of the reporting month');

            $table->string('month_label', 20)->nullable();
            $table->string('pollen_variety', 200)->nullable();
            $table->string('ending_balance_prev', 50)->nullable()->comment('Ending Balance Last Month');

            // Pollens received
            $table->string('pollen_source', 200)->nullable();
            $table->string('date_received', 50)->nullable();
            $table->string('pollens_received', 50)->nullable();

            // Weekly utilization
            $table->string('week1', 20)->nullable();
            $table->string('week2', 20)->nullable();
            $table->string('week3', 20)->nullable();
            $table->string('week4', 20)->nullable();
            $table->string('week5', 20)->nullable();
            $table->string('total_utilization', 20)->nullable();

            $table->string('ending_balance', 50)->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['field_site_id', 'report_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pollen_productions');
    }
};
