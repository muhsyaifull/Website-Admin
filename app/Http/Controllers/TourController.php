<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tour;
use Illuminate\Support\Str;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::withCount([
            'packages',
            'sessionTemplates',
            'tourSessions' => fn($q) => $q->whereDate('date', now()->toDateString()),
            'educators',
        ])
            ->ordered()
            ->get();

        return view('admin.tours.index', compact('tours'));
    }

    public function create()
    {
        return view('admin.tours.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (Tour::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $maxOrder = Tour::max('sort_order') ?? 0;

        Tour::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'icon' => 'fas fa-map-marker-alt',
            'color' => '#4e73df',
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return redirect()->route('panel.tours.index')
            ->with('success', 'Tour created successfully!');
    }

    public function edit(Tour $tour)
    {
        return view('admin.tours.edit', compact('tour'));
    }

    public function update(Request $request, Tour $tour)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ];

        if ($tour->name !== $request->name) {
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $counter = 1;
            while (Tour::where('slug', $slug)->where('id', '!=', $tour->id)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }
            $data['slug'] = $slug;
        }

        $tour->update($data);

        return redirect()->route('panel.tours.index')
            ->with('success', 'Tour updated successfully!');
    }

    public function toggle(Tour $tour)
    {
        $tour->update(['is_active' => !$tour->is_active]);
        $status = $tour->is_active ? 'activated' : 'deactivated';

        return redirect()->route('panel.tours.index')
            ->with('success', "Tour successfully {$status}!");
    }

    public function destroy(Tour $tour)
    {
        if ($tour->tourSessions()->where('booked', '>', 0)->exists()) {
            return redirect()->route('panel.tours.index')
                ->with('error', 'Cannot delete tour that has sessions with bookings!');
        }

        if ($tour->packages()->exists()) {
            return redirect()->route('panel.tours.index')
                ->with('error', 'Cannot delete tour that is assigned to packages! Remove it from packages first.');
        }

        $tour->sessionTemplates()->delete();
        $tour->tourSessions()->delete();
        $tour->delete();

        return redirect()->route('panel.tours.index')
            ->with('success', 'Tour deleted successfully!');
    }
}
