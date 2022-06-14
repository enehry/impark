<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
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

        return Inertia::render('MenuAdmin', [
          'product_count' => $product_count,
        ],);
      } else {
        return Inertia::render('MenuUser');
      }
    }
  }
}
