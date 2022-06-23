<?php

namespace App\Http\Controllers\WebUI;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;


class ReportController extends Controller
{

    public function index()
    {
        $reports = $this->getUserReports();

        return view('report.index')->with('reports', $reports);
    }

    public function show($id)
    {
        $target = User::with('targets')
            ->where('id', '=', $id)
            ->first();

        if($target == null){
            return redirect()->route('report.index')->with('error', 'Not found');
        }

        return view('report.show')->with('target', $target);
    }

    public function acceptReport($id)
    {
        error_log('Accept report');

        $report = UserReport::find($id);

        if ($report == null) {
            return redirect()->route('report.index')->with('error', 'Report with id : ' . $id . ' is not found !!');
        }

        $user = User::find($report->target_id);

        $user->delete();
        $report->delete();

        $reports = $this->getUserReports();

        return redirect()->route('report.index')
            ->with('success', 'Reporting was processed successfully')
            ->with('reports', $reports);
    }

    public function rejectReport($id)
    {
        $report = UserReport::find($id);

        if ($report == null) {
            return redirect()->route('report.index')->with('error', 'Report with id : ' . $id . ' is not found !!');
        }

        $report->delete();


        return redirect()->back()->with('success', 'Reporting was processed successfully');

    }

    private function getUserReports(): \Illuminate\Support\Collection
    {
        $reports = DB::table('user_reports')
            ->join('users', 'users.id', '=', 'user_reports.target_id')
            ->select('user_reports.target_id','users.first_name','users.last_name', DB::raw('count(user_reports.id) as count'))
            ->groupBy(
                'user_reports.target_id',
                'users.id',
                'user_reports.reporter_id',
                'users.first_name',
                'users.last_name')
            ->whereNull('users.deleted_at')
            ->whereNull('user_reports.deleted_at')
            ->get();

        return $reports;
    }

}
