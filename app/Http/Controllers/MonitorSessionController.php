<?php

namespace App\Http\Controllers;

use App\Models\SessionTemplate;
use App\Models\Tour;
use App\Models\TourSession;
use Carbon\Carbon;

class MonitorSessionController extends Controller
{
    public function index()
    {
        SessionTemplate::ensureSessionsForDate(Carbon::today());

        $tours = Tour::active()->ordered()->get();
        $tourSessions = [];

        foreach ($tours as $tour) {
            $tourSessions[$tour->id] = TourSession::with('educator')
                ->where('tour_id', $tour->id)
                ->forToday()
                ->fromActiveTemplate()
                ->active()
                ->orderedByTime('start_time', 'asc')
                ->get();
        }

        return view('monitor.index', [
            'tours' => $tours,
            'tourSessions' => $tourSessions,
            'lastUpdated' => Carbon::now(),
        ]);
    }
}
