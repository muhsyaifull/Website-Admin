<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_tours');
    }

    public function educators()
    {
        return $this->belongsToMany(Educator::class, 'educator_tours');
    }

    public function sessionTemplates()
    {
        return $this->hasMany(SessionTemplate::class);
    }

    public function tourSessions()
    {
        return $this->hasMany(TourSession::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
