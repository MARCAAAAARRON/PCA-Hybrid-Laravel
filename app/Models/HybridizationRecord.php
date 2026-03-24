<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Scopes\FieldSiteScope;
use App\Models\Traits\HasApprovalWorkflow;

class HybridizationRecord extends Model implements HasMedia
{
    use InteractsWithMedia, HasApprovalWorkflow;

    protected static function booted(): void
    {
        static::addGlobalScope(new FieldSiteScope);
    }

    protected $fillable = [
        'field_site_id',
        'created_by',
        'crop_type',
        'parent_line_a',
        'parent_line_b',
        'hybrid_code',
        'date_planted',
        'growth_status',
        'notes',
        'status',
        'prepared_by',
        'date_prepared',
        'reviewed_by',
        'date_reviewed',
        'noted_by',
        'date_noted',
        'admin_remarks',
    ];

    public const STATUS_CHOICES = [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'validated' => 'Validated',
        'revision' => 'Needs Revision',
    ];

    public const GROWTH_STATUS_CHOICES = [
        'seedling' => 'Seedling',
        'vegetative' => 'Vegetative',
        'flowering' => 'Flowering',
        'fruiting' => 'Fruiting',
        'harvested' => 'Harvested',
    ];

    protected function casts(): array
    {
        return [
            'date_planted' => 'date',
        ];
    }

    public function fieldSite(): BelongsTo
    {
        return $this->belongsTo(FieldSite::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(RecordImage::class);
    }

    /**
     * Register media collections for Spatie Media Library.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('field_images');
    }
}
