<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cinema extends Model
{
    use HasFactory, SoftDeletes;

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

    protected $dates = ['deleted_at'];

    /**
     * Sinemanın ait olduğu şehir
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    /**
     * Sinemaya ait salonlar
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function halls()
    {
        return $this->hasMany(CinemaHall::class);
    }
}