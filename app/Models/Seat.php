<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seat extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cinema_hall_id',
        'seat_data',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'seat_data' => 'array',
    ];

    /**
     * Koltuğun ait olduğu salon
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cinemaHall()
    {
        return $this->belongsTo(CinemaHall::class);
    }
} 