<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class place_trip extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['place_id','trip_id','order','description'];
    protected $dates = ['deleted_at'];

}
