<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlaceSuggestion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['organizer_id','place_name','place_address','description'];

    public function organizer(){
        return $this->belongsTo(Organizer::class,'organizer_id','id');
    }
}
