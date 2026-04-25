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
        Schema::table('nursery_batches', function (Blueprint $table) {
            $table->integer('culled_seednuts')->default(0)->after('seednuts_harvested');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nursery_batches', function (Blueprint $table) {
            $table->dropColumn('culled_seednuts');
        });
    }
};
