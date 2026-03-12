<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'tour_id',
        'description',
        'is_default',
        'apply_days',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'apply_days' => 'array',
    ];

    public function slots()
    {
        return $this->hasMany(SessionTemplateSlot::class)->orderBy('sort_order');
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function tourSessions()
    {
        return $this->hasMany(TourSession::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTour($query, $tourId)
    {
        return $query->where('tour_id', $tourId);
    }

    // Accessors
    public function getTourTypeLabelAttribute()
    {
        return $this->tour ? $this->tour->name : ($this->type === 'taman' ? 'Taman Atsiri' : 'Museum Atsiri');
    }

    public function getApplyDaysLabelAttribute()
    {
        if ($this->is_default) {
            return 'Default (All Days)';
        }

        if (empty($this->apply_days)) {
            return 'Manual';
        }

        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $days = collect($this->apply_days)->map(fn($d) => $dayNames[$d] ?? $d);
        return $days->join(', ');
    }

    /**
     * Find the best matching template for a given date and tour.
     */
    public static function findForDate($date, $tourId)
    {
        $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;

        $template = static::active()
            ->where('tour_id', $tourId)
            ->where('is_default', false)
            ->whereJsonContains('apply_days', $dayOfWeek)
            ->first();

        if ($template) {
            return $template;
        }

        return static::active()
            ->where('tour_id', $tourId)
            ->where('is_default', true)
            ->where(function ($q) use ($dayOfWeek) {
                $q->whereJsonContains('apply_days', $dayOfWeek)
                    ->orWhereNull('apply_days');
            })
            ->first();
    }

    /**
     * Ensure tour sessions exist for a given date.
     * Auto-generates from all matching active templates for all active tours.
     */
    public static function ensureSessionsForDate($date)
    {
        $date = \Carbon\Carbon::parse($date);
        $dayOfWeek = $date->dayOfWeek;

        $tours = Tour::active()->get();

        foreach ($tours as $tour) {
            $templates = static::active()
                ->where('tour_id', $tour->id)
                ->where(function ($q) use ($dayOfWeek) {
                    $q->whereJsonContains('apply_days', $dayOfWeek)
                        ->orWhere(function ($q2) {
                            $q2->whereNull('apply_days')
                                ->where('is_default', true);
                        });
                })
                ->get();

            $hasSpecific = $templates->where('is_default', false)->isNotEmpty();
            if ($hasSpecific) {
                $templates = $templates->where('is_default', false);
            }

            $matchingIds = $templates->pluck('id')->toArray();
            TourSession::where('tour_id', $tour->id)
                ->whereDate('date', $date)
                ->where('date', '>', \Carbon\Carbon::today())
                ->whereNotNull('session_template_id')
                ->whereNotIn('session_template_id', $matchingIds)
                ->where('booked', 0)
                ->delete();

            foreach ($templates as $template) {
                $existing = TourSession::where('tour_id', $tour->id)
                    ->whereDate('date', $date)
                    ->where('session_template_id', $template->id)
                    ->exists();

                if ($existing) {
                    continue;
                }

                foreach ($template->slots()->where('is_active', true)->get() as $slot) {
                    $educatorId = $slot->educator_id
                        ?? Educator::active()
                            ->whereHas('tours', fn($q) => $q->where('tours.id', $tour->id))
                            ->inRandomOrder()
                            ->first()?->id;

                    if (!$educatorId) {
                        continue;
                    }

                    TourSession::create([
                        'type' => $tour->slug,
                        'tour_id' => $tour->id,
                        'date' => $date->toDateString(),
                        'start_time' => \Carbon\Carbon::parse($slot->start_time)->format('H:i'),
                        'end_time' => \Carbon\Carbon::parse($slot->end_time)->format('H:i'),
                        'label' => $slot->label,
                        'capacity' => $slot->capacity,
                        'booked' => 0,
                        'educator_id' => $educatorId,
                        'is_active' => true,
                        'sort_order' => $slot->sort_order,
                        'session_template_id' => $template->id,
                    ]);
                }
            }
        }
    }
}
