<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use JWTAuth;
use Dingo\Api\Routing\Helpers;
use App\User;
use Carbon\Carbon;
use App\Lost_time;

class LostTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        //
        $lost_time = DB::table('lost_times');

        //kalau get ada parameter start_date, maka yang muncul adalah between start_date s/d start_date->addWeek()

        // parameter tanggal
        if( $req->start_date != null ){

            $lost_time = $lost_time->whereBetween('tanggal', [Carbon::createFromFormat('Y-m-d', $req->start_date ), 
                Carbon::createFromFormat('Y-m-d', $req->start_date )->addWeek() ]  );
        }

        /*parameter tanggal*/
        if( $req->tanggal != null ){

            $lost_time = $lost_time->where('tanggal','=', $req->tanggal ) ;
            // return $req->tanggal;
        }

        if ($req->shift != null) {
            # code...
            $lost_time = $lost_time->where('shift', '=', $req->shift);
        }

        if ($req->line_name != null) {
            # code...
            $lost_time = $lost_time->where('line_name', '=', $req->line_name);
        }

        //pagination
        if ( $req->limit !=null){
            $lost_time = $lost_time->paginate($req->limit);
        }else{
            $lost_time = $lost_time->paginate(15);
        }

        //jika jumlah $lost_time > 0 maka $message = data found, otherwise data not found;
        if (count($lost_time) > 0) {
            $message = 'Data found';
        }else{
            $message = 'Data not found';
        }

        //collect adalah helper laravel untuk array
        $additional_message = collect(['_meta'=> [
            'message'=>$message,
            'count'=> count($lost_time)
        ] ]);
        //adding additional message
        $lost_time = $additional_message->merge($lost_time);
        //$lost_time is object, need to changes to array first!
        $lost_time = $lost_time->toArray();

        //cek kalu $lost_time->data kosong dan parameter2 tsb memenuhi, maka add.
        if( $req->tanggal != null && $req->shift != null && $req->line_name != null && 
            empty($lost_time['data']) )
        {
            //aunthenticate users id
            $currentUser = JWTAuth::parseToken()->authenticate();

            $result = $this->input_data($req->tanggal, $req->shift,$req->line_name, $currentUser->id );
            // return $result;
            $lost_time['data'] = $result;
        }

        return $lost_time;
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
    public function store(Request $req)
    {
        $currentUser = JWTAuth::parseToken()->authenticate();
        //return $currentUser;

        $lost_time = new Lost_time;
        //inputan
          $lost_time->line_name = $req->input('line_name', null);
          $lost_time->shift = $req->input('shift', null);
          $lost_time->time = $req->input('time', null);
          $lost_time->problem = $req->input('problem', null);
          $lost_time->lost_time = $req->input('lost_time', null);
          $lost_time->cause = $req->input('cause', null);
          $lost_time->action = $req->input('action', null);
          $lost_time->tanggal = $req->input('tanggal', null);
          $lost_time->followed_by = $req->input('followed_by', null);
          $lost_time->user_id = $req->input('user_id', null);
        //inputan end

        $lost_time->save();

        return [
            '_meta'=>[
                'status'=> "SUCCESS",
                'userMessage'=> "Data saved",
                'count'=>count($lost_time)
            ],
            'data'=>$lost_time
        ];
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
