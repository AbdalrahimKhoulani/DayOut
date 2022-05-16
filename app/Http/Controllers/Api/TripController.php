<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\CustomerTrip;
use App\Models\Passenger;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
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
    public function organizerTrip()
    {
        $user = User::find(Auth::id());
        if ($user == null) {
            error_log('User not found');
            return $this->sendError('User not found');
        }

        $organizer = $user->organizer;
        if ($organizer == null) {
            error_log('This account not authorized');
            return $this->sendError('This account not authorized', [], 401);
        }

        $trips = Trip::where('organizer_id', $organizer->id)
            ->with(['placeTrips', 'tripPhotos' => function ($query) {
                $query->select(['id', 'trip_id']);
            }])->paginate(10);

        foreach ($trips as $trip) {

            if (Carbon::now() < $trip['begin_date'])
                $trip['status'] = 'Upcoming';
            else if ($trip['begin_date'] < Carbon::now() &&
                Carbon::now() < $trip['expire_date'])
                $trip['status'] = 'Active';
            else if ($trip['expire_date'] < Carbon::now())
                $trip['status'] = 'History';
        }

        error_log('Trip for Organizer received successfully');
        return $this->sendResponse($trips, "Trip for Organizer received successfully");
    }


    public function tripPhoto($id)
    {
        $tripPhoto = TripPhoto::find($id);
        if($tripPhoto == null ){
            error_log('Photo with id : '.$id .' not found');
            return $this->sendError('Photo with id : '.$id .' not found');
        }

        $img_data = base64_decode($tripPhoto->path);
        $image = imagecreatefromstring($img_data);

        $finfo = finfo_open();
        $extension = finfo_buffer($finfo, $img_data, FILEINFO_MIME_TYPE);
        header('Content-Type: image/' . str_replace('image/', '', $extension));
        return imagejpeg($image);
    }

    public function createTrip(Request $request)
    {
        error_log('Create trip request!');
        $id = Auth::id();
        $organizer = Organizer::where('user_id', $id)->first();
        if ($organizer == null) {
            error_log('User not authorized!');
            return $this->sendError('User not authorized!', [], 401);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'string',
            'description' => 'string',
            'begin_date' => 'required',
            'expire_date' => 'required',
            'end_booking' => 'required',
            'price' => 'numeric',
            'types' => 'required'
        ]);

        if ($validator->fails()) {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }
        $trip = new Trip;
        $trip->title = $request['title'];
        $trip->organizer()->associate($organizer->id);
        $trip->description = $request['description'];
        $trip->begin_date = $request['begin_date'];
        $trip->expire_date = $request['expire_date'];
        $trip->end_booking = $request['end_booking'];
        $trip->price = $request['price'];
        $trip_status = TripStatus::where('name', 'available')->first();
        $trip->trip_status_id = $trip_status->id;
        $trip->save();
        $trip->load('tripPhotos');
        $trip->load('placeTrips');
        $trip->load('types');
        error_log('Add trip succeeded!');
        return $this->sendResponse($trip, 'Succeeded!');
    }


    public function editTrip(Request $request, $id)
    {
        $user_id = Auth::id();
        $organizer = Organizer::where('user_id', $user_id)->first();
        if ($organizer == null) {
            error_log('User not authorized!');
            return $this->sendError('User not authorized!', [], 401);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string',
            'description' => 'string',
            'begin_date' => 'required',
            'expire_date' => 'required',
            'end_booking' => 'required',
            'price' => 'numeric',
            'types' => 'required'
        ]);

        if ($validator->fails()) {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }

        $trip = Trip::find($id);

        if($trip  == null){
            error_log('Trip not found');
            return $this->sendError('Trip not found');
        }

        $trip->title = $request['title'];
        $trip->organizer()->associate($organizer->id);
        $types = $request->types;
        $trip->description = $request['description'];
        $trip->begin_date = $request['begin_date'];
        $trip->expire_date = $request['expire_date'];
        $trip->end_booking = $request['end_booking'];
        $trip->price = $request['price'];
        $trip_status = TripStatus::where('name', 'available')->first();
        $trip->trip_status_id = $trip_status->id;
        $trip->save();

      $trip->types()->sync($types);


        error_log('Add trip succeeded!');
        return $this->sendResponse($trip, 'Succeeded!');

    }


    public function editTripPhotos(Request $request)
    {
        error_log('Edit trip photos request');
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
            'photos' => 'required'
        ]);

        if ($validator->fails()) {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }
        $trip = Trip::find($request['trip_id']);
        if ($trip == null) {
            error_log('Trip not exist!');
            return $this->sendError('Trip not exist!');
        }


        $trip->tripPhotos()->delete();

        $photos = $request['photos'];
        for ($i = 0; $i < count($photos); $i++) {
            $tripPhoto = new TripPhoto;
            $tripPhoto->path = $photos[$i]['image'];
            $tripPhoto->trip()->associate($trip->id);
            $tripPhoto->save();
        }
//        $trip->tripPhotos()->saveMany($photos);

        error_log('Edit trip photos succeeded!');
        return $this->sendResponse($trip, 'Succeeded!');
    }


    public function addTripPhotos(Request $request)
    {
        error_log('Add trip photos request');
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
            'photos' => 'required'
        ]);

        if ($validator->fails()) {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }
        $trip = Trip::find($request['trip_id']);
        if ($trip == null) {
            error_log('Trip not exist!');
            return $this->sendError('Trip not exist!');
        }
        $photos = $request['photos'];
        for ($i = 0; $i < count($photos); $i++) {
            $tripPhoto = new TripPhoto;
            $tripPhoto->path = $photos[$i]['image'];
            $tripPhoto->trip()->associate($trip->id);
            $tripPhoto->save();
        }
        $trip->load('tripPhotos');
        $trip->load('placeTrips');
        $trip->load('types');
        error_log('Add trip photos succeeded!');
        return $this->sendResponse($trip, 'Succeeded!');
    }

    public function addPlacesToTrip(Request $request)
    {
        error_log('Add places to trip request');
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
            'places' => 'required'
        ]);

        if ($validator->fails()) {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }
        $trip = Trip::find($request['trip_id']);
        if ($trip == null) {
            error_log('Trip not exist!');
            return $this->sendError('Trip not exist!');
        }
        $places = $request['places'];
        for ($i = 0; $i < count($places); $i++) {
            $placeTrip = new PlaceTrip;
            $placeTrip->place_id = $places[$i]['place_id'];
            $placeTrip->order = $places[$i]['order'];
            $placeTrip->description = $places[$i]['description'];
            $placeTrip->trip()->associate($trip->id);
            $placeTrip->save();
        }
        error_log('Add places to trip succeeded!');
        return $this->sendResponse($trip, 'Succeeded!');
    }

    public function editTripPlaces(Request $request)
    {
        error_log('Edit places to trip request');
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
            'places' => 'required'
        ]);

        if ($validator->fails()) {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }
        $trip = Trip::find($request['trip_id']);
        if ($trip == null) {
            error_log('Trip not exist!');
            return $this->sendError('Trip not exist!');
        }

        $trip->placeTrips()->delete();
        $places = $request['places'];
        for ($i = 0; $i < count($places); $i++) {
            $placeTrip = new PlaceTrip;
            $placeTrip->place_id = $places[$i]['place_id'];
            $placeTrip->order = $places[$i]['order'];
            $placeTrip->description = $places[$i]['description'];
            $placeTrip->trip()->associate($trip->id);
            $placeTrip->save();
        }
        $trip->load('tripPhotos');
        $trip->load('placeTrips');
        $trip->load('types');
        error_log('Add places to trip succeeded!');
        return $this->sendResponse($trip,'Succeeded!');
    }
    public function addTripType(Request $request)
    {
        error_log('Add trip type request');
        $validator = Validator::make($request->all(),[
            'trip_id' => 'required',
            'types' => 'required'
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
        $types = $request->types;
        $trip->types()->sync($types);
        $trip->load('tripPhotos');
        $trip->load('placeTrips');
        $trip->load('types');
        error_log('Add places to trip succeeded!');
        return $this->sendResponse($trip, 'Succeeded!');
    }
    public function getTripDetails($id)
    {
        error_log('Get trip details!');
        $trip = Trip::find($id)
            ->with(['placeTrips','tripPhotos'=>function($query){
                $query->select(['id','trip_id']);
            }])->first();
        if($trip == null)
        {
            error_log('Trip not found!');
            return $this->sendError('Trip not found!');
        }
        error_log('Get trip details!');
        return $this->sendResponse($trip,'Succeeded!');
    }
    public function bookTrip(Request $request)
    {
        error_log('Book trip request!');
        $id = Auth::id();
        $user = User::find($id);
        if($user == null)
        {
            error_log('User not found!');
            return $this->sendError('User not found!');
        }

        $validator = Validator::make($request->all(),[
            'trip_id' => 'required',
            'passengers' => 'required'
        ]);
        if ($validator->fails())
        {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }
        $trip = Trip::find($request['trip_id'])->with(['organizer' => function ($query) use ($id) {
            $query->where('user_id',$id);
        }])->first();
        if($trip->organizer != null)
        {
            error_log('User is the one that created the trip!');
            return $this->sendError('User is the one that created the trip!',[],405);
        }
        $passengers = $request['passengers'];
        $customerTrip = new CustomerTrip();
        $customerTrip->trip()->associate($request['trip_id']);
        $customerTrip->user()->associate($id);
        $customerTrip->checkout = false;
        $customerTrip->save();
        for($i=0 ; $i<count($passengers);$i++)
        {
            $passenger = new Passenger;
            $passenger->passenger_name = $passengers[$i]['name'];
            $passenger->customerTrip()->associate($customerTrip->id);
            $passenger->save();
        }
        error_log('book trip request succeeded!');
        return $this->sendResponse($customerTrip,'Succeeded!');
    }

    public function rateTrip(Request $request)
    {
        error_log('Rate trip request');
        $id = Auth::id();
        $validator = Validator::make($request->all(),[
            'trip_id' => 'required',
            'rate' => 'required|integer|between:1,5'
        ]);
        if ($validator->fails())
        {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }
        $customerTrip = CustomerTrip::where('trip_id',$request['trip_id'])->where('customer_id',$id)->first();
        if($customerTrip == null)
        {
            error_log('No customer trip!');
            return $this->sendError('No customer trip!');
        }
        $customerTrip->rate = $request['rate'];
        $customerTrip->save();
        error_log('Rate trip succeeded!');
        return $this->sendResponse($customerTrip,'Succeeded!');
    }

    public function getTrips()
    {
        error_log('Get trips request!');
        $trips = Trip::with(['placeTrips','tripPhotos'=>function($query){
                $query->select(['id','trip_id']);
            }])->orderByDesc('created_at')->paginate(10);

        foreach ($trips as $trip){

            if(Carbon::now()<$trip['begin_date'])
                $trip['status'] = 'Upcoming';
            else if($trip['begin_date']<Carbon::now() &&
                Carbon::now() < $trip['expire_date'])
                $trip['status']='Active';
            else if($trip['expire_date']<Carbon::now())
                $trip['status'] = 'History';
        }

        error_log('Get trips request succeeded!');
        return $this->sendResponse($trips,'Get trips request succeeded!');
    }

}
