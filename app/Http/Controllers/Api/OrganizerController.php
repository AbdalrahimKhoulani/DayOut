<?php

namespace App\Http\Controllers\Api;

use App\Models\Organizer;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class OrganizerController extends BaseController
{
    public function organizerProfile($id)
    {
        error_log('Organizer profile request');
            $organizer = Organizer::select(['id', 'user_id','bio'])->where('user_id', $id)->with(['user' => function ($query) {
                $query->select(['id', 'first_name', 'last_name', 'email', 'phone_number', 'gender']);
            }])->withCount('followers', 'trips')->first();
            if ($organizer != null) {
                error_log('Organizer profile request succeeded');
                return $this->sendResponse($organizer, 'Succeeded!');
            }

            error_log('Organizer not found!');
            return $this->sendError('Organizer not found!');


    }

    public function editOrganizerProfile(Request $request)
    {
        error_log('Organizer profile edit request');


        $id = Auth::id();
        if(count($request->all()) <=0)
        {
            error_log('No data were sent!');
            return $this->sendError('No data were sent!',[],500);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'regex:/^[\pL\s\-]+$/u',
            'last_name' => 'regex:/^[\pL\s\-]+$/u',
            'gender' => ['in:Male,Female']
        ]);

        if ($validator->fails())
        {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }

        $organizer = Organizer::where('user_id', $id)->with('user')->first();
        if ($organizer != null) {
            if ($request->has('first_name'))
                $organizer['user']->first_name = $request['first_name'];
            if ($request->has('last_name'))
                $organizer['user']->last_name = $request['last_name'];
            if ($request->has('photo'))
                $organizer['user']->photo = $this->storeProfileImage($request['photo']);
            if ($request->has('gender'))
                $organizer['user']->gender = $request['gender'];
            if ($request->has('bio'))
                $organizer['bio'] = $request['bio'];
            if ($request->has('email'))
                $organizer['user']->email = $request['email'];
            $organizer['user']->save();
            $organizer->save();
            error_log('Organizer edit request succeeded!');
            return $this->sendResponse($organizer, 'Edit succeeded!');
        }
        error_log('Organizer not found!');
        return $this->sendError('Organizer not found!');

    }

    private function storeProfileImage( $photo)
    {
        $image = base64_decode($photo);
        $filename = uniqid();
        $extention = '.png';
        $f = finfo_open();
        $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
        if($result == 'image/jpeg')
            $extention = '.jpeg';
        elseif ($result == 'image/webp')
            $extention = '.webp';
        elseif($result == 'image/x-ms-bmp')
            $extention = '.bmp';
        Storage::disk('users')->put($filename . $extention, $image);
        return Storage::disk('users')->url('users/'.$filename . $extention);
    }

    public function deleteProfileImage(){
        $id = Auth::id();
        $user = User::find($id);
        $file = Storage::path($user['photo']);
        $file = str_replace('/', '\\', $file);

        $pieces = explode('\\', $file);


        $last_word = array_pop($pieces);
        Storage::disk('public')->delete('users/'.$last_word);

        $user['photo']='';
        $user->save();
        error_log('File deleted successful');
        return $this->sendResponse($user,'Succeeded');
    }

}
