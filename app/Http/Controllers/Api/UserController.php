<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController as BaseController;

class UserController extends Controller
{
    public function login(Request $request)
    {
        if(Auth::attempt(['phone_number' => $request->phone_number , 'password' => $request->password]))
        {
            $user = Auth::user();
            $success['token'] = $user->createToken('salem')->accessToken;
            $success['name'] = $user->first_name;
            return $this->sendResponse('Login successful!',$success);
        }else
        {
            return $this->sendError('Login information are not correct!',['error'=> 'Unauthorized']);

        }


    }
}
