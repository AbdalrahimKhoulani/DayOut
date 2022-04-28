<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController as BaseController;

class UserController extends BaseController
{
    public function login(Request $request)
    {
        if(Auth::attempt(['phone_number' => $request->phone_number , 'password' => $request->password]))
        {
            $user = Auth::user();
            $success['token'] = $user->createToken($user->phone_number.$user->password)->accessToken;
            return $this->sendResponse($success,'Login successful!');
        }else
        {
            return $this->sendError('Login information are not correct!',['error'=> 'Unauthorized'],406);

        }


    }
}
