<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\CustomerTrip;
use App\Models\Organizer;
use App\Models\Passenger;
use App\Models\Trip;
use App\Models\User;
use App\Services\FCM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BookingsController extends BaseController
{

    /**
     * @param $trip_id
     * @return mixed
     * $customers = User::whereIn('id',Organizer::select(['user_id'])->get(['user_id']))
     * ->paginate(10);
     */

    public function getPassengersForTrip($trip_id)
    {

        $trip = Trip::find($trip_id);
        if ($trip == null) {
            error_log('This trip is not found');
            return $this->sendError('This trip is not found');
        }

//        $passengers = DB::table('customer_trips')
//            ->join('passengers','customer_trips.id','=','passengers.customer_trip_id')
//            ->where('customer_trips.trip_id',$trip_id)
//            ->select('passengers.*')->get();
          $passengers = Passenger::with(['customerTrip' => function($query) use ($trip_id) {
              $query->where('trip_id',$trip_id);
          }])->get();

        if (count($passengers) == 0) {
            error_log('This trip has no passengers');
            return $this->sendError('This trip has no passengers');
        }
        error_log('Passengers list returned successfully');

        return $this->sendResponse($passengers, 'Passengers list returned successfully');
    }

    public function getBookingsForTrip($trip_id)
    {

        $trip = Trip::find($trip_id);
        if ($trip == null) {
            error_log('This trip is not found');
            return $this->sendError('This trip is not found');
        }
        $tripCustomers = CustomerTrip::with(['user', 'passengers'])->where('trip_id', $trip_id)->get();

        if (count($tripCustomers) == 0) {
            error_log('This trip has no bookings');
            return $this->sendError('This trip has no bookings');
        }
        error_log('Bookings list returned successfully');
        return $this->sendResponse($tripCustomers, 'Bookings list returned successfully');
    }

    /*
     * Message to ABD : خليت الطلب ياخد رقم المستخدم و رقم الرحلة لإنو الفرونت ما عندن رقم الحجز*/
    public function confirmBooking($customerId,$tripId)
    {
        $booking = CustomerTrip::with(['user','passengers'])->where('customer_Id',$customerId)->where('trip_id',$tripId)->first();
       // $booking = CustomerTrip::with(['user', 'passengers'])->where('id', $id)->first();
        if ($booking == null) {
            error_log('This booking not found');
            return $this->sendError('This booking not found');
        }
        $trip = Trip::find($booking->trip_id);
        $organizer = $trip->organizer;

        if ($organizer->user_id != Auth::id()) {
            error_log('Unauthorized');
            return $this->sendError('Unauthorized',[], 401);
        }
        if ($booking->confirmed_at == null)
            $booking->confirmBooking();

        $user = User::where('id', $booking->user_id)->get();

        $fcm = new FCM();
        $fcm->sendNotification($user, 'Confirm booking',
            'Your booking in '.$booking->trip->title.' confirmed successfully by '.$booking->trip->organizer->user->first_name.' '.$booking->trip->organizer->user->last_name);

        return $this->sendResponse($booking, 'This booking confirmed successfully');
    }

    public function cancelConfirmBooking($customerId,$tripId)
    {
        $booking = CustomerTrip::with(['user','passengers'])->where('customer_Id',$customerId)->where('trip_id',$tripId)->first();
        //$booking = CustomerTrip::with(['user', 'passengers'])->where('id', $id)->first();
        if ($booking == null) {
            error_log('This booking not found');
            return $this->sendError('This booking not found');
        }
        $trip = Trip::find($booking->trip_id);
        $organizer = $trip->organizer;

        if ($organizer->user_id != Auth::id()) {
            error_log('Unauthorized');
            return $this->sendError('Unauthorized',[], 401);
        }
        if ($booking->confirmed_at == null) {
            error_log('This booking not confirmed');
            return $this->sendError('This booking not confirmed');
        }
        $booking->confirmed_at = null;
        $booking->save();

        return $this->sendResponse($booking,'This booking canceled successfully');
    }

    public function bookTrip(Request $request)
    {
        error_log('Book trip request!');
        $id = Auth::id();
        $user = User::find($id);
        if ($user == null) {
            error_log('User not found!');
            return $this->sendError('User not found!');
        }

        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
        ]);
        if ($validator->fails()) {
            error_log($validator->errors());
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }
        if($this->isInTrip(Auth::id(),$request['trip_id'])){
            $this->sendErrorToLog('User already booked this trip',[]);
            return $this->sendError('User already booked this trip',[],406);
        }
        $trip = Trip::find($request['trip_id'])->with(['organizer' => function ($query) use ($id) {
            $query->where('user_id', $id);
        }])->first();
        if ($trip->organizer != null) {
            error_log('User is the one that created the trip!');
            return $this->sendError('User is the one that created the trip!', [], 405);
        }

        $customerTrip = new CustomerTrip();
        $customerTrip->trip()->associate($request['trip_id']);
        $customerTrip->user()->associate($id);
        $customerTrip->save();
        if($request['passengers']!=null) {
            $passengers = $request['passengers'];
            for ($i = 0; $i < count($passengers); $i++) {
                $passenger = new Passenger;
                $passenger->passenger_name = $passengers[$i]['name'];
                $passenger->customerTrip()->associate($customerTrip->id);
                $passenger->save();
            }
        }

        $fcm = new FCM();
        $fcm->sendNotification($trip->organizer->user, 'Booking trip',
            $user->first_name.' '.$user->last_name .' booking in trip '.$trip->title);


        error_log('book trip request succeeded!');
        return $this->sendResponse($customerTrip, 'Succeeded!');
    }


    public function cancelBookingByUser($tripId){
        $booking = CustomerTrip::where('customer_Id',Auth::id())->where('trip_id',$tripId)->first();



        if($booking == null){
            error_log('Booking not found');
            return $this->sendError('Booking with id : '.$tripId.' not found');
        }
        if($booking->customer_id!= Auth::id()){
            error_log('Do not have permission to this booking');
            return $this->sendError('Do not have permission to this booking',[],401);
        }
        if($booking->confirmed_at != null){
            error_log('This booking is confirmed , Please see organizer');
            return $this->sendError('This booking is confirmed , Please see organizer',[],401);
        }
        $booking->delete();

        $user = User::where('id', $booking->user_id)->get();
        $fcm = new FCM();
        $fcm->sendNotification($user, 'Cancel booking',
            'Your booking in '.$booking->trip->title.' canceled successfully');
        $fcm->sendNotification($booking->trip->organizer->user,'Cancel booking',
            'Booking in '.$booking->trip->title.' canceled by '.$user->first_name.' '.$user->last_name);

        error_log('Booking canceled successfully');
        return $this->sendResponse($booking,'Booking canceled successfully');
    }

    public function cancelBookingByOrganizer($id){
        $booking = CustomerTrip::find($id);

        if($booking == null){
            error_log('Booking not found');
            return $this->sendError('Booking with id : '.$id.' not found');
        }
        if($booking->trip->organizer->user_id != Auth::id()){
            error_log('Do not have permission to this booking');
            return $this->sendError('Do not have permission to this booking',[],401);
        }
        $booking->delete();

        $user = User::where('id', $booking->user_id)->get();
        $fcm = new FCM();
        $fcm->sendNotification($user, 'Cancel booking',
            'Your booking in '.$booking->trip->title.' canceled by organizer ');
        $fcm->sendNotification($booking->trip->organizer->user,'Cancel booking',
            'Booking in '.$booking->trip->title.' canceled successfully');


        error_log('Booking canceled successfully');
        $booking->makeHidden('trip');
        return $this->sendResponse($booking,'Booking canceled successfully');
    }

    public function isInTrip($customerId,$tripId){

        $customerTrip = CustomerTrip::where('customer_id',$customerId)->where('trip_id',$tripId)->first();

        if ($customerTrip != null){
            return true;
        }else{
            return false;
        }

    }
    private function sendInfoToLog($message,$context){
        Log::channel('requestlog')->info($message,$context);
    }

    private function sendErrorToLog($message,$context){
        Log::channel('requestlog')->error($message,$context);

    }
}
