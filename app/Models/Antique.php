<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antique extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'user_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    // Add accessor for image URL if needed
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // If image path starts with http, return as is
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }
            // Otherwise, prepend storage path
            return asset('storage/' . $this->image);
        }
        // Return default image if none exists
        return asset('images/default-antique.jpg');
    }
}
