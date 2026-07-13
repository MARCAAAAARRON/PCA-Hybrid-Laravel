<?php

namespace App\Models\Concerns;

trait HasWorkflowStatus
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PREPARED = 'prepared';
    public const STATUS_REVIEWED = 'reviewed';
    public const STATUS_NOTED = 'noted';

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }
}