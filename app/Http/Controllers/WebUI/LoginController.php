<?php

namespace App\Http\Controllers\WebUI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'password' => 'required'
        ]);
        if (Auth::attempt(['phone_number' => $request['phone_number'], 'password' => $request['password']])) {
           return redirect()->route('place.index');
        }
    }

}
