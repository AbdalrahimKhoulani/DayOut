<?php

namespace App\Http\Controllers\WebUI;

use App\Http\Controllers\Controller;
use App\Models\PlaceSuggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProposedPlacesController extends Controller
{
    public function index(){
        $suggestions = PlaceSuggestion::latest()->paginate(10);
        return view('place.suggestion.index')->with('suggestions',$suggestions);
    }

    public function delete($id){
        $suggestion = PlaceSuggestion::find($id);
        if($suggestion==null){
            return redirect()->back()->with('error','Not found');
        }
        $suggestion->delete();
        return redirect()->back()->with('success','Suggestion deleted successfully');
    }
}
