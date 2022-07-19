<?php

namespace App\Http\Controllers;

use App\Models\ForecastSetting;
use App\Models\PlannedOrder;
use App\Models\Product;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ForecastingController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    // get user branch
    $branch_id = auth()->user()->branch_id;
    //
    $stocks = DB::table('stocks')
      ->join('products', 'stocks.product_id', '=', 'products.id')
      ->leftJoin('forecast_settings', 'stocks.id', '=', 'forecast_settings.stock_id')
      ->where('stocks.is_forecasted', false)
      ->where('stocks.branch_id', $branch_id)
      ->when($request->has('product_type'), function ($query) use ($request) {
        return $query->where('products.type', $request->product_type);
      })->when(
        $request->has('search'),
        function ($query) use ($request) {
          return $query->where('products.name', 'like', '%' . $request->search . '%');
        }
      )
      ->select(
        'forecast_settings.default_kg_per_day as user_kg_per_day',
        'forecast_settings.reordering_point as user_rop',
        'products.reordering_point as default_rop',
        'products.default_kg_per_day as default_kg_per_day',
        'stocks.quantity as quantity',
        'stocks.is_forecasted as is_forecasted',
        'stocks.id as stock_id',
        'products.name as name',
        'products.type as type',
        'products.price as price',
        'products.id as product_id',
        'products.maximum_shelf_life as maximum_shelf_life',
        // check if reordering point is set by user if not use default
      )->addSelect(
        DB::raw(
          'IF(forecast_settings.reordering_point IS NULL, 
          products.reordering_point, 
          forecast_settings.reordering_point) 
          as rop'
        )
      )->addSelect(
        DB::raw(
          'IF(forecast_settings.default_kg_per_day IS NULL, 
          products.default_kg_per_day, 
          forecast_settings.default_kg_per_day) 
          as kg_per_day'
        )
      )
      // multiply maximum shelf life by default kg per day
      ->addSelect(
        DB::raw(
          'IF(forecast_settings.default_kg_per_day IS NULL, 
          products.default_kg_per_day, 
          forecast_settings.default_kg_per_day) 
          * 3 as forecast_quantity'
        )
      )
      ->where(
        'stocks.quantity',
        '<=',
        DB::raw('IF(forecast_settings.reordering_point IS NULL, 
        products.reordering_point, 
        forecast_settings.reordering_point)')
      )->when($request->has('sort'), function ($query) use ($request) {
        return $query->orderBy($request->sort, $request->order);
      });

    return Inertia::render('User/Forecasting/Index', [
      'forecast_stocks' => $stocks->get(),
      'forecast_filter' => $request->all(['product_type', 'search', 'sort', 'order']),
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
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }

  public function confirmForecast(Request $request)
  {
    // validate array of stock ids and quantity
    $request->validate([
      'forecastStocks' => 'required|array',
      'forecastStocks.*.stock_id' => 'required|exists:stocks,id',
      'forecastStocks.*.forecast_quantity' => 'required|integer|min:1',
    ]);

    // get user branch
    $branch_id = auth()->user()->branch_id;
    // get user id
    $user_id = auth()->user()->id;

    // collect planned orders ids
    $planned_order_ids = [];
    // insert forecast to Planned Orders
    foreach ($request->forecastStocks as $stock) {

      // check if stock is already forecasted
      if ($stock['is_forecasted'] == 1) {
        return Redirect::back()->dangerBanner('Stock already forecasted');
      }

      $planned_orders = PlannedOrder::create([
        'stock_id' => $stock['stock_id'],
        'order_quantity' => $stock['forecast_quantity'],
        'branch_id' => $branch_id,
        'user_id' => $user_id,
      ]);

      $planned_order_ids[] = $planned_orders->id;
    }

    // update is_forecasted to true
    DB::table('stocks')
      ->whereIn('id', collect($request->forecastStocks)->pluck('stock_id'))
      ->update(['is_forecasted' => true]);

    // log the action
    LogHelper::log(
      'confirmed forecast',
      Auth::user()->name . ' confirmed forecast for ' . count($request->forecastStocks) . ' stocks',
      'planned_orders',
      $planned_order_ids,
    );


    return Redirect::back()->banner('Forecast successfully confirmed');
  }

  public function bypassNextDay()
  {
    // get all issue_products of all branches group by stock_id and branch_id sum the sold_quantity
    $total_sold_quantity = DB::table('issue_products')
      ->select(
        'issue_products.stock_id',
        DB::raw('SUM(issue_products.sold_quantity) as sold_quantity')
      )
      ->selectRaw('IFNULL(forecast_settings.reordering_point, products.reordering_point) as reordering_point')
      ->selectRaw('IFNULL(forecast_settings.default_kg_per_day, products.default_kg_per_day) as default_kg_per_day')
      ->groupBy('issue_products.stock_id')
      ->leftJoin('forecast_settings', 'issue_products.stock_id', '=', 'forecast_settings.stock_id')
      ->leftJoin('stocks', 'issue_products.stock_id', '=', 'stocks.id')
      ->leftJoin('products', 'stocks.product_id', '=', 'products.id')
      ->whereDate('issue_products.created_at', '=', date('Y-m-d'))
      ->get();

    // loop through all stocks and update the forecast_default kg per day if the total sold
    // is greater than the forecast_default kg per day
    foreach ($total_sold_quantity as $stock) {
      // kg/per day 45,  nabenta today 49, = 50 = kg/day update
      // kg/per day 45, nabenta 44, = 43 = kg/day update
      $kg_per_day = $stock->default_kg_per_day;
      if ($stock->sold_quantity > $kg_per_day) {
        $kg_per_day++;
      } else if ($stock->sold_quantity < $kg_per_day) {
        $kg_per_day--;
      }
      // update if exist if not create
      ForecastSetting::updateOrCreate(
        ['stock_id' => $stock->stock_id],
        ['reordering_point' => $stock->reordering_point, 'default_kg_per_day' => $kg_per_day],
      );
    }
    // get all stocks
    // 
    return Redirect::back()->banner('Forecast Settings successfully bypassed');
  }
}
