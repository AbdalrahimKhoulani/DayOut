<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['title','body','user_id'];
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
