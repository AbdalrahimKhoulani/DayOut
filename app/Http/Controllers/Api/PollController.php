<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PollRequest;
use App\Models\Organizer;
use App\Models\Poll;
use App\Models\PollChoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PollController extends BaseController
{

    public function index(){
        Log::channel('requestlog')->info('Get all polls request');
        $polls = Poll::all();
        $polls->load('organizer');
        $polls->load('pollChoices');
        return $this->sendResponse($polls,'Succeeded!');
    }

    public function create(PollRequest $request){
        Log::channel('requestlog')->info('Create poll request!',[$request->all()]);

        $poll = new Poll();
        if($this->getUserId() == null){
            Log::channel('requestlog')->error('User is not organizer',[$request->all()]);
            return $this->sendError('User is not organizer');
        }
        $request['organizer_id'] = $this->getUserId();
        $poll = $poll->create($request->all());

        for($i = 0 ; $i<sizeof($request['choices']);$i++){
            $this->createPollChoice($poll->id,$request->choices[$i]['value']);
        }

        Log::channel('requestlog')->info('Create poll request succeeded!',[$poll]);
        return $this->sendResponse($poll,'Succeeded!');
    }

    private function getUserId(){
        $organizer = Organizer::where('user_id',Auth::id())->first();
        if($organizer == null)
            return null;
        return $organizer->id;
    }

    private function createPollChoice($poll_id,$value){

        $pollChoice = new PollChoice();
        $pollChoice->create(['poll_id'=>$poll_id,'value'=>$value]);
    }
}
