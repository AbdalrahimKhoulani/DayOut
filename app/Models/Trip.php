<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['title','organizer_id','trip_status_id','description','begin_date','expire_date','end_booking','price'];
    protected $dates = ['deleted_at'];
    protected $hidden = ['pivot','deleted_at'];


    public function getNextId()
    {
        $statement = DB::select('title','trips');
        return $statement[0]->Auto_increment;
    }
    public function customerTrips()
    {
        return $this->hasMany(CustomerTrip::class);
    }
    public function  placeTrips()
    {
        return $this->hasMany(PlaceTrip::class)->orderBy('order');
    }
    public function tripPhotos()
    {
        return $this->hasMany(TripPhoto::class);
    }
    public function types()
    {
        return $this->belongsToMany(Type::class,'trip_types','trip_id','type_id');
    }
    public function organizer()
    {
        return $this->belongsTo(Organizer::class,'organizer_id');
    }
    public function tripStatus()
    {
        return $this->belongsTo(TripStatus::class,'trip_status_id');
    }

}
