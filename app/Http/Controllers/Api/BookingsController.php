<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\CustomerTrip;
use App\Models\Organizer;
use App\Models\Passenger;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingsController extends BaseController
{

    /**
     * @param $trip_id
     * @return mixed
     * $customers = User::whereIn('id',Organizer::select(['user_id'])->get(['user_id']))
     * ->paginate(10);
     */
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

        return $this->sendResponse($booking, 'This booking confirmed successfully');
    }

    public function cancelBooking($customerId,$tripId)
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
        $customerPassenger = new Passenger();
        $customerPassenger->passenger_name= $user->first_name;
        $customerPassenger->customerTrip()->associate($customerTrip->id);
        $customerPassenger->save();
        if($request['passengers']!=null) {
            $passengers = $request['passengers'];

            for ($i = 0; $i < count($passengers); $i++) {
                $passenger = new Passenger;
                $passenger->passenger_name = $passengers[$i]['name'];
                $passenger->customerTrip()->associate($customerTrip->id);
                $passenger->save();
            }
        }
        error_log('book trip request succeeded!');
        return $this->sendResponse($customerTrip, 'Succeeded!');
    }

}
