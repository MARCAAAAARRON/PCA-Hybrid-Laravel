<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\FieldSiteScope;
use App\Models\Traits\HasApprovalWorkflow;

class PollenProduction extends Model
{
    use HasApprovalWorkflow;

    protected static function booted(): void
    {
        static::addGlobalScope(new FieldSiteScope);
    }
    protected $fillable = [
        'field_site_id',
        'upload_id',
        'report_month',
        'status',
        'prepared_by',
        'date_prepared',
        'reviewed_by',
        'date_reviewed',
        'noted_by',
        'date_noted',
        'month_label',
        'pollen_variety',
        'ending_balance_prev',
        'pollen_source',
        'date_received',
        'pollens_received',
        'week1',
        'week2',
        'week3',
        'week4',
        'week5',
        'total_utilization',
        'ending_balance',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'report_month' => 'date',
        ];
    }

    public function fieldSite(): BelongsTo
    {
        return $this->belongsTo(FieldSite::class);
    }

    public function upload(): BelongsTo
    {
        return $this->belongsTo(ExcelUpload::class, 'upload_id');
    }
}
