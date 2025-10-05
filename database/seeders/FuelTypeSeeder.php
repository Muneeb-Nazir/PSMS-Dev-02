<?php

namespace Database\Seeders;

use App\Models\FuelType;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class FuelTypeSeeder extends Seeder
{
    public function run(): void
    {
        $fuelTypes = [
            ['name' => 'Petrol', 'price_per_liter' => 272.89, 'stock' => 5000],
            ['name' => 'Diesel', 'price_per_liter' => 283.63, 'stock' => 3000],
            ['name' => 'CNG', 'price_per_liter' => 89.00, 'stock' => 2000],
        ];

        foreach ($fuelTypes as $fuelData) {
            $fuelType = FuelType::create([
                'name' => $fuelData['name'],
                'price_per_liter' => $fuelData['price_per_liter'],
            ]);

            Stock::create([
                'fuel_type_id' => $fuelType->id,
                'quantity' => $fuelData['stock'],
                'last_updated' => now(),
            ]);
        }
    }
}
