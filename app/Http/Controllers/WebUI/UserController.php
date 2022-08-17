<?php

namespace App\Http\Controllers\WebUI;

use App\Http\Controllers\Controller;
use App\Models\Follower;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Models\Organizer;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $users = User::latest()
            ->paginate(10);

        return view('user.index')->with('users', $users);
    }

    public function blockedUsers(){
        $users = User::onlyTrashed()
            ->latest()
            ->paginate(10);
        return view('user.block.index')->with('users', $users);
    }
public function unblockUser($id){
    $user = User::withTrashed()->where('id',$id)->first();
    if($user == null){
        return redirect()->route('user.blocked.index')
            ->with('error', 'User with id ' . $id . ' not found');
    }

    $user->deleted_at = null;
    $user->save();

    return redirect()->back()
        ->with('error', 'User with id ' . $id . ' not found');

}

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        $user = User::withTrashed()->where('id',$id)->first();
        if($user == null){
            return redirect()->route('user.index')
                ->with('error', 'User with id ' . $id . ' not found');
        }
        $user['follows'] = $this->calculateFollowsCount($user->id);
        if($user->organizer != null){
            $user['followersCount'] = $this->calculateFollowersCount($user->organizer->id);
            $user['rate'] = $this->calculateOrganizerRating($user->id);
        }

       return view('user.show')->with('user',$user);

        //  return  $user;
    }
    private function calculateOrganizerRating($id): float|int
    {

        $organizer = Organizer::where('user_id', $id)->first();
        if ($organizer == null) {

            return 0;
        }

        $trips = Trip::where('organizer_id', $organizer->id)->with('customerTrips');
        $rating = 0;
        $trips->each(function ($trip) use (&$rating) {

            $rating = $rating + $this->calculateAvgTripRating($trip);
        });
        if ($trips->count() == 0) {
            return 0;
        }
        $rating = $rating / $trips->count();

        return $rating;
    }
    private function calculateAvgTripRating($trip): float|int
    {
        $avg = 0;
        $customerTrips = $trip['customerTrips'];


        $customerTrips->each(function ($customerTrip) use (&$avg) {
            $avg = $avg + $customerTrip->rate;
        });
        if ($customerTrips->count() == 0) {
            return 0;
        }
        return $avg / $customerTrips->count();
    }

    private function calculateFollowersCount($organizer_id): int
    {
        $followers = Follower::where('organizer_id', $organizer_id)->get();
        if($followers==null){
            return 0;
        }
       $followersCount = count($followers);

       return $followersCount;
    }

    private function calculateFollowsCount($user_id): int
    {
        $followers = Follower::where('user_id', $user_id)->get();
        if($followers==null){
            return 0;
        }
        $followersCount = count($followers);

        return $followersCount;
    }
}
