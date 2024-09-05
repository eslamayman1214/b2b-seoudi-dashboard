<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;
    protected $fillable = ['key', 'value', 'customer_endpoint', 'customer_token', 'base_url', 'products_endpoint', 'products_token'];
    public static function getValueByKey($key)
    {
        $config = self::where('key', $key)->first();
        return $config ? $config->value : null;
    }
}