<?php

namespace App\Http\Controllers;

use App\Exports\ReceivedProductsExport;
use App\Exports\TransactionExport;
use App\Models\Branch;
use App\Models\PlannedOrder;
use App\Models\StockAge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ReceiveProductController extends Controller
{
  //
  public function index(Request $request)
  {

    // we need to get the receivable products that are not yet received
    // by using received_at = null && delivered_at != null 
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
      // if search request is present, filter the results
      ->when($request->has('search'), function ($query) use ($request) {
        return $query->where('products.name', 'like', '%' . $request->search . '%');
      })
      // if field and direction are present, order the results
      ->when($request->has('field') && $request->has('direction'), function ($query) use ($request) {
        return $query->orderBy($request->field, $request->direction);
      })
      // if product_type is present, filter the results
      ->when($request->has('product_type'), function ($query) use ($request) {
        return $query->where('products.type', '=', $request->product_type);
      })
      // add additional 3 days to the delivered_at date
      ->selectRaw('DATE_ADD(planned_orders.delivered_at, INTERVAL 3 DAY) as expected_delivery_date')
      ->paginate(20);

    return Inertia::render('User/ReceiveProducts/Index', [
      'receivables' => $receivables,
      // include the filter in the components
      'receivables_filter' => $request->all([
        'search',
        'field',
        'direction',
        'product_type',
      ]),
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
      $stock->is_forecasted = false;

      // save the stock
      $stock->save();

      // create new stock_age

      $stockAge = StockAge::create([
        'stock_id' => $stock->id,
        'product_id' => $stock->product_id,
        'branch_id' => $stock->branch_id,
        'quantity' => $planned_order->order_quantity,
      ]);
    }

    // log actions
    LogHelper::log(
      'received products',
      Auth::user()->name . ' received ' . count($planned_orders) . ' products',
      'planned_orders',
      $request->ids,
    );

    return redirect()->back()->banner('Products received successfully');
  }

  //
  public function ReceivedProductLogs(Request $request)
  {

    $field = $request->field ?? 'planned_orders.created_at';
    $direction =  $request->direction ?? 'desc';
    // we need to get the receivable products that are not yet received
    // by using received_at = null && delivered_at != null 
    $receivables =
      PlannedOrder::where('deleted_at', '=', null)
      // ->where('planned_orders.branch_id', Auth::user()->branch_id)
      ->leftJoin('stocks', 'planned_orders.stock_id', '=', 'stocks.id')
      ->leftJoin('products', 'stocks.product_id', '=', 'products.id')
      ->leftJoin('branches', 'stocks.branch_id', '=', 'branches.id')
      ->select(
        'planned_orders.id',
        'products.type',
        'products.name',
        'branches.name as branch_name',
        'planned_orders.order_quantity as quantity',
        'planned_orders.delivered_at as delivered_at',
        'planned_orders.received_at as received_at',
        // format planned_orders.delivered_at MM/DD/YYYY HH:MM
        DB::raw('DATE_FORMAT(planned_orders.delivered_at, "%m/%d/%Y %H:%i") as formatted_delivered_at'),
        // format planned_orders.received_at MM/DD/YYYY HH:MM
        DB::raw('DATE_FORMAT(planned_orders.received_at, "%m/%d/%Y %H:%i") as formatted_received_at')
      )
      ->orderBy($field, $direction)
      // date range filter if start_date is only set filter by start_date else filter by start_date and end_date
      ->when($request->start_date, function ($query) use ($request) {
        return $request->end_date ? $query->whereBetween('planned_orders.received_at', [$request->start_date, $request->end_date])
          : $query->where('planned_orders.received_at', '>=', $request->start_date);
      })
      // if branch_id has requested, then we need to filter by branch_id
      ->where('planned_orders.branch_id', Auth::user()->branch_id)
      // if search request is present, filter the results
      ->when($request->has('search'), function ($query) use ($request) {
        return $query->where('products.name', 'like', '%' . $request->search . '%');
      })
      // if product_type is present, filter the results
      ->when($request->has('product_type'), function ($query) use ($request) {
        return $query->where('products.type', '=', $request->product_type);
      })
      // add additional 3 days to the delivered_at date
      ->selectRaw('DATE_ADD(planned_orders.delivered_at, INTERVAL 3 DAY) as expected_delivery_date')
      ->paginate(20)->withQueryString();

    return Inertia::render('User/ReceiveProducts/ReceivedProductsLogs', [
      'transactions' => $receivables,
      // include the filter in the components
      'transaction_filter' => $request->all([
        'search',
        'field',
        'direction',
        'product_type',
        'branch_id',
        'start_date',
        'end_date'
      ]),
      "transaction_branches" => Branch::all('id', 'name'),
    ]);
  }

  private function transactionData(Request $request)
  {
    $request->validate([
      'branch_id' => 'exists:branches,id',
      'start_date' => 'date',
      'end_date' => 'date',
    ]);


    $field = $request->field ?? 'planned_orders.created_at';
    $direction =  $request->direction ?? 'desc';

    return
      DB::table('planned_orders')
      ->where('deleted_at', '=', null)
      // ->where('planned_orders.branch_id', Auth::user()->branch_id)
      ->leftJoin('stocks', 'planned_orders.stock_id', '=', 'stocks.id')
      ->leftJoin('products', 'stocks.product_id', '=', 'products.id')
      ->leftJoin('branches', 'stocks.branch_id', '=', 'branches.id')
      ->select(
        'planned_orders.id',
        'products.type',
        'products.name',
        'branches.name as branch_name',
        'planned_orders.order_quantity as quantity',
        'planned_orders.delivered_at as delivered_at',
        'planned_orders.received_at as received_at',
        // format planned_orders.delivered_at MM/DD/YYYY HH:MM
        DB::raw('DATE_FORMAT(planned_orders.delivered_at, "%m/%d/%Y %H:%i") as formatted_delivered_at'),
        // format planned_orders.received_at MM/DD/YYYY HH:MM
        DB::raw('DATE_FORMAT(planned_orders.received_at, "%m/%d/%Y %H:%i") as formatted_received_at')
      )
      ->where('planned_orders.branch_id', Auth::user()->branch_id)
      ->orderBy($field, $direction)
      // date range filter if start_date is only set filter by start_date else filter by start_date and end_date
      ->when($request->start_date, function ($query) use ($request) {
        return $request->end_date ? $query->whereBetween('planned_orders.received_at', [$request->start_date, $request->end_date])
          : $query->where('planned_orders.received_at', '>=', $request->start_date);
      })
      // if search request is present, filter the results
      ->when($request->has('search'), function ($query) use ($request) {
        return $query->where('products.name', 'like', '%' . $request->search . '%');
      })
      // if field and direction are present, order the results
      // if product_type is present, filter the results
      ->when($request->has('product_type'), function ($query) use ($request) {
        return $query->where('products.type', '=', $request->product_type);
      })
      // add additional 3 days to the delivered_at date
      ->selectRaw('DATE_ADD(planned_orders.delivered_at, INTERVAL 3 DAY) as expected_delivery_date');
  }


  public function downloadExcel(Request $request)
  {
    $data = $this->transactionData($request)->get();

    $start_date = null;
    if ($request->start_date) {
      $start_date = date('m/d/Y', strtotime($request->start_date));
    }

    $end_date = null;
    if ($request->end_date) {
      $end_date =  date('m/d/Y', strtotime($request->end_date));
    }

    return Excel::download(new ReceivedProductsExport(
      $data,
      Auth::user()->branch->name,
      $start_date,
      $end_date,
    ), 'transaction_report_' . now()->toDateString() . ' .xlsx');
  }

  public function downloadPDF(Request $request)
  {
    $data = $this->transactionData($request)->get();

    $start_date = null;
    if ($request->start_date) {
      $start_date = date('m/d/Y', strtotime($request->start_date));
    }

    $end_date = null;
    if ($request->end_date) {
      $end_date =  date('m/d/Y', strtotime($request->end_date));
    }


    return Excel::download(new ReceivedProductsExport(
      $data,
      Auth::user()->branch->name,
      $start_date,
      $end_date,
    ), 'transaction_report_' . now()->toDateString() . ' .pdf', \Maatwebsite\Excel\Excel::DOMPDF);
  }
}
