<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromotionRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['user_id', 'description', 'admin_message', 'credential_photo'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function promotionStatus()
    {
        return $this->belongsTo(PromotionStatus::class, 'status_id', 'id');
    }
}
