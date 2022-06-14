<?php

namespace App\Http\Controllers;

use App\Models\IssueProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IssueProductController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    // get all products
    $product_dropdown = Product::all(['id', 'name']);

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
}
