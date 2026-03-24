<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('excel_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->string('upload_type', 20); // distribution, harvest, nursery, pollen
            $table->foreignId('field_site_id')->nullable()->constrained('field_sites')->nullOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('records_created')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('excel_uploads');
    }
};
