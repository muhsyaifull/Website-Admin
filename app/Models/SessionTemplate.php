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

    public function tourSessions()
    {
        return $this->hasMany(TourSession::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTaman($query)
    {
        return $query->where('type', 'taman');
    }

    public function scopeMuseum($query)
    {
        return $query->where('type', 'museum');
    }

    // Accessors
    public function getTourTypeLabelAttribute()
    {
        return $this->type === 'taman' ? 'Taman Atsiri' : 'Museum Atsiri';
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
     * Find the best matching template for a given date and type.
     * Priority: specific day match > default (if day matches or no days set) > null
     */
    public static function findForDate($date, $type)
    {
        $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek; // 0=Sun, 6=Sat

        // First: look for active non-default template with matching day
        $template = static::active()
            ->where('type', $type)
            ->where('is_default', false)
            ->whereJsonContains('apply_days', $dayOfWeek)
            ->first();

        if ($template) {
            return $template;
        }

        // Fallback: default template, but only if its apply_days includes this day or is empty
        return static::active()
            ->where('type', $type)
            ->where('is_default', true)
            ->where(function ($q) use ($dayOfWeek) {
                $q->whereJsonContains('apply_days', $dayOfWeek)
                    ->orWhereNull('apply_days')
                    ->orWhereRaw('JSON_LENGTH(apply_days) = 0');
            })
            ->first();
    }

    /**
     * Ensure tour sessions exist for a given date.
     * Auto-generates from all matching active templates.
     * apply_days is always respected — even for default templates.
     */
    public static function ensureSessionsForDate($date)
    {
        $date = \Carbon\Carbon::parse($date);
        $dayOfWeek = $date->dayOfWeek;

        foreach (['taman', 'museum'] as $type) {
            // Get all active templates whose apply_days match this day (or is empty/null for defaults)
            $templates = static::active()
                ->where('type', $type)
                ->where(function ($q) use ($dayOfWeek) {
                    $q->whereJsonContains('apply_days', $dayOfWeek)
                        ->orWhereNull('apply_days')
                        ->orWhereRaw('JSON_LENGTH(apply_days) = 0');
                })
                ->get();

            // If a specific (non-default) template matches, exclude defaults
            $hasSpecific = $templates->where('is_default', false)->isNotEmpty();
            if ($hasSpecific) {
                $templates = $templates->where('is_default', false);
            }

            // Clean up sessions from non-matching templates (only those with 0 bookings)
            $matchingIds = $templates->pluck('id')->toArray();
            TourSession::where('type', $type)
                ->whereDate('date', $date)
                ->whereNotNull('session_template_id')
                ->whereNotIn('session_template_id', $matchingIds)
                ->where('booked', 0)
                ->delete();

            foreach ($templates as $template) {
                // Skip if sessions from THIS template already exist for this date
                $existing = TourSession::where('type', $type)
                    ->whereDate('date', $date)
                    ->where('session_template_id', $template->id)
                    ->exists();

                if ($existing) {
                    continue;
                }

                foreach ($template->slots()->where('is_active', true)->get() as $slot) {
                    $educatorId = $slot->educator_id
                        ?? Educator::active()->where('specialization', $type)->inRandomOrder()->first()?->id;

                    if (!$educatorId) {
                        continue;
                    }

                    TourSession::create([
                        'type' => $type,
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
