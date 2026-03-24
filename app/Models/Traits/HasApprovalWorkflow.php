<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasApprovalWorkflow
{
    // ───── Relationships ─────

    public function preparedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function reviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function notedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'noted_by');
    }

    // ───── Status Helpers ─────

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPrepared(): bool
    {
        return $this->status === 'prepared';
    }

    public function isReviewed(): bool
    {
        return $this->status === 'reviewed';
    }

    public function isNoted(): bool
    {
        return $this->status === 'noted';
    }

    // ───── Workflow Actions ─────

    /**
     * Mark as Prepared. Implements signatory attribution:
     * If the acting user is NOT a supervisor, attribute to the site's supervisor.
     */
    public function markAsPrepared(User $actingUser): string
    {
        $sigUser = $actingUser;

        // Attribution: If not a supervisor, try to find the site's supervisor
        if ($actingUser->role !== 'supervisor' && $this->field_site_id) {
            $supervisor = User::where('field_site_id', $this->field_site_id)
                ->where('role', 'supervisor')
                ->first();
            if ($supervisor) {
                $sigUser = $supervisor;
            }
        }

        $this->status = 'prepared';
        $this->prepared_by = $sigUser->id;
        $this->date_prepared = now();
        $this->save();

        return "Record prepared (attributed to {$sigUser->name}).";
    }

    /**
     * Mark as Reviewed. Implements:
     * - Maker-checker: cannot review if you prepared it.
     * - Attribution: If a chief/superadmin, attribute to site's admin.
     */
    public function markAsReviewed(User $actingUser): string|false
    {
        // Trapping: Cannot review own prepared record
        if ($this->prepared_by === $actingUser->id) {
            return false;
        }

        $sigUser = $actingUser;

        // Attribution: If a chief, try to find the site's admin
        if (in_array($actingUser->role, ['superadmin', 'sysadmin']) && $this->field_site_id) {
            $adminUser = User::where('field_site_id', $this->field_site_id)
                ->where('role', 'admin')
                ->first();
            if ($adminUser) {
                $sigUser = $adminUser;
            }
        }

        $this->status = 'reviewed';
        $this->reviewed_by = $sigUser->id;
        $this->date_reviewed = now();
        $this->save();

        return "Record reviewed (attributed to {$sigUser->name}).";
    }

    /**
     * Mark as Noted. Implements:
     * - Maker-checker: cannot note if you prepared or reviewed it.
     */
    public function markAsNoted(User $actingUser): string|false
    {
        // Trapping: Cannot note if you prepared or reviewed
        if (in_array($actingUser->id, [$this->prepared_by, $this->reviewed_by])) {
            return false;
        }

        $this->status = 'noted';
        $this->noted_by = $actingUser->id;
        $this->date_noted = now();
        $this->save();

        return 'Record officially noted.';
    }

    /**
     * Return to Draft. Resets all signatories.
     */
    public function returnToDraft(): string
    {
        $this->status = 'draft';
        $this->prepared_by = null;
        $this->date_prepared = null;
        $this->reviewed_by = null;
        $this->date_reviewed = null;
        $this->noted_by = null;
        $this->date_noted = null;
        $this->save();

        return 'Record returned to draft.';
    }
}
