<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
      //  dump($organizations= $users = DB::table('organizations')->where('user_id', '=',  Auth::id())->get());
        $this->authorize('viewAny', Organization::class);
        if (Auth::user()->role == 'Admin'){
            $organizations = Organization::all();
        }
        if (Auth::user()->role == 'Employer'){
            $organizations=DB::table('organizations')->whereUser_idAndDeleted_at(Auth::id(),null)->get();
        }



        return response()->json($organizations);
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
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        //$this->authorize('create', Organization::class);
        if (Auth::user()->role == 'Employer') {

        $organization = Organization::make();
        $organization->user_id = auth()->id();
        $organization->title = $request->title;
        $organization->country = $request->country;
        $organization->city = $request->city;
        $organization->save();

        return response()->json($organization);
        }
        return response()->json('Only Employers can store organizations');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {

        $this->authorize('view', $organization);
        $vacancies = $_GET['vacancies'];
        $workers = $_GET['workers'];
        $result=collect ();
        if ($vacancies == 1) {

           $result= DB::table('vacancies')
           ->whereDeleted_atAndOrganization_id(null,$organization->id)
           ->whereColumn('workers_amount', '>' , 'workers_apply_amount')
           ->get()
           ->prepend('list of active vacancies');
        }

        if ($vacancies == 2) {

             $result= DB::table('vacancies')
           ->whereDeleted_atAndOrganization_id(null,$organization->id)
           ->whereColumn('workers_amount', '<=' , 'workers_apply_amount')
           ->get()
           ->prepend('list of closed (full) vacancies');
        }

        if ($vacancies == 3) {


            $result=DB::table('vacancies')
            ->whereOrganization_idAndDeleted_at($organization->id,null)->get()->prepend('list of all organization vacancies');
        }

        if ($workers == 1) {
            $vacancies=DB::table('vacancies')->whereOrganization_idAndDeleted_at($organization->id,null)->pluck('id');
            $array = Arr::flatten($vacancies);
            $item=collect();
            foreach ($array as $value) {
                $item->push(DB::table('user_vacancy')->whereVacancy_id($value)->pluck('user_id'));
            }

            $flattened = Arr::flatten($item);
            $result->push('Booked workers');
            $result->push(User::find($flattened));

        }
        return response()->json($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function edit(Organization $organization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);
        $organization->update($request->all());
        return response()->json($organization);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization)
    {
        $this->authorize('delete', $organization);
        $organization->delete();
        return response()->json(['message'=> 'organization with id: '.$organization->id.' successfully deleted']);
    }


    /**
     * statsOrganization.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function statsOrganization(){
        if (Auth::user()->role == 'Admin') {
            $active= count(DB::table('organizations')->whereDeleted_at(null)->get());
            $softdeleted= count(DB::table('organizations')->where('deleted_at','!=', null)->get());
            $all= count(DB::table('organizations')->get());


            return response()->json(['Active organizations : ' =>$active, 'SoftDeleted organizations: ' =>$softdeleted, 'All organizations: '=>$all]);
        }
        return response()->json('Only Admin authorized for this action');
    }
}
