<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPollChoice extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id','poll_id','poll_choice_id'];
}
