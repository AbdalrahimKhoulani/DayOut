<?php

namespace App\Http\Controllers\Api;

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

class OrganizerController extends BaseController
{
    public function index(){

        /**
         *      "id": 2,
        "user_id": 2,
        "credential_photo": "https://via.placeholder.com/640x480.png/00cc22?text=dicta",
        "created_at": "2022-05-06 15:43:47",
        "updated_at": "2022-05-06 15:43:47",
        "bio": "Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.",
        "first_name": "abd",
        "last_name": "kholani",
        "email": "abd.kholani@gmail.com",
        "phone_number": "6598485",
        "password": "$2y$10$sUX8rBTwrzfBqNabptHWsuGn0zdKoRF6aLzKeHpEdS2U7kVR46YmW",
        "photo": null,
        "gender": "male",
        "mobile_token": null,
        "verified_at": "2022-05-06 15:43:47",
        "is_active": 1,
        "deleted_at": null
         */
        $organizers = DB::table('organizers')
            ->join('users','users.id','=','organizers.user_id')
            ->select(['organizers.id','user_id','first_name','last_name','bio','email','phone_number','photo','gender'])
            ->where('is_active','=',true)
            ->get();

        return $this->sendResponse($organizers,'Organizers retrieved successfully');
    }

    public function organizerProfile($id)
    {
        error_log('Organizer profile request');
            $organizer = Organizer::select(['id', 'user_id','bio'])->where('user_id', $id)->
            with(['user' => function ($query) {
                $query->select(['id', 'first_name', 'last_name', 'email', 'phone_number', 'gender','photo']);
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

    private function getOrganizerId($id){
        $organizer = Organizer::where('user_id',$id)->first();
        if($organizer == null)
            return null;
        return $organizer->id;
    }

    private function calculateOrganizerRating($id){

        $organizerId = $this->getOrganizerId($id);
        if($organizerId == null){
            $this->sendErrorToLog('User is not organizer',[]);
            return $this->sendError('User is not organizer',[],403);
        }

        $trips = Trip::where('organizer_id',$organizerId)->with('customerTrips');
        $rating = 0;
        $trips->each(function ($trip) use (&$rating) {

            $rating = $rating + $this->calculateAvgTripRating($trip);
            $this->sendInfoToLog('',[$rating]);

        });
        $rating = $rating/$trips->count();

        return $rating;
    }

    private  function calculateAvgTripRating($trip){
        $avg = 0;
        $customerTrips = $trip['customerTrips'];


        $customerTrips->each(function ($customerTrip) use (&$avg) {
            $avg = $avg + $customerTrip->rate;
        });
        if($customerTrips->count() == 0){
            return 0;
        }
        return $avg/$customerTrips->count();
    }
    private function sendInfoToLog($message,$context){
        Log::channel('requestlog')->info($message,$context);
    }

    private function sendErrorToLog($message,$context){
        Log::channel('requestlog')->error($message,$context);

    }
}
