<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'description',
        'price',
        'includes',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'includes' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'package_tours');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Accessors
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format((float) ($this->price ?? 0), 0, ',', '.');
    }

    public function getFormattedRevenueAttribute()
    {
        $revenue = $this->bookings()->sum('total_price');
        return 'Rp ' . number_format((float) $revenue, 0, ',', '.');
    }
}