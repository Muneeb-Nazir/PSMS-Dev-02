<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::all();
        $totalBalance = $accounts->sum('balance');
        
        return view('accounts.index', compact('accounts', 'totalBalance'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:cash,bank,credit',
            'balance' => 'required|numeric',
        ]);
        
        $account = Account::create($validated);
        
        return redirect()->route('accounts.index')
                       ->with('success', 'Account created successfully!');
    }

    public function edit(Account $account)
    {
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:cash,bank,credit',
            'balance' => 'required|numeric',
        ]);
        
        $account->update($validated);
        
        return redirect()->route('accounts.index')
                       ->with('success', 'Account updated successfully!');
    }

    public function destroy(Account $account)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $account->delete();
        
        return redirect()->route('accounts.index')
                       ->with('success', 'Account deleted successfully!');
    }
}
