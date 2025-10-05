<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'key',
        'value',
    ];

    public static function get($category, $key, $default = null)
    {
        $setting = self::where('category', $category)
                      ->where('key', $key)
                      ->first();
        
        return $setting ? $setting->value : $default;
    }

    public static function set($category, $key, $value)
    {
        return self::updateOrCreate(
            ['category' => $category, 'key' => $key],
            ['value' => $value]
        );
    }
}
