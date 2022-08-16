<?php

namespace App\Http\Controllers\Api;

use App\Models\Follower;
use App\Models\Organizer;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrganizerController extends BaseController
{
    public function index()
    {

        $user_id = Auth::id();

        $organizers = Organizer::with(['user'])
//            ->whereHas('followers', function ($query) use ($user_id) {
//                $query->where('user_id', $user_id);
//            })
            ->withCount(['trips', 'followers'])->paginate(10);

        foreach ($organizers as $organizer) {
            $organizer['rating'] = $this->calculateOrganizerRating($organizer->user_id);
            $organizer['iFollowHim'] = (Follower::where('user_id','=',$user_id)
                    ->where('organizer_id','=',$organizer->id)
                    ->first()!=null);
        }




        return $this->sendResponse($organizers, 'Organizers retrieved successfully');
    }

    public function organizerProfile($id)
    {
        error_log('Organizer profile request');
        $organizer = Organizer::select(['id', 'user_id', 'bio'])->where('user_id', $id)->
        with(['user' => function ($query) {
            $query->select(['id', 'first_name', 'last_name', 'email', 'phone_number', 'gender', 'photo']);
        }])->
        withCount('followers', 'trips')->first();
        if ($organizer != null) {
            $organizer['rating'] = $this->calculateOrganizerRating($id);
            error_log('Organizer profile request succeeded');
            return $this->sendResponse($organizer, 'Succeeded!');
        }

        error_log('Organizer not found!');
        return $this->sendError('Organizer not found!');


    }

    public function editOrganizerProfile(Request $request)
    {
      $this->sendInfoToLog('Organizer profile edit request',[]);


        $id = Auth::id();
        if (count($request->all()) <= 0) {
            error_log('No data were sent!');
            return $this->sendError('No data were sent!', [], 500);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'regex:/^[\pL\s\-]+$/u',
            'last_name' => 'regex:/^[\pL\s\-]+$/u',
            'gender' => ['in:Male,Female'],
            'photo' => 'image'
        ]);

        if ($validator->fails()) {
            $this->sendErrorToLog('Validator failed! check the data',[$validator->errors()]);
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }

        $organizer = Organizer::where('user_id', $id)->with('user')->first();
        if ($organizer != null) {
            if ($request->has('first_name'))
                $organizer['user']->first_name = $request['first_name'];
            if ($request->has('last_name'))
                $organizer['user']->last_name = $request['last_name'];
            if ($request->has('photo'))
            {
                $this->deleteProfileImage();
                $organizer['user']->photo = $this->storeMultiPartImage($request['photo']);}
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

    private function storeProfileImageBase64($photo)
    {
        $image = base64_decode($photo);
        $filename = uniqid();
        $extention = '.png';
        $f = finfo_open();
        $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
        if ($result == 'image/jpeg')
            $extention = '.jpeg';
        elseif ($result == 'image/webp')
            $extention = '.webp';
        elseif ($result == 'image/x-ms-bmp')
            $extention = '.bmp';
        Storage::disk('users')->put($filename . $extention, $image);
        return Storage::disk('users')->url('users/' . $filename . $extention);
    }

    private function storeMultiPartImage($image){

        $filename = $image->store('users',['disk' => 'public']);
        if(!Str::contains($filename,'.'))
            return Storage::url('public/' . $filename . '.jpg' );
        return Storage::url('public/'.$filename);
    }

    public function deleteProfileImage()
    {
        $id = Auth::id();
        $user = User::find($id);
        if($user['photo'] == null){
            return $this->sendResponse($user, 'Succeeded');
        }
        $file = Storage::path($user['photo']);
        $file = str_replace('/', '\\', $file);

        $pieces = explode('\\', $file);


        $last_word = array_pop($pieces);
        Storage::disk('public')->delete('users/' . $last_word);

        $user['photo'] = '';
        $user->save();
        error_log('File deleted successful');
        return $this->sendResponse($user, 'Succeeded');
    }

    private function getOrganizerId($id)
    {
        $organizer = Organizer::where('user_id', $id)->first();
        if ($organizer == null)
            return null;
        return $organizer->id;
    }

    private function calculateOrganizerRating($id)
    {

        $organizerId = $this->getOrganizerId($id);
        if ($organizerId == null) {
            $this->sendErrorToLog('User is not organizer', []);
//            return $this->sendError('User is not organizer', [], 403);
            return 0;
        }

        $trips = Trip::where('organizer_id', $organizerId)->with('customerTrips');
        $rating = 0;
        $trips->each(function ($trip) use (&$rating) {

            $rating = $rating + $this->calculateAvgTripRating($trip);
            $this->sendInfoToLog('', [$rating]);

        });
        if ($trips->count() == 0) {
            return 0;
        }
        $rating = $rating / $trips->count();

        return $rating;
    }

    private function calculateAvgTripRating($trip)
    {
        $avg = 0;
        $customerTrips = $trip['customerTrips'];


        $customerTrips->each(function ($customerTrip) use (&$avg) {
            $avg = $avg + $customerTrip->rate;
        });
        if ($customerTrips->count() == 0) {
            return 0;
        }
        return $avg / $customerTrips->count();
    }

    private function sendInfoToLog($message, $context)
    {
        Log::channel('requestlog')->info($message, $context);
    }

    private function sendErrorToLog($message, $context)
    {
        Log::channel('requestlog')->error($message, $context);

    }
}
