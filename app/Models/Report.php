<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'generated_by',
        'report_type',
        'field_site_id',
        'title',
        'file',
    ];

    public const REPORT_TYPES = [
        'pdf' => 'PDF Report',
        'csv' => 'CSV Export',
        'excel' => 'Excel Export',
    ];

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function fieldSite(): BelongsTo
    {
        return $this->belongsTo(FieldSite::class);
    }
}
