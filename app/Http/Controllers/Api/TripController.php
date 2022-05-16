<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Organizer;
use App\Models\PlaceTrip;
use App\Models\TripPhoto;
use App\Models\TripStatus;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Type\Integer;
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
    public function createTrip(Request $request)
    {
        error_log('Create trip request!');
        $id = Auth::id();
        $organizer = Organizer::where('user_id',$id)->first();
        if($organizer == null)
        {
            error_log('User not authorized!');
            return $this->sendError('User not authorized!',[],401);
        }
        $validator = Validator::make($request->all(),[
            'title' => 'string',
            'description' => 'string',
            'begin_date' => 'required',
            'expire_date' => 'required',
            'end_booking' => 'required',
            'price' => 'numeric',
            'types' => 'required'
        ]);

        if ($validator->fails())
        {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }
        $photos = $request['photos'];
        $places = $request['places'];
        $trip = new Trip;
        $trip->title = $request['title'];
        $trip->organizer()->associate($organizer->id);
        $types = $request->types;
        $trip->description = $request['description'];
        $trip->begin_date = $request['begin_date'];
        $trip->expire_date = $request['expire_date'];
        $trip->end_booking = $request['end_booking'];
        $trip->price = $request['price'];
        $trip_status = TripStatus::where('name','available')->first();
        $trip->trip_status_id = $trip_status->id;
        $trip->save();
        for($i=0;$i<count($types);$i++)
        {
            $trip->types()->attach($types[$i]['type_id']);
        }


        error_log('Add trip succeeded!');
        return $this->sendResponse($trip,'Succeeded!');
    }
    public function addTripPhotos(Request $request)
    {
        error_log('Add trip photos request');
        $validator = Validator::make($request->all(),[
           'trip_id' => 'required',
           'photos' => 'required'
        ]);

        if ($validator->fails())
        {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }
        $trip = Trip::find($request['trip_id']);
        if($trip == null)
        {
            error_log('Trip not exist!');
            return $this->sendError('Trip not exist!');
        }
        $photos= $request['photos'];
        for( $i=0;$i<count($photos);$i++)
        {
            $tripPhoto = new TripPhoto;
            $tripPhoto->path = $photos[$i]['image'];
            $tripPhoto->trip()->associate($trip->id);
            $tripPhoto->save();
        }
        error_log('Add trip photos succeeded!');
        return $this->sendResponse($trip,'Succeeded!');
    }
    public function addPlacesToTrip(Request $request)
    {
        error_log('Add places to trip request');
        $validator = Validator::make($request->all(),[
            'trip_id' => 'required',
            'places' => 'required'
        ]);

        if ($validator->fails())
        {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }
        $trip = Trip::find($request['trip_id']);
        if($trip == null)
        {
            error_log('Trip not exist!');
            return $this->sendError('Trip not exist!');
        }
        $places = $request['places'];
        for($i=0 ; $i<count($places) ; $i++)
        {
            $placeTrip = new PlaceTrip;
            $placeTrip->place_id = $places[$i]['place_id'];
            $placeTrip->order = $places[$i]['order'];
            $placeTrip->description = $places[$i]['description'];
            $placeTrip->trip()->associate($trip->id);
            $placeTrip->save();
        }
        error_log('Add places to trip succeeded!');
        return $this->sendResponse($trip,'Succeeded!');
    }
}
