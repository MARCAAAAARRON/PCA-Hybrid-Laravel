<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NurseryBatch extends Model
{
    protected $fillable = [
        'nursery_operation_id',
        'seednuts_harvested',
        'culled_seednuts',
        'date_harvested',
        'date_received',
        'source_of_seednuts',
    ];

    protected function casts(): array
    {
        return [
            'seednuts_harvested' => 'integer',
            'culled_seednuts' => 'integer',
        ];
    }

    public function nurseryOperation(): BelongsTo
    {
        return $this->belongsTo(NurseryOperation::class);
    }

    public function varieties(): HasMany
    {
        return $this->hasMany(NurseryBatchVariety::class);
    }
}
