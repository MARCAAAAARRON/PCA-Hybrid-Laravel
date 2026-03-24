<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExcelUpload extends Model
{
    protected $fillable = [
        'file',
        'upload_type',
        'field_site_id',
        'uploaded_by',
        'records_created',
    ];

    public const UPLOAD_TYPES = [
        'distribution' => 'Hybrid Distribution',
        'harvest' => 'Monthly Harvest',
        'nursery' => 'Nursery Operations',
        'pollen' => 'Pollen Production',
    ];

    public function fieldSite(): BelongsTo
    {
        return $this->belongsTo(FieldSite::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function distributionRecords(): HasMany
    {
        return $this->hasMany(HybridDistribution::class, 'upload_id');
    }

    public function harvestRecords(): HasMany
    {
        return $this->hasMany(MonthlyHarvest::class, 'upload_id');
    }

    public function nurseryRecords(): HasMany
    {
        return $this->hasMany(NurseryOperation::class, 'upload_id');
    }

    public function pollenRecords(): HasMany
    {
        return $this->hasMany(PollenProduction::class, 'upload_id');
    }
}
