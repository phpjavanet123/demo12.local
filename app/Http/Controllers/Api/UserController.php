<?php

namespace App\Http\Controllers\Api;

use App\Currency;
use App\User;
use App\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return response()->json(User::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ClientRequest $request
     * @return Response
     */
    public function store(ClientRequest $request)
    {
        $user = User::create($request->getOnlyFillableModified());
        $currency = Currency::whereCode($request->get('currency_code'))->firstOrFail();
        //We use create here because we want to be sure that we are CREATING ($wallet->save() - used for update/insert)
        $wallet = Wallet::create([
            'user_id'     => $user->id,
            'currency_id' => $currency->id,
            'number'      => 1,  //temporary used as flag
        ]);
        $wallet->number = $wallet->id;
        $wallet->save(); //here we update with unique account number

        return response()->json($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ClientRequest $request
     * @param User $user
     * @return Response
     */
    public function update(ClientRequest $request, User $user)
    {
        $user->fill($request->only(['name', 'email', 'country', 'city']));
        $user->save();
        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return Response
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        return response()->json($user);
        //Otherwise
        $user->delete();
        return response()->json(['success' => true]);
    }
}
