<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
      'type' => 'in:chicken,pork,beef'
    ]);

    $query = Product::when($request->has('search'), function ($query) use ($request) {
        $query->where('name', 'like', '%' . $request->search . '%');
      });
    if ($request->has(['field', 'direction'])) {
      $query->orderBy($request->field, $request->direction);
    }

    if ($request->has('type')) {
      $query->where('type', $request->type);
    }

    return Inertia::render('Admin/BOM/Index', [
      'products' => $query->paginate(20)->withQueryString(),
      'bom_filter' => $request->All(['search', 'field', 'direction', 'type'])
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

    $old_price = $product->price;

    $product->price = $request->price;
    $product->save();


    // log the action
    if ($product) {
      LogHelper::log('updated', Auth::user()->name . ' updated price of product' . $product->name . ' from ' .
        $old_price . '-' . $product->price, 'products', [$product->id]);
    }



    return Redirect::back()->banner('Product price updated successfully');
  }
}
