<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
    $term = $_GET['search'];
    $result =User::where('first_name', 'like', "%{$term}%")
            ->orWhere('last_name', 'like', "%{$term}%")
            ->orWhere('city', 'like', "%{$term}%")
            ->orWhere('country', 'like', "%{$term}%")
            ->get();


 return response()->json($result);
      // $users = User::all();
      // return response()->json($users);
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
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
       return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $user->update($request->all());
        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return response()->json(['message'=> 'User with id: '.$user->id.' successfully deleted']);
    }

    /**
     * statsUser.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function statsUser(){
        if (Auth::user()->role == 'Admin') {
            $admins= count(DB::table('users')->whereRole('Admin')->get());
            $employers= count(DB::table('users')->whereRole('Employer')->get());
            $workers= count(DB::table('users')->whereRole('Worker')->get());


            return response()->json(['Admins : ' =>$admins, 'Employers: ' =>$employers, 'Workers: '=>$workers]);
        }
        return response()->json('Only Admin authorized for this action');
    }
}
