<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class BranchController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
    $branches = Branch::all();

    return Inertia::render('Admin/Branches/Index', [
      'branches' => $branches,
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
    return Inertia::render('Admin/Branches/Create');
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
    $request->validate([
      'name' => 'required|min:3',
    ]);

    $branch = Branch::create($request->all());

    return Redirect::route('branch.index')->banner('Branch has been created successfully');
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Branch  $branch
   * @return \Illuminate\Http\Response
   */
  public function show(Branch $branch)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Branch  $branch
   * @return \Illuminate\Http\Response
   */
  public function edit(Branch $branch)
  {
    //
    return Inertia::render('Admin/Branches/Edit', [
      'branch' => $branch,
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Branch  $branch
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Branch $branch)
  {
    //
    $request->validate([
      'name' => 'required|min:3',
    ]);

    $branch->update($request->all());
    return Redirect::route('branch.index')->banner('Branch has been updated successfully');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Branch  $branch
   * @return \Illuminate\Http\Response
   */
  public function destroy(Branch $branch)
  {
    //delete branch
    $branch->delete();
    return Redirect::back()->banner('Branch delete successfully.');
  }
}
