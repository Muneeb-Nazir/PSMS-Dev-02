<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price_per_liter',
    ];

    protected function casts(): array
    {
        return [
            'price_per_liter' => 'decimal:2',
        ];
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }
}
