<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Passenger;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CheckOutController extends BaseController
{
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
            'passengers_ids' => 'required'
        ]);

        if($validator->fails()){
            $this->sendErrorToLog('Validator failed',[$validator->errors()]);
            return $this->sendError('Validator failed',$validator->errors());
        }



        $trip_id = $request['trip_id'];

        $trip = Trip::with('organizer')->where('id', $trip_id)->first();
        if ($trip == null) {
            error_log('This trip not found');
            return $this->sendError('This trip not found');
        }

        if ($trip->organizer->user_id != Auth::id()) {
            error_log('Unauthorized');
            return $this->sendError('Unauthorized',[], 401);
        }



        $passengers_ids = $request['passengers_ids'];

        $passengers = Passenger::with('customerTrip')->whereNotIn('id', $passengers_ids)->get();
        foreach ($passengers as $passenger) {
            if ($passenger->customerTrip->trip_id == $trip_id) {
                $passenger->checkout = false;
                $passenger->save();
            }
        }
        $passengers = Passenger::with('customerTrip')->whereIn('id', $passengers_ids)->get();
        foreach ($passengers as $passenger) {
            if ($passenger->customerTrip->trip_id == $trip_id) {
               $passenger->checkout = true;
               $passenger->save();
            }
        }

        return $this->sendResponse($passengers,'Passengers were checkout successfully');
    }

    private function sendInfoToLog($message,$context){
        Log::channel('requestlog')->info($message,$context);
    }

    private function sendErrorToLog($message,$context){
        Log::channel('requestlog')->error($message,$context);

    }


}
