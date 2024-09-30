<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'item_code',
        'price',
        'stock',
        'tiers',
    ];

    protected $casts = [
        'tiers' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
