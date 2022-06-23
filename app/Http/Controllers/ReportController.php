<?php

namespace App\Http\Controllers;

use App\Models\StockAge;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
  //
  public function index()
  {
    // get all stock_ages that are datediff(now(), stock_ages.created_at) >= products.maximum_shelf_life
    $age_counter = StockAge::join(
      'stocks',
      'stocks.id',
      '=',
      'stock_ages.stock_id'
    )
      ->join(
        'products',
        'products.id',
        '=',
        'stocks.product_id'
      )
      ->whereRaw('DATEDIFF(NOW(), stock_ages.created_at) >= products.maximum_shelf_life')->count();

    return Inertia::render('Admin/Reports/Index', [
      'age_counter' => $age_counter,
    ]);
  }

  public function indexBranch()
  {
    $age_counter = StockAge::join(
      'stocks',
      'stocks.id',
      '=',
      'stock_ages.stock_id'
    )
      ->join(
        'products',
        'products.id',
        '=',
        'stocks.product_id'
      )
      ->whereRaw('DATEDIFF(NOW(), stock_ages.created_at) >= products.maximum_shelf_life')->count();

    return Inertia::render('User/Reports/Index', [
      'age_counter' => $age_counter,
    ]);
  }
}
