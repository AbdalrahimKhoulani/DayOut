<?php

namespace App\Http\Controllers\Api;

use App\Models\ConfirmationCode;
use App\Models\PromotionRequest;
use App\Models\PromotionStatus;
use App\Models\Role;
use App\Models\User;


use App\Models\UserRole;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isEmpty;

class UserController extends BaseController
{
    public function login(Request $request)
    {
        error_log('Login request');
        if (Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password])) {
            $user = Auth::user();
            $success['id'] = $user->id;
            $success['role'] = $user->roles;
            $success['token'] = $user->createToken($user->phone_number )->accessToken;

            error_log('Login successful!');
            return $this->sendResponse($success, 'Login successful!');
        } else {
            error_log('Login information are not correct!');
            return $this->sendError('Login information are not correct!', ['error' => 'Unauthorized'], 404);

        }
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|regex:/^[\pL\s\-]+$/u',
            'last_name' => 'required|regex:/^[\pL\s\-]+$/u',
            'phone_number' => 'required|regex:/(09)[3-9][0-9]{7}/',
            'password' => 'required',
            'photo' => 'image',
            'gender' => 'required|in:Male,Female',
            'mobile_token' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Registration failed', $validator->errors());
        }

        $checkUser = User::where('phone_number', $request['phone_number'])->get()->first();

        if ($checkUser != null) {

            return $this->sendError('Phone number already exists');
        }

        $role = Role::where('name', 'customer')->first();

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $role->id
        ]);

        $success['user'] = $user;
        $success['token'] = $user->createToken($user->phone_number)->accessToken;
        //$this->createConfirmCodeForUser($user->id);

        return $this->sendResponse($success, "User registered successfully");
    }

    public function organizerRegister(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|regex:/^[\pL\s\-]+$/u',
            'last_name' => 'required|regex:/^[\pL\s\-]+$/u',
            'phone_number' => 'required|regex:/(09)[3-9][0-9]{7}/',
            'password' => 'required',
            'photo' => 'string',
            'gender' => 'required|in:Male,Female',
            'mobile_token' => 'string',
            'credential_photo'=>'required',
            //'credential_photo'=>'required|is_img',
            'description'=>'string'
        ]);



        if ($validator->fails()) {
            return $this->sendError('Registration failed', $validator->errors());
        }

        $checkUser = User::where('phone_number', $request['phone_number'])->get()->first();

        if ($checkUser != null) {

            return $this->sendError('Phone number already exists');
        }

        $role = Role::where('name', 'customer')->first();

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $role->id
        ]);

        $promotionStatus = PromotionStatus::where('name', 'Pending')->first();

        $promotionRequest = new PromotionRequest();
        $promotionRequest->status_id = $promotionStatus->id;
        $promotionRequest->user_id = $user->id;
        $promotionRequest->credential_photo = $request['credential_photo'];
        if($request->has('description'))
            $promotionRequest->description = $request['description'];
        $promotionRequest->save();

        $success['user'] = $user;
        $success['token'] = $user->createToken($user->phone_number)->accessToken;
        $success['request_id'] = $promotionRequest->id;

        //$this->createConfirmCodeForUser($user->id);

        return $this->sendResponse($success, "User registered successfully");
    }

    public function confirmAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'confirm_code' => 'required',
//            'phone_number' => 'required',
//            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Confirmation failed', $validator->errors());
        }

        $user = User::find($request['user_id']);
        $confirm_code = $user->confirmCode;

        if ($request['confirm_code'] != $confirm_code->code) {
            return $this->sendError("Confirmation code is not valid");
        }

        $user->verifiedAccount();
        $confirm_code->delete();


        $success['token'] = $user->createToken($user->phone_number.$user->password)->accessToken;

        return $this->sendResponse($success, "The confirmation process succeeded");
    }

    public function requestPromotion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|regex:/(09)[3-9][0-9]{7}/',
            'password' => 'required',
            'description' => 'string',
            'credential_photo' => 'required',
        ]);

        $user = User::where('phone_number', $request['phone_number'])->first();
        if ($user == null) {
            return $this->sendError('The account is not exists');
        }

       // dd($user->organizer);

        if ($user->organizer != null) {
            return $this->sendError('The account already have organizer role');
        }

        if (Auth::attempt(
            ['phone_number' => $request['phone_number'],
                'password' => $request['password']])) {

            $promotionStatus = PromotionStatus::where('name', '=', 'Pending')->first();

            $promotionRequest = new PromotionRequest();
            $promotionRequest->status_id = $promotionStatus->id;
            $promotionRequest->user_id = Auth::id();
            $promotionRequest->credential_photo = $request['credential_photo'];
            if($request->has('description'))
                $promotionRequest->description = $request['description'];
            $promotionRequest->save();


            $success['request_id'] = $promotionRequest->id;
            $success['user_id'] = Auth::id();

            return $this->sendResponse($success, 'The promotion request was successfully sent');
        }

     return $this->sendError('Promotion request failure!');
    }

    private function createConfirmCodeForUser($user_id)
    {
        $old_code = ConfirmationCode::where('user_id', '=', $user_id)->first();
        if ($old_code != null) {
            $old_code->delete();
        }

        $code = mt_rand(1000, 9999);
        $confirm_code = new ConfirmationCode();

        $confirm_code->user_id = $user_id;
        $confirm_code->code = $code;
        $confirm_code->save();
    }

    public function profileCustomer($id)
    {
        error_log('Customer profile request');
        $user = User::select(['id','first_name','last_name','email','phone_number','gender'])->withCount(['customerTrip', 'organizerFollow'])->find($id);
        if ($user != null) {
            error_log('Customer profile request succeeded!');
            return $this->sendResponse($user, 'Succeeded!');
        }
        error_log('User not found!');
        return $this->sendError('User not found!');
    }

    public function editProfileCustomer(Request $request)
    {
        error_log('Customer profile edit request');
        $id = Auth::id();
        if ($request->has('photo')) {
            $request['photo'] = str_replace('data:image/png;base64,', '', $request['photo']);
            $request['photo'] = str_replace('data:image/webp;base64,', '', $request['photo']);
            $request['photo'] = str_replace('data:image/jpeg;base64,', '', $request['photo']);
            $request['photo'] = str_replace('data:image/bmp;base64,', '', $request['photo']);
            $request['photo'] = str_replace(' ', '+', $request['photo']);
        }



        $validator = Validator::make($request->all(), [
            'first_name' => 'regex:/^[\pL\s\-]+$/u',
            'last_name' => 'regex:/^[\pL\s\-]+$/u',
            'photo' => 'is_img',
            'gender' => ['in:Male,Female'],
        ]);

        if ($validator->fails()) {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }

        $user = User::find($id);
        if ($user != null) {

            if ($request->has('first_name'))
                $user['first_name'] = $request['first_name'];
            if ($request->has('last_name'))
                $user['last_name'] = $request['last_name'];
            if ($request->has('photo')) {
                if ($request->has('first_name') && $request->has('last_name'))
                    $user['photo'] = $this->storeProfileImage($request['first_name'], $request['last_name'], $request['photo']);
                elseif ($request->has('first_name'))
                    $user['photo'] = $this->storeProfileImage($request['first_name'], $user['last_name'], $request['photo']);
                elseif ($request->has('last_name'))
                    $user['photo'] = $this->storeProfileImage($user['first_name'], $request['last_name'], $request['photo']);
                else
                    $user['photo'] = $this->storeProfileImage($user['first_name'], $user['last_name'], $request['photo']);


            }
            if($request->has('gender'))
                $user['gender'] = $request['gender'];
            if($request->has('email'))
                $user['email'] = $request['email'];
            $user->save();
            $user->makeHidden('photo');
            error_log('Customer profile edit request succeeded!');
            return $this->sendResponse($user, 'Edit succeeded!');
        }
        error_log('User not found!');
        return $this->sendError('User not found!');

    }

    private function storeProfileImage($firstname, $lastname, $photo)
    {
        $image = base64_decode($photo);
        $extention = '.png';
        $f = finfo_open();
        $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
        if($result == 'image/jpeg')
            $extention = '.jpeg';
        elseif ($result == 'image/webp')
            $extention = '.webp';
        elseif($result == 'image/x-ms-bmp')
            $extention = '.bmp';
        Storage::disk('local')->put('public/users/' . $firstname . $lastname . Carbon::now()->toDateString() . $extention, $image);
        return Storage::url('users/' . $firstname . $lastname . Carbon::now()->toDateString() . $extention);
    }

    public function profilePhoto($id){
        $user = User::find($id);

        $img_data = base64_decode($user->photo);
        $image = imagecreatefromstring($img_data);

        $finfo = finfo_open();
        $extension = finfo_buffer($finfo,$img_data,FILEINFO_MIME_TYPE);
        header('Content-Type: image/'.str_replace('image/','',$extension));
        return imagejpeg($image);
    }
}
