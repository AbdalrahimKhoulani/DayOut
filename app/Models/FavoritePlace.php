<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoritePlace extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','place_id'];
    protected $table = 'favorite_places';


    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function place()
    {
        return $this->belongsTo(Place::class,'place_id');
    }
}
