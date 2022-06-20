<?php

namespace App\Http\Controllers;

use App\Exports\UserLogsExport;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class UserLogController extends Controller
{
  //
  public function index(Request $request)
  {
    // check role of logged user
    $role = Auth::user()->role;
    // if role is admin, show all logs
    if ($role == 1) {

      $logs = UserLog::with('user')
        // when search, show only logs that match the search in action column and description column
        ->when($request->search, function ($query) use ($request) {
          return $query->where('description', 'like', '%' . $request->search . '%');
          // ->orWhere('description', 'like', '%' . $request->search . '%');
        })
        // check if start date and end date is set and if so, show logs between start date and end date
        ->when($request->startDate && $request->endDate, function ($query) use ($request) {
          return $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        })
        // check if start date is set and if so, show logs after start date
        ->when($request->startDate, function ($query) use ($request) {
          return $query->where('created_at', '>=', $request->startDate);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(12)->withQueryString();

      return Inertia::render('Admin/Logs/Index', [
        'logs' => $logs,
        'filter_logs' => $request->all(['search', 'startDate', 'endDate']),
      ]);
    } else {
      $logs = UserLog::with('user')
        // when search, show only logs that match the search in action column and description column
        ->when($request->search, function ($query) use ($request) {
          return $query->where('description', 'like', '%' . $request->search . '%');
          // ->orWhere('description', 'like', '%' . $request->search . '%');
        })
        // check if start date and end date is set and if so, show logs between start date and end date
        ->when($request->startDate && $request->endDate, function ($query) use ($request) {
          return $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        })
        // check if start date is set and if so, show logs after start date
        ->when($request->startDate, function ($query) use ($request) {
          return $query->where('created_at', '>=', $request->startDate);
        })
        ->where('user_id', Auth::user()->id)
        ->orderBy('created_at', 'desc')
        ->paginate(12)->withQueryString();

      return Inertia::render('User/Logs/Index', [
        'logs' => $logs,
        'filter_logs' => $request->all(['search', 'startDate', 'endDate']),
      ]);
    }
  }

  public function downloadPdf(Request $request)
  {
    return Excel::download(new UserLogsExport($this->getLogs($request)), 'user-logs' . now()->toDateString() . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
  }

  public function downloadExcel(Request $request)
  {
    return Excel::download(new UserLogsExport($this->getLogs($request)), 'user-logs' . now()->toDateString() . '.xlsx');
  }


  public function getLogs(Request $request)
  {
    // check role of logged user
    $role = Auth::user()->role;
    // if role is admin, show all logs
    if ($role == 1) {

      $logs = UserLog::with('user')
        // when search, show only logs that match the search in action column and description column
        ->when($request->search, function ($query) use ($request) {
          return $query->where('description', 'like', '%' . $request->search . '%');
          // ->orWhere('description', 'like', '%' . $request->search . '%');
        })
        // check if start date and end date is set and if so, show logs between start date and end date
        ->when($request->startDate && $request->endDate, function ($query) use ($request) {
          return $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        })
        // check if start date is set and if so, show logs after start date
        ->when($request->startDate, function ($query) use ($request) {
          return $query->where('created_at', '>=', $request->startDate);
        })
        ->orderBy('created_at', 'desc')
        ->get();

      return $logs;
    } else {
      $logs = UserLog::with('user')
        // when search, show only logs that match the search in action column and description column
        ->when($request->search, function ($query) use ($request) {
          return $query->where('description', 'like', '%' . $request->search . '%');
          // ->orWhere('description', 'like', '%' . $request->search . '%');
        })
        // check if start date and end date is set and if so, show logs between start date and end date
        ->when($request->startDate && $request->endDate, function ($query) use ($request) {
          return $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        })
        // check if start date is set and if so, show logs after start date
        ->when($request->startDate, function ($query) use ($request) {
          return $query->where('created_at', '>=', $request->startDate);
        })
        ->where('user_id', Auth::user()->id)
        ->orderBy('created_at', 'desc')
        ->get();

      return $logs;
    }
  }
}
