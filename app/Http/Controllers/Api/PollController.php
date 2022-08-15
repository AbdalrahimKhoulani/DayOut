<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PollRequest;
use App\Models\CustomerPollChoice;
use App\Models\Organizer;
use App\Models\Poll;
use App\Models\PollChoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PollController extends BaseController
{

    public function index(){
        Log::channel('requestlog')->info('Get all polls request');
        $polls = Poll::where('expire_date','>',now())->paginate(10);
        $polls->load('organizer');
        $polls->load('pollChoices.users');
        return $this->sendResponse($polls,'Succeeded!');
    }

    public function organizerPolls(){

        Log::channel('requestlog')->info('Get organizer polls request!',['user_id',Auth::id()]);
        if($this->getOrganizerId() == null){
            Log::channel('requestlog')->error('User is not organizer');
            return $this->sendError('User is not organizer!!');
        }
        $polls = Poll::where('organizer_id',$this->getOrganizerId())->with('pollChoices.users')->paginate(10);

        if (sizeof($polls) <=0)
            Log::channel('requestlog')->warning('Organizer has no polls!');

        Log::channel('requestlog')->info('Get organizer polls request succeeded!');
        return $this->sendResponse($polls,'Succeeded!');

    }

    public function create(Request $request){
        Log::channel('requestlog')->info('Create poll request!',[$request->all()]);
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'expire_date' => 'required',
            'choices' => 'required'
        ]);

        if ($validator->fails()) {
            $this->sendErrorToLog('Validator failed! check the data',[$validator->errors()]);
            return $this->sendError('Validator failed! check the data', $validator->errors());
        }
        $poll = new Poll();
        if($this->getOrganizerId() == null){
            Log::channel('requestlog')->error('User is not organizer',[$request->all()]);
            return $this->sendError('User is not organizer');
        }
        $request['organizer_id'] = $this->getOrganizerId();
        $poll = $poll->create($request->all());

        for($i = 0 ; $i<sizeof($request['choices']);$i++){
            $this->createPollChoice($poll->id,$request->choices[$i]['value']);
        }

        Log::channel('requestlog')->info('Create poll request succeeded!',[$poll]);
        return $this->sendResponse($poll,'Succeeded!');
    }

    public function vote($pollId,$pollChoiceId){

        $this->sendInfoToLog('Vote request!',['user_id'=>Auth::id()]);

        if($this->checkUserMadePoll($pollId) != null) {
            $this->sendErrorToLog('User made this poll!', []);
            return $this->sendError('User made this poll');
        }
        $res = CustomerPollChoice::where('poll_id',$pollId)->where('user_id',Auth::id())->where('poll_choice_id',$pollChoiceId)->first();
        if($res != null){
            $res->delete();
            $this->sendInfoToLog('Vote deleted!',[]);
            return $this->sendResponse([],'Vote deleted!');
        }
        $customerPollChoice = new CustomerPollChoice();
        $customerPollChoice->create(['poll_id' => $pollId , 'user_id' => Auth::id(), 'poll_choice_id' => $pollChoiceId]);

        $this->sendInfoToLog('Vote request succeeded!',[]);
        return $this->sendResponse($customerPollChoice,'Succeeded!');

    }

    public function delete($pollId){
        $this->sendInfoToLog('Delete poll request',[$pollId]);

        $organizer = Organizer::where('user_id',Auth::id())->first();

        if($organizer == null){
            $this->sendErrorToLog('User is not organizer',[Auth::id()]);
            return $this->sendError('User is not organizer',[],403);
        }

        $poll = Poll::where('organizer_id',$organizer->id)->where('id',$pollId)->first();

        if($poll == null){
            $this->sendErrorToLog('Poll does not exist',[$pollId]);
            return $this->sendError('Poll does not exist',[]);
        }

        $poll->delete();

        return $this->sendResponse([],'Poll deleted successfully');
    }

    private function getOrganizerId(){
        $organizer = Organizer::where('user_id',Auth::id())->first();
        if($organizer == null)
            return null;
        return $organizer->id;
    }

    private function createPollChoice($pollId, $value){

        $pollChoice = new PollChoice();
        $pollChoice->create(['poll_id'=>$pollId,'value'=>$value]);
    }

    private function sendInfoToLog($message,$context){
        Log::channel('requestlog')->info($message,$context);
    }

    private function sendErrorToLog($message,$context){
        Log::channel('requestlog')->error($message,$context);

    }

    private function checkUserMadePoll($poll_id){
        $organizerId = $this->getOrganizerId();
        if($organizerId == null){
            return null;
        }
        $poll = Poll::where('id',$poll_id)->where('organizer_id',$organizerId)->first();

        if($poll == null){
            return null;
        }else
            return $poll;
    }

}
