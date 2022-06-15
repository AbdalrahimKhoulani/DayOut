<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FavoritePlace;
use App\Models\Place;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FavoritesController extends BaseController
{
    public function getFavoritePlaces(){

        $userId = Auth::id();
        $this->sendInfoToLog('Get favorite places request!',['user_id' => $userId]);
        $favoritePlaces = Place::whereHas('favorites', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->get();

        if($favoritePlaces->count() <= 0){
            $this->sendErrorToLog('User has no favorite places',['user_id' => $userId]);
        }
        $this->sendInfoToLog('Get favorite place request succeeded!',[$favoritePlaces]);
        return $this->sendResponse($favoritePlaces,'Get favorite place request succeeded!');
    }

    private function sendInfoToLog($message,$context){
        Log::channel('requestlog')->info($message,$context);
    }

    private function sendErrorToLog($message,$context){
        Log::channel('requestlog')->error($message,$context);

    }
}
