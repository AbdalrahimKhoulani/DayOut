<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follower;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FollowController extends BaseController
{
    public function followOrganizer($userOrganizerId){
        $customerId = Auth::id();
        $this->sendInfoToLog('Follow organizer request!',['customerId' => $customerId,'organizer_user_id' => $userOrganizerId]);
        $organizerId = $this->getOrganizerId($userOrganizerId);

        if($organizerId == null){
            $this->sendErrorToLog('The user is not organizer', ['organizer_user_id'=>$userOrganizerId]);
            return $this->sendError('The user is not organizer',[],406);
        }
        $customer = User::find($customerId);
        $customer->organizerFollow()->toggle($organizerId);

        $this->sendInfoToLog('Follow organizer request!',['customerId' => $customerId,'organizer_user_id' => $userOrganizerId]);
        return $this->sendResponse([],'Follow organizer request!');

    }

    public function getFollowedOrganizers(){
        $userId = Auth::id();
        $this->sendInfoToLog('Get followed organizers request!',['user_id',$userId]);

        $followedOrganizers = Organizer::with('user')->whereHas('followers',function ($query) use ($userId) {
            $query->where('user_id',$userId);
        })->paginate(10);
//        $followedOrganizers = DB::table('organizers as organizers')
//            ->join('users as users','users.id','=','organizers.user_id')
//            ->join('followers as f','organizers.id','=','f.organizer_id')
//            ->select(['organizers.id','organizers.user_id','first_name','last_name','bio','email','phone_number','photo','gender'])
//            ->where('is_active','=',true)
//            ->where('f.user_id','=',$userId)
//            ->get();

        if($followedOrganizers->count() <= 0){
            $this->sendErrorToLog('User is not following anyone!',['user_id',$userId]);
        }
        $this->sendInfoToLog('Get followed organizers request!',[$followedOrganizers]);

        return $this->sendResponse($followedOrganizers,'Get followed organizers request!');
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
