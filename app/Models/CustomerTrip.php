<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerTrip extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['customer_id','trip_id','confirmed_at','rate','rate_comment'];
    protected $dates = ['deleted_at'];
    protected $hidden = ['pivot','deleted_at'];
    protected $casts = [
        'confirmed_at' => 'datetime'
    ];


    public function confirmBooking(){
        return $this->forceFill(['confirmed_at' => $this->freshTimestamp()])->save();
    }
    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }
    public function trip()
    {
        return $this->belongsTo(Trip::class,'trip_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'customer_id','id');
    }

}
