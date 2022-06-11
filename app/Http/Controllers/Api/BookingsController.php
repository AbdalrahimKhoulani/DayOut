<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\CustomerTrip;
use App\Models\Organizer;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $passengers = DB::table('customer_trips')
            ->join('passengers','customer_trips.id','=','passengers.customer_trip_id')
            ->where('customer_trips.trip_id',$trip_id)
            ->select('passengers.*')->get();


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

    public function confirmBooking($id)
    {
        $booking = CustomerTrip::with(['user', 'passengers'])->where('id', $id)->first();
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

    public function cancelBooking($id)
    {
        $booking = CustomerTrip::with(['user', 'passengers'])->where('id', $id)->first();
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
}
