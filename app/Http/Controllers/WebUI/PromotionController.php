<?php

namespace App\Http\Controllers\WebUI;

use App\Http\Controllers\Controller;
use App\Models\Organizer;
use App\Models\PromotionRequest;
use App\Models\PromotionStatus;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = DB::table('promotion_requests')
            ->join('users', 'promotion_requests.user_id', '=', 'users.id')
            ->join('promotion_statuses', 'promotion_statuses.id', '=', 'promotion_requests.status_id')
            ->where('promotion_statuses.name', '=', 'Pending')
            ->where('users.is_active', '=', true)
            ->select(['promotion_requests.id', 'first_name', 'last_name', 'gender', 'phone_number', 'promotion_requests.created_at'])
            ->get();

        return view('promotion.index')->with('promotions', $promotions);
    }

    public function show($id)
    {
        $promotion = PromotionRequest::with('user')->where('id', $id)->first();
        if ($promotion == null) {
            return redirect()->route('promotion.index')->with('error', 'Promotion request with id ' . $id . ' not found');
        }
        return view('promotion.show')->with('promotion', $promotion);
    }

    public function acceptPromotion($id,Request $request)
    {
        error_log('accept');
        $promotion = PromotionRequest::find($id);

        if ($promotion == null) {
            return redirect()->route('promotion.index')->with('error', 'Promotion request with id ' . $id . ' not found');
        }

        $organizer = Organizer::where('user_id','=',$promotion->user_id)->first();

        if($organizer!=null){
            return redirect()->back()->with('error','This person already organizer');
        }

        $status = PromotionStatus::where('name', 'Accept')->first();

        $organizer = Organizer::create([
            'user_id' => $promotion->user_id,
            'credential_photo' => $promotion->credential_photo
        ]);
       $userRole = new UserRole();
       $userRole->user_id= $promotion->user_id;
       $userRole->role_id = 3;
       $userRole->save();


        if ($organizer == null) {
            return redirect()->route('promotion.index')
                ->with('error', 'Unexpected error occurred ,Please try again');
        }
        if($request['admin_message'] != null){
            $promotion->admin_message = $request['admin_message'] ;
        }
        $promotion->status_id = $status->id;
        $promotion->save();

        return redirect()->route('promotion.index')
            ->with('success', 'Promotion request accepted successfully');

    }

    public function rejectPromotion($id,Request $request)
    {

        $request->validate([
            'admin_message'=>'required'
        ]);

        error_log('reject');


        $promotion = PromotionRequest::find($id);

        if ($promotion == null) {
            return redirect()->route('promotion.index')
                ->with('error', 'Promotion request with id ' . $id . ' not found');
        }

        $organizer = Organizer::where('user_id','=',$promotion->user_id)->first();

        if($organizer!=null){
            return redirect()->back()->with('error','This person already organizer');
        }

        $status = PromotionStatus::where('name', 'Reject')->first();

        $promotion->admin_message = $request['admin_message'] ;
        $promotion->status_id = $status->id;
        $promotion->save();

        return redirect()->route('promotion.index')
            ->with('success', 'Promotion request accepted successfully');

    }
}
