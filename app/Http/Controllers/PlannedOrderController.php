<?php

namespace App\Http\Controllers;

use App\Models\PlannedOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PlannedOrderController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
    $planned_orders = DB::table('planned_orders')
      // get only planned orders that have not been delivered
      ->where('planned_orders.delivered_at', null)
      ->where('planned_orders.deleted_at', null)
      // get only specific branch based on $request->branch_id
      ->when(request()->has('branch_id'), function ($query) {
        return $query->where('planned_orders.branch_id', request()->branch_id);
      })
      // if there is a search request
      ->when(request()->has('search'), function ($query) {
        return $query->where('products.name', 'like', '%' . request()->search . '%');
      })
      // if there is branch_id request
      ->when(request()->has('branch'), function ($query) {
        return $query->where('planned_orders.branch_id', request()->branch);
      })
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

    return Inertia::render('Admin/PlannedOrders/Index', [
      'planned_orders_admin' => $planned_orders,
      'po_branches' => DB::table('branches')->select('id', 'name')->get(),
    ]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\PlannedOrder  $plannedOrder
   * @return \Illuminate\Http\Response
   */
  public function show(PlannedOrder $plannedOrder)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\PlannedOrder  $plannedOrder
   * @return \Illuminate\Http\Response
   */
  public function edit(PlannedOrder $plannedOrder)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\PlannedOrder  $plannedOrder
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, PlannedOrder $plannedOrder)
  {
    // validate request if number
    $request->validate([
      'quantity' => 'required|numeric',
    ]);
    // update order_quantity
    $plannedOrder->update([
      'order_quantity' => $request->quantity,
    ]);

    // redirect to index
    return Redirect::back()->banner('Planned order updated successfully');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\PlannedOrder  $plannedOrder
   * @return \Illuminate\Http\Response
   */
  public function destroy(PlannedOrder $plannedOrder)
  {
    //
  }

  public function deliver(Request $request)
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

    return Redirect::back()->banner('Planned order(s) converted successfully');
  }

  public function trash($id)
  {
    // soft delete planned order
    PlannedOrder::where('id', $id)
      ->delete();

    return Redirect::back()->banner('Planned order moved to trash successfully');
  }

  public function cancelAll(Request $request)
  {

    // validate array of ids check if id exist on
    // table planned_orders
    $validatedData = $request->validate([
      'ids' => 'required|array',
      'ids.*' => 'required|exists:planned_orders,id|numeric',
    ]);
    // soft delete all  ids
    $ids = $request->ids;
    foreach ($ids as $id) {
      PlannedOrder::where('id', $id)
        ->delete();
    }


    return Redirect::back()->banner('All planned orders moved to trash successfully');
  }

  public function restore($id)
  {
    // restore soft deleted planned order
    PlannedOrder::withTrashed()
      ->where('id', $id)
      ->restore();

    return Redirect::back()->banner('Planned order restored successfully');
  }

  public function restoreAll(Request $request)
  {
    // validate array of ids check if id exist on
    // table planned_orders
    $validatedData = $request->validate([
      'ids' => 'required|array',
      'ids.*' => 'required|exists:planned_orders,id|numeric',
    ]);
    // restore soft deleted all  ids
    $ids = $request->ids;
    foreach ($ids as $id) {
      PlannedOrder::withTrashed()
        ->where('id', $id)
        ->restore();
    }

    return Redirect::back()->banner('All planned orders restored successfully');
  }

  public function allTrashed()
  {
    //
    $planned_orders = DB::table('planned_orders')
      // get only planned orders that have not been delivered
      ->where('planned_orders.delivered_at', null)
      ->where('planned_orders.deleted_at', '!=', null)
      // get only specific branch based on $request->branch_id
      ->when(request()->has('branch_id'), function ($query) {
        return $query->where('planned_orders.branch_id', request()->branch_id);
      })
      // if there is a search request
      ->when(request()->has('search'), function ($query) {
        return $query->where('products.name', 'like', '%' . request()->search . '%');
      })
      // if there is branch_id request
      ->when(request()->has('branch'), function ($query) {
        return $query->where('planned_orders.branch_id', request()->branch);
      })
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

    return Inertia::render('Admin/PlannedOrders/Trash', [
      'planned_orders_admin_trash' => $planned_orders,
      'po_branches' => DB::table('branches')->select('id', 'name')->get(),
    ]);
  }
}
