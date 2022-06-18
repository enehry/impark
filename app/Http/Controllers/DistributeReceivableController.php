<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\PlannedOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class DistributeReceivableController extends Controller
{
  //
  public function index(Request $request)
  {

    // products for the selection dropdown
    $products = [];
    if ($request->has('branch_id') && $request->branch_id) {
      // validate branch_id from request
      $request->validate([
        'branch_id' => 'exists:branches,id',
      ]);


      // get all stocks and product of specific branch 
      // we need the stocks id later on proceed function
      $products = DB::table('stocks')
        ->where('branch_id', $request->branch_id)
        ->leftJoin('products', 'stocks.product_id', '=', 'products.id')
        ->select(
          'stocks.id as id',
          'products.name as name',
          'products.type as type',
          'products.price as price',
        )
        ->get();
    }




    return Inertia::render('Admin/DistributeReceivables/Index', [
      'admin_products_dropdown' =>  $products,
      'admin_branch_dropdown' => Branch::all(),
      // send filter to our components 
      'receivables_filter' => $request->all(['branch_id']),
    ]);
  }

  public function proceed(Request $request)
  {
    // validate distribute_receivables array
    $request->validate([
      'distribute_receivables' => 'required|array',
      'distribute_receivables.*.id' => 'required|exists:stocks,id',
      'distribute_receivables.*.branch_id' => 'required|exists:branches,id',
      'distribute_receivables.*.quantity' => 'required|numeric',
    ]);

    // add to planned orders and add date now in delivered_at
    foreach ($request->distribute_receivables as $distribute_receivable) {
      $planned_orders = PlannedOrder::created([
        'stock_id' => $distribute_receivable['id'],
        'user_id' => auth()->id(),
        'branch_id' => $distribute_receivable['branch_id'],
        'order_quantity' => $distribute_receivable['quantity'],
        'delivered_at' => now(),
      ]);
    }
    // because this products are directly delivered to branch


    return Redirect::back()->banner('Receivables distributed successfully');
  }
}