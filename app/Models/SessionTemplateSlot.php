<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionTemplateSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_template_id',
        'start_time',
        'end_time',
        'capacity',
        'educator_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(SessionTemplate::class, 'session_template_id');
    }

    public function educator()
    {
        return $this->belongsTo(Educator::class);
    }

    public function getLabelAttribute()
    {
        return \Carbon\Carbon::parse($this->start_time)->format('H:i') . ' – ' . \Carbon\Carbon::parse($this->end_time)->format('H:i');
    }
}
