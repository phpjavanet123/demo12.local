<?php

namespace App\Http\Controllers;

use App\Currency;
use App\Wallet;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currencies = Currency::all();
        return view('users.create', compact('currencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //TODO::: Can be moved to Request separate object and checked there - to make controller more clean
      $request->validate([
        'name'=>'required',
        'email'=> 'required',
        'password'=> 'required',
      ]);
      //TODO::: GOOD INEA MOVE TO UserService.php - and also use for API: api/users
      $user = new User([
        'name' => $request->get('name'),
        'email'=> $request->get('email'),
        'password' => Hash::make($request->get('password')),
        'role_id' => $request->get('role'),
      ]);
        $user->save();
        $currency = Currency::whereCode($request->get('currency_code'))->firstOrFail();
        $wallet = Wallet::create([
            'user_id'     => $user->id,
            'currency_id' => $currency->id,
            'number'      => 1,  //temporary used as flag
        ]);
        $wallet->number = $wallet->id;
        $wallet->save(); //here we update with unique account number

      return redirect('/users')->with('success', 'User has been added');
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
        //$user = User::with()->find($id); //we want together
        $user = User::with('wallet')->whereId($id)->firstOrFail();
        $currencies = Currency::all();

        return view('users.edit', compact('user', 'currencies'));
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
      //TODO::: Can be moved to Request separate object and checked there - to make controller more clean
      $request->validate([
        'name'=>'required',
        'email'=> 'required',
      ]);

      $user = User::find($id);
      $user->name = $request->get('name');
      $user->email = $request->get('email');
      $user->role_id = $request->get('role');
      if (!empty($request->get('password'))) {
          $user->password = Hash::make($request->get('password'));
      }
      $user->save();

      return redirect('/users')->with('success', 'User has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		$user = User::find($id);
		$user->delete();

		return redirect('/users')->with('success', 'User has been deleted Successfully');
    }
}
