<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'position',
        'bio',
        'avatar',
        'is_active',
        'last_login_at',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * FilamentUser implementasyonu için gerekli metod
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // Üretim ortamında uygun izin kontrollerini ekleyin
    }

    /**
     * Get the pages created by the user.
     */
    public function createdPages(): HasMany
    {
        return $this->hasMany(Page::class, 'created_by');
    }

    /**
     * Get the pages updated by the user.
     */
    public function updatedPages(): HasMany
    {
        return $this->hasMany(Page::class, 'updated_by');
    }

    /**
     * Get the settings created by the user.
     */
    public function createdSettings(): HasMany
    {
        return $this->hasMany(Setting::class, 'created_by');
    }

    /**
     * Get the settings updated by the user.
     */
    public function updatedSettings(): HasMany
    {
        return $this->hasMany(Setting::class, 'updated_by');
    }
}
