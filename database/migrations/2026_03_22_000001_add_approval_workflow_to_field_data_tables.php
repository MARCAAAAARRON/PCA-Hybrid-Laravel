<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add approval workflow columns to all field-data tables.
     */
    public function up(): void
    {
        $tables = [
            'hybrid_distributions',
            'monthly_harvests',
            'nursery_operations',
            'pollen_productions',
            'hybridization_records',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->string('status', 20)->default('draft')->after('id');
                }
                if (!Schema::hasColumn($tableName, 'prepared_by')) {
                    $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn($tableName, 'date_prepared')) {
                    $table->dateTime('date_prepared')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'reviewed_by')) {
                    $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn($tableName, 'date_reviewed')) {
                    $table->dateTime('date_reviewed')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'noted_by')) {
                    $table->foreignId('noted_by')->nullable()->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn($tableName, 'date_noted')) {
                    $table->dateTime('date_noted')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'hybrid_distributions',
            'monthly_harvests',
            'nursery_operations',
            'pollen_productions',
            'hybridization_records',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['prepared_by']);
                $table->dropForeign(['reviewed_by']);
                $table->dropForeign(['noted_by']);
                $table->dropColumn([
                    'status',
                    'prepared_by',
                    'date_prepared',
                    'reviewed_by',
                    'date_reviewed',
                    'noted_by',
                    'date_noted',
                ]);
            });
        }
    }
};
