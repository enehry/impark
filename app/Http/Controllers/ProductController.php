<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

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
      'field' => 'in:name,price,type,quantity',
      'branch_id' =>  'numeric|exists:branches,id',
      'type' => 'in:pork,beef,chicken',
    ]);

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
      })->paginate(20);


    return Inertia::render('Admin/Products/Index', [
      //@ignore-line
      'products_admin' => $query->withQueryString(),
      'p_branches' => DB::table('branches')->select('id', 'name')->get(),
      'products_filter' => $request->all(['search', 'branch', 'field', 'direction', 'type']),
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

    // create stock for all the branch
    $branches = Branch::all();
    foreach ($branches as $branch) {
      Stock::create([
        'branch_id' => $branch->id,
        'product_id' => $product->id,
        'quantity' => 0,
      ]);
    }
    // log action
    LogHelper::log(
      'created',
      Auth::user()->name . ' created a product ' . $product->name,
      'products',
      [$product->id]
    );
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

    // log action
    LogHelper::log(
      'updated',
      Auth::user()->name . ' updated a product ' . $product->name,
      'products',
      [$product->id]
    );


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

    // log action
    LogHelper::log(
      'deleted',
      Auth::user()->name . ' deleted a product ' . $product->name,
      'products',
      [$product->id]
    );
    return Redirect::route('products.index')->banner('Product deleted successfully');
  }

  public function downloadExcel(Request $request)
  {


    $request->validate([
      'direction' => 'in:asc,desc',
      'field' => 'in:name,price,type,quantity',
      'branch_id' =>  'numeric|exists:branches,id',
      'type' => 'in:pork,beef,chicken',
    ]);

    $query =
      DB::table('products')
      ->select('products.*', DB::raw('sum(stocks.quantity) as quantity'))
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
      })->get();

    $branch = null;
    if ($request->has('branch')) {
      $branch = Branch::find($request->branch)->name;
    }


    // log action
    LogHelper::log(
      'downloaded excel',
      Auth::user()->name . ' downloaded a product list excel',
      'products',
      []
    );
    // download excel in export product
    return Excel::download(new ProductExport($query, $branch), 'stocks-' . now()->toDateString() . '.xlsx');
  }

  public function downloadPDF(Request $request)
  {

    $request->validate([
      'direction' => 'in:asc,desc',
      'field' => 'in:name,price,type,quantity',
      'branch_id' =>  'numeric|exists:branches,id',
      'type' => 'in:pork,beef,chicken',
    ]);

    $query =
      DB::table('products')
      ->select('products.*', DB::raw('sum(stocks.quantity) as quantity'))
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
      })->get();

    $branch = null;
    if ($request->has('branch')) {
      $branch = Branch::find($request->branch)->name;
    }

    // log action
    LogHelper::log(
      'downloaded pdf',
      Auth::user()->name . ' downloaded a product list pdf',
      'products',
      []
    );

    // download excel in export product
    return Excel::download(new ProductExport($query, $branch), 'stocks-' . now()->toDateString() . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
  }
}
