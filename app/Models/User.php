<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public const SUPER_ADMIN_EMAIL = 'admin@iremetech.com';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
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

    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin();
    }

    public function roleLabel(): string
    {
        return $this->isAdmin() ? 'Admin' : 'Staff';
    }
}
