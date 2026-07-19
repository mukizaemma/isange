<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    public const ROLE_STAFF = '0';

    public const ROLE_ADMIN = '1';

    public const ROLE_GUEST = 'guest';

    public const SUPER_ADMIN_EMAIL = 'admin@iremetech.com';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at',
        'marketing_opt_in',
        'marketing_consented_at',
        'marketing_unsubscribe_token',
        'email_otp_hash',
        'email_otp_expires_at',
        'email_otp_attempts',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'email_otp_hash',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'marketing_opt_in' => 'boolean',
        'marketing_consented_at' => 'datetime',
        'email_otp_expires_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    public function foodorders()
    {
        return $this->hasMany(Foodorder::class);
    }

    public function isSuperAdmin(): bool
    {
        return strtolower((string) $this->email) === self::SUPER_ADMIN_EMAIL;
    }

    public function isAdmin(): bool
    {
        return (string) $this->role === self::ROLE_ADMIN;
    }

    public function isGuest(): bool
    {
        return (string) $this->role === self::ROLE_GUEST;
    }

    public function hasUnlockedDiscount(): bool
    {
        return $this->isGuest() && $this->email_verified_at !== null;
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(GuestBookingRequest::class);
    }

    public function updateRecipients(): HasMany
    {
        return $this->hasMany(GuestUpdateRecipient::class);
    }

    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin();
    }

    public function roleLabel(): string
    {
        return $this->isAdmin() ? 'Admin' : ($this->isGuest() ? 'Guest' : 'Staff');
    }
}
