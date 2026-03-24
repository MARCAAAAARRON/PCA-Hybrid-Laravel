<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable, HasApiTokens;

    public const ROLE_CHOICES = [
        'supervisor' => 'COS / Agriculturist',
        'admin' => 'Senior Agriculturist',
        'superadmin' => 'PCDM / Division Chief I',
        'sysadmin' => 'System Administrator',
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'middle_initial',
        'name',
        'email',
        'password',
        'avatar_url',
        'signature_image',
        'role',
        'field_site_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ───── Relationships ─────

    public function fieldSite(): BelongsTo
    {
        return $this->belongsTo(FieldSite::class);
    }

    public function hybridizationRecords(): HasMany
    {
        return $this->hasMany(HybridizationRecord::class, 'created_by');
    }

    public function excelUploads(): HasMany
    {
        return $this->hasMany(ExcelUpload::class, 'uploaded_by');
    }

    public function userNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function generatedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'generated_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ───── Role Helpers ─────

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isSysAdmin(): bool
    {
        return $this->role === 'sysadmin';
    }

    public function getRoleDisplayAttribute(): string
    {
        return self::ROLE_CHOICES[$this->role] ?? $this->role;
    }

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            // If first_name or last_name are dirty, update name
            if ($user->isDirty(['first_name', 'last_name'])) {
                $user->name = trim("{$user->first_name} {$user->last_name}");
            }
            
            // Reverse: If name is present but first/last are empty, split name
            if ($user->name && empty($user->first_name) && empty($user->last_name)) {
                $parts = explode(' ', $user->name, 2);
                $user->first_name = $parts[0];
                $user->last_name = $parts[1] ?? '';
            }
        });

        static::saved(function (User $user) {
            if ($user->wasChanged('role') || $user->wasRecentlyCreated) {
                // Remove old roles and assign the new one from the 'role' column
                // This ensures Spatie roles match the simple 'role' attribute
                $user->syncRoles([$user->role]);
            }
        });
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Only allow users with a valid PCA role to access the admin panel
        return in_array($this->role, array_keys(self::ROLE_CHOICES));
    }
}
