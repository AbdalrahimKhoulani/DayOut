<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserReport extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $dates = ['deleted_at'];
    protected $fillable = ['reporter_id','target_id','report'];

    public function reporter(){
        return $this->belongsTo(User::class,'reporter_id');
    }

    public function target(){
        return $this->belongsTo(User::class,'target_id');
    }
}
