<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_harvests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_site_id')->constrained('field_sites')->cascadeOnDelete();
            $table->foreignId('upload_id')->nullable()->constrained('excel_uploads')->cascadeOnDelete();
            $table->date('report_month')->comment('First day of the reporting month');

            $table->string('location', 200)->nullable()->comment('Farm Location');
            $table->string('farm_name', 200)->nullable()->comment('Name of Partner/Farm');
            $table->string('area_ha', 20)->nullable()->comment('Area (Ha.)');
            $table->string('age_of_palms', 50)->nullable()->comment('Age of Palms (Years)');
            $table->unsignedInteger('num_hybridized_palms')->default(0);
            $table->string('variety', 200)->nullable()->comment('Variety / Hybrid Crosses');
            $table->string('seednuts_produced', 20)->nullable()->comment('Seednuts Produced (OPV/Hybrid)');

            // Monthly production columns
            $table->unsignedInteger('production_jan')->default(0);
            $table->unsignedInteger('production_feb')->default(0);
            $table->unsignedInteger('production_mar')->default(0);
            $table->unsignedInteger('production_apr')->default(0);
            $table->unsignedInteger('production_may')->default(0);
            $table->unsignedInteger('production_jun')->default(0);
            $table->unsignedInteger('production_jul')->default(0);
            $table->unsignedInteger('production_aug')->default(0);
            $table->unsignedInteger('production_sep')->default(0);
            $table->unsignedInteger('production_oct')->default(0);
            $table->unsignedInteger('production_nov')->default(0);
            $table->unsignedInteger('production_dec')->default(0);

            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['field_site_id', 'report_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_harvests');
    }
};
