<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VacancyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$only_active=$_GET['only_active'];
        //dd(response()->json(Vacancy::all()));
        if (Auth::user()->role == 'Admin' and $_GET['only_active'] == 'true'){

        return DB::table('vacancies')->whereColumn('workers_amount', '>' , 'workers_apply_amount')->where('deleted_at', '=', null)->get();
        }
        if (Auth::user()->role == 'Admin' and $_GET['only_active'] == 'false'){

        return response()->json(Vacancy::all());
        }

        if (Auth::user()->role == 'Employer' or Auth::user()->role == 'Worker') {

        return DB::table('vacancies')->whereColumn('workers_amount', '>' , 'workers_apply_amount')->where('deleted_at', '=', null)->get();
        //return DB::table('vacancies')->whereVacancy_activeAndDeleted_at(1,null)->get();

        }
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
         //$this->authorize('create', $request);
        if (Auth::user()->role == 'Employer' and Organization::find ($request->organization_id)->user_id == Auth::user()->id) {
            $vacancy = Vacancy::make();
            $vacancy->vacancy_name = $request->vacancy_name;
            $vacancy->workers_amount = $request->workers_amount;
            $vacancy->organization_id = $request->organization_id;
            $vacancy->salary = $request->salary;
            $vacancy->user_id=Auth::user()->id;
            $vacancy->save();

            return response()->json($vacancy);
        }
        return response()->json('Only the owner can create vacancies for this organization.');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vacancy  $vacancy
     * @return \Illuminate\Http\Response
     */
    public function show(Vacancy $vacancy)
    {
        if (Auth::user()->role == 'Worker'){
            return response()->json($vacancy);
        }
        if (Auth::user()->role == 'Employer' and Auth::user()->id == $vacancy->user_id  or Auth::user()->role == 'Admin'){
             $workers=DB::table('user_vacancy')->whereVacancy_id($vacancy->id)->pluck('user_id');
             $workers= User::find($workers);


            return response()->json(['Vacancy: ' => $vacancy,'Workers: ' => $workers]);
        }
        if (Auth::user()->role == 'Employer' and Auth::user()->id != $vacancy->user_id) {
            return response()->json('User is not the owner of this organization');
        }


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vacancy  $vacancy
     * @return \Illuminate\Http\Response
     */
    public function edit(Vacancy $vacancy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vacancy  $vacancy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vacancy $vacancy)
    {
        $this->authorize('update', $vacancy);
        $vacancy->workers_apply_amount=0;
         $vacancy->update($request->all());
        return response()->json($vacancy);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vacancy  $vacancy
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vacancy $vacancy)
    {
        $this->authorize('delete', $vacancy);
        $vacancy->delete();
        return response()->json(['message'=> 'vacancy with id: '.$vacancy->id.' successfully deleted']);
    }

    /**
     * Book vacancy.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function book(Request $request)
    {
        //dd(Vacancy::find($request->vacancy_id));


//        $this->authorize('book', $request);
        $check=Vacancy::find($request->vacancy_id);

        if ($check === null) {
           return response()->json('There is no such vacancy');
        }
        if ($check->workers_apply_amount >= $check->workers_amount) {
            return response()->json('This vacancy is full');
        }
        if ($check != null) {

            if (Auth::user()->id == $request->user_id and Auth::user()->role == 'Worker' or Auth::user()->role == 'Admin') {



           $check = DB::table('user_vacancy')->whereUser_idAndVacancy_id($request->user_id,$request->vacancy_id)->first();
        if($check === null){
            DB::table('user_vacancy')
                ->insert(['user_id' => $request->user_id, 'vacancy_id' => $request->vacancy_id]);
            DB::table('vacancies')
                ->where('id', $request->vacancy_id)
                ->update(['workers_apply_amount' => DB::raw('workers_apply_amount + 1')]);
                return response()->json('User '.$request->user_id.' booked on vacancy '.$request->vacancy_id);
            }
        if($check != null){
             return response()->json('This user already booked on the vacancy');
            }
        }
        }
        if (Auth::user()->role == 'Employer' or Auth::user()->id != $request->user_id ) {
                return response()->json('This user not authorized for this action');
        }

    }

    /**
     * UnBook vacancy.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unbook(Request $request)
    {
       // $organization=Vacancy::find($request->vacancy_id)->organization_id;
       // dd(Organization::find(Vacancy::find($request->vacancy_id)->organization_id)->user_id);
       // dd(Vacancy::find($request->vacancy_id)->organization_id);


        $check=Vacancy::find($request->vacancy_id);

        if ($check === null) {
           return response()->json('There is no such vacancy');
        }

        if ($check != null) {
            if (Auth::user()->id == $request->user_id and Auth::user()->role == 'Worker'
                or Auth::user()->role == 'Admin'
                or Organization::find(Vacancy::find($request->vacancy_id)->organization_id)->user_id == Auth::user()->id and Auth::user()->role == 'Employer') {

           $check = DB::table('user_vacancy')->whereUser_idAndVacancy_id($request->user_id,$request->vacancy_id)->first();
        if($check != null){
             DB::table('user_vacancy')
                ->whereUser_idAndVacancy_id($request->user_id,$request->vacancy_id)
                ->delete();
             DB::table('vacancies')
                ->where('id', $request->vacancy_id)
                ->update(['workers_apply_amount' => DB::raw('workers_apply_amount - 1')]);
           return response()->json('User '.$request->user_id.' unbooked on vacancy '.$request->vacancy_id);
            }
        if($check === null){
                return response()->json('User '.$request->user_id.' not booked on vacancy '.$request->vacancy_id);
            }
        }
        }
        if (Auth::user()->id != $request->user_id or Auth::user()->role != 'Employer') {
                return response()->json('This user not authorized for this action');

            }

    }


    /**
     * statsVacancy.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function statsVacancy()
    {

        if (Auth::user()->role == 'Admin') {
           $active=count(DB::table('vacancies')->whereColumn('workers_amount', '>' , 'workers_apply_amount')->where('deleted_at', '=', null)->get());
           $full=count(DB::table('vacancies')->whereColumn('workers_amount', '=' , 'workers_apply_amount')->where('deleted_at', '=', null)->get());
           $all=count(Vacancy::all());
           return response()->json(['Active vacancies : ' =>$active, 'Full vacancies: ' =>$full, 'All vacancies: '=>$all]);
        }
        return response()->json('Only Admin authorized for this action');
    }


}
