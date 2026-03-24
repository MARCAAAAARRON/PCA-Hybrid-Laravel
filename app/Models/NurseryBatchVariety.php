<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NurseryBatchVariety extends Model
{
    protected $fillable = [
        'nursery_batch_id',
        'variety',
        'seednuts_sown',
        'date_sown',
        'seedlings_germinated',
        'ungerminated_seednuts',
        'culled_seedlings',
        'good_seedlings',
        'ready_to_plant',
        'seedlings_dispatched',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'seednuts_sown' => 'integer',
            'seedlings_germinated' => 'integer',
            'ungerminated_seednuts' => 'integer',
            'culled_seedlings' => 'integer',
            'good_seedlings' => 'integer',
            'ready_to_plant' => 'integer',
            'seedlings_dispatched' => 'integer',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(NurseryBatch::class, 'nursery_batch_id');
    }
}
