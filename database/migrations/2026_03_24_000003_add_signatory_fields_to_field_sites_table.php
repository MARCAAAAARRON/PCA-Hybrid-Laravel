<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('field_sites', function (Blueprint $table) {
            // Prepared By
            $table->string('prepared_by_label', 50)->default('Prepared by:')->after('description');
            $table->string('prepared_by_name')->nullable()->after('prepared_by_label');
            $table->string('prepared_by_title')->nullable()->after('prepared_by_name');

            // Reviewed By
            $table->string('reviewed_by_label', 50)->default('Reviewed by:')->after('prepared_by_title');
            $table->string('reviewed_by_name')->nullable()->after('reviewed_by_label');
            $table->string('reviewed_by_title')->nullable()->after('reviewed_by_name');

            // Noted By
            $table->string('noted_by_label', 50)->default('Noted by:')->after('reviewed_by_title');
            $table->string('noted_by_name')->nullable()->after('noted_by_label');
            $table->string('noted_by_title')->nullable()->after('noted_by_name');
        });
    }

    public function down(): void
    {
        Schema::table('field_sites', function (Blueprint $table) {
            $table->dropColumn([
                'prepared_by_label', 'prepared_by_name', 'prepared_by_title',
                'reviewed_by_label', 'reviewed_by_name', 'reviewed_by_title',
                'noted_by_label', 'noted_by_name', 'noted_by_title'
            ]);
        });
    }
};
