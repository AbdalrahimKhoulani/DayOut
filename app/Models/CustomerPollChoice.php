<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPollChoice extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','poll_id','poll_choice_id'];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function poll(){
        return $this->belongsTo(Poll::class,'poll_id','id');
    }

    public function pollChoice(){
        return $this->belongsTo(PollChoice::class,'poll_choice_id','id');
    }
}
