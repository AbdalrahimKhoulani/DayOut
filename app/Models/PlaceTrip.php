<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlaceTrip extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['place_id','trip_id','order','description'];
    protected $dates = ['deleted_at'];

    public function trip()
    {
        return $this->belongsTo(Trip::class,'trip_id');
    }
    public function place()
    {
        return $this->belongsTo(Place::class,'place_id');
    }

}
