<?php

namespace App\Http\Controllers;

use App\Models\ForecastSetting;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StocksExport;
use Carbon\Carbon;

class StockController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    //
    $request->validate([
      'direction' => 'in:asc,desc',
      'field' => 'in:name,price,type,quantity',
      'product_type' => 'in:chicken,pork,beef'
    ]);


    // get user branch
    $branch_id = auth()->user()->branch_id;

    $stocks = DB::table('stocks')
      ->join('products', 'stocks.product_id', '=', 'products.id')
      ->leftJoin('forecast_settings', 'stocks.id', '=', 'forecast_settings.stock_id')
      ->where('stocks.branch_id', $branch_id)
      ->when($request->has('product_type'), function ($query) use ($request) {
        return $query->where('products.type', $request->product_type);
      })
      ->select(
        'stocks.quantity as quantity',
        'stocks.id as stock_id',
        'forecast_settings.reordering_point as users_rop',
        'forecast_settings.default_kg_per_day as users_kg_per_day',
        'products.*'
      );

    if ($request->has('search')) {
      $stocks->where('name', 'like', '%' . $request->search . '%');
    }

    if ($request->has(['field', 'direction'])) {
      $stocks->orderBy($request->field, $request->direction);
    }


    return Inertia::render('User/Stocks/Index', [
      'stocks' => $stocks->paginate(10)->withQueryString()
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
   * @param  \App\Models\Stock  $stock
   * @return \Illuminate\Http\Response
   */
  public function show(Stock $stock)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Stock  $stock
   * @return \Illuminate\Http\Response
   */
  public function edit(Stock $stock)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Stock  $stock
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Stock $stock)
  {
    return response()->json([
      'success' => true,
      'message' => 'Settings updated successfully',
      request()->all(),
    ]);
    // update or create the Forecast settings using the product id
    $request->validate([
      'id' => 'required|integer',
      'reordering_point' => 'required|integer',
      'default_kg_per_day' => 'required|integer',
    ]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Stock  $stock
   * @return \Illuminate\Http\Response
   */
  public function destroy(Stock $stock)
  {
    //
  }

  public function updateOrCreateForecastSetting(Request $request)
  {


    $request->validate([
      'stock_id' => 'required|integer',
      'reordering_point' => 'required|integer',
      'default_kg_per_day' => 'required|integer',
    ]);


    $forecastSetting = ForecastSetting::where('stock_id', $request->stock_id)->first();

    if ($forecastSetting) {
      $forecastSetting->update([
        'reordering_point' => $request->reordering_point,
        'default_kg_per_day' => $request->default_kg_per_day,
      ]);
    } else {

      $forecastSetting = ForecastSetting::create([
        'stock_id' => $request->stock_id,
        'reordering_point' => $request->reordering_point,
        'default_kg_per_day' => $request->default_kg_per_day,
      ]);
    }

    return Redirect::back()->banner('Product updated successfully');
  }

  public function exportExcel(Request $request)
  {
    return Excel::download(new StocksExport($request), 'stocks-' . Carbon::today()->toDateString() . '.xlsx');
  }

  public function exportPDF(Request $request)
  {
    return Excel::download(new StocksExport($request), 'stocks-' . Carbon::today()->toDateString() . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
  }
}
