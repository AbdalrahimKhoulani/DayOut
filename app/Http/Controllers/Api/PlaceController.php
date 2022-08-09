<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PlaceSuggestionRequest;
use App\Models\FavoritePlace;
use App\Models\Organizer;
use App\Models\Place;
use App\Models\PlaceSuggestion;
use App\Models\PlaceType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use  App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Passport;
use function PHPUnit\Framework\isEmpty;
use App\Models\PlacePhotos;

class PlaceController extends BaseController
{
    public function index()
    {
        error_log('Get places request!');
        $places = Place::with(['photos'])->paginate(10);
        error_log('Get places request succeeded!');
        return $this->sendResponse($places, 'Succeeded');
    }

    public function popularPlaces($id)
    {

        error_log('Popular places request');
        $places = Place::with('photos')->withCount('placeTrips')
            ->withCount(['favorites' => function ($query) use ($id) {
            $query->where('user_id', $id);
        }])->get();
        $places = collect($places)->sortBy('placeTrips_count')->toArray();
        error_log('Popular places request succeeded!');
        return $this->sendResponse($places, 'Succeeded');

    }

    public function favorite(Request $request)
    {
        error_log('Add/Remove to/from favorite request');
        $favorite = FavoritePlace::where('place_id', $request->placeId)->where('user_id', $request->userId)->first();

        if ($favorite == null) {
            $place = Place::find($request->placeId);
            if ($place != null) {
                $place->favorites()->attach($request->userId);
                return $this->sendResponse(null, 'Place added to favorites!');
            }
            error_log('Place not found!');
            return $this->sendError('Place not found!');

        }
        $place = Place::find($request->placeId);
        if ($place != null) {
            $place->favorites()->detach($request->userId);
            return $this->sendResponse(null, 'Place removed from favorites!');
        }
        error_log('Place not found!');
        return $this->sendError('Place not found!');

    }

    public function isFavorite($userId, $placeId)
    {
        $favorite = FavoritePlace::where('place_id', $placeId)->where('user_id', $userId)->first();
        if ($favorite != null) {
            return $this->sendResponse(null, 'Place is in favorites!');
        } else {
            return $this->sendResponse(null, 'Place is not in favorites!');
        }
    }

    public function placePhoto($id)
    {
        $photo = PlacePhotos::find($id);


        $img_data = base64_decode($photo->path);
        $image = imagecreatefromstring($img_data);

        $finfo = finfo_open();
        $extension = finfo_buffer($finfo, $img_data, FILEINFO_MIME_TYPE);
        header('Content-Type: image/' . str_replace('image/', '', $extension));
        return imagejpeg($image);
    }

    public function placeDetails($id){
        Log::channel('requestlog')->info('Get place details',['place_id' => $id]);

        $place = Place::find($id);

        if($place == null){
            Log::channel('requestlog')->error('Place does not exist!');
            return $this->sendError('Place does not exist!',404);
        }

        $place->load(['photos','type']);

        Log::channel('requestlog')->info('Get place details request succeeded!!');
        return $this->sendResponse($place,'Succeeded!');
    }

    public function suggestPlace(Request $request){

        $this->sendInfoToLog('Suggest place request',['user_id' => Auth::id()]);

        $validator = Validator::make($request->all(), [
            'place_name' => 'required',
            'place_address' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            $this->sendErrorToLog('Validator failed! check the data',[$validator->errors()]);
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }
        $organizerId = $this->getOrganizerId(Auth::id());

        $placeSuggestion = new PlaceSuggestion();
        $placeSuggestion->organizer()->associate($organizerId);
        $placeSuggestion->place_name = $request['place_name'];
        $placeSuggestion->place_address = $request['place_address'];
        $placeSuggestion->description = $request['description'];
        $placeSuggestion->save();

        $this->sendInfoToLog('Suggest place request succeeded!',[$placeSuggestion]);
        return $this->sendResponse($placeSuggestion,'Suggest place request succeeded!');
    }
    private function getOrganizerId($id){
        $organizer = Organizer::where('user_id',$id)->first();
        if($organizer == null)
            return null;
        return $organizer->id;
    }

    private function sendInfoToLog($message,$context){
        Log::channel('requestlog')->info($message,$context);
    }

    private function sendErrorToLog($message,$context){
        Log::channel('requestlog')->error($message,$context);

    }

}
