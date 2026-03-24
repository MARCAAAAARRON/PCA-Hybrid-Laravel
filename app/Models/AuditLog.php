<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_name',
        'object_id',
        'details',
        'ip_address',
    ];

    public const ACTION_CHOICES = [
        'login' => 'Login',
        'logout' => 'Logout',
        'create' => 'Create',
        'update' => 'Update',
        'delete' => 'Delete',
        'submit' => 'Submit',
        'validate' => 'Validate',
        'revision' => 'Request Revision',
        'report' => 'Generate Report',
        'user_mgmt' => 'User Management',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Format details for display.
     */
    public function getFormattedDetailsAttribute(): string
    {
        if (empty($this->details)) {
            return '—';
        }

        $items = [];
        foreach ($this->details as $key => $value) {
            if ($key === 'type' || empty($value)) {
                continue;
            }
            $cleanKey = str_replace('_', ' ', ucwords($key, '_'));
            $items[] = "{$cleanKey}: {$value}";
        }

        if (empty($items)) {
            return $this->details['type'] ?? '—';
        }

        return implode(' | ', $items);
    }
}
