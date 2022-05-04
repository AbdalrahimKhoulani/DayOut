<?php

namespace App\Http\Controllers\Api;

use App\Models\Organizer;
use Illuminate\Http\Request;

class OrganizerController extends BaseController
{
    public function organizerProfile(Request $request)
    {
        $organizer = Organizer::with('user')->withCount('followers','trips')->where('id',$request->organizer_id)->first();
        if ($organizer !=null)
        {
            return $this->sendResponse($organizer,'Succeeded!');
        }
        return $this->sendError('Organizer not found!');
    }
}
