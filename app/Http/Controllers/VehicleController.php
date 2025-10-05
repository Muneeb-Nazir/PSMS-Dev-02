<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('vehicle_number', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('owner_contact', 'like', "%{$search}%");
        }
        
        $vehicles = $query->latest()->paginate(20);
        
        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|max:255|unique:vehicles,vehicle_number',
            'owner_name' => 'required|string|max:255',
            'owner_contact' => 'nullable|string|max:255',
        ]);
        
        $vehicle = Vehicle::create($validated);
        
        return redirect()->route('vehicles.show', $vehicle)
                       ->with('success', 'Vehicle registered successfully!');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['transactions' => function($query) {
            $query->with(['fuelType', 'user'])->latest()->take(10);
        }]);
        
        $totalTransactions = $vehicle->transactions()->count();
        $totalSpent = $vehicle->transactions()->sum('total_amount');
        
        return view('vehicles.show', compact('vehicle', 'totalTransactions', 'totalSpent'));
    }

    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|max:255|unique:vehicles,vehicle_number,' . $vehicle->id,
            'owner_name' => 'required|string|max:255',
            'owner_contact' => 'nullable|string|max:255',
        ]);
        
        $vehicle->update($validated);
        
        return redirect()->route('vehicles.show', $vehicle)
                       ->with('success', 'Vehicle updated successfully!');
    }

    public function destroy(Vehicle $vehicle)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $vehicle->delete();
        
        return redirect()->route('vehicles.index')
                       ->with('success', 'Vehicle deleted successfully!');
    }
}
