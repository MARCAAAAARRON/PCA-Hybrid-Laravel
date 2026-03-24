<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nursery_batch_varieties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nursery_batch_id')->constrained('nursery_batches')->cascadeOnDelete();

            $table->string('variety', 100)->nullable();
            $table->unsignedInteger('seednuts_sown')->default(0);
            $table->string('date_sown', 50)->nullable();
            $table->unsignedInteger('seedlings_germinated')->default(0);
            $table->unsignedInteger('ungerminated_seednuts')->default(0);
            $table->unsignedInteger('culled_seedlings')->default(0);
            $table->unsignedInteger('good_seedlings')->default(0)->comment('Good Seedlings @ 1 ft tall');
            $table->unsignedInteger('ready_to_plant')->default(0)->comment('Ready to Plant (Polybagged)');
            $table->unsignedInteger('seedlings_dispatched')->default(0);
            $table->string('remarks', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nursery_batch_varieties');
    }
};
