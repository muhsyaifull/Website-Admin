<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Tour;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::withCount('bookings')
            ->with([
                'tours',
                'bookings' => function ($query) {
                    $query->select('package_id', 'total_price');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = $packages->sum(function ($package) {
            return $package->bookings->sum('total_price');
        });
        $totalRevenue = 'Rp ' . number_format($totalRevenue, 0, ',', '.');

        return view('admin.packages.index', compact('packages', 'totalRevenue'));
    }

    public function show(Package $package)
    {
        $package->load([
            'tours',
            'bookings' => function ($query) {
                $query->with(['user', 'bookingSessions.tour', 'bookingSessions.educator'])
                    ->orderBy('created_at', 'desc');
            }
        ]);

        return view('admin.packages.show', compact('package'));
    }

    public function create()
    {
        $tours = Tour::active()->ordered()->get();
        return view('admin.packages.create', compact('tours'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'label' => 'required|string|max:50',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'includes' => 'nullable|array',
            'includes.*' => 'nullable|string',
            'is_active' => 'boolean',
            'tour_ids' => 'required|array|min:1',
            'tour_ids.*' => 'exists:tours,id',
        ]);

        $data = $request->except(['tour_ids']);
        $data['includes'] = array_values(array_filter($request->includes ?? []));
        $package = Package::create($data);
        $package->tours()->sync($request->tour_ids);

        return redirect()->route('panel.packages.index')
            ->with('success', 'Package created successfully!');
    }

    public function edit(Package $package)
    {
        $tours = Tour::active()->ordered()->get();
        $package->load('tours');
        return view('admin.packages.edit', compact('package', 'tours'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'label' => 'required|string|max:50',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'includes' => 'nullable|array',
            'includes.*' => 'nullable|string',
            'is_active' => 'boolean',
            'tour_ids' => 'required|array|min:1',
            'tour_ids.*' => 'exists:tours,id',
        ]);

        $data = $request->except(['tour_ids']);
        $data['includes'] = array_values(array_filter($request->includes ?? []));

        $package->update($data);
        $package->tours()->sync($request->tour_ids);

        return redirect()->route('panel.packages.index')
            ->with('success', 'Package updated successfully!');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('panel.packages.index')
            ->with('success', 'Package deleted successfully!');
    }
}
