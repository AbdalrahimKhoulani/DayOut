<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;
    protected $fillable = ['name'];


    public function trips()
    {
        return $this->belongsToMany(Trip::class,'trip_types','type_id','trip_id');
    }
}
