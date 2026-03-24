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
        Schema::table('nursery_operations', function (Blueprint $table) {
            if (!Schema::hasColumn('nursery_operations', 'nursery_start_date')) {
                $table->date('nursery_start_date')->nullable()->after('report_type');
            }
            if (!Schema::hasColumn('nursery_operations', 'date_ready_for_distribution')) {
                $table->date('date_ready_for_distribution')->nullable()->after('nursery_start_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nursery_operations', function (Blueprint $table) {
            $table->dropColumn(['nursery_start_date', 'date_ready_for_distribution']);
        });
    }
};
