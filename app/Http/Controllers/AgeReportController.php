<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\StockAge;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgeReportController extends Controller
{
  //
  public function index(Request $request)
  {

    $data = StockAge::join('stocks', 'stocks.id', '=', 'stock_ages.stock_id')
      ->join('products', 'products.id', '=', 'stocks.product_id')
      ->join('branches', 'branches.id', '=', 'stock_ages.branch_id')
      ->select(
        'stock_ages.branch_id as branch_id',
        'stock_ages.stock_id as stock_id',
        'stock_ages.quantity as quantity',
        'stock_ages.created_at as date',
        'products.name as name',
        'products.type as type',
        'products.id as product_id',
        'branches.name as branch_name',
        'products.maximum_shelf_life as maximum_shelf_life',
      )
      // minus the stock_ages.created_at to todays date
      ->selectRaw('DATEDIFF(NOW(), stock_ages.created_at) as age')
      // if the age is greater than the maximum shelf life, then sell immediately
      ->selectRaw('IF(DATEDIFF(NOW(), stock_ages.created_at) >= products.maximum_shelf_life, "Sell immediately", "Good") as status')
      // get only the stocks are not less than 0
      ->where('stock_ages.quantity', '>', 0)
      ->when($request->branch_id, function ($query) use ($request) {
        return $query->where('stock_ages.branch_id', $request->branch_id);
      })
      // search for product name
      ->when($request->search, function ($query) use ($request) {
        return $query->where('products.name', 'like', '%' . $request->search . '%');
      })
      // sort by field and direction
      ->when($request->field && $request->direction, function ($query) use ($request) {
        return $query->orderBy($request->field, $request->direction);
      })
      // when type
      ->when($request->type, function ($query) use ($request) {
        return $query->where('products.type', $request->type);
      })
      ->paginate(20);


    return Inertia::render('Admin/Reports/Age/Index', [
      'age_report_data' => $data,
      'age_report_branches' => Branch::all(['id', 'name']),
      'age_report_filter' => $request->all([
        'search',
        'branch_id',
        'type',
        'field',
        'direction',
      ]),
    ]);
  }
}