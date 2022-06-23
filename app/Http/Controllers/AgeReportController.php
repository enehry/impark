<?php

namespace App\Http\Controllers;

use App\Exports\AgeReportExport;
use App\Models\Branch;
use App\Models\StockAge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class AgeReportController extends Controller
{
  //
  public function index(Request $request)
  {
    $request->validate([
      'direction' => 'in:asc,desc',
      'field' => 'in:name,price,type,quantity',
      'branch_id' =>  'numeric|exists:branches,id',
      'type' => 'in:pork,beef,chicken',
    ]);

    $field = $request->field ?? 'date';
    $direction = $request->direction ?? 'asc';


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
      ->selectRaw('DATE_FORMAT(stock_ages.created_at, "%m/%d/%Y %H:%i") as formatted_date')
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
      ->orderBy($field, $direction)
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

  public function ageReportData(Request $request)
  {
    return StockAge::join('stocks', 'stocks.id', '=', 'stock_ages.stock_id')
      ->join('products', 'products.id', '=', 'stocks.product_id')
      ->join('branches', 'branches.id', '=', 'stock_ages.branch_id')
      ->select(
        'stock_ages.id as id',
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
      // format date mm/dd/yyyy hh::mm
      ->selectRaw('DATE_FORMAT(stock_ages.created_at, "%m/%d/%Y %H:%i") as formatted_date')
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
      })->get();
  }

  public function downloadExcel(Request $request)
  {
    $branch = null;
    $query = $this->ageReportData($request);
    if ($request->branch_id) {
      $branch = Branch::find($request->branch_id)->name;
    }

    return Excel::download(new AgeReportExport($query, $branch), 'age_report-' . now()->toDateString() . '.xlsx');
  }

  public function downloadPDF(Request $request)
  {
    $query = $this->ageReportData($request);
    $branch = null;
    if ($request->branch_id) {
      $branch = Branch::find($request->branch_id)->name;
    }

    return Excel::download(new AgeReportExport($query, $branch), 'age_report-' . now()->toDateString() . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
  }


  public function indexBranch(Request $request)
  {

    $data = $this->ageReportBranchData($request)->paginate(20)->withQueryString();
    return Inertia::render('User/Reports/Age/Index', [
      'age_report_data' => $data,
      'age_report_filter' => $request->all([
        'search',
        'type',
        'field',
        'direction',
      ]),
    ]);
  }

  public function ageReportBranchData(Request $request)
  {
    $request->validate([
      'direction' => 'in:asc,desc',
      'field' => 'in:name,price,type,quantity',
      'type' => 'in:pork,beef,chicken',
    ]);

    $field = $request->field ?? 'date';
    $direction = $request->direction ?? 'asc';


    return StockAge::join('stocks', 'stocks.id', '=', 'stock_ages.stock_id')
      ->join('products', 'products.id', '=', 'stocks.product_id')
      ->join('branches', 'branches.id', '=', 'stock_ages.branch_id')
      ->select(
        'products.id as id',
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
      ->selectRaw('DATE_FORMAT(stock_ages.created_at, "%m/%d/%Y %H:%i") as formatted_date')
      // minus the stock_ages.created_at to todays date
      ->selectRaw('DATEDIFF(NOW(), stock_ages.created_at) as age')
      // if the age is greater than the maximum shelf life, then sell immediately
      ->selectRaw('IF(DATEDIFF(NOW(), stock_ages.created_at) >= products.maximum_shelf_life, "Sell immediately", "Good") as status')
      // get only the stocks are not less than 0
      ->where('stock_ages.quantity', '>', 0)
      ->where('stock_ages.branch_id', Auth::user()->branch_id)
      // search for product name
      ->when($request->search, function ($query) use ($request) {
        return $query->where('products.name', 'like', '%' . $request->search . '%');
      })
      // sort by field and direction
      ->orderBy($field, $direction)
      // when type
      ->when($request->type, function ($query) use ($request) {
        return $query->where('products.type', $request->type);
      });
  }

  public function downloadAgeReportBranchExcel(Request $request)
  {
    $branch = Branch::find(Auth::user()->branch_id)->name;

    $query = $this->ageReportBranchData($request)->get();

    return Excel::download(new AgeReportExport($query, $branch), 'age_report-' . now()->toDateString() . '.xlsx');
  }

  public function downloadAgeReportBranchPDF(Request $request)
  {
    $query = $this->ageReportBranchData($request)->get();
    $branch = Branch::find(Auth::user()->branch_id)->name;

    return Excel::download(new AgeReportExport($query, $branch), 'age_report-' . now()->toDateString() . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
  }
}
