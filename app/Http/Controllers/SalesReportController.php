<?php

namespace App\Http\Controllers;

use App\Exports\SalesHistoryReportExport;
use App\Exports\SalesReportExport;
use App\Models\Branch;
use App\Models\IssueProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class SalesReportController extends Controller
{
  //
  public function index()
  {
    // get all issue products group by stocks sum the sold quantity per day

    // realtime sales report
    $issueProducts =
      IssueProduct::groupBy('products.id')
      ->selectRaw(
        "products.id as product_id,
      products.name as name, 
      products.type as type, 
      products.price as price, 
      sum(issue_products.total_price) as total_sales,
      sum(sold_quantity) as sold_quantity"
      )
      // get only issue_products today
      ->whereDate('issue_products.created_at', '=', date('Y-m-d'))
      ->leftJoin('stocks', 'issue_products.stock_id', '=', 'stocks.id')
      ->leftJoin('products', 'stocks.product_id', '=', 'products.id')
      // if filter search by products.name
      ->when(request('search'), function ($query) {
        return $query->where('products.name', 'like', '%' . request('search') . '%');
      })
      // if filter by branch_id
      ->when(request('branch_id'), function ($query) {
        return $query->where('stocks.branch_id', request('branch_id'));
      })
      // if filter by type
      ->when(request('type'), function ($query) {
        return $query->where('products.type', request('type'));
      })
      // if filed and direction exist on request
      ->when(request('field') && request('direction'), function ($query) {
        return $query->orderBy(request('field'), request('direction'));
      })
      ->paginate(20)->withQueryString();


    return Inertia::render('Admin/Reports/Sales/Index', [
      'sales' => $issueProducts,
      'sales_filter' => request()->all(['search', 'branch_id', 'field', 'direction', 'type']),
      'sales_branches' => Branch::all(['id', 'name']),
    ]);
  }


  public function todayData()
  {
    $issueProducts =
      IssueProduct::groupBy('products.id')
      ->selectRaw(
        "products.id as product_id,
    products.name as name, 
    products.type as type, 
    products.price as price, 
    sum(issue_products.total_price) as total_sales,
    sum(sold_quantity) as sold_quantity"
      )
      // get only issue_products today
      ->whereDate('issue_products.created_at', '=', date('Y-m-d'))
      ->leftJoin('stocks', 'issue_products.stock_id', '=', 'stocks.id')
      ->leftJoin('products', 'stocks.product_id', '=', 'products.id')
      // if filter search by products.name
      ->when(request('search'), function ($query) {
        return $query->where('products.name', 'like', '%' . request('search') . '%');
      })
      // if filter by branch_id
      ->when(request('branch_id'), function ($query) {
        return $query->where('stocks.branch_id', request('branch_id'));
      })
      // if filter by type
      ->when(request('type'), function ($query) {
        return $query->where('products.type', request('type'));
      })
      // if filed and direction exist on request
      ->when(request('field') && request('direction'), function ($query) {
        return $query->orderBy(request('field'), request('direction'));
      })->get();



    return $issueProducts;
  }

  public function downloadExcel()
  {
    $issueProducts = $this->todayData();
    $branch = null;
    if (request('branch_id')) {
      $branch = Branch::find(request('branch_id'))->name;
    }

    return Excel::download(new SalesReportExport($issueProducts, $branch), 'sales_report' . now()->toDateString() . ' .xlsx');
  }

  public function downloadPDF()
  {
    $issueProducts = $this->todayData();
    $branch = null;
    if (request('branch_id')) {
      $branch = Branch::find(request('branch_id'))->name;
    }

    return Excel::download(new SalesReportExport($issueProducts, $branch), 'sales_report' . now()->toDateString() . ' .pdf', \Maatwebsite\Excel\Excel::DOMPDF);
  }

  public function historicalData(Request $request)
  {
    $request->validate([
      'start_date' => 'date',
      'end_date' => 'date|after:start_date',
      'branch_id' => 'exists:branches,id',
      'type' => 'in:chicken,beef,pork',
      'direction' => 'in:asc,desc',
    ]);

    $start_date = now()->toDateString();

    if ($request->start_date) {
      $start_date = $request->start_date;
    }

    $issueProducts =
      IssueProduct::groupBy(DB::raw('DATE_FORMAT(issue_products.created_at, "%Y-%m-%d")'))
      ->groupBy('products.id')
      ->selectRaw(
        "products.id as product_id,
      products.name as name, 
      products.type as type, 
      products.price as price, 
      sum(issue_products.total_price) as total_sales,
      sum(sold_quantity) as sold_quantity,
      issue_products.created_at as date"
      )
      // get only issue_products today
      // display today issue products when there is no end date
      ->when($start_date, function ($query) use ($request, $start_date) {
        return $request->end_date ? $query->whereBetween('issue_products.created_at', [$start_date, $request->end_date]) : $query->whereDate('issue_products.created_at', '>=', $start_date);
      })
      // get only issue_products today
      // ->whereBetween('issue_products.created_at', [$request->start_date, $request->end_date])
      ->leftJoin('stocks', 'issue_products.stock_id', '=', 'stocks.id')
      ->leftJoin('products', 'stocks.product_id', '=', 'products.id')
      // if filter search by products.name
      ->when(request('search'), function ($query) {
        return $query->where('products.name', 'like', '%' . request('search') . '%');
      })
      // if filter by branch_id
      ->when(request('branch_id'), function ($query) {
        return $query->where('stocks.branch_id', request('branch_id'));
      })
      // if filter by type
      ->when(request('type'), function ($query) {
        return $query->where('products.type', request('type'));
      })
      // if filed and direction exist on request
      ->when(request('field') && request('direction'), function ($query) {
        return $query->orderBy(request('field'), request('direction'));
      })
      ->paginate(20)->withQueryString();

    return Inertia::render('Admin/Reports/Sales/HistoricalData', [
      'sales_history' => $issueProducts,
      'sales_history_filter' => request()->all([
        'search',
        'branch_id',
        'field',
        'direction',
        'type',
        'start_date',
        'end_date'
      ]),
      'sales_history_branches' => Branch::all(['id', 'name']),
    ]);
  }


  public function dataOfHistorical(Request $request)
  {
    $request->validate([
      'start_date' => 'date',
      'end_date' => 'date|after:start_date',
      'branch_id' => 'exists:branches,id',
      'type' => 'in:chicken,beef,pork',
      'direction' => 'in:asc,desc',
    ]);

    $start_date = now()->toDateString();

    if ($request->start_date) {
      $start_date = $request->start_date;
    }

    $issueProducts =
      IssueProduct::groupBy(DB::raw('DATE_FORMAT(issue_products.created_at, "%Y-%m-%d")'))
      ->groupBy('products.id')
      ->selectRaw(
        "products.id as product_id,
      products.name as name, 
      products.type as type, 
      products.price as price, 
      sum(issue_products.total_price) as total_sales,
      sum(sold_quantity) as sold_quantity,
      issue_products.created_at as date"
      )
      // group by day month and year
      // get only issue_products today
      // display today issue products when there is no end date
      ->when($start_date, function ($query) use ($request, $start_date) {
        return $request->end_date ? $query->whereBetween('issue_products.created_at', [$start_date, $request->end_date]) : $query->whereDate('issue_products.created_at', '>=', $start_date);
      })
      // get only issue_products today
      // ->whereBetween('issue_products.created_at', [$request->start_date, $request->end_date])
      ->leftJoin('stocks', 'issue_products.stock_id', '=', 'stocks.id')
      ->leftJoin('products', 'stocks.product_id', '=', 'products.id')
      // if filter search by products.name
      ->when(request('search'), function ($query) {
        return $query->where('products.name', 'like', '%' . request('search') . '%');
      })
      // if filter by branch_id
      ->when(request('branch_id'), function ($query) {
        return $query->where('stocks.branch_id', request('branch_id'));
      })
      // if filter by type
      ->when(request('type'), function ($query) {
        return $query->where('products.type', request('type'));
      })
      // if filed and direction exist on request
      ->when(request('field') && request('direction'), function ($query) {
        return $query->orderBy(request('field'), request('direction'));
      })
      ->get();

    return $issueProducts;
  }

  public function downloadHistoricalDataExcel(Request $request)
  {
    $issueProducts = $this->dataOfHistorical($request);
    $branch = null;
    if (request('branch_id')) {
      $branch = Branch::find(request('branch_id'))->name;
    }

    $start_date = date('m/d/Y', strtotime(now()->toDateString()));

    if ($request->start_date) {
      $start_date = $request->start_date;
    }

    $end_date = null;
    if ($request->end_date) {
      $end_date =  date('m/d/Y', strtotime($request->end_date));
    }

    return Excel::download(new SalesHistoryReportExport($issueProducts, $branch, $start_date, $end_date), 'sales_report' . now()->toDateString() . ' .xlsx');
  }

  public function downloadHistoricalDataPDF(Request $request)
  {
    $issueProducts = $this->dataOfHistorical($request);
    $branch = null;
    if (request('branch_id')) {
      $branch = Branch::find(request('branch_id'))->name;
    }

    $start_date = date('m/d/Y', strtotime(now()->toDateString()));

    if ($request->start_date) {
      $start_date = $request->start_date;
    }

    $end_date = null;
    if ($request->end_date) {
      $end_date =  date('m/d/Y', strtotime($request->end_date));
    }

    return Excel::download(new SalesHistoryReportExport($issueProducts, $branch, $start_date, $end_date), 'sales_report' . now()->toDateString() . ' .pdf', \Maatwebsite\Excel\Excel::DOMPDF);
  }
}
