<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Showtime extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'movie_id',
        'cinema_hall_id',
        'start_time',
        'end_time',
        'price',
        'available_seats',
        'seat_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'price' => 'decimal:2',
        'available_seats' => 'integer',
        'seat_status' => 'json',
    ];

    /**
     * Seansın ait olduğu film
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    /**
     * Seansın ait olduğu salon
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cinemaHall()
    {
        return $this->belongsTo(CinemaHall::class);
    }

    /**
     * Seansa ait biletler
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
} 