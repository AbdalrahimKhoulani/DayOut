<?php

namespace App\Http\Controllers\WebUI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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
            'phone_number' => 'required|regex:/(09)[3-9][0-9]{7}/',
            'password' => 'required'
        ]);
        if (Auth::attempt(['phone_number' => $request['phone_number'], 'password' => $request['password']])) {
            if (Auth::user()->isAdmin()) {

                $intended = Session::get('url.intended', url('/place'));
//                dd($intended);

                return redirect()->intended($intended);

            } else {
                return redirect()->route('home');
            }
        } else {
            return redirect()->route('home')->with('error', 'Phone number or password is invalid');
        }
    }

}
