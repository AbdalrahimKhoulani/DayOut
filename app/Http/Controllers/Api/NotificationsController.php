<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends BaseController
{
    public function index()
    {
        $user = Auth::user();


        $notifications = $user->notifications;

        if(count($notifications)==0){
            return $this->sendError('No Notifications');
        }

        return $this->sendResponse($notifications,'Notification retrieved successfully');
    }
}
