<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cinema extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'total_capacity',
        'phone',
        'description',
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
}