<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class favorite_place extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','place_id'];
}
