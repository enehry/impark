<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class BOMController extends Controller
{
  //
  public function index(Request $request)
  {
    //
    $request->validate([
      'direction' => 'in:asc,desc',
      'field' => 'in:name,price,type',
    ]);

    $query = Product::query();
    if ($request->has('search')) {
      $query->where('name', 'like', '%' . $request->search . '%');
    }

    if ($request->has(['field', 'direction'])) {
      $query->orderBy($request->field, $request->direction);
    }

    return Inertia::render('Admin/BOM/Index', [
      'products' => $query->paginate(10),
    ]);
  }

  public function updatePrice(Request $request)
  {
    //

    $request->validate([
      'id' => 'required|numeric',
      'price' => 'required|numeric',
    ]);

    $product = Product::find($request->id);
    $product->price = $request->price;
    $product->save();

    return Redirect::back()->banner('Product price updated successfully');
  }
}
