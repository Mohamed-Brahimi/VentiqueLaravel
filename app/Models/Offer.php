<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $User;
    protected $Antique;

    protected $fillable = [
        'price',
        'dateOffered',
        'erased',
        'antique_id',
        'user_id'
    ];
    protected $guarded = [
        'id'
    ];
    public function antique()
    {
        return $this->belongsTo(Antique::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
