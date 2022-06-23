<?php

namespace App\Http\Controllers;

use App\Exports\SalesHistoryReportExport;
use App\Exports\SalesReportExport;
use App\Models\Branch;
use App\Models\IssueProduct;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
      'group_by' => 'in:weekly,monthly,yearly',
    ]);

    // default daily
    $group_by = 'DATE_FORMAT(issue_products.created_at, "%Y-%m-%d")';
    $formatted_date = 'DATE_FORMAT(issue_products.created_at, "%Y-%m-%d")';


    if ($request->has('group_by')) {
      if ($request->group_by == 'weekly') {
        $group_by = 'WEEK(issue_products.created_at)';
        $formatted_date =  'CONCAT(WEEK(issue_products.created_at), "-", YEAR(issue_products.created_at))';
      } else if ($request->group_by == 'monthly') {
        $group_by = 'MONTH(issue_products.created_at)';
        $formatted_date =  'DATE_FORMAT(issue_products.created_at, "%M-%Y")';
      } else if ($request->group_by == 'yearly') {
        $group_by = 'YEAR(issue_products.created_at)';
        $formatted_date = 'DATE_FORMAT(issue_products.created_at, "%Y")';
      }
    }

    $direction = 'DESC';
    if ($request->has('direction')) {
      $direction = $request->direction;
    }
    $field = 'DATE';
    if ($request->has('field')) {
      $field = $request->field;
    }

    $issueProducts =
      // group by requested group_by
      IssueProduct::groupBy(DB::raw($group_by))
      ->groupBy('products.id')
      ->selectRaw(
        "products.id as product_id,
      products.name as name, 
      products.type as type, 
      products.price as price, 
      sum(issue_products.total_price) as total_sales,
      sum(sold_quantity) as sold_quantity,
      issue_products.created_at as date
      "
      )
      ->selectRaw(
        $formatted_date . ' as formatted_date'
      )
      // display today issue products when there is no end date
      ->when($request->has('start_date'), function ($query) use ($request) {
        return $request->end_date ?
          $query->whereBetween('issue_products.created_at', [$request->start_date, $request->end_date]) :
          $query->whereDate('issue_products.created_at', '>=', $request->start_date);
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
      ->orderBy($field, $direction)
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
        'end_date',
        'group_by',
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
      'group_by' => 'in:weekly,monthly,yearly',
    ]);

    // default daily
    $group_by = 'DATE_FORMAT(issue_products.created_at, "%Y-%m-%d")';
    $formatted_date = 'DATE_FORMAT(issue_products.created_at, "%Y-%m-%d")';


    if ($request->has('group_by')) {
      if ($request->group_by == 'weekly') {
        $group_by = 'WEEK(issue_products.created_at)';
        $formatted_date =  'CONCAT(WEEK(issue_products.created_at), "-", YEAR(issue_products.created_at))';
      } else if ($request->group_by == 'monthly') {
        $group_by = 'MONTH(issue_products.created_at)';
        $formatted_date =  'DATE_FORMAT(issue_products.created_at, "%M-%Y")';
      } else if ($request->group_by == 'yearly') {
        $group_by = 'YEAR(issue_products.created_at)';
        $formatted_date = 'DATE_FORMAT(issue_products.created_at, "%Y")';
      }
    }

    $direction = 'DESC';
    if ($request->has('direction')) {
      $direction = $request->direction;
    }
    $field = 'DATE';
    if ($request->has('field')) {
      $field = $request->field;
    }

    $issueProducts =
      // group by requested group_by
      IssueProduct::groupBy(DB::raw($group_by))
      ->groupBy('products.id')
      ->selectRaw(
        "products.id as product_id,
      products.name as name, 
      products.type as type, 
      products.price as price, 
      sum(issue_products.total_price) as total_sales,
      sum(sold_quantity) as sold_quantity,
      issue_products.created_at as date"
      )->selectRaw($formatted_date . ' as formatted_date')
      // display today issue products when there is no end date
      ->when($request->has('start_date'), function ($query) use ($request) {
        return $request->end_date ?
          $query->whereBetween('issue_products.created_at', [$request->start_date, $request->end_date]) :
          $query->whereDate('issue_products.created_at', '>=', $request->start_date);
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
      ->orderBy($field, $direction)->get();

    return $issueProducts;
  }

  public function downloadHistoricalDataExcel(Request $request)
  {
    $issueProducts = $this->dataOfHistorical($request);
    $branch = null;
    if (request('branch_id')) {
      $branch = Branch::find(request('branch_id'))->name;
    }

    $start_date = null;

    if ($request->start_date) {
      $start_date = date('m/d/Y', strtotime($request->start_date));
    }

    $end_date = null;
    if ($request->end_date) {
      $end_date =  date('m/d/Y', strtotime($request->end_date));
    }

    $group_by = 'DAILY';
    if ($request->group_by) {
      $group_by = strtoupper($request->group_by);
    }

    return Excel::download(new SalesHistoryReportExport($issueProducts, $branch, $start_date, $end_date, $group_by), 'sales_report' . now()->toDateString() . ' .xlsx');
  }

  public function downloadHistoricalDataPDF(Request $request)
  {
    $issueProducts = $this->dataOfHistorical($request);
    $branch = null;
    if (request('branch_id')) {
      $branch = Branch::find(request('branch_id'))->name;
    }

    $start_date = null;

    if ($request->start_date) {
      $start_date = date('m/d/Y', strtotime($request->start_date));
    }

    $end_date = null;
    if ($request->end_date) {
      $end_date =  date('m/d/Y', strtotime($request->end_date));
    }
    $group_by = 'DAILY';
    if ($request->group_by) {
      $group_by = strtoupper($request->group_by);
    }

    return Excel::download(new SalesHistoryReportExport($issueProducts, $branch, $start_date, $end_date, $group_by), 'sales_report' . now()->toDateString() . ' .pdf', \Maatwebsite\Excel\Excel::DOMPDF);
  }

  public function chart(Request $request)
  {
    $request->validate([
      'start_date' => 'date',
      'end_date' => 'date|after:start_date',
      'branch_id' => 'exists:branches,id',
      'type' => 'in:chicken,beef,pork',
      'group_by' => 'in:weekly,monthly,yearly',
    ]);

    // default daily
    $group_by = 'DATE_FORMAT(issue_products.created_at, "%Y-%m-%d")';
    $formatted_date = 'DATE_FORMAT(issue_products.created_at, "%Y-%m-%d")';


    if ($request->has('group_by')) {
      if ($request->group_by == 'weekly') {
        $group_by = 'WEEK(issue_products.created_at)';
        $formatted_date =  'WEEK(issue_products.created_at)';
      } else if ($request->group_by == 'monthly') {
        $group_by = 'MONTH(issue_products.created_at)';
        $formatted_date =  'DATE_FORMAT(issue_products.created_at, "%M")';
      } else if ($request->group_by == 'yearly') {
        $group_by = 'YEAR(issue_products.created_at)';
        $formatted_date = 'DATE_FORMAT(issue_products.created_at, "%Y")';
      }
    }

    $issueProducts =
      // group by requested group_by
      IssueProduct::groupBy(DB::raw($group_by))
      ->selectRaw(
        "products.id as product_id,
      products.name as name, 
      products.type as type, 
      products.price as price, 
      sum(issue_products.total_price) as total_sales,
      sum(sold_quantity) as sold_quantity,
      issue_products.created_at as date"
      )->selectRaw($formatted_date . ' as formatted_date')
      // display today issue products when there is no end date
      ->when($request->has('start_date'), function ($query) use ($request) {
        return $request->end_date ?
          $query->whereBetween('issue_products.created_at', [$request->start_date, $request->end_date]) :
          $query->whereDate('issue_products.created_at', '>=', $request->start_date);
      })
      // get only issue_products today
      // ->whereBetween('issue_products.created_at', [$request->start_date, $request->end_date])
      ->leftJoin('stocks', 'issue_products.stock_id', '=', 'stocks.id')
      ->leftJoin('products', 'stocks.product_id', '=', 'products.id')
      // if filter by branch_id
      ->when(request('branch_id'), function ($query) {
        return $query->where('stocks.branch_id', request('branch_id'));
      })
      // if filter by type
      ->when(request('type'), function ($query) {
        return $query->where('products.type', request('type'));
      })
      // sort by date
      ->orderBy('date', 'asc')
      ->get();

    return Inertia::render('Admin/Reports/Sales/HistoricalDataChart', [
      'sales_chart_data' => $issueProducts,
      'sales_chart_branches' => Branch::all(['id', 'name']),
      'sales_chart_filter' => $request->all([
        'start_date',
        'end_date',
        'branch_id',
        'type',
        'group_by'
      ]),
    ]);
  }

  public function indexBranch(Request $request)
  {
    $data = $this->salesBranchData($request)
      ->paginate(20)->withQueryString();


    return Inertia::render('User/Reports/Sales/Index', [
      'sales' => $data,
      'sales_filter' => request()->all(['search', 'field', 'direction', 'type']),
    ]);
  }


  private function salesBranchData(Request $request)
  {
    $request->validate([
      'direction' => 'in:asc,desc',
      'type' => 'in:chicken,beef,pork',
    ]);

    return
      IssueProduct::groupBy('products.id')
      ->selectRaw(
        "products.id as product_id,
    products.name as name, 
    products.type as type, 
    products.price as price, 
    sum(issue_products.total_price) as total_sales,
    sum(sold_quantity) as sold_quantity"
      )
      ->where('stocks.branch_id', Auth::user()->branch_id)
      // get only issue_products today
      ->whereDate('issue_products.created_at', '=', date('Y-m-d'))
      ->leftJoin('stocks', 'issue_products.stock_id', '=', 'stocks.id')
      ->leftJoin('products', 'stocks.product_id', '=', 'products.id')
      // if filter search by products.name
      ->when(request('search'), function ($query) {
        return $query->where('products.name', 'like', '%' . request('search') . '%');
      })


      // if filter by type
      ->when(request('type'), function ($query) {
        return $query->where('products.type', request('type'));
      })
      // if filed and direction exist on request
      ->when(request('field') && request('direction'), function ($query) {
        return $query->orderBy(request('field'), request('direction'));
      });
  }

  public function downloadBranchExcel(Request $request)
  {
    $data = $this->salesBranchData($request)->get();

    return Excel::download(new SalesReportExport($data, Auth::user()->branch->name), 'sales_branch_' . now()->toDateString() . '.xlsx');
  }

  public function downloadBranchPDF(Request $request)
  {
    $data = $this->salesBranchData($request)->get();

    return Excel::download(new SalesReportExport($data, Auth::user()->branch->name), 'sales_branch_' . now()->toDateString() . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
  }

  public function historicalDataBranch(Request $request)
  {
    $request->validate([
      'start_date' => 'date',
      'end_date' => 'date|after:start_date',
      'type' => 'in:chicken,beef,pork',
      'direction' => 'in:asc,desc',
      'group_by' => 'in:weekly,monthly,yearly',
    ]);

    // default daily
    $group_by = 'DATE_FORMAT(issue_products.created_at, "%Y-%m-%d")';
    $formatted_date = 'DATE_FORMAT(issue_products.created_at, "%Y-%m-%d")';


    if ($request->has('group_by')) {
      if ($request->group_by == 'weekly') {
        $group_by = 'WEEK(issue_products.created_at)';
        $formatted_date =  'CONCAT(WEEK(issue_products.created_at), "-", YEAR(issue_products.created_at))';
      } else if ($request->group_by == 'monthly') {
        $group_by = 'MONTH(issue_products.created_at)';
        $formatted_date =  'DATE_FORMAT(issue_products.created_at, "%M-%Y")';
      } else if ($request->group_by == 'yearly') {
        $group_by = 'YEAR(issue_products.created_at)';
        $formatted_date = 'DATE_FORMAT(issue_products.created_at, "%Y")';
      }
    }

    $direction = 'DESC';
    if ($request->has('direction')) {
      $direction = $request->direction;
    }
    $field = 'DATE';
    if ($request->has('field')) {
      $field = $request->field;
    }

    return
      // group by requested group_by
      IssueProduct::groupBy(DB::raw($group_by))
      ->groupBy('products.id')
      ->selectRaw(
        "products.id as product_id,
      products.name as name, 
      products.type as type, 
      products.price as price, 
      sum(issue_products.total_price) as total_sales,
      sum(sold_quantity) as sold_quantity,
      issue_products.created_at as date
      "
      )
      ->selectRaw(
        $formatted_date . ' as formatted_date'
      )
      ->where('stocks.branch_id', Auth::user()->branch_id)
      // display today issue products when there is no end date
      ->when($request->has('start_date'), function ($query) use ($request) {
        return $request->end_date ?
          $query->whereBetween('issue_products.created_at', [$request->start_date, $request->end_date]) :
          $query->whereDate('issue_products.created_at', '>=', $request->start_date);
      })
      // get only issue_products today
      // ->whereBetween('issue_products.created_at', [$request->start_date, $request->end_date])
      ->leftJoin('stocks', 'issue_products.stock_id', '=', 'stocks.id')
      ->leftJoin('products', 'stocks.product_id', '=', 'products.id')
      // if filter search by products.name
      ->when(request('search'), function ($query) {
        return $query->where('products.name', 'like', '%' . request('search') . '%');
      })
      // if filter by type
      ->when(request('type'), function ($query) {
        return $query->where('products.type', request('type'));
      })
      // if filed and direction exist on request
      ->orderBy($field, $direction);
  }

  public function historicalDataIndex(Request $request)
  {

    $data = $this->historicalDataBranch($request)
      ->paginate(20)->withQueryString();

    return Inertia::render('User/Reports/Sales/HistoricalData', [
      'sales_history' => $data,
      'sales_history_filter' => request()->all([
        'search',
        'field',
        'direction',
        'type',
        'start_date',
        'end_date',
        'group_by',
      ]),
    ]);
  }

  public function downloadHistoricalDataBranchExcel(Request $request)
  {
    $start_date = null;
    if ($request->start_date) {
      $start_date = date('m/d/Y', strtotime($request->start_date));
    }

    $end_date = null;
    if ($request->end_date) {
      $end_date =  date('m/d/Y', strtotime($request->end_date));
    }

    $group_by = 'DAILY';
    if ($request->group_by) {
      $group_by = strtoupper($request->group_by);
    }

    return Excel::download(new SalesHistoryReportExport(
      $this->historicalDataBranch($request)->get(),
      Auth::user()->branch->name,
      $start_date,
      $end_date,
      $group_by
    ), 'sales_report_branch_' . now()->toDateString() . ' .xlsx');
  }

  public function downloadHistoricalDataBranchPDF(Request $request)
  {
    $start_date = null;
    if ($request->start_date) {
      $start_date = date('m/d/Y', strtotime($request->start_date));
    }

    $end_date = null;
    if ($request->end_date) {
      $end_date =  date('m/d/Y', strtotime($request->end_date));
    }

    $group_by = 'DAILY';
    if ($request->group_by) {
      $group_by = strtoupper($request->group_by);
    }

    return Excel::download(new SalesHistoryReportExport(
      $this->historicalDataBranch($request)->get(),
      Auth::user()->branch->name,
      $start_date,
      $end_date,
      $group_by
    ), 'sales_report_branch_' . now()->toDateString() . ' .pdf', \Maatwebsite\Excel\Excel::DOMPDF);
  }

  public function chartBranch(Request $request)
  {
    $request->validate([
      'start_date' => 'date',
      'end_date' => 'date|after:start_date',
      'type' => 'in:chicken,beef,pork',
      'group_by' => 'in:weekly,monthly,yearly',
    ]);

    // default daily
    $group_by = 'DATE_FORMAT(issue_products.created_at, "%Y-%m-%d")';
    $formatted_date = 'DATE_FORMAT(issue_products.created_at, "%Y-%m-%d")';


    if ($request->has('group_by')) {
      if ($request->group_by == 'weekly') {
        $group_by = 'WEEK(issue_products.created_at)';
        $formatted_date =  'WEEK(issue_products.created_at)';
      } else if ($request->group_by == 'monthly') {
        $group_by = 'MONTH(issue_products.created_at)';
        $formatted_date =  'DATE_FORMAT(issue_products.created_at, "%M")';
      } else if ($request->group_by == 'yearly') {
        $group_by = 'YEAR(issue_products.created_at)';
        $formatted_date = 'DATE_FORMAT(issue_products.created_at, "%Y")';
      }
    }

    $data =
      // group by requested group_by
      IssueProduct::groupBy(DB::raw($group_by))
      ->selectRaw(
        "products.id as product_id,
      products.name as name, 
      products.type as type, 
      products.price as price, 
      sum(issue_products.total_price) as total_sales,
      sum(sold_quantity) as sold_quantity,
      issue_products.created_at as date"
      )->selectRaw($formatted_date . ' as formatted_date')
      // display today issue products when there is no end date
      ->when($request->has('start_date'), function ($query) use ($request) {
        return $request->end_date ?
          $query->whereBetween('issue_products.created_at', [$request->start_date, $request->end_date]) :
          $query->whereDate('issue_products.created_at', '>=', $request->start_date);
      })
      // get only issue_products today
      // ->whereBetween('issue_products.created_at', [$request->start_date, $request->end_date])
      ->leftJoin('stocks', 'issue_products.stock_id', '=', 'stocks.id')
      ->leftJoin('products', 'stocks.product_id', '=', 'products.id')
      // if filter by branch_id
      ->where('stocks.branch_id', Auth::user()->branch_id)
      // if filter by type
      ->when(request('type'), function ($query) {
        return $query->where('products.type', request('type'));
      })
      // sort by date
      ->orderBy('date', 'asc')
      ->get();

    return Inertia::render('User/Reports/Sales/HistoricalDataChart', [
      'sales_chart_data' => $data,
      'sales_chart_filter' => $request->all([
        'start_date',
        'end_date',
        'type',
        'group_by'
      ]),
    ]);
  }
}
