<?php

namespace App\Http\Controllers\Api;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use  App\Http\Controllers\Api\BaseController;

class PlaceController extends BaseController
{
    public function index()
    {
        //$places = Place::withCount('placeTrips');
        //$places = collect($places)->sortBy('placeTrips_count')->reverse()->toArray();
        $places = Place::all();
        return $this->sendResponse($places,'Succeeded');

    }
}
