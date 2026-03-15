<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'package_id',
        'user_id',
        'representative_name',
        'representative_address',
        'representative_phone',
        'adult_count',
        'child_count',
        'total_participants',
        'unit_price',
        'total_price',
        'visit_date',
        'status',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'unit_price' => 'decimal:0',
        'total_price' => 'decimal:0',
    ];

    /**
     * Boot method to generate booking code
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->booking_code)) {
                $model->booking_code = static::generateBookingCode();
            }
        });
    }

    /**
     * Relationships
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookingSessions()
    {
        return $this->belongsToMany(TourSession::class, 'booking_sessions')
            ->withPivot('tour_id')
            ->withTimestamps();
    }

    public function getSessionForTour($tourId)
    {
        return $this->bookingSessions()->wherePivot('tour_id', $tourId)->first();
    }

    /**
     * Scopes
     */
    public function scopeToday($query)
    {
        return $query->whereDate('visit_date', Carbon::today());
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Accessors & Methods
     */
    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getFormattedUnitPriceAttribute()
    {
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }

    public function getFormattedVisitDateAttribute()
    {
        return $this->visit_date->format('d M Y');
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => '#F39C12',
            'confirmed' => '#27AE60',
            'completed' => '#3498DB',
            'cancelled' => '#E74C3C',
        ];

        return $colors[$this->status] ?? '#7F8C8D';
    }

    /**
     * Static methods
     */
    public static function generateBookingCode()
    {
        $date = Carbon::now()->format('ymd');
        $sequence = static::whereDate('created_at', Carbon::today())->count() + 1;
        return 'TRK' . $date . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate total participants for adults only (for pricing)
     */
    public function getTotalPaidParticipantsAttribute()
    {
        return $this->adult_count; // Only adults are counted for payment
    }
}