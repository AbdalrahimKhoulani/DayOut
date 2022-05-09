<?php

namespace App\Http\Controllers\Api;

use App\Models\Organizer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class OrganizerController extends BaseController
{
    public function organizerProfile($id)
    {
        $organizer = Organizer::with('user')->withCount('followers', 'trips')->where('id', $id)->first();
        if ($organizer != null) {
            return $this->sendResponse($organizer, 'Succeeded!');
        }
        return $this->sendError('Organizer not found!');
    }

    public function editOrganizerProfile(Request $request)
    {
        if ($request->has('photo')) {
            $request['photo'] = str_replace('data:image/png;base64,', '', $request['photo']);
            $request['photo'] = str_replace('data:image/webp;base64,', '', $request['photo']);
            $request['photo'] = str_replace('data:image/jpeg;base64,', '', $request['photo']);
            $request['photo'] = str_replace('data:image/bmp;base64,', '', $request['photo']);
            $request['photo'] = str_replace(' ', '+', $request['photo']);
        }
        $validator = Validator::make($request->all(), [
            'organizer_id' => 'required|int',
            'first_name' => 'regex:/^[\pL\s\-]+$/u',
            'last_name' => 'regex:/^[\pL\s\-]+$/u',
            'photo' => 'is_img',
            'gender' => ['in:Male,Female'],
            'bio' => 'string'

        ]);
        if ($validator->fails()) {
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }

        $organizer = Organizer::find($request['organizer_id'])->with('user')->first();
        if ($organizer != null) {
            if ($request->has('first_name'))
                $organizer['user']->first_name = $request['first_name'];
            if ($request->has('last_name'))
                $organizer['user']->last_name = $request['last_name'];
            if ($request->has('photo')) {
                if ($request->has('first_name') && $request->has('last_name'))
                    $organizer['user']->photo = $this->storeProfileImage($request['first_name'], $request['last_name'], $request['photo']);
                elseif ($request->has('first_name'))
                    $organizer['user']->photo = $this->storeProfileImage($request['first_name'], $organizer['user']->last_name, $request['photo']);
                elseif ($request->has('last_name'))
                    $organizer['user']->photo = $this->storeProfileImage($organizer['user']->first_name, $request['last_name'], $request['photo']);
                else
                    $organizer['user']->photo = $this->storeProfileImage($organizer['user']->first_name, $organizer['user']->last_name, $request['photo']);


            }
            if ($request->has('gender'))
                $organizer['user']->gender = $request['gender'];
            if ($request->has('bio'))
                $organizer['bio'] = $request['bio'];
            $organizer['user']->save();
            $organizer->save();
            return $this->sendResponse($organizer, 'Edit succeeded!');
        }
        return $this->sendError('Organizer not found!');

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
        Storage::disk('local')->put('public/organizers/' . $firstname . $lastname . Carbon::now()->toDateString() . $extention, $image);
        return Storage::url('organizers/' . $firstname . $lastname . Carbon::now()->toDateString() . $extention);
    }
}
