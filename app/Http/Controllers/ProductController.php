<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ProductController extends Controller
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
      'field' => 'in:name,price,type',
    ]);

    $query =

      // get all stocks and sum it up by a product
      // Stock::selectRaw('product_id, sum(quantity) as quantity')
      // ->groupBy('product_id')->with(['product' => function ($query) use ($request) {
      //   $query->when($request->has('search'), function ($query) use ($request) {
      //     $query->where('name', 'like', '%' . $request->search . '%');
      //   });
      //   $query->when($request->has('field'), function ($query) use ($request) {
      //     $query->orderBy($request->field, $request->direction);
      //   });
      // }]);
      tap(Product::with('stocks')
        ->when($request->has('search'), function ($query) use ($request) {
          $query->where('name', 'like', '%' . $request->search . '%');
        })
        ->when($request->has('field'), function ($query) use ($request) {
          $query->orderBy($request->field, $request->direction);
        })
        ->paginate(10))
      // map the stocks and sum it up by a product
      ->map(function ($product) {
        $product->quantity = $product->stocks->sum('quantity');
        return $product;
      });

    // if ($request->has('search')) {
    //   $query->where('product.name', 'like', '%' . $request->search . '%');
    // }

    // if ($request->has(['field', 'direction'])) {
    //   $query->orderBy($request->field, $request->direction);
    // }


    return Inertia::render('Admin/Products/Index', [
      'products_admin' => $query,
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
    return Inertia::render('Admin/Products/Create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    // validate product
    $request->validate([
      'name' => 'required',
      'price' => 'required',
      'type' => 'required|in:pork,beef,chicken',
    ]);

    // create product
    $product = Product::create($request->all());
    return redirect()->route('products.index')->banner('Product created successfully');
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Product  $product
   * @return \Illuminate\Http\Response
   */
  public function show(Product $product)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Product  $product
   * @return \Illuminate\Http\Response
   */
  public function edit(Product $product)
  {
    // find prodcut

    return Inertia::render('Admin/Products/Edit', [
      'product' => $product
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Product  $product
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {

    //
    $request->validate([
      'name' => 'required',
      'price' => 'required',
      'type' => 'required|in:pork,beef,chicken',
      'reordering_point' => 'required',
      'maximum_shelf_life' => 'required',
    ]);


    $product = Product::find($id);
    $product->name = $request->name;
    $product->price = $request->price;
    $product->type = $request->type;
    $product->reordering_point = $request->reordering_point;
    $product->maximum_shelf_life = $request->maximum_shelf_life;
    $product->save();


    return redirect()->route('products.index')->banner('Product updated successfully');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Product  $product
   * @return \Illuminate\Http\Response
   */
  public function destroy(Product $product)
  {
    //
    $product->delete();
    return Redirect::route('products.index')->banner('Product deleted successfully');
  }
}
