<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class customer_trip extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['customer_id','trip_id','checkout','rate','rate_comment'];
    protected $dates = ['deleted_at'];
}
