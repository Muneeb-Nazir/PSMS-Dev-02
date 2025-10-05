<?php

namespace App\Http\Controllers;

use App\Models\FuelType;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuelTypeController extends Controller
{
    public function index()
    {
        $fuelTypes = FuelType::with('stock')->get();
        return view('fuel-types.index', compact('fuelTypes'));
    }

    public function create()
    {
        return view('fuel-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fuel_types,name',
            'price_per_liter' => 'required|numeric|min:0',
            'initial_stock' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            $fuelType = FuelType::create([
                'name' => $validated['name'],
                'price_per_liter' => $validated['price_per_liter'],
            ]);
            
            Stock::create([
                'fuel_type_id' => $fuelType->id,
                'quantity' => $validated['initial_stock'],
                'last_updated' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('fuel-types.index')
                           ->with('success', 'Fuel type created successfully!');
                           
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create fuel type.'])->withInput();
        }
    }

    public function edit(FuelType $fuelType)
    {
        return view('fuel-types.edit', compact('fuelType'));
    }

    public function update(Request $request, FuelType $fuelType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fuel_types,name,' . $fuelType->id,
            'price_per_liter' => 'required|numeric|min:0',
        ]);
        
        $fuelType->update($validated);
        
        return redirect()->route('fuel-types.index')
                       ->with('success', 'Fuel type updated successfully!');
    }

    public function destroy(FuelType $fuelType)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $fuelType->delete();
        
        return redirect()->route('fuel-types.index')
                       ->with('success', 'Fuel type deleted successfully!');
    }
}
