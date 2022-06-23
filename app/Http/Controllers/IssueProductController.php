<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\IssueProduct;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockAge;
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
      $issue_products_id = [];
      // create issueProducts
      foreach ($request->issueProducts as $issueProduct) {
        $issue_product = IssueProduct::create([
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


        $tempSoldQuantity = $issueProduct['quantity'];

        // subtract sold quantity from stock age quantity 
        // loop it until tempSoldQuantity is 0
        while ($tempSoldQuantity > 0) {
          $stock_age = StockAge::where('stock_id', $issueProduct['id'])
            ->where('quantity', '>', 0)
            ->orderBy('created_at', 'asc')
            ->first();

          if ($stock_age) {
            if ($tempSoldQuantity >= $stock_age->quantity) {
              $tempSoldQuantity -= $stock_age->quantity;
              $stock_age->update([
                'quantity' => 0,
              ]);
            } else {
              $stock_age->update([
                'quantity' => DB::raw('quantity - ' . $tempSoldQuantity),
              ]);
              $tempSoldQuantity = 0;
            }
          } else {
            return Redirect::back()->bannerStyle([
              'Error out of stock',
              'error'
            ]);
          }
        }
        // add to issue_products_id array
        $issue_product_ids[] = $issue_product->id;
        // log the action
      }
      LogHelper::log(
        'issued products',
        Auth::user()->name . ' issued ' . count($issue_product_ids) . ' products.',
        'issue_products',
        $issue_product_ids,
      );
    }

    // create issueProducts
    return Redirect::route('issue-products.index', [
      'issue' => IssueProduct::where('issue_id', $issue->id)->get(),
    ])->banner('Transaction Successful');
  }

  public function receiptIssueProduct()
  {
    // get log user branch_id
    $branch_id = Auth::user()->branch_id;
    // get latest issue with issueProducts stocks and product filter with branch
    $issue = Issue::where('branch_id', $branch_id)
      ->with(['branch', 'user'])
      ->orderBy('created_at', 'desc')
      ->first();

    $issue_product = IssueProduct::where('issue_id', $issue->id)
      ->with('stock.product')
      ->get();

    // return response()->json([
    //   'issue_products' => $issue_product,
    //   'issue' => $issue,
    // ]);

    LogHelper::log(
      'downloaded pdf',
      Auth::user()->name . ' downloaded pdf id ' . $issue->id  . ' issued products.',
      'issues',
      [$issue->id],
    );

    $pdf = PDF::loadView('exports/issue_product_receipt', [
      'issue' => $issue,
      'issue_products' => $issue_product,
    ]);
    return $pdf->download('receipt-' . $issue->id . '.pdf');
  }
}
