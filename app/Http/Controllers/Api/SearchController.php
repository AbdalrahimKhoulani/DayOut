<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Trip;

class SearchController extends BaseController
{
    public function search(Request $request){

        $trips = Trip::select(['id','title','description','begin_date','expire_date','price'])
            ->where('begin_date','>',Carbon::now())->
            withCount('customerTrips')->
            with(['types','placeTrips'=> function($query){
                $query->with('place');
            }, 'tripPhotos' ]);


        //$trips = Trip::with('types');


        if($request->has('title')){
            $trips = Trip::where('title','like', '%'.$request['title'].'%');
        }


        if($request->has('type')){

            $trips = $trips->whereHas('types',function($query) use($request) {
                $query->where('name',$request['type']);
            });
        }

        if($request->has('min_price')){


            $trips= $trips->where('price','>=',$request['min_price']);
        }

        if($request->has('max_price')){
            $trips= $trips->where('price','<=',$request['max_price']);
        }



        return $this->sendResponse($trips->get(),'Result retrieved successfully') ;
    }
}
