<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordConfirmationCode extends Model
{
    use HasFactory;
    protected $fillable = ['confirm_code','user_id'];
    protected $table = 'password_confirm_code';
}
