<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        Account::create([
            'account_name' => 'Cash Account',
            'account_type' => 'cash',
            'balance' => 50000.00,
        ]);

        Account::create([
            'account_name' => 'Bank Account',
            'account_type' => 'bank',
            'balance' => 200000.00,
        ]);

        Account::create([
            'account_name' => 'Credit Account',
            'account_type' => 'credit',
            'balance' => 0.00,
        ]);
    }
}
