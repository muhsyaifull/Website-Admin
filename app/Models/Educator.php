<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Educator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'specialization',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function tourSessions()
    {
        return $this->hasMany(TourSession::class);
    }

    public function activeSessions()
    {
        return $this->hasMany(TourSession::class)->where('is_active', true);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySpecialization($query, $specialization)
    {
        return $query->where('specialization', $specialization);
    }

    /**
     * Methods
     */
    public function canHandleTaman()
    {
        return in_array($this->specialization, ['taman', 'both']);
    }

    public function canHandleMuseum()
    {
        return in_array($this->specialization, ['museum', 'both']);
    }

    // Accessors for admin views
    public function getTodaySessionsAttribute()
    {
        return $this->tourSessions()->whereDate('date', Carbon::today())->count();
    }

    public function getUpcomingSessionsAttribute()
    {
        return $this->tourSessions()->where('date', '>', Carbon::today())->count();
    }

    public function getIsAvailableTodayAttribute()
    {
        return $this->tourSessions()->whereDate('date', Carbon::today())->exists();
    }
}