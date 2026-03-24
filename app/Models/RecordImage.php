<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordImage extends Model
{
    protected $fillable = [
        'hybridization_record_id',
        'image',
        'caption',
    ];

    public function hybridizationRecord(): BelongsTo
    {
        return $this->belongsTo(HybridizationRecord::class);
    }
}
