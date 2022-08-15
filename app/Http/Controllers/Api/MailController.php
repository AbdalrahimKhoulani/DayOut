<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ConfirmationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public static function sendConfirmCode($code,$email){

        Mail::to($email)->send(new ConfirmationCode($code));
    }


}
