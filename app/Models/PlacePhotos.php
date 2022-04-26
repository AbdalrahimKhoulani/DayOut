<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlacePhotos extends Model
{
    use HasFactory;
    protected $fillable = ['place_id','path'];

    public function place()
    {
        return $this->belongsTo(Place::class,'place_id');
    }
}
