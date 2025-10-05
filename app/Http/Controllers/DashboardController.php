<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Stock;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Today's statistics
        $todayRevenue = Transaction::whereDate('created_at', today())->sum('total_amount');
        $todayTransactions = Transaction::whereDate('created_at', today())->count();
        
        // This month's statistics
        $monthRevenue = Transaction::whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->sum('total_amount');
        
        // Total statistics
        $totalRevenue = Transaction::sum('total_amount');
        $totalTransactions = Transaction::count();
        
        // Low stock alerts (less than 1000 liters)
        $lowStockItems = Stock::with('fuelType')
                             ->where('quantity', '<', 1000)
                             ->get();
        
        // Recent transactions
        $recentTransactions = Transaction::with(['vehicle', 'fuelType', 'user'])
                                        ->latest()
                                        ->take(10)
                                        ->get();
        
        // Revenue by fuel type (this month)
        $revenueByFuelType = Transaction::select('fuel_type_id', DB::raw('SUM(total_amount) as revenue'))
                                       ->with('fuelType')
                                       ->whereMonth('created_at', now()->month)
                                       ->whereYear('created_at', now()->year)
                                       ->groupBy('fuel_type_id')
                                       ->get();
        
        // Account balances
        $accounts = Account::all();
        $totalBalance = $accounts->sum('balance');
        
        return view('dashboard.index', compact(
            'todayRevenue',
            'todayTransactions',
            'monthRevenue',
            'totalRevenue',
            'totalTransactions',
            'lowStockItems',
            'recentTransactions',
            'revenueByFuelType',
            'accounts',
            'totalBalance'
        ));
    }
}
