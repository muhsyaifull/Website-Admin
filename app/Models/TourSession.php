<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TourSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'date',
        'start_time',
        'end_time',
        'label',
        'capacity',
        'booked',
        'educator_id',
        'is_active',
        'sort_order',
        'session_template_id',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function educator()
    {
        return $this->belongsTo(Educator::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function sessionTemplate()
    {
        return $this->belongsTo(SessionTemplate::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_sessions')
            ->withPivot('tour_id')
            ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTour($query, $tourId)
    {
        return $query->where('tour_id', $tourId);
    }

    public function scopeForToday($query)
    {
        return $query->whereDate('date', \Carbon\Carbon::today());
    }

    public function scopeFromActiveTemplate($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('session_template_id')
                ->orWhereHas('sessionTemplate', fn($sq) => $sq->where('is_active', true))
                ->orWhereDate('date', \Carbon\Carbon::today());
        });
    }

    public function scopeOrderedByTime($query)
    {
        return $query->orderBy('sort_order')->orderBy('start_time');
    }

    /**
     * Accessors & Methods
     */
    public function getAvailableAttribute()
    {
        return $this->capacity - $this->booked;
    }

    public function getIsFullAttribute()
    {
        return $this->available === 0;
    }

    public function getIsLowAttribute()
    {
        return $this->available > 0 && $this->available <= 3;
    }

    public function getBookingPercentageAttribute()
    {
        return round(($this->booked / $this->capacity) * 100);
    }

    public function getStatusAttribute()
    {
        if ($this->is_full) {
            return 'FULL';
        } elseif ($this->is_low) {
            return "{$this->available} left";
        } else {
            return "Available {$this->available}";
        }
    }

    public function getBarColorAttribute()
    {
        if ($this->is_full)
            return '#C0392B';
        if ($this->is_low)
            return '#E67E22';
        return '#27AE60';
    }

    public function getStatusBackgroundAttribute()
    {
        if ($this->is_full)
            return '#FDECEA';
        if ($this->is_low)
            return '#FEF9E7';
        return '#EAFAF1';
    }

    public function getStatusColorAttribute()
    {
        if ($this->is_full)
            return '#C0392B';
        if ($this->is_low)
            return '#D35400';
        return '#1E8449';
    }

    public function canAccommodate($participantCount)
    {
        return $this->available >= $participantCount;
    }

    public function isCurrentlyActive()
    {
        $now = Carbon::now()->format('H:i');
        return $now >= $this->start_time->format('H:i') && $now <= $this->end_time->format('H:i');
    }

    public function isUpcoming()
    {
        $now = Carbon::now()->format('H:i');
        $start = $this->start_time->format('H:i');
        $timeDiff = Carbon::createFromTimeString($start)->diffInMinutes(Carbon::createFromTimeString($now));

        return $start > $now && $timeDiff <= 30; // Upcoming in next 30 minutes
    }

    // Additional accessors for admin views
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d M Y');
    }

    public function getDayNameAttribute()
    {
        return $this->date->format('l');
    }

    public function getTimeSlotAttribute()
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    public function getCapacityColorAttribute()
    {
        $percentage = $this->capacity_percentage;

        if ($percentage >= 100)
            return 'bg-danger';
        if ($percentage >= 80)
            return 'bg-warning';
        if ($percentage >= 60)
            return 'bg-info';
        return 'bg-success';
    }

    public function getCapacityPercentageAttribute()
    {
        if ($this->capacity == 0)
            return 0;
        return round(($this->booked / $this->capacity) * 100);
    }

    public function getAvailableSpotsAttribute()
    {
        return max(0, $this->capacity - $this->booked);
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->is_full)
            return 'badge-danger';
        if ($this->is_low)
            return 'badge-warning';
        return 'badge-success';
    }

    public function getStatusLabelAttribute()
    {
        if ($this->is_full)
            return 'Full';
        if ($this->is_low)
            return 'Nearly Full';
        return 'Available';
    }

    public function getCanEditAttribute()
    {
        // Can edit if session is in the future
        return $this->date->isFuture() || ($this->date->isToday() && Carbon::now()->format('H:i') < $this->start_time->format('H:i'));
    }

    public function getCanDeleteAttribute()
    {
        // Can delete if no bookings and session is in the future
        return $this->booked == 0 && ($this->date->isFuture() || ($this->date->isToday() && Carbon::now()->format('H:i') < $this->start_time->format('H:i')));
    }

    public function getTourTypeLabelAttribute()
    {
        return $this->tour ? $this->tour->name : 'Tour';
    }
}