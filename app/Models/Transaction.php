<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'fuel_type_id',
        'quantity',
        'price_per_liter',
        'total_amount',
        'payment_method',
        'user_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'price_per_liter' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
