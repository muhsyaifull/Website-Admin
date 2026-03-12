<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TourSession;
use App\Models\Booking;
use App\Models\Educator;
use App\Models\SessionTemplate;
use App\Models\SessionTemplateSlot;
use App\Models\Tour;
use Carbon\Carbon;

class SessionController extends Controller
{
    // ─── Tour Sessions ─────────────────────────────────────────────────

    public function index()
    {
        $today = Carbon::today();
        for ($i = 0; $i <= 7; $i++) {
            SessionTemplate::ensureSessionsForDate($today->copy()->addDays($i));
        }

        $query = TourSession::with(['educator', 'bookings', 'tour'])
            ->fromActiveTemplate();

        $dateFilter = request('date_filter', 'today');
        switch ($dateFilter) {
            case 'today':
                $query->whereDate('date', Carbon::today());
                break;
            case 'tomorrow':
                $query->whereDate('date', Carbon::tomorrow());
                break;
            case 'this_week':
                $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'next_week':
                $query->whereBetween('date', [Carbon::now()->addWeek()->startOfWeek(), Carbon::now()->addWeek()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('date', Carbon::now()->month);
                break;
        }

        if (request('type_filter')) {
            $query->where('tour_id', request('type_filter'));
        }

        if (request('educator_filter')) {
            $query->where('educator_id', request('educator_filter'));
        }

        if (request('capacity_filter')) {
            switch (request('capacity_filter')) {
                case 'available':
                    $query->whereRaw('capacity > (SELECT COALESCE(SUM(total_participants), 0) FROM bookings WHERE (taman_session_id = tour_sessions.id OR museum_session_id = tour_sessions.id))');
                    break;
                case 'full':
                    $query->whereRaw('capacity <= (SELECT COALESCE(SUM(total_participants), 0) FROM bookings WHERE (taman_session_id = tour_sessions.id OR museum_session_id = tour_sessions.id))');
                    break;
                case 'near_full':
                    $query->whereRaw('capacity * 0.8 <= (SELECT COALESCE(SUM(total_participants), 0) FROM bookings WHERE (taman_session_id = tour_sessions.id OR museum_session_id = tour_sessions.id))');
                    break;
            }
        }

        $sessions = $query->orderBy('date', 'asc')->orderBy('start_time', 'asc')->get();

        $todaySessions = TourSession::whereDate('date', Carbon::today())->fromActiveTemplate()->count();
        $fullSessions = TourSession::fromActiveTemplate()->whereRaw('capacity <= (SELECT COALESCE(SUM(total_participants), 0) FROM bookings WHERE (taman_session_id = tour_sessions.id OR museum_session_id = tour_sessions.id))')->count();
        $totalVisitors = Booking::whereDate('visit_date', Carbon::today())->where('status', 'confirmed')->sum('total_participants');
        $bookableSessions = TourSession::whereDate('date', Carbon::today())
            ->fromActiveTemplate()
            ->active()
            ->where('start_time', '>', Carbon::now()->format('H:i:s'))
            ->whereRaw('capacity > (SELECT COALESCE(SUM(total_participants), 0) FROM bookings WHERE (taman_session_id = tour_sessions.id OR museum_session_id = tour_sessions.id))')
            ->count();

        $educators = Educator::where('is_active', true)->get();
        $tours = Tour::active()->ordered()->get();

        return view('admin.sessions.index', compact('sessions', 'educators', 'tours', 'todaySessions', 'fullSessions', 'totalVisitors', 'bookableSessions'));
    }

    public function create()
    {
        $tours = Tour::active()->ordered()->get();
        $educators = Educator::active()->orderBy('name')->get();
        return view('admin.sessions.create', compact('tours', 'educators'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'capacity' => 'required|integer|min:1|max:50',
            'educator_id' => 'required|exists:educators,id',
        ]);

        $tour = Tour::findOrFail($request->tour_id);
        $label = $request->start_time . ' – ' . $request->end_time;

        TourSession::create([
            'type' => $tour->slug,
            'tour_id' => $tour->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'label' => $label,
            'capacity' => $request->capacity,
            'booked' => 0,
            'educator_id' => $request->educator_id,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        return redirect()->route('panel.sessions.index')
            ->with('success', 'Session created successfully!');
    }

    public function edit(TourSession $session)
    {
        $educators = Educator::active()->get();
        $tours = Tour::active()->ordered()->get();
        $session->load('tour');
        return view('admin.sessions.edit', compact('session', 'educators', 'tours'));
    }

    public function update(Request $request, TourSession $session)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'capacity' => 'required|integer|min:1|max:50',
            'educator_id' => 'required|exists:educators,id',
            'is_active' => 'boolean',
        ]);

        $label = $request->start_time . ' – ' . $request->end_time;

        $session->update([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'label' => $label,
            'capacity' => max($request->capacity, $session->booked),
            'educator_id' => $request->educator_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('panel.sessions.index')
            ->with('success', 'Tour session updated successfully!');
    }

    public function toggle(TourSession $session)
    {
        $session->update(['is_active' => !$session->is_active]);

        $status = $session->is_active ? 'activated' : 'deactivated';
        return redirect()->route('panel.sessions.index')
            ->with('success', "Session successfully {$status}!");
    }

    public function destroy(TourSession $session)
    {
        if ($session->booked > 0) {
            return redirect()->route('panel.sessions.index')
                ->with('error', 'Cannot delete session that already has bookings!');
        }

        $session->delete();

        return redirect()->route('panel.sessions.index')
            ->with('success', 'Session deleted successfully!');
    }

    // ─── Session Templates ─────────────────────────────────────────────

    public function templates()
    {
        $templates = SessionTemplate::withCount('slots')
            ->with(['slots.educator', 'tour'])
            ->orderBy('tour_id')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        $tours = Tour::active()->ordered()->get();

        return view('admin.templates.index', compact('templates', 'tours'));
    }

    public function createTemplate()
    {
        $educators = Educator::active()->get();
        $tours = Tour::active()->ordered()->get();
        return view('admin.templates.create', compact('educators', 'tours'));
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'tour_id' => 'required|exists:tours,id',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'apply_days' => 'nullable|array',
            'apply_days.*' => 'integer|between:0,6',
            'slots' => 'required|array|min:1',
            'slots.*.start_time' => 'required|date_format:H:i',
            'slots.*.end_time' => 'required|date_format:H:i',
            'slots.*.capacity' => 'required|integer|min:1|max:50',
            'slots.*.educator_id' => 'nullable|exists:educators,id',
        ]);

        $tour = Tour::findOrFail($request->tour_id);

        if ($request->is_default) {
            SessionTemplate::where('tour_id', $request->tour_id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $isDefault = $request->boolean('is_default');
        $applyDays = $request->apply_days ? array_map('intval', $request->apply_days) : ($isDefault ? null : []);

        $template = SessionTemplate::create([
            'name' => $request->name,
            'type' => $tour->slug,
            'tour_id' => $tour->id,
            'description' => $request->description,
            'is_default' => $isDefault,
            'apply_days' => $applyDays,
            'is_active' => true,
        ]);

        foreach ($request->slots as $index => $slot) {
            $template->slots()->create([
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
                'capacity' => $slot['capacity'],
                'educator_id' => $slot['educator_id'] ?? null,
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }

        return redirect()->route('panel.templates.index')
            ->with('success', 'Session template created successfully!');
    }

    public function editTemplate(SessionTemplate $template)
    {
        $template->load('slots.educator');
        $educators = Educator::active()->get();
        $tours = Tour::active()->ordered()->get();
        return view('admin.templates.edit', compact('template', 'educators', 'tours'));
    }

    public function updateTemplate(Request $request, SessionTemplate $template)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'apply_days' => 'nullable|array',
            'apply_days.*' => 'integer|between:0,6',
            'slots' => 'required|array|min:1',
            'slots.*.id' => 'nullable|exists:session_template_slots,id',
            'slots.*.start_time' => 'required|date_format:H:i',
            'slots.*.end_time' => 'required|date_format:H:i',
            'slots.*.capacity' => 'required|integer|min:1|max:50',
            'slots.*.educator_id' => 'nullable|exists:educators,id',
        ]);

        if ($request->is_default) {
            SessionTemplate::where('tour_id', $template->tour_id)
                ->where('is_default', true)
                ->where('id', '!=', $template->id)
                ->update(['is_default' => false]);
        }

        $isDefault = $request->boolean('is_default');
        $applyDays = $request->apply_days ? array_map('intval', $request->apply_days) : ($isDefault ? null : []);

        $template->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_default' => $isDefault,
            'apply_days' => $applyDays,
        ]);

        $keepIds = [];
        foreach ($request->slots as $index => $slotData) {
            if (!empty($slotData['id'])) {
                $slot = $template->slots()->find($slotData['id']);
                if ($slot) {
                    $slot->update([
                        'start_time' => $slotData['start_time'],
                        'end_time' => $slotData['end_time'],
                        'capacity' => $slotData['capacity'],
                        'educator_id' => $slotData['educator_id'] ?? null,
                        'sort_order' => $index + 1,
                    ]);
                    $keepIds[] = $slot->id;
                }
            } else {
                $newSlot = $template->slots()->create([
                    'start_time' => $slotData['start_time'],
                    'end_time' => $slotData['end_time'],
                    'capacity' => $slotData['capacity'],
                    'educator_id' => $slotData['educator_id'] ?? null,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]);
                $keepIds[] = $newSlot->id;
            }
        }

        $template->slots()->whereNotIn('id', $keepIds)->delete();

        $this->syncSessionsFromTemplate($template);

        return redirect()->route('panel.templates.index')
            ->with('success', 'Session template updated successfully!');
    }

    public function deleteTemplate(SessionTemplate $template)
    {
        $template->delete();
        return redirect()->route('panel.templates.index')
            ->with('success', 'Template deleted successfully!');
    }

    public function toggleTemplate(SessionTemplate $template)
    {
        $template->update(['is_active' => !$template->is_active]);
        $status = $template->is_active ? 'activated' : 'deactivated';
        return redirect()->route('panel.templates.index')
            ->with('success', "Template successfully {$status}!");
    }

    private function syncSessionsFromTemplate(SessionTemplate $template)
    {
        $today = Carbon::today();

        TourSession::where('session_template_id', $template->id)
            ->whereDate('date', '>=', $today)
            ->where('booked', 0)
            ->delete();

        for ($i = 0; $i <= 7; $i++) {
            SessionTemplate::ensureSessionsForDate($today->copy()->addDays($i));
        }
    }
}
