<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\CustomerTrip;
use App\Models\Passenger;
use App\Models\Place;
use App\Models\PlacePhotos;
use App\Models\Trip;
use App\Models\Type;
use App\Models\User;
use Carbon\Carbon;
use http\Header;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use App\Models\Organizer;
use App\Models\PlaceTrip;
use App\Models\TripPhoto;
use App\Models\TripStatus;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Ramsey\Uuid\Type\Integer;
use Symfony\Component\Console\Input\Input;

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

        error_log('Trip for Organizer received successfully');
        return $this->sendResponse($trips, "Trip for Organizer received successfully");
    }

    public function getTypes()
    {
        error_log('Get types request!');
        $types = Type::all();

        error_log('Get types request succeeded!');
        return $this->sendResponse($types, 'Succeeded!');
    }

    private function filterTrip($trips, $request)
    {


        //        $trips = DB::table('trips')
        //            ->join('')


        //$trips = Trip::with('types');


        if ($request->has('title')) {
            $trips = Trip::where('title', 'like', '%' . $request['title'] . '%');
        }


        if ($request->has('type')) {

            $trips = $trips->whereHas('types', function ($query) use ($request) {
                $query->where('name', $request['type']);
            });
        }

        if ($request->has('place')) {
            $place = Place::where('name', 'like', '%' . $request['place'] . '%')->first();

            if ($place != null) {
                $trips = $trips->whereHas('placeTrips', function ($query) use ($place) {
                    $query->where('place_id', $place->id);
                });
            } else {
                $trips = $trips->whereNull('id');
            }
        }

        if ($request->has('min_price')) {
            $trips = $trips->where('price', '>=', $request['min_price']);
        }

        if ($request->has('max_price')) {
            $trips = $trips->where('price', '<=', $request['max_price']);
        }

        return $trips;
    }

    public function getActiveTrips4Organizer(Request $request)
    {
        error_log('Get active trips request!');
        $id = Auth::id();
        $organizer = Organizer::where('user_id', $id)->first();

        $trips = Trip::select(['id', 'title', 'description', 'begin_date', 'expire_date', 'price'])
            ->where('organizer_id', $organizer->id)->where('begin_date', '<=', Carbon::now())
            ->withCount('customerTrips')
            ->where('expire_date', '>', Carbon::now())
            ->with(['types', 'placeTrips' => function ($query) {
                $query->with('place');
            }, 'tripPhotos'])->where('trip_status_id', 1);


        $trips = $this->filterTrip($trips, $request);

        error_log('Get active trips request succeeded!');
        return $this->sendResponse($trips->paginate(10), 'Succeeded!');
    }

    public function getActiveTrips4Customer(Request $request)
    {
        error_log('Get active trips request!');
        $id = Auth::id();
        $trips = Trip::select(['id', 'title', 'description', 'begin_date', 'expire_date', 'price'])
            ->whereHas('customerTrips', function ($query) use ($id) {
                $query->where('customer_id', $id);
            }, '!=', 0)
            ->withCount('customerTrips')
            ->where('begin_date', '<=', Carbon::now())
            ->where('expire_date', '>', Carbon::now())
            ->with(['types', 'placeTrips' => function ($query) {
                $query->with('place');
            }, 'tripPhotos']);


        $trips = $this->filterTrip($trips, $request);

        error_log('Get active trips request succeeded!');
        return $this->sendResponse($trips->paginate(10), 'Succeeded!');
    }


    public function getUpcomingTrips4Organizer(Request $request)
    {
        error_log('Get upcoming trips request!');
        $id = Auth::id();

        $organizer = Organizer::where('user_id', $id)->first();
        $trips = Trip::select(['id', 'title', 'description', 'begin_date', 'expire_date', 'price'])
            ->where('organizer_id', $organizer->id)
            ->where('begin_date', '>', Carbon::now())->withCount('customerTrips')->with(['types', 'placeTrips' => function ($query) {
                $query->with('place');
            }, 'tripPhotos']);


        $trips = $this->filterTrip($trips, $request);

        error_log('Get upcoming trips request succeeded!');
        return $this->sendResponse($trips->paginate(10), 'Succeeded!');
    }

    public function getUpcomingTrips4Customer(Request $request)
    {
        error_log('Get upcoming trips request!');
        $id = Auth::id();

        $trips = Trip::select(['id', 'title', 'description', 'begin_date', 'expire_date', 'price'])
            ->whereHas('customerTrips', function ($query) use ($id) {
                $query->where('customer_id', $id);
            }, '!=', 0)
            ->withCount('customerTrips')
            ->with('types')
            ->where('begin_date', '>', Carbon::now())
            ->with(['placeTrips' => function ($query) {
                $query->with('place');
            }, 'tripPhotos']);

        $trips = $this->filterTrip($trips, $request);

        error_log('Get upcoming trips request succeeded!');
        return $this->sendResponse($trips->paginate(10), 'Succeeded!');
    }


    public function getHistoryTrips4Organizer(Request $request)
    {
        error_log('Get history trips request!');
        $id = Auth::id();

        $organizer = Organizer::where('user_id', $id)->first();
        $trips = Trip::select(['id', 'title', 'description', 'begin_date', 'expire_date', 'price'])
            ->withCount('customerTrips')
            ->where('organizer_id', $organizer->id)
            ->where('expire_date', '<', Carbon::now())
            ->with(['types', 'placeTrips' => function ($query) {
                $query->with('place');
            }, 'tripPhotos']);


        $trips = $this->filterTrip($trips, $request);

        error_log('Get history trips request succeeded!');
        return $this->sendResponse($trips->paginate(10), 'Succeeded!');
    }

    public function getHistoryTrips4Customer(Request $request)
    {
        error_log('Get history trips request!');
        $id = Auth::id();

        $trips = Trip::select(['id', 'title', 'description', 'begin_date', 'expire_date', 'price'])
            ->withCount('customerTrips')->with('customerTrips', function ($query) use ($id) {
                $query->where('customer_id', $id);
            })
            ->whereHas('customerTrips', function ($query) use ($id) {
                $query->where('customer_id', $id);
            }, '!=', 0)
            ->with('types')->where('expire_date', '<', Carbon::now())
            ->with(['placeTrips' => function ($query) {
                $query->with('place');
            }, 'tripPhotos']);

        $trips = $this->filterTrip($trips, $request);

        error_log('Get history trips request succeeded!');
        return $this->sendResponse($trips->paginate(10), 'Succeeded!');
    }

    //    public function getActiveTrips($type)
    //    {
    //        error_log('Get active trips request!');
    //        $id = Auth::id();
    //        $organizer = Organizer::where('user_id', $id)->first();
    //        if ($type == "organizer") {
    //            $trips = Trip::select(['id', 'title', 'description', 'begin_date', 'expire_date', 'price'])
    //                ->where('organizer_id', $organizer->id)->where('begin_date', '<=', Carbon::now())
    //                ->withCount('customerTrips')
    //                ->where('expire_date', '>', Carbon::now())
    //                ->with(['types', 'placeTrips' => function ($query) {
    //                    $query->with('place');
    //                }, 'tripPhotos'])->where('trip_status_id',1)->get();
    //        } else {
    //            $trips = Trip::select(['id', 'title', 'description', 'begin_date', 'expire_date', 'price'])
    //                ->whereHas('customerTrips', function ($query) use ($id) {
    //                    $query->where('customer_id', $id);
    //                }, '!=', 0)
    //                ->withCount('customerTrips')
    //                ->where('begin_date', '<=', Carbon::now())
    //                ->where('expire_date', '>', Carbon::now())
    //                ->with(['types', 'placeTrips' => function ($query) {
    //                    $query->with('place');
    //                }, 'tripPhotos'])->get();
    //        }
    //
    //       // $trips = $this->filterTrip($trips,$request);
    //
    //        error_log('Get active trips request succeeded!');
    //        return $this->sendResponse($trips, 'Succeeded!');
    //    }

    //    public function getUpcomingTrips($type)
    //    {
    //        error_log('Get upcoming trips request!');
    //        $id = Auth::id();
    //
    //        if ($type == "organizer") {
    //            $organizer = Organizer::where('user_id', $id)->first();
    //            $trips = Trip::select(['id', 'title', 'description', 'begin_date', 'expire_date', 'price'])
    //                ->where('organizer_id', $organizer->id)
    //                ->where('begin_date', '>', Carbon::now())->
    //                withCount('customerTrips')->
    //                with(['types', 'placeTrips' => function ($query) {
    //                    $query->with('place');
    //                }, 'tripPhotos'])->get();
    //        } else {
    //
    //            $trips = Trip::select(['id', 'title', 'description', 'begin_date', 'expire_date', 'price'])
    //                ->whereHas('customerTrips', function ($query) use ($id) {
    //                    $query->where('customer_id', $id);
    //                }, '!=', 0)
    //                ->withCount('customerTrips')
    //                ->with('types')->where('begin_date', '>', Carbon::now())
    //                ->with(['placeTrips' => function ($query) {
    //                    $query->with('place');
    //                }, 'tripPhotos'])->get();
    //        }
    //
    //        error_log('Get upcoming trips request succeeded!');
    //        return $this->sendResponse($trips, 'Succeeded!');
    //    }

    //    public function getHistoryTrips($type)
    //    {
    //        error_log('Get history trips request!');
    //        $id = Auth::id();
    //
    //        if ($type == "organizer") {
    //            $organizer = Organizer::where('user_id', $id)->first();
    //            $trips = Trip::select(['id', 'title', 'description', 'begin_date', 'expire_date', 'price'])
    //                ->withCount('customerTrips')->
    //                where('organizer_id', $organizer->id)->
    //                where('expire_date', '<', Carbon::now())->
    //                with(['types', 'placeTrips' => function ($query) {
    //                    $query->with('place');
    //                }, 'tripPhotos'])->get();
    //        } else {
    //            $trips = Trip::select(['id', 'title', 'description', 'begin_date', 'expire_date', 'price'])->
    //            withCount('customerTrips')->
    //                with('customerTrips',function ($query) use ($id) {
    //                    $query->where('customer_id',$id);
    //            })->
    //            whereHas('customerTrips', function ($query) use ($id) {
    //                $query->where('customer_id', $id);
    //            }, '!=', 0)->
    //            with('types')->where('expire_date', '<', Carbon::now())
    //                ->with(['placeTrips' => function ($query) {
    //                    $query->with('place');
    //                }, 'tripPhotos'])->get();
    //
    //
    //        }
    //
    //
    //        error_log('Get history trips request succeeded!');
    //        return $this->sendResponse($trips, 'Succeeded!');
    //    }

    public function tripPhotoAsBase64($id)
    {
        $tripPhoto = TripPhoto::find($id);
        if ($tripPhoto == null) {
            error_log('Photo with id  ' . $id . ' not found');
            return $this->sendError('Photo with id  ' . $id . ' not found');
        }


        $pieces = explode('/', $tripPhoto->path);

        $last_word = array_pop($pieces);
        $image = Storage::disk('public')->get('\trips\\' . $last_word);


        $base64 = base64_encode($image);

        return $this->sendResponse($base64, 'Base64 for image ' . $id . ' retrieved successfully');
    }

    public function getTripPhotos($trip_id)
    {
        $trip = Trip::find($trip_id);

        if ($trip == null) {
            return $this->sendError('Trip not found');
        }
        $photos = $trip->tripPhotos;

        if (count($photos) == 0) {
            return $this->sendError('No images for this trip');
        }
        $trip_images = [];
        foreach ($photos as $photo) {
            array_push($trip_images, Storage::url($photo['path']));
        }
        return $this->sendResponse($photos, 'Photos retrieved successfully ');
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


    public function editTrip(Request $request)
    {
        $user_id = Auth::id();
        $organizer = Organizer::where('user_id', $user_id)->first();
        if ($organizer == null) {
            error_log('User not authorized!');
            return $this->sendError('User not authorized!', [], 401);
        }

        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
            'title' => 'string',
            'description' => 'string',
            'begin_date' => 'required',
            'expire_date' => 'required',
            'end_booking' => 'required',
            'price' => 'numeric',
        ]);

        if ($validator->fails()) {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }

        $trip = Trip::find($request['trip_id']);

        if ($trip == null) {
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


        error_log('Edit trip succeeded!');
        return $this->sendResponse($trip, 'Succeeded!');
    }


    public function editTripTypes(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'types' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Edit trip types failed', $validator->errors(), 422);
        }
        $trip = Trip::find($id);
        if ($trip == null) {
            return $this->sendError('Edit trip types failed');
        }

        $types = $request['types'];
        $trip->types()->detach();
        for ($i = 0; $i < sizeof($types); $i++) {
            $trip->types()->attach($types[$i]['id']);
        }

        $trip['types'] = $trip->types;

        return $this->sendResponse($trip, 'Succeeded!');
    }

    public function editTripPhotos(Request $request)
    {
        error_log('Edit trip photos request');
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
            'photos.*' => 'required|mimes:jpg,jpeg,png,bmp|max:20000'
        ],[
            'photos.*.required' => 'Please upload an image',
            'photos.*.mimes' => 'Only jpeg,png and bmp images are allowed',
            'photos.*.max' => 'Sorry! Maximum allowed size for an image is 20MB',
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

        $photos = $trip->tripPhotos()->get();


        foreach ($photos as $photo) {
            $file = Storage::path($photo['path']);
            $file = str_replace('/', '\\', $file);

            $pieces = explode('\\', $file);

            $last_word = array_pop($pieces);
            Storage::disk('public')->delete('\trips\\' . $last_word);

            error_log('File deleted successful');

        }
        $trip->tripPhotos()->delete();


        $new_photos = $request['photos'];
        $place_images = [];
//        for ($i = 0; $i < count($new_photos); $i++) {
//
//            $img_data = $new_photos[$i]['image'];
//            $image = base64_decode($img_data);
//            $filename = uniqid();
//            //$extension = '.png';
//            $file = finfo_open();
//            $result = finfo_buffer($file, $image, FILEINFO_MIME_TYPE);
//            $extension = str_replace('image/', '.', $result);
//
//            Storage::put('public/trips/' . $filename . $extension, $image);
//
//
//            $place_images[$i] = TripPhoto::create([
//                'trip_id' => $trip->id,
//                'path' => Storage::url('public/trips/' . $filename . $extension)
//            ]);
//        }
        $photos = $request->file('photos');
        foreach ($photos as $photo){
            $tripPhoto = new TripPhoto();
            $tripPhoto->path = $this->storeMultiPartImage($photo);
            $tripPhoto->trip()->associate($trip->id);
            $tripPhoto->save();
        }

        error_log('Edit trip photos succeeded!');
        return $this->sendResponse($trip, 'Succeeded!');
    }
    public function editTripPhotosMultipart(Request $request){

        $this->sendInfoToLog('Edit trip photos with multipart request!',[]);

//        $validator = Validator::make($request->all(), [
//            'trip_id' => 'required',
//            'photos.*' => 'required|mimes:jpg,jpeg,png,bmp|max:20000'
//        ],[
//            'photos.*.required' => 'Please upload an image',
//            'photos.*.mimes' => 'Only jpeg,png and bmp images are allowed',
//            'photos.*.max' => 'Sorry! Maximum allowed size for an image is 20MB',
//        ]);
//
//        if ($validator->fails()) {
//            $this->sendErrorToLog($validator->errors(),[]);
//            return $this->sendError('Validator failed! check the data', $validator->errors());
//        }
        $trip = Trip::find($request['trip_id']);
        if ($trip == null) {
            $this->sendErrorToLog('Trip not exist!',[]);
            return $this->sendError('Trip not exist!');
        }
        if($request->has('deleted_photo_ids')){
            $deletedPhotoIds = $request['deleted_photo_ids'];
            foreach ($deletedPhotoIds as $deletedPhotoId){
                $tripPhoto = TripPhoto::find($deletedPhotoId);
                if($tripPhoto != null){
                    $file = Storage::path($tripPhoto->path);
                    $file = str_replace('/', '\\', $file);

                    $pieces = explode('\\', $file);

                    $last_word = array_pop($pieces);
                    Storage::disk('public')->delete('\trips\\' . $last_word);
                    $tripPhoto->delete();
                }
            }
        }

        $photos = $request->file('photos');
        if($photos!= null) {
            foreach ($photos as $photo) {
                $tripPhoto = new TripPhoto();
                $tripPhoto->path = $this->storeMultiPartImage($photo);
                $tripPhoto->trip()->associate($trip->id);
                $tripPhoto->save();
            }
        }

        $this->sendInfoToLog('Edit trip photos with multipart request succeeded!',[]);
        return $this->sendResponse($trip, 'Succeeded!');

    }

    public function addTripPhotos(Request $request)
    {
        error_log('Add trip photos request');
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
            'photos.*' => 'required|mimes:jpg,jpeg,png,bmp|max:20000'
        ],[
            'photos.*.required' => 'Please upload an image',
            'photos.*.mimes' => 'Only jpeg,png and bmp images are allowed',
            'photos.*.max' => 'Sorry! Maximum allowed size for an image is 20MB',
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
        $photos = $request->file('photos');

//        for ($i = 0; $i < count($photos); $i++) {
//            $tripPhoto = new TripPhoto;
//            $tripPhoto->path = $this->storeBase64Image($photos[$i]['image']);
//            $tripPhoto->trip()->associate($trip->id);
//            $tripPhoto->save();
//        }


        foreach ($photos as $photo){
            $tripPhoto = new TripPhoto();
            $tripPhoto->path = $this->storeMultiPartImage($photo);
            $tripPhoto->trip()->associate($trip->id);
            $tripPhoto->save();
        }

        $trip->load(['tripPhotos' => function ($query) {
            $query->select(['id', 'trip_id']);
        }]);
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

        error_log($request['trip_id']);
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
        $trip->load(['tripPhotos' => function ($query) {
            $query->select(['id', 'trip_id']);
        }]);
        $trip->load('placeTrips');
        $trip->load('types');
        error_log('Add places to trip succeeded!');
        return $this->sendResponse($trip, 'Succeeded!');
    }

    public function addTripType(Request $request)
    {
        error_log('Add trip type request');
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
            'types' => 'required'
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
        $types = $request['types'];
        for ($i = 0; $i < sizeof($types); $i++) {
            $trip->types()->attach($types[$i]['id']);
        }
        $trip->load(['tripPhotos' => function ($query) {
            $query->select(['id', 'trip_id']);
        }]);
        $trip->load('placeTrips');
        $trip->load('types');
        error_log('Add places to trip succeeded!');
        return $this->sendResponse($trip, 'Succeeded!');
    }

    public function getTripDetails($id)
    {
        error_log('Get trip details!');

        $trip = Trip::where('id', $id)
            ->with(['types', 'placeTrips' => function ($query) {
                $query->with('place');
            }, 'tripPhotos' => function ($query) {
                $query->select(['id', 'trip_id']);
            }])->first();
        if ($trip == null) {
            error_log('Trip not found!');
            return $this->sendError('Trip not found!');
        }
        $bookingController = new BookingsController();
        $trip['is_in_trip'] = $bookingController->isInTrip(Auth::guard('api')->id(),$trip->id);
        $trip['customerTrips'] = CustomerTrip::where('customer_id',Auth::guard('api')->id())
            ->where('trip_id',$trip->id)->with('user')->first();
        error_log('Get trip details succeeded!');
        return $this->sendResponse($trip, 'Succeeded!');
    }

    public function getTripDetailsOrganizer($id){

        error_log('Get trip details organizer!');


        $trip = Trip::where('id', $id)
            ->with(['types', 'customerTrips' => function ($query) {
                return $query->with('user')->with('passengers');
            }, 'placeTrips' => function ($query) {
                $query->with('place');
            }, 'tripPhotos' => function ($query) {
                $query->select(['id', 'trip_id']);
            }])->first();
        if ($trip == null) {
            error_log('Trip not found!');
            return $this->sendError('Trip not found!');
        }
        $bookingController = new BookingsController();
        $trip['is_in_trip'] = $bookingController->isInTrip(Auth::guard('api')->id(),$trip->id);

        error_log('Get trip details organizer succeeded!');
        return $this->sendResponse($trip, 'Succeeded!');
    }

    public function rateTrip(Request $request)
    {
        error_log('Rate trip request');
        $id = Auth::id();
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
            'rate' => 'required'
        ]);
        if ($validator->fails()) {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }
        $customerTrip = CustomerTrip::where('trip_id', $request['trip_id'])->where('customer_id', $id)->first();
        if ($customerTrip == null) {
            error_log('No customer trip!');
            return $this->sendError('No customer trip!');
        }
        $customerTrip->rate = $request['rate'];
        $customerTrip->save();
        error_log('Rate trip succeeded!');
        return $this->sendResponse($customerTrip, 'Succeeded!');
    }

    public function getTrips()
    {
//TODO get by followeres
        error_log('Get trips request!');
        $trips = Trip::has('placeTrips')->has('tripPhotos')->has('types')
            ->with(['placeTrips', 'types', 'tripPhotos'])
            ->withCount('customerTrips')
            ->with(['placeTrips' => function ($query) {
                $query->with('place:id,name');
            }])
            ->where('begin_date', '>', Carbon::now())->orderByDesc('created_at')->paginate(10);

//        $trips->load(['placeTrips','types','tripPhotos','placeTrips' => function($query){
//            $query->with('place:id,name');
//        }]);

        error_log('Get trips request succeeded!');
        return $this->sendResponse($trips, 'Get trips request succeeded!');
    }

    private function storeBase64Image($photo)
    {
        $image = base64_decode($photo);
        $filename = uniqid();
        $extension = '.png';
        $f = finfo_open();
        $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
        if ($result == 'image/jpeg')
            $extension = '.jpeg';
        elseif ($result == 'image/webp')
            $extension = '.webp';
        elseif ($result == 'image/x-ms-bmp')
            $extension = '.bmp';
        Storage::put('public/trips/' . $filename . $extension, $image);
        return Storage::url('public/trips/' . $filename . $extension);
    }

    private function storeMultiPartImage($image){

       // dd($image);

        $filename = $image->store('trips',['disk' => 'public']);
        if(!Str::contains($filename,'.'))
        return Storage::url('public/' . $filename . '.jpg' );
        return Storage::url('public/'.$filename);
    }

    public function beginTrip($id)
    {
        $trip = Trip::with('organizer')->where('id', $id)->first();

        if ($trip == null) {
            error_log('This trip not found');
            return $this->sendError('This trip not found');
        }


        if ($trip->organizer->user_id != Auth::id()) {
            error_log('Unauthorized');
            return $this->sendError('Unauthorized', [], 401);
        }

        $activeStatus = TripStatus::where('name', 'started')->first();

        $trip->trip_status_id = $activeStatus->id;
        $trip->save();

        $users = User::whereHas('customerTrip', function ($query) use ($trip) {
            $query->where('trip', $trip->id);
        })->get();

        $fcm = new FCM();
        $fcm->sendNotification($users, 'Start trip', 'Your trip ' . $trip->title . ' started successfully ,we wish you enjoy it');


        return $this->sendResponse($trip, 'Trip started successfully');
    }

    public function endTrip($id)
    {
        $trip = Trip::with('organizer')->where('id', $id)->first();

        if ($trip == null) {
            error_log('This trip not found');
            return $this->sendError('This trip not found');
        }

        if ($trip->organizer->user_id != Auth::id()) {
            error_log('Unauthorized');
            return $this->sendError('Unauthorized', [], 401);
        }

        $activeStatus = TripStatus::where('name', 'ended')->first();

        $trip->trip_status_id = $activeStatus->id;
        $trip->save();

        $users = User::whereHas('customerTrip', function ($query) use ($trip) {
            $query->where('trip', $trip->id);
        })->get();

        $fcm = new FCM();
        $fcm->sendNotification($users, 'End trip', 'Your trip ' . $trip->title . ' ended successfully');

        return $this->sendResponse($trip, 'Trip ended successfully');
    }

    public function updatePlaceStatus($trip_id, $place_id)
    {

        Log::channel('requestlog')->info('Update place status request!', [
            'trip_id' => $trip_id,
            'place_id' => $place_id
        ]);

        $placeTrip = PlaceTrip::where('trip_id', $trip_id)->where('place_id', $place_id)->first();
        if ($placeTrip == null) {
            Log::channel('requestlog')->error('Trip or place does not exist!');
            return $this->sendError('Trip or place does not exist!', 404);
        } elseif ($placeTrip->status == false) {
            $placeTrip->status = true;
            $placeTrip->save();
        } else {
            $placeTrip->status = false;
            $placeTrip->save();
        }


        Log::channel('requestlog')->info('Succeeded!');
        return $this->sendResponse($placeTrip, 'Succeeded!');
    }

    public function deleteTrip($tripId){

        $this->sendInfoToLog('Delete trip request',['user_id' => Auth::id()]);

        $organizerId = $this->getOrganizerId();

        if($organizerId == null){
            $this->sendErrorToLog('User is not organizer!',['user_id' => Auth::id()]);
            return $this->sendError('User is not organizer!',[],403);
        }

        $trip = Trip::where('id',$tripId)->where('organizer_id',$organizerId)->first();

        if($trip == null){
            $this->sendErrorToLog('Trip does not exist or organizer did not create this trip!',['trip_id' => $tripId]);
            return $this->sendError('Trip does not exist or organizer did not create this trip!',[],404);
        }

        if($trip->begin_date < now()){
            $this->sendErrorToLog('Trip is active',['trip_id' => $tripId]);
            return $this->sendError('Trip is active',[],406);
        }

        $trip->delete();

        $this->sendInfoToLog('Delete trip request succeeded',['user_id' => Auth::id()]);
        return $this->sendResponse(null,'Delete trip request succeeded');

    }

    private function getOrganizerId(){
        $organizer = Organizer::where('user_id',Auth::id())->first();
        if($organizer == null)
            return null;
        return $organizer->id;
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
