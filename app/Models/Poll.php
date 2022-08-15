<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poll extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['title', 'description', 'organizer_id','expire_date'];


    public function organizer()
    {
        return $this->belongsTo(Organizer::class, 'organizer_id');
    }

    public function pollChoices()
    {
        return $this->hasMany(PollChoice::class, 'poll_id', 'id');
    }

    public function users(){
        return $this->belongsToMany(User::class,'customer_poll_choice','poll_id','user_id');
    }
}
