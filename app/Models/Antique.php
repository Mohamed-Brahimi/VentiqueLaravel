<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antique extends Model
{
    protected $User;
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'user_id'
    ];
    protected $guarded = [
        'id'
    ];
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
    public function user()
    {
        return $this->belongTo(User::class);
    }
}
