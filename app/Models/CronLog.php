<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'executed_at',
        'edited_products_count',
        'status',
    ];
}
