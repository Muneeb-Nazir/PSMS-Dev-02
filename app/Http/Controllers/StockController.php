<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\FuelType;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::with('fuelType')->get();
        return view('stock.index', compact('stocks'));
    }

    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);
        
        $stock->update([
            'quantity' => $validated['quantity'],
            'last_updated' => now(),
        ]);
        
        return redirect()->route('stock.index')
                       ->with('success', 'Stock updated successfully!');
    }
}
