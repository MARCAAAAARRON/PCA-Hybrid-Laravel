<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HarvestVariety extends Model
{
    protected $fillable = [
        'monthly_harvest_id',
        'variety',
        'seednuts_type',
        'seednuts_count',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'seednuts_count' => 'integer',
        ];
    }

    public function monthlyHarvest(): BelongsTo
    {
        return $this->belongsTo(MonthlyHarvest::class);
    }
}
