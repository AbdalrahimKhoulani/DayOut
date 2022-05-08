<?php

namespace App\Http\Controllers\Api;

use App\Models\FavoritePlace;
use App\Models\Place;
use App\Models\PlaceType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use  App\Http\Controllers\Api\BaseController;
use function PHPUnit\Framework\isEmpty;

class PlaceController extends BaseController
{
    public function index()
    {
        $places = Place::with('photos')->get();
        return $this->sendResponse($places,'Succeeded');
    }
    public function popularPlaces()
    {
        $places = Place::with('photos')->withCount('placeTrips')->get();
        $places = collect($places)->sortBy('placeTrips_count')->toArray();
        return $this->sendResponse($places,'Succeeded');
    }
    public function favorite(Request $request)
    {
        $favorite = FavoritePlace::where('place_id',$request->placeId)->where('user_id', $request->userId)->first();

        if ($favorite == null) {
            $place = Place::find($request->placeId);
            if ($place->count() != 0) {
                $place->favorites()->attach($request->userId);
                return $this->sendResponse(null, 'Place added to favorites!');
            }
            return $this->sendError('Place not found!');

        }
            $place = Place::find($request->placeId);
            if($place->count() != 0)
            {
                $place->favorites()->detach($request->userId);
                return $this->sendResponse(null, 'Place removed from favorites!');
            }
        return $this->sendError('Place not found!');

    }

    public function isFavorite($userId,$placeId)
    {
        $favorite = FavoritePlace::where('place_id',$placeId)->where('user_id', $userId)->first();
        if($favorite!=null)
        {
            return $this->sendResponse(null,'Place is in favorites!');
        }
        else
        {
            return $this->sendResponse(null,'Place is not in favorites!');
        }
    }

}
