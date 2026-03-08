<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TourSession;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Educator;
use App\Models\SessionTemplate;
use Carbon\Carbon;

class EducatorController extends Controller
{
    /**
     * Display educator dashboard
     */
    public function index()
    {
        $today = Carbon::today();
        $now = Carbon::now();

        // Auto-generate sessions for today from templates
        SessionTemplate::ensureSessionsForDate($today);

        // Get today's tour sessions from active templates
        $tamanSessions = TourSession::with([
            'educator',
            'tamanBookings' => function ($q) use ($today) {
                $q->whereDate('visit_date', $today)->confirmed();
            }
        ])
            ->where('type', 'taman')
            ->forToday()
            ->fromActiveTemplate()
            ->active()
            ->orderedByTime()
            ->get();

        $museumSessions = TourSession::with([
            'educator',
            'museumBookings' => function ($q) use ($today) {
                $q->whereDate('visit_date', $today)->confirmed();
            }
        ])
            ->where('type', 'museum')
            ->forToday()
            ->fromActiveTemplate()
            ->active()
            ->orderedByTime()
            ->get();

        // Check for upcoming sessions (within 30 minutes)
        $upcomingSessions = collect($tamanSessions)->merge($museumSessions)
            ->filter(function ($session) {
                return $session->isUpcoming();
            });

        // Get today's visitor statistics
        $todaysBookings = Booking::today()->confirmed()->get();
        $totalVisitors = $todaysBookings->sum('total_participants');
        $totalRevenue = $todaysBookings->sum('total_price');

        return view('educator.index', compact(
            'tamanSessions',
            'museumSessions',
            'upcomingSessions',
            'totalVisitors',
            'totalRevenue'
        ));
    }

    /**
     * Package management
     */
    public function packages()
    {
        $packages = Package::orderBy('created_at', 'desc')->get();

        return view('educator.packages.index', compact('packages'));
    }

    public function createPackage()
    {
        return view('educator.packages.create');
    }

    public function storePackage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'label' => 'required|string|max:50',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'includes' => 'required|array',
            'includes.*' => 'string',
            'color' => 'required|string|max:7',
            'bg_color' => 'required|string|max:7',
            'has_saldo' => 'boolean',
            'saldo_amount' => 'nullable|numeric|min:0',
            'has_resto' => 'boolean',
        ]);

        Package::create($request->all());

        return redirect()->route('educator.packages')
            ->with('success', 'Package created successfully!');
    }

    public function editPackage(Package $package)
    {
        return view('educator.packages.edit', compact('package'));
    }

    public function updatePackage(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'label' => 'required|string|max:50',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'includes' => 'required|array',
            'includes.*' => 'string',
            'color' => 'required|string|max:7',
            'bg_color' => 'required|string|max:7',
            'has_saldo' => 'boolean',
            'saldo_amount' => 'nullable|numeric|min:0',
            'has_resto' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['has_saldo'] = $request->has('has_saldo') ? 1 : 0;
        $data['has_resto'] = $request->has('has_resto') ? 1 : 0;
        if (!$data['has_saldo']) {
            $data['saldo_amount'] = 0;
        }

        $package->update($data);

        return redirect()->route('educator.packages')
            ->with('success', 'Package updated successfully!');
    }

    public function deletePackage(Package $package)
    {
        $package->delete();

        return redirect()->route('educator.packages')
            ->with('success', 'Package deleted successfully!');
    }

    /**
     * Tour session management
     */
    public function sessions()
    {
        $tamanSessions = TourSession::with('educator')->where('type', 'taman')->orderedByTime()->get();
        $museumSessions = TourSession::with('educator')->where('type', 'museum')->orderedByTime()->get();
        $educators = Educator::active()->get();

        return view('educator.sessions.index', compact('tamanSessions', 'museumSessions', 'educators'));
    }

    public function createSession()
    {
        $educators = Educator::active()->get();
        return view('educator.sessions.create', compact('educators'));
    }

    public function storeSession(Request $request)
    {
        $request->validate([
            'type' => 'required|in:taman,museum',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'capacity' => 'required|integer|min:1|max:50',
            'educator_id' => 'required|exists:educators,id',
        ]);

        // Generate label
        $label = $request->start_time . ' – ' . $request->end_time;

        // Get next sort order
        $maxSortOrder = TourSession::where('type', $request->type)->max('sort_order') ?? 0;

        TourSession::create([
            'type' => $request->type,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'label' => $label,
            'capacity' => $request->capacity,
            'educator_id' => $request->educator_id,
            'is_active' => true,
            'sort_order' => $maxSortOrder + 1,
        ]);

        return redirect()->route('educator.sessions')
            ->with('success', 'Tour session created successfully!');
    }

    public function editSession(TourSession $session)
    {
        $educators = Educator::active()->get();
        return view('educator.sessions.edit', compact('session', 'educators'));
    }

    public function updateSession(Request $request, TourSession $session)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'capacity' => 'required|integer|min:1|max:50',
            'educator_id' => 'required|exists:educators,id',
            'is_active' => 'boolean',
        ]);

        $label = $request->start_time . ' – ' . $request->end_time;

        $session->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'label' => $label,
            'capacity' => max($request->capacity, $session->booked), // Can't reduce below booked
            'educator_id' => $request->educator_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('educator.sessions')
            ->with('success', 'Tour session updated successfully!');
    }

    public function toggleSession(TourSession $session)
    {
        $session->update(['is_active' => !$session->is_active]);

        $status = $session->is_active ? 'activated' : 'deactivated';
        return redirect()->route('educator.sessions')
            ->with('success', "Session successfully {$status}!");
    }

    public function deleteSession(TourSession $session)
    {
        if ($session->booked > 0) {
            return redirect()->route('educator.sessions')
                ->with('error', 'Cannot delete session that already has bookings!');
        }

        $session->delete();

        return redirect()->route('educator.sessions')
            ->with('success', 'Session deleted successfully!');
    }

    /**
     * Educator management
     */
    public function educators()
    {
        $educators = Educator::orderBy('name')->get();

        return view('educator.educators.index', compact('educators'));
    }

    public function createEducator()
    {
        return view('educator.educators.create');
    }

    public function storeEducator(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'required|in:taman,museum,both',
        ]);

        Educator::create($request->all());

        return redirect()->route('educator.educators')
            ->with('success', 'Educator created successfully!');
    }

    public function editEducator(Educator $educator)
    {
        return view('educator.educators.edit', compact('educator'));
    }

    public function updateEducator(Request $request, Educator $educator)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'required|in:taman,museum,both',
            'is_active' => 'boolean',
        ]);

        $educator->update($request->all());

        return redirect()->route('educator.educators')
            ->with('success', 'Educator updated successfully!');
    }
}