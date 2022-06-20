<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
    return Inertia::render('Admin/Users/Index', [
      'users' => User::with('branch')->get(),
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
    $branches = Branch::All(['id', 'name']);
    return Inertia::render('Admin/Users/Create', [
      'branches' => $branches,
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //validate request
    $request->validate([
      'name' => 'required',
      'role' => 'required|in:1,2',
      'email' => 'required|email|unique:users',
      'password' => 'required|confirmed|min:8',
      'branch_id' => 'required_if:role,2',
    ]);

    //create user
    $user = User::create([
      'name' => $request->name,
      'role' => $request->role,
      'email' => $request->email,
      'password' => bcrypt($request->password),
      'branch_id' => $request->branch_id,
    ]);

    //log action
    LogHelper::log(
      'created',
      Auth::user()->name . ' created a user ' . $user->name,
      'users',
      [$user->id]
    );

    //return response
    return redirect()->route('users.index')->banner('User created successfully.');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //

    return Inertia::render('Admin/Users/Edit', [
      'user_data' => User::findOrFail($id),
      'branches' => Branch::All(['id', 'name']),
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    // validate request
    $request->validate([
      'name' => 'required',
      'role' => 'required|in:1,2',
      'email' => 'required|email|unique:users,email,' . $id,
      'branch_id' => 'required_if:role,2',
    ]);

    // update user
    $user = User::findOrFail($id);
    $user->name = $request->name;
    $user->role = $request->role;
    $user->email = $request->email;
    if ($request->password) {
      $user->password = bcrypt($request->password);
    }
    $user->branch_id = $request->branch_id;
    $user->save();


    //log action
    LogHelper::log(
      'updated',
      Auth::user()->name . ' updated a user ' . $user->name,
      'users',
      [$user->id]
    );

    // return response
    return redirect()->route('users.index')->banner('User updated successfully.');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    // delete user permanently
    $user = User::findOrFail($id);
    $user->forceDelete();

    //log action
    LogHelper::log(
      'deleted',
      Auth::user()->name . ' deleted a user ' . $user->name,
      'users',
      [$user->id]
    );
    return redirect()->route('users.index')->banner('User deleted successfully.');
  }
}
