<?php

namespace App\Http\Controllers;

use App\Models\PlannedOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReceiveProductController extends Controller
{
  //
  public function index()
  {
    $receivables =
      DB::table('planned_orders')
      ->where('delivered_at', '!=', null)
      ->where('received_at', null)
      ->where('deleted_at', '=', null)
      ->where('planned_orders.branch_id', Auth::user()->branch_id)
      ->leftJoin('stocks', 'planned_orders.stock_id', '=', 'stocks.id')
      ->leftJoin('products', 'stocks.product_id', '=', 'products.id')
      ->select(
        'planned_orders.id',
        'products.type',
        'products.name',
        'planned_orders.order_quantity as quantity',
        'planned_orders.delivered_at',
      )
      // add additional 3 days to the delivered_at date
      ->selectRaw('DATE_ADD(planned_orders.delivered_at, INTERVAL 3 DAY) as expected_delivery_date')
      ->paginate(20);

    return Inertia::render('User/ReceiveProducts/Index', [
      'receivables' => $receivables,
    ]);
  }

  public function receiveProducts(Request $request)
  {
    // get the data from the request
    // validate the array ids planned orders

    $request->validate([
      'ids' => 'required|array',
      'ids.*' => 'required|exists:planned_orders,id|numeric',
    ]);

    // update the received_at date of the planned orders 
    // with the current date and update the stock quantity based on order_quantity

    $planned_orders = PlannedOrder::whereIn('id', $request->ids)->get();

    foreach ($planned_orders as $planned_order) {
      $planned_order->received_at = now();
      $planned_order->save();

      // get the stock using planned order stock_id
      $stock = $planned_order->stock;

      // update the stock quantity
      $stock->quantity += $planned_order->order_quantity;

      // save the stock
      $stock->save();
    }

    return redirect()->back()->banner('Products received successfully');
  }
}