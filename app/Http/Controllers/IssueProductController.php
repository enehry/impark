<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\IssueProduct;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf as PDF;



class IssueProductController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    // get all products
    $product_dropdown =
      DB::table('stocks')
      ->where('branch_id', Auth::user()->branch_id)
      ->leftJoin('products', 'stocks.product_id', '=', 'products.id')

      ->select(
        'stocks.id as id',
        'products.name as name',
        'products.price as price',
        'stocks.quantity as quantity',
        'products.type as type'
      )->get();

    // Stock::where('branch_id', Auth::user()->branch_id)
    // ->with('product')
    // ->get()
    // ->map
    // ->only(
    //   [
    //     'id',
    //     'product.name',
    //     'product.price',
    //     'quantity',
    //   ]
    // );


    return Inertia::render('User/IssueProducts/Index', [
      'product_dropdown' => $product_dropdown,
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
   * @param  \App\Models\IssueProduct  $issueProduct
   * @return \Illuminate\Http\Response
   */
  public function show(IssueProduct $issueProduct)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\IssueProduct  $issueProduct
   * @return \Illuminate\Http\Response
   */
  public function edit(IssueProduct $issueProduct)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\IssueProduct  $issueProduct
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, IssueProduct $issueProduct)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\IssueProduct  $issueProduct
   * @return \Illuminate\Http\Response
   */
  public function destroy(IssueProduct $issueProduct)
  {
    //
  }

  public function proceedTransaction(Request $request)
  {
    // validate issueProducts array
    $request->validate([
      'issueProducts' => 'required|array',
      'issueProducts.*.id' => 'required|exists:stocks,id',
      'issueProducts.*.quantity' => 'required|integer|min:1',
      'issueProducts.*.total' => 'required|numeric',
    ]);

    // sum total price
    $sum_total_price = 0;
    foreach ($request->issueProducts as $issueProduct) {
      $sum_total_price += $issueProduct['total'];
    }

    // create issue
    $issue = Issue::create([
      'user_id' => Auth::user()->id,
      'branch_id' => Auth::user()->branch_id,
      'sum_total_price' => $sum_total_price,
    ]);

    if ($issue) {
      // create issueProducts
      foreach ($request->issueProducts as $issueProduct) {
        IssueProduct::create([
          'total_price' => $issueProduct['total'],
          'sold_quantity' => $issueProduct['quantity'],
          'stock_id' => $issueProduct['id'],
          'issue_id' => $issue->id,
        ]);

        // reduce stocks quantity
        Stock::where('id', $issueProduct['id'])
          ->update([
            'quantity' => DB::raw('quantity - ' . $issueProduct['quantity']),
          ]);
      }
    }

    // create issueProducts
    return Redirect::route('issue-products.index', [
      'issue' => IssueProduct::where('issue_id', $issue->id)->get(),
    ])->banner('Transaction Successful');
  }

  public function testPDF()
  {
    $data = [
      'title' => 'Welcome to Tutsmake.com',
      'date' => date('m/d/Y')
    ];;
    $pdf = PDF::loadView('exports/issue_product_receipt', $data);
    return $pdf->download('test.pdf');
  }

  public function receiptIssueProduct()
  {
    // get log user branch_id
    $branch_id = Auth::user()->branch_id;
    // get latest issue with issueProducts stocks and product filter with branch
    $issue = Issue::where('branch_id', $branch_id)
      ->with(['issueProducts.stock.product', 'user'])
      ->orderBy('id', 'desc')
      ->first();

    // return response()->json([
    //   'issue' => $issue,
    // ]);

    $pdf = PDF::loadView('exports/issue_product_receipt', [
      'issue' => $issue,
    ]);
    return $pdf->download('receipt-' . $issue->id . '.pdf');
  }
}
