<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'credential_photo'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'organizer_id', 'user_id')->withTimestamps();
    }

    public function placeSuggestion(){
        return $this->hasMany(PlaceSuggestion::class,'organizer_id','id');
    }

    public function polls(){
        return $this->hasMany(Poll::class,'organizer_id','id');
    }
    public function trips(){
        return $this->hasMany(Trip::class,'organizer_id','id');
    }
}
