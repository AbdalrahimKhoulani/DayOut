<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['name','address','summary','description'];
    protected $dates = ['deleted_at'];


    public function placeTrips()
    {
        return $this->hasMany(PlaceTrip::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class,'favorite_places','place_id','user_id')->withTimestamps();
    }
    public function photos()
    {
        return $this->hasMany(PlacePhotos::class);
    }
    public function type()
    {
        return $this->belongsTo(PlaceType::class,'type_id');
    }

}
