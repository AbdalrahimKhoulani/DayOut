<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoadMapController extends BaseController
{
    public function getRoadMapPlaces($tripId)
    {
        Log::channel('requestlog')->info("Get road map places request!");
        $trip = Trip::find($tripId);

        if($trip == null){
            Log::channel('requestlog')->error("Trip does not exist!");
            return $this->sendError('Trip does not exist!',404);
        }

        $trip->load('placeTrips.place:id,name');

        if(sizeof($trip->placeTrips) <= 0){
            Log::channel('requestlog')->error("Trip has no places!!");
        }

        Log::channel('requestlog')->info("Get road map places request succeeded!!");
        return $this->sendResponse($trip,"Succeeded!");
    }
}
