<?php

namespace App\Http\Controllers\WebUI;

use App\Http\Controllers\Controller;
use App\Models\UserReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
  public function index(){
      $reports = DB::table('user_reports')
          ->join('users','users.id','=','user_reports.reporter_id')
          ->get();

      return view('report.index')->with('reports',$reports);
  }

  public function show($id){

  }
}
