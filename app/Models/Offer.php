<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'price',
        'dateOffered',
        'erased',
        'antique_id',
        'user_id'
    ];
}
