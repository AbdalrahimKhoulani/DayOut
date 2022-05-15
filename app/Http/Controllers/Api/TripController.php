<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends BaseController
{
    public function organizerTrip(){
        $user = User::find(Auth::id());
        if($user==null){
            return $this->sendError('User not found');
        }

        $organizer = $user->organizer;
        if($organizer == null){
            return $this->sendError('This account not authorized',[],401);
        }

        $trips = Trip::where('organizer_id',$organizer->id)->with('placeTrips')->get();
        

       return $this->sendResponse($trips,"Trip for Organizer received successfully");
    }
}
