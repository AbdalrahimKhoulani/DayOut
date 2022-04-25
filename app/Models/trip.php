<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class trip extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['title','organizer_id','description','begin_date','expire_date','price'];
    protected $dates = ['deleted_at'];
}
