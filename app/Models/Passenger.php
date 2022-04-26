<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;
    protected $fillable = ['customer_trip_id','passenger_name'];

    public function customerTrip()
    {
        return $this->belongsTo(CustomerTrip::class,'customer_trip_id');
    }

}
