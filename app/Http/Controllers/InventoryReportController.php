<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    if ($request->branch === 'warehouse') {
      $query = WarehouseStock::join('products', 'products.id', '=', 'warehouse_stocks.product_id')
        ->select('products.*', 'warehouse_stocks.quantity')
        ->when($request->has('search'), function ($query) use ($request) {
          $query->where('products.name', 'like', '%' . $request->search . '%');
        })
        ->when($request->has('type'), function ($query) use ($request) {
          $query->where('products.type', $request->type);
        })
        ->when($request->has('field'), function ($query) use ($request) {
          $query->orderBy($request->field, $request->direction);
        });
    } else {
      $query =
        Product::select('products.*', DB::raw('sum(stocks.quantity) as quantity'))
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
        });
    }

    return Inertia::render('Admin/Reports/Inventory/Index', [
      'stocks' =>  $query->paginate(20)->withQueryString(),
      'inventory_branches' => Branch::All(['id', 'name']),
      'inventory_filter' => request()->all(['branch_id', 'search', 'field', 'direction', 'type']),
    ]);
  }

  public function chart()
  {
    if (request('branch_id') === 'warehouse') {
      $stocks = WarehouseStock::join('products', 'products.id', '=', 'warehouse_stocks.product_id')
        ->select('products.*', 'warehouse_stocks.quantity')
        ->when(request('type'), function ($query) {
          return $query->where('products.type', request('type'));
        })
        ->orderBy('products.name', 'asc')
        ->get();
    } else {
      $stocks =
        Stock::select(
          'products.name as name',
          DB::raw('SUM(quantity) as quantity')
        )
        ->when(request('branch_id'), function ($query) {
          return $query->where('branch_id', request('branch_id'));
        })
        ->when(request('type'), function ($query) {
          return $query->where('products.type', request('type'));
        })
        ->groupBy('product_id')
        ->join('products', 'products.id', '=', 'stocks.product_id')
        ->get();
    }

    return Inertia::render('Admin/Reports/Inventory/Chart', [
      'stocks_quantity' => $stocks,
      'inventory_branches' => Branch::All(['id', 'name']),
      'inventory_filter' => request()->all(['branch_id', 'type']),
    ]);
  }

  public function lineChart()
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

    $stocks_vs_rop = Stock::select(
      'products.name as name',
      DB::raw('SUM(quantity) as quantity'),
      DB::raw('SUM(quantity) / products.rop as rop')
    );


    return Inertia::render('Admin/Reports/Inventory/LineChart', [
      'stocks_quantity' => $stocks,
      'inventory_branches' => Branch::All(['id', 'name']),
      'inventory_filter' => request()->all(['branch_id']),
    ]);
  }

  public function downloadExcel(Request $request)
  {
    $branch = null;
    if ($request->has('branch')) {
      if ($request->branch === 'warehouse') {
        $branch = 'Warehouse';
      } else {
        $branch = Branch::find($request->branch)->name;
      }
    }

    // download in excel format
    return Excel::download(new ProductExport($this->data($request), $branch), 'stocks-' . now()->toDateString() . '.xlsx');
  }

  public function downloadPdf(Request $request)
  {
    $branch = null;
    if ($request->has('branch')) {
      if ($request->branch === 'warehouse') {
        $branch = 'Warehouse';
      } else {
        $branch = Branch::find($request->branch)->name;
      }
    }

    return Excel::download(new ProductExport($this->data($request), $branch), 'stocks-' . now()->toDateString() . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
  }

  public function data(Request $request)
  {
    $request->validate([
      'direction' => 'in:asc,desc',
      'field' => 'in:name,price,type,quantity',
      // 'branch_id' =>  'numeric|exists:branches,id',
      'type' => 'in:pork,beef,chicken',
    ]);

    if ($request->branch === 'warehouse') {
      $query = WarehouseStock::join('products', 'products.id', '=', 'warehouse_stocks.product_id')
        ->select('products.*', 'warehouse_stocks.quantity')
        ->when($request->has('search'), function ($query) use ($request) {
          $query->where('products.name', 'like', '%' . $request->search . '%');
        })
        ->when($request->has('type'), function ($query) use ($request) {
          $query->where('products.type', $request->type);
        })
        ->when($request->has('field'), function ($query) use ($request) {
          $query->orderBy($request->field, $request->direction);
        });
    } else {
      $query =
        Product::select('products.*', DB::raw('sum(stocks.quantity) as quantity'))
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
        });
    }

    // $query =
    //   DB::table('products')
    //   ->select('products.*', DB::raw('sum(stocks.quantity) as quantity'))
    //   ->join('stocks', 'products.id', '=', 'stocks.product_id')
    //   ->groupBy('products.id')
    //   ->when($request->has('search'), function ($query) use ($request) {
    //     $query->where('name', 'like', '%' . $request->search . '%');
    //   })
    //   ->when($request->has('branch'), function ($query) use ($request) {
    //     $query->where('stocks.branch_id', $request->branch);
    //   })
    //   ->when($request->has('type'), function ($query) use ($request) {
    //     $query->where('products.type', $request->type);
    //   })
    //   ->when($request->has('field'), function ($query) use ($request) {
    //     $query->orderBy($request->field, $request->direction);
    //   })->get();

    return $query->get();
  }

  // for branch inventory report
  public function indexBranch(Request $request)
  {

    // sum all stock quantity by product_id
    $request->validate([
      'direction' => 'in:asc,desc',
      'field' => 'in:name,price,type,quantity',
      'type' => 'in:pork,beef,chicken',
    ]);

    $query =
      Product::select('products.*', DB::raw('sum(stocks.quantity) as quantity'))
      ->where('stocks.branch_id', Auth::user()->branch_id)
      ->join('stocks', 'products.id', '=', 'stocks.product_id')
      ->groupBy('products.id')
      ->when($request->has('search'), function ($query) use ($request) {
        $query->where('name', 'like', '%' . $request->search . '%');
      })
      ->when($request->has('type'), function ($query) use ($request) {
        $query->where('products.type', $request->type);
      })
      ->when($request->has('field'), function ($query) use ($request) {
        $query->orderBy($request->field, $request->direction);
      })->paginate(20)->withQueryString();


    return Inertia::render('User/Reports/Inventory/Index', [
      'stocks' => $query,
      'inventory_filter' => request()->all(['search', 'field', 'direction', 'type']),
    ]);
  }

  public function branchInventoryData(Request $request)
  {
    $request->validate([
      'direction' => 'in:asc,desc',
      'field' => 'in:name,price,type,quantity',
      'type' => 'in:pork,beef,chicken',
    ]);

    return
      Product::select('products.*', DB::raw('sum(stocks.quantity) as quantity'))
      ->where('stocks.branch_id', Auth::user()->branch_id)
      ->join('stocks', 'products.id', '=', 'stocks.product_id')
      ->groupBy('products.id')
      ->when($request->has('search'), function ($query) use ($request) {
        $query->where('name', 'like', '%' . $request->search . '%');
      })
      ->when($request->has('type'), function ($query) use ($request) {
        $query->where('products.type', $request->type);
      })
      ->when($request->has('field'), function ($query) use ($request) {
        $query->orderBy($request->field, $request->direction);
      })->get();
  }

  public function downloadBranchExcel(Request $request)
  {
    $branch = Branch::find(Auth::user()->branch_id)->name;

    // download in excel format
    return Excel::download(new ProductExport($this->branchInventoryData($request), $branch), 'stocks-' . now()->toDateString() . '.xlsx');
  }

  // download pdf
  public function downloadBranchPdf(Request $request)
  {
    $branch = Branch::find(Auth::user()->branch_id)->name;
    return Excel::download(new ProductExport($this->branchInventoryData($request), $branch), 'stocks-' . now()->toDateString() . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
  }

  public function chartBranch()
  {
    $stocks =
      Stock::select(
        'products.name as name',
        DB::raw('SUM(quantity) as quantity')
      )
      ->when(request('type'), function ($query) {
        $query->where('products.type', request('type'));
      })
      ->where('branch_id', Auth::user()->branch_id)
      ->groupBy('product_id')

      ->join('products', 'products.id', '=', 'stocks.product_id')
      ->get();

    return Inertia::render('User/Reports/Inventory/Chart', [
      'stocks_quantity' => $stocks,
      'inventory_report_filter' => request()->all(['type']),
    ]);
  }

  public function lineChartBranch()
  {
    $stocks =
      Stock::select(
        'products.name as name',
        DB::raw('SUM(quantity) as quantity')
      )
      ->when(request('branch_id'), function ($query) {
        return $query->where('branch_id', request('branch_id'));
      })

      ->where('branch_id', Auth::user()->branch_id)
      ->groupBy('product_id')
      ->join('products', 'products.id', '=', 'stocks.product_id')
      ->leftJoin('forecast_settings', 'forecast_settings.stock_id', '=', 'stocks.id')
      // check if the stocks has forecast_setting if not then use the default reordering_point
      ->selectRaw('IFNULL(forecast_settings.reordering_point, products.reordering_point) as reordering_point')
      ->get();



    return Inertia::render('User/Reports/Inventory/LineChart', [
      'stocks_quantity' => $stocks,
    ]);
  }
}
