<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
        'last_login',
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
        'last_login' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Role checking methods
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isEducator()
    {
        return $this->role === 'educator';
    }

    public function isCashier()
    {
        return $this->role === 'cashier';
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->is_active;
    }

    // Accessors for admin views
    public function getRoleColorAttribute()
    {
        switch ($this->role) {
            case 'admin':
                return 'bg-danger';
            case 'educator':
                return 'bg-warning';
            case 'cashier':
                return 'bg-info';
            default:
                return 'bg-secondary';
        }
    }

    public function getRoleBadgeAttribute()
    {
        switch ($this->role) {
            case 'admin':
                return 'badge-danger';
            case 'educator':
                return 'badge-warning';
            case 'cashier':
                return 'badge-info';
            default:
                return 'badge-secondary';
        }
    }

    public function getRoleIconAttribute()
    {
        switch ($this->role) {
            case 'admin':
                return 'fas fa-user-shield';
            case 'educator':
                return 'fas fa-chalkboard-teacher';
            case 'cashier':
                return 'fas fa-cash-register';
            default:
                return 'fas fa-user';
        }
    }
}