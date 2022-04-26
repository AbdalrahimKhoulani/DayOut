<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollChoice extends Model
{
    use HasFactory;

    protected $fillable = ['poll_id', 'value'];

    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'customer_poll_choices', 'poll_choice_id', 'user_id');

    }

}
