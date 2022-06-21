<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Models\Branch;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class InventoryReportController extends Controller
{
  //

  public function index(Request $request)
  {
    // sum all stock quantity by product_id
    $request->validate([
      'direction' => 'in:asc,desc',
      'field' => 'in:name,price,type,quantity',
      'branch_id' =>  'numeric|exists:branches,id',
      'type' => 'in:pork,beef,chicken',
    ]);

    $query =
      DB::table('products')
      ->select('products.*', DB::raw('sum(stocks.quantity) as quantity'))
      ->join('stocks', 'products.id', '=', 'stocks.product_id')
      ->groupBy('products.id')
      ->when($request->has('search'), function ($query) use ($request) {
        $query->where('name', 'like', '%' . $request->search . '%');
      })
      ->when($request->has('branch'), function ($query) use ($request) {
        $query->where('stocks.branch_id', $request->branch);
      })
      ->when($request->has('type'), function ($query) use ($request) {
        $query->where('products.type', $request->type);
      })
      ->when($request->has('field'), function ($query) use ($request) {
        $query->orderBy($request->field, $request->direction);
      })->paginate(20)->withQueryString();


    return Inertia::render('Admin/Reports/Inventory/Index', [
      'stocks' => $query,
      'inventory_branches' => Branch::All(['id', 'name']),
      'inventory_filter' => request()->all(['branch_id', 'search', 'field', 'direction', 'type']),
    ]);
  }

  public function chart()
  {
    $stocks =
      Stock::select(
        'products.name as name',
        DB::raw('SUM(quantity) as quantity')
      )
      ->when(request('branch_id'), function ($query) {
        return $query->where('branch_id', request('branch_id'));
      })
      ->groupBy('product_id')
      ->join('products', 'products.id', '=', 'stocks.product_id')
      ->get();

    return Inertia::render('Admin/Reports/Inventory/Chart', [
      'stocks_quantity' => $stocks,
      'inventory_branches' => Branch::All(['id', 'name']),
      'inventory_filter' => request()->all(['branch_id']),
    ]);
  }

  public function downloadExcel(Request $request)
  {
    $branch = null;
    if ($request->has('branch')) {
      $branch = Branch::find($request->branch)->name;
    }

    // download in excel format
    return Excel::download(new ProductExport($this->data($request), $branch), 'stocks-' . now()->toDateString() . '.xlsx');
  }

  public function downloadPdf(Request $request)
  {
    $branch = null;
    if ($request->has('branch')) {
      $branch = Branch::find($request->branch)->name;
    }

    return Excel::download(new ProductExport($this->data($request), $branch), 'stocks-' . now()->toDateString() . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
  }

  public function data(Request $request)
  {
    $request->validate([
      'direction' => 'in:asc,desc',
      'field' => 'in:name,price,type,quantity',
      'branch_id' =>  'numeric|exists:branches,id',
      'type' => 'in:pork,beef,chicken',
    ]);

    $query =
      DB::table('products')
      ->select('products.*', DB::raw('sum(stocks.quantity) as quantity'))
      ->join('stocks', 'products.id', '=', 'stocks.product_id')
      ->groupBy('products.id')
      ->when($request->has('search'), function ($query) use ($request) {
        $query->where('name', 'like', '%' . $request->search . '%');
      })
      ->when($request->has('branch'), function ($query) use ($request) {
        $query->where('stocks.branch_id', $request->branch);
      })
      ->when($request->has('type'), function ($query) use ($request) {
        $query->where('products.type', $request->type);
      })
      ->when($request->has('field'), function ($query) use ($request) {
        $query->orderBy($request->field, $request->direction);
      })->get();



    return $query;
  }
}
