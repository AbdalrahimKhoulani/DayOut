<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class place extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['name','address','summary','description'];
    protected $dates = ['deleted_at'];

}
