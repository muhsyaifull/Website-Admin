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

    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'educator_tours');
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

    public function scopeForTour($query, $tourId)
    {
        return $query->whereHas('tours', fn($q) => $q->where('tours.id', $tourId));
    }

    /**
     * Methods
     */
    public function canHandleTour($tourId)
    {
        return $this->tours()->where('tours.id', $tourId)->exists();
    }

    public function canHandleTaman()
    {
        return in_array($this->specialization, ['taman', 'both'])
            || $this->tours()->where('slug', 'taman')->exists();
    }

    public function canHandleMuseum()
    {
        return in_array($this->specialization, ['museum', 'both'])
            || $this->tours()->where('slug', 'museum')->exists();
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