<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Trip;


class SearchController extends BaseController
{
    public function searchForTrip(Request $request){

        $trips = Trip::has('placeTrips')->has('types')->has('tripPhotos')
            ->where('begin_date','>',Carbon::now())->
            withCount('customerTrips')->
            with(['types','placeTrips'=> function($query){
                $query->with('place');
            }, 'tripPhotos' ]);


//        $trips = DB::table('trips')
//            ->join('')


        //$trips = Trip::with('types');


        if($request->has('title')){
            $trips = Trip::where('title','like', '%'.$request['title'].'%')->has('placeTrips')->has('types')->has('tripPhotos')
                ->where('begin_date','>',Carbon::now())->
                withCount('customerTrips')->
                with(['types','placeTrips'=> function($query){
                    $query->with('place');
                }, 'tripPhotos' ]);
        }


        if($request->has('type')){

            $trips = $trips->whereHas('types',function($query) use($request) {
                $query->where('name',$request['type']);
            })->has('placeTrips')->has('types')->has('tripPhotos')->where('begin_date','>',Carbon::now())->
            withCount('customerTrips')->
            with(['types','placeTrips'=> function($query){
                $query->with('place');
            }, 'tripPhotos' ]);
        }

        if($request->has('place')){
            $place = Place::where('name','like', '%'.$request['place'].'%')->first();

            if($place!= null){
                $trips = $trips->whereHas('placeTrips',function($query) use($place) {
                    $query->where('place_id',$place->id);
                })->has('placeTrips')->has('types')->has('tripPhotos')->where('begin_date','>',Carbon::now())->
                withCount('customerTrips')->
                with(['types','placeTrips'=> function($query){
                    $query->with('place');
                }, 'tripPhotos' ]);

            }
            else{
                $trips = $trips->whereNull('id');
            }

        }

        if($request->has('min_price')){
            $trips= $trips->where('price','>=',$request['min_price'])->has('placeTrips')->has('types')->has('tripPhotos')
                ->where('begin_date','>',Carbon::now())->
                withCount('customerTrips')->
                with(['types','placeTrips'=> function($query){
                    $query->with('place');
                }, 'tripPhotos' ]);
        }

        if($request->has('max_price')){
            $trips= $trips->where('price','<=',$request['max_price'])->has('placeTrips')->has('types')->has('tripPhotos')->where('begin_date','>',Carbon::now())->
            withCount('customerTrips')->
            with(['types','placeTrips'=> function($query){
                $query->with('place');
            }, 'tripPhotos' ]);
        }

        return $this->sendResponse($trips->paginate(10),'Result retrieved successfully') ;
    }

    public function searchForPlace(Request $request){
        $places = Place::
            with(['type','photos']);



        if($request->has('name')){
            $places = $places->where('name','LIKE','%'.$request['name'].'%');
        }

        return $this->sendResponse($places->paginate(10)
            ,'Result retrieved successfully');
    }
}
