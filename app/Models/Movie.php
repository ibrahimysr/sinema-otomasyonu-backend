<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'genre',
        'duration',
        'poster_url',
        'language',
        'release_date',
        'is_in_theaters',
        'imdb_id',
        'imdb_rating',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'duration' => 'integer',
        'release_date' => 'date',
        'is_in_theaters' => 'boolean',
        'imdb_rating' => 'float',
    ];

    /**
     * Filmin tam poster URL'sini döndürür
     *
     * @return string|null
     */
    public function getPosterUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        if (!str_starts_with($value, 'http')) {
            return "https://img.omdbapi.com/?i={$this->imdb_id}&apikey=4ad67668&h=600";
        }

        return $value;
    }
} 