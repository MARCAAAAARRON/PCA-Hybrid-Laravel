<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldSite extends Model
{
    protected $fillable = [
        'name',
        'description',
        'prepared_by_label',
        'prepared_by_name',
        'prepared_by_title',
        'reviewed_by_label',
        'reviewed_by_name',
        'reviewed_by_title',
        'noted_by_label',
        'noted_by_name',
        'noted_by_title',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(HybridDistribution::class);
    }

    public function harvests(): HasMany
    {
        return $this->hasMany(MonthlyHarvest::class);
    }

    public function nurseryOperations(): HasMany
    {
        return $this->hasMany(NurseryOperation::class);
    }

    public function pollenRecords(): HasMany
    {
        return $this->hasMany(PollenProduction::class);
    }

    public function hybridizationRecords(): HasMany
    {
        return $this->hasMany(HybridizationRecord::class);
    }

    public function excelUploads(): HasMany
    {
        return $this->hasMany(ExcelUpload::class);
    }
}
