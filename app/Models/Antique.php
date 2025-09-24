<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antique extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'user_id'
    ];
}
