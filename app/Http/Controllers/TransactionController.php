<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\FuelType;
use App\Models\Stock;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['vehicle', 'fuelType', 'user']);
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('vehicle', function($q) use ($search) {
                $q->where('vehicle_number', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }
        
        // Filter by fuel type
        if ($request->filled('fuel_type')) {
            $query->where('fuel_type_id', $request->fuel_type);
        }
        
        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $transactions = $query->latest()->paginate(20);
        $fuelTypes = FuelType::all();
        
        return view('transactions.index', compact('transactions', 'fuelTypes'));
    }

    public function create()
    {
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        $fuelTypes = FuelType::with('stock')->get();
        
        return view('transactions.create', compact('vehicles', 'fuelTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'quantity' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,credit',
            'notes' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Get fuel type and check stock
            $fuelType = FuelType::findOrFail($validated['fuel_type_id']);
            $stock = Stock::where('fuel_type_id', $fuelType->id)->first();
            
            if (!$stock || $stock->quantity < $validated['quantity']) {
                return back()->withErrors(['quantity' => 'Insufficient stock available.'])->withInput();
            }
            
            // Create transaction
            $transaction = Transaction::create([
                'vehicle_id' => $validated['vehicle_id'],
                'fuel_type_id' => $validated['fuel_type_id'],
                'quantity' => $validated['quantity'],
                'price_per_liter' => $fuelType->price_per_liter,
                'total_amount' => $validated['quantity'] * $fuelType->price_per_liter,
                'payment_method' => $validated['payment_method'],
                'user_id' => auth()->id(),
                'notes' => $validated['notes'],
            ]);
            
            // Update stock
            $stock->decrement('quantity', $validated['quantity']);
            $stock->update(['last_updated' => now()]);
            
            // Update account balance
            $accountType = $validated['payment_method'];
            $account = Account::where('account_type', $accountType)->first();
            if ($account) {
                $account->increment('balance', $transaction->total_amount);
            }
            
            DB::commit();
            
            return redirect()->route('transactions.show', $transaction)
                           ->with('success', 'Transaction created successfully!');
                           
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create transaction: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['vehicle', 'fuelType', 'user']);
        return view('transactions.show', compact('transaction'));
    }

    public function destroy(Transaction $transaction)
    {
        // Only admins can delete transactions
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        DB::beginTransaction();
        
        try {
            // Restore stock
            $stock = Stock::where('fuel_type_id', $transaction->fuel_type_id)->first();
            if ($stock) {
                $stock->increment('quantity', $transaction->quantity);
                $stock->update(['last_updated' => now()]);
            }
            
            // Update account balance
            $account = Account::where('account_type', $transaction->payment_method)->first();
            if ($account) {
                $account->decrement('balance', $transaction->total_amount);
            }
            
            $transaction->delete();
            
            DB::commit();
            
            return redirect()->route('transactions.index')
                           ->with('success', 'Transaction deleted successfully!');
                           
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete transaction: ' . $e->getMessage()]);
        }
    }
}
