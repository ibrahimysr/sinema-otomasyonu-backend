<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'integer'; 

    protected $fillable = [
        'id',
        'name',
    ];
}