<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Trip;

class SearchController extends BaseController
{
    public function search(Request $request){
        $trips = Trip::with('types')->select(['trips.id','trips.title']);


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
