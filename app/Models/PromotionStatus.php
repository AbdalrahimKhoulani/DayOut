<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function promotionRequest()
    {
        return $this->hasMany(PromotionRequest::class, 'status_id', 'id');
    }
}
