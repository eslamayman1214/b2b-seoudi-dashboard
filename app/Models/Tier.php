<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'tier_name', 'min_quantity', 'max_quantity', 'value', 'type',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}