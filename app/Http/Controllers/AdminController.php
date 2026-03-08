<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Package;
use App\Models\TourSession;
use App\Models\Booking;
use App\Models\Educator;
use App\Models\SessionTemplate;
use App\Models\SessionTemplateSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        // Ensure today's sessions are generated from templates
        SessionTemplate::ensureSessionsForDate(Carbon::today());

        // Get system statistics
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $totalBookings = Booking::count();
        $todaysBookings = Booking::today()->count();
        $totalRevenue = Booking::sum('total_price');
        $todaysRevenue = Booking::today()->sum('total_price');

        // Recent activities
        $recentBookings = Booking::with(['package', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.index', compact(
            'totalUsers',
            'activeUsers',
            'totalBookings',
            'todaysBookings',
            'totalRevenue',
            'todaysRevenue',
            'recentBookings',
            'recentUsers'
        ));
    }

    /**
     * User management
     */
    public function users()
    {
        $users = User::withCount([
            'bookings as bookings_count' => function ($query) {
                // Only count bookings for cashier users
            }
        ])->orderBy('created_at', 'desc')->get();

        // Recent users (created in last 30 days)
        $recentUsers = User::where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.users.index', compact('users', 'recentUsers'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'nullable|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:cashier,educator,admin',
            'status' => 'required|boolean',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'role' => $request->role,
            'is_active' => $request->status,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    public function showUser(User $user)
    {
        $user->load('bookings');

        return view('admin.users.show', compact('user'));
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:cashier,educator,admin',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'username', 'email', 'phone', 'role']);
        $data['is_active'] = $request->has('is_active');

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $data['password'] = $request->password;
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    public function deleteUser(User $user)
    {
        // Prevent deleting users with active bookings
        if ($user->bookings()->count() > 0) {
            return redirect()->route('admin.users')
                ->with('error', 'Cannot delete user that has bookings!');
        }

        // Prevent deleting current user
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'Cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Package management (inherit from EducatorController)
     */
    public function packages()
    {
        $packages = Package::withCount('bookings')
            ->with([
                'bookings' => function ($query) {
                    $query->select('package_id', 'total_price');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate total revenue
        $totalRevenue = $packages->sum(function ($package) {
            return $package->bookings->sum('total_price');
        });

        // Format total revenue
        $totalRevenue = 'Rp ' . number_format($totalRevenue, 0, ',', '.');

        return view('admin.packages.index', compact('packages', 'totalRevenue'));
    }

    public function showPackage(Package $package)
    {
        $package->load([
            'bookings' => function ($query) {
                $query->with(['user', 'tamanSession.educator', 'museumSession.educator'])
                    ->orderBy('created_at', 'desc');
            }
        ]);

        return view('admin.packages.show', compact('package'));
    }

    public function createPackage()
    {
        return view('admin.packages.create');
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
            'is_active' => 'boolean',
        ]);

        Package::create($request->all());

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package created successfully!');
    }

    public function editPackage(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
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

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package updated successfully!');
    }

    public function deletePackage(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package deleted successfully!');
    }

    /**
     * Tour session management (inherit from EducatorController)
     */
    public function sessions()
    {
        // Auto-generate sessions for today and upcoming days from templates
        $today = Carbon::today();
        for ($i = 0; $i <= 7; $i++) {
            SessionTemplate::ensureSessionsForDate($today->copy()->addDays($i));
        }

        $query = TourSession::with(['educator', 'bookings'])
            ->fromActiveTemplate();

        // Apply filters if provided (default: today)
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
            $query->where('type', request('type_filter'));
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

        // Calculate statistics (only from active templates)
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

        return view('admin.sessions.index', compact('sessions', 'educators', 'todaySessions', 'fullSessions', 'totalVisitors', 'bookableSessions'));
    }

    public function editSession(TourSession $session)
    {
        $educators = Educator::active()->get();
        return view('admin.sessions.edit', compact('session', 'educators'));
    }

    public function updateSession(Request $request, TourSession $session)
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

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Tour session updated successfully!');
    }

    public function toggleSession(TourSession $session)
    {
        $session->update(['is_active' => !$session->is_active]);

        $status = $session->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.sessions.index')
            ->with('success', "Session successfully {$status}!");
    }

    public function deleteSession(TourSession $session)
    {
        if ($session->booked > 0) {
            return redirect()->route('admin.sessions.index')
                ->with('error', 'Cannot delete session that already has bookings!');
        }

        $session->delete();

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Session deleted successfully!');
    }

    /**
     * Session Template Management
     */
    public function templates()
    {
        $templates = SessionTemplate::withCount('slots')
            ->with('slots.educator')
            ->orderBy('type')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return view('admin.templates.index', compact('templates'));
    }

    public function createTemplate()
    {
        $educators = Educator::active()->get();
        return view('admin.templates.create', compact('educators'));
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:taman,museum',
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

        // If setting as default, unset other defaults of same type
        if ($request->is_default) {
            SessionTemplate::where('type', $request->type)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $template = SessionTemplate::create([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'is_default' => $request->boolean('is_default'),
            'apply_days' => $request->apply_days ? array_map('intval', $request->apply_days) : null,
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

        return redirect()->route('admin.templates.index')
            ->with('success', 'Session template created successfully!');
    }

    public function editTemplate(SessionTemplate $template)
    {
        $template->load('slots.educator');
        $educators = Educator::active()->get();
        return view('admin.templates.edit', compact('template', 'educators'));
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

        // If setting as default, unset other defaults of same type
        if ($request->is_default) {
            SessionTemplate::where('type', $template->type)
                ->where('is_default', true)
                ->where('id', '!=', $template->id)
                ->update(['is_default' => false]);
        }

        $template->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_default' => $request->boolean('is_default'),
            'apply_days' => $request->apply_days ? array_map('intval', $request->apply_days) : null,
        ]);

        // Sync slots: keep existing IDs, create new, delete removed
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

        // Delete removed slots
        $template->slots()->whereNotIn('id', $keepIds)->delete();

        // Sync already-generated sessions (today and future) that have 0 bookings
        // so they reflect the updated template slots (educator, capacity, times)
        $this->syncSessionsFromTemplate($template);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Session template updated successfully!');
    }

    public function deleteTemplate(SessionTemplate $template)
    {
        $template->delete(); // cascade deletes slots
        return redirect()->route('admin.templates.index')
            ->with('success', 'Template deleted successfully!');
    }

    public function toggleTemplate(SessionTemplate $template)
    {
        $template->update(['is_active' => !$template->is_active]);
        $status = $template->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.templates.index')
            ->with('success', "Template successfully {$status}!");
    }

    /**
     * Sync already-generated tour sessions from an updated template.
     * Deletes unbooked sessions for today+ and regenerates them from current slots.
     */
    private function syncSessionsFromTemplate(SessionTemplate $template)
    {
        $today = Carbon::today();

        // Delete future/today sessions from this template that have no bookings
        TourSession::where('session_template_id', $template->id)
            ->whereDate('date', '>=', $today)
            ->where('booked', 0)
            ->delete();

        // Regenerate for today + upcoming 7 days
        for ($i = 0; $i <= 7; $i++) {
            SessionTemplate::ensureSessionsForDate($today->copy()->addDays($i));
        }
    }

    /**
     * Educator management (inherit from EducatorController)
     */
    public function educators()
    {
        $educators = Educator::withCount(['tourSessions as sessions_count'])
            ->with([
                'tourSessions' => function ($query) {
                    $query->whereDate('date', Carbon::today());
                }
            ])
            ->orderBy('name')
            ->get();

        // Calculate statistics
        $todaySessions = TourSession::whereDate('date', Carbon::today())->count();

        // Today's assignments
        $todayAssignments = TourSession::with('educator')
            ->whereDate('date', Carbon::today())
            ->orderBy('start_time')
            ->get();

        return view('admin.educators.index', compact('educators', 'todaySessions', 'todayAssignments'));
    }

    public function createEducator()
    {
        return view('admin.educators.create');
    }

    public function showEducator(Educator $educator)
    {
        $educator->load([
            'tourSessions' => function ($q) {
                $q->whereDate('date', '>=', Carbon::today())
                    ->orderBy('date')
                    ->orderBy('start_time');
            }
        ]);

        $todaySessions = $educator->tourSessions->where('date', Carbon::today());
        $upcomingSessions = $educator->tourSessions->where('date', '>', Carbon::today());

        return view('admin.educators.show', compact('educator', 'todaySessions', 'upcomingSessions'));
    }

    public function storeEducator(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'required|in:taman,museum,both',
        ]);

        Educator::create($request->all());

        return redirect()->route('admin.educators.index')
            ->with('success', 'Educator created successfully!');
    }

    public function editEducator(Educator $educator)
    {
        return view('admin.educators.edit', compact('educator'));
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

        return redirect()->route('admin.educators.index')
            ->with('success', 'Educator updated successfully!');
    }

    public function deleteEducator(Educator $educator)
    {
        // Check if educator has active sessions
        if ($educator->activeSessions()->count() > 0) {
            return redirect()->route('admin.educators.index')
                ->with('error', 'Cannot delete educator that has active sessions!');
        }

        $educator->delete();

        return redirect()->route('admin.educators.index')
            ->with('success', 'Educator deleted successfully!');
    }

    /**
     * Booking overview
     */
    public function bookings()
    {
        $bookings = Booking::with(['package', 'user', 'tamanSession.educator', 'museumSession.educator'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function showBooking(Booking $booking)
    {
        $booking->load(['package', 'user', 'tamanSession.educator', 'museumSession.educator']);

        return view('admin.bookings.show', compact('booking'));
    }
}