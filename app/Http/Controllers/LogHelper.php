<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
  public static function log(string $action, string $description, string $table, array $table_ids)
  {
    $log = new UserLog();
    $log->user_id = Auth::user()->id;
    $log->action = $action;
    $log->description = $description;
    $log->table = $table;
    $log->table_ids = json_encode($table_ids);
    $log->save();

    return $log;
  }
}
