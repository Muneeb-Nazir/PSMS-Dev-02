<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';

    protected $fillable = [
        'fuel_type_id',
        'quantity',
        'last_updated',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'last_updated' => 'datetime',
        ];
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class);
    }
}
