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
        'color',
        'bg_color',
        'has_saldo',
        'saldo_amount',
        'has_resto',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'saldo_amount' => 'decimal:0',
        'includes' => 'array',
        'has_saldo' => 'boolean',
        'has_resto' => 'boolean',
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
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedSaldoAmountAttribute()
    {
        return $this->saldo_amount ? 'Rp ' . number_format($this->saldo_amount, 0, ',', '.') : null;
    }

    public function getFormattedRevenueAttribute()
    {
        $revenue = $this->bookings->sum('total_price');
        return 'Rp ' . number_format($revenue, 0, ',', '.');
    }
}