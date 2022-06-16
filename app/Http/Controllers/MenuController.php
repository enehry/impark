<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MenuController extends Controller
{
  //
  public function index()
  {
    // check who logged in and show the menu accordingly
    if (auth()->check()) {
      if (auth()->user()->role == 1) {
        // count products
        $product_count = Product::count();
        // count branch
        $branch_count = Branch::count();

        return Inertia::render('MenuAdmin', [
          'product_count' => $product_count,
          'branch_count' => $branch_count,
        ],);
      } else {
        // get user branch
        $branch_id = auth()->user()->branch_id;
        //
        $forecast_count = DB::table('stocks')
          ->join('products', 'stocks.product_id', '=', 'products.id')
          ->leftJoin('forecast_settings', 'stocks.id', '=', 'forecast_settings.stock_id')
          ->where('stocks.is_forecasted', false)
          ->where('stocks.branch_id', $branch_id)
          ->select(
            'forecast_settings.default_kg_per_day as user_kg_per_day',
            'forecast_settings.reordering_point as user_rop',
            'products.reordering_point as default_rop',
            'products.default_kg_per_day as default_kg_per_day',
            'stocks.quantity as quantity',
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
          * products.maximum_shelf_life as forecast_quantity'
            )
          )
          ->where(
            'stocks.quantity',
            '<=',
            DB::raw('IF(forecast_settings.reordering_point IS NULL, 
        products.reordering_point, 
        forecast_settings.reordering_point)')
          )->count();


        return Inertia::render('MenuUser', [
          'forecast_count' => $forecast_count,
        ]);
      }
    }
  }
}
