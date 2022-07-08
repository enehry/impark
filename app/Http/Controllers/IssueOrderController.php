<?php

namespace App\Http\Controllers;

use App\Models\PlannedOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class IssueOrderController extends Controller
{
  //
  public function index()
  {
    //
    $planned_orders = DB::table('planned_orders')
      // get only planned orders that have not been delivered
      ->where('planned_orders.delivered_at', null)
      ->where('planned_orders.issued_at', '!=', null)
      ->where('planned_orders.deleted_at', null)
      // get only specific branch based on $request->branch_id
      ->when(request()->has('branch'), function ($query) {
        return $query->where('planned_orders.branch_id', request()->branch);
      })
      // if there is a search request
      ->when(request()->has('search'), function ($query) {
        return $query->where('products.name', 'like', '%' . request()->search . '%');
      })
      // leftJoin branches to get the branch of the planned order
      ->leftJoin('branches', 'planned_orders.branch_id', '=', 'branches.id')
      ->leftJoin('stocks', 'stocks.id', '=', 'planned_orders.stock_id')
      ->leftJoin('products', 'products.id', '=', 'stocks.product_id')
      ->select(
        'planned_orders.id as id',
        'planned_orders.order_quantity as quantity',
        'planned_orders.created_at as created_at',
        'branches.name as branch_name',
        'products.name as product_name',
        'products.type as product_type',
      )
      // if there is a sort request
      ->when(request()->has('field'), function ($query) {
        return $query->orderBy(request()->field, request()->order);
      })
      ->get();

    return Inertia::render('Admin/IssueOrders/Index', [
      'planned_orders_admin' => $planned_orders,
      'po_branches' => DB::table('branches')->select('id', 'name')->get(),
      'po_filters' => request()->all(['search', 'branch', 'field', 'order']),
    ]);
  }

  public function proceed(Request $request)
  {
    // validate array of ids check if id exist on
    // table planned_orders
    $validatedData = $request->validate([
      'ids' => 'required|array',
      'ids.*' => 'required|exists:planned_orders,id|numeric',
    ]);


    // update delivered_at for each id
    $ids = $request->ids;

    foreach ($ids as $id) {
      PlannedOrder::where('id', $id)
        ->update([
          'delivered_at' => now(),
        ]);
    }

    // log action
    LogHelper::log(
      'converted',
      Auth::user()->name . ' Issue ' . count($ids) . ' Issue orders  to deliver orders',
      'planned_orders',
      $ids
    );

    return Redirect::back()->banner('Planned order(s) converted successfully');
  }
}
