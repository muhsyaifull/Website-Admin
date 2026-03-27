<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Educator;
use App\Models\TourSession;
use App\Models\Tour;
use Carbon\Carbon;

class EducatorController extends Controller
{
    public function index()
    {
        $educators = Educator::withCount(['tourSessions as sessions_count'])
            ->with([
                'tours',
                'tourSessions' => function ($query) {
                    $query->whereDate('date', Carbon::today());
                }
            ])
            ->orderBy('name')
            ->get();

        $todaySessions = TourSession::whereDate('date', Carbon::today())->count();

        $todayAssignments = TourSession::with(['educator', 'tour'])
            ->whereDate('date', Carbon::today())
            ->orderBy('start_time')
            ->get();

        return view('admin.educators.index', compact('educators', 'todaySessions', 'todayAssignments'));
    }

    public function create()
    {
        $tours = Tour::active()->ordered()->get();
        return view('admin.educators.create', compact('tours'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'tour_ids' => 'required|array|min:1',
            'tour_ids.*' => 'exists:tours,id',
        ]);

        $educator = Educator::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'specialization' => $this->deriveSpecialization($request->tour_ids),
        ]);
        $educator->tours()->sync($request->tour_ids);

        return redirect()->route('panel.educators.index')
            ->with('success', 'Educator created successfully!');
    }

    public function show(Educator $educator)
    {
        $educator->load([
            'tours',
            'tourSessions' => function ($q) {
                $q->with('tour')
                    ->whereDate('date', '>=', Carbon::today())
                    ->orderBy('date')
                    ->orderBy('start_time');
            }
        ]);

        $todaySessions = $educator->tourSessions->where('date', Carbon::today());
        $upcomingSessions = $educator->tourSessions->where('date', '>', Carbon::today());

        return view('admin.educators.show', compact('educator', 'todaySessions', 'upcomingSessions'));
    }

    public function edit(Educator $educator)
    {
        $tours = Tour::active()->ordered()->get();
        $educator->load('tours');
        return view('admin.educators.edit', compact('educator', 'tours'));
    }

    public function update(Request $request, Educator $educator)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'tour_ids' => 'required|array|min:1',
            'tour_ids.*' => 'exists:tours,id',
        ]);

        $educator->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'is_active' => $request->input('is_active'),
            'specialization' => $this->deriveSpecialization($request->tour_ids),
        ]);
        $educator->tours()->sync($request->tour_ids);

        return redirect()->route('panel.educators.index')
            ->with('success', 'Educator updated successfully!');
    }

    public function destroy(Educator $educator)
    {
        if ($educator->activeSessions()->count() > 0) {
            return redirect()->route('panel.educators.index')
                ->with('error', 'Cannot delete educator that has active sessions!');
        }

        $educator->delete();

        return redirect()->route('panel.educators.index')
            ->with('success', 'Educator deleted successfully!');
    }

    private function deriveSpecialization(array $tourIds): string
    {
        $slugs = Tour::whereIn('id', $tourIds)->pluck('slug')->toArray();

        if (count($slugs) > 1) {
            return 'both';
        }

        return $slugs[0] ?? 'both';
    }
}