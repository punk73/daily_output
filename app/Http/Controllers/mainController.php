<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Daily_output;
use DB;
use Carbon\Carbon;

class mainController extends Controller
{
    //
    public function index(Request $req){
    	
        $do = DB::table('daily_outputs');

        //kalau get ada parameter start_date, maka yang muncul adalah between start_date s/d start_date->addWeek()
        if( $req->start_date != null ){

            $do = $do->whereBetween('tanggal', [Carbon::createFromFormat('Y-m-d', $req->start_date ), 
                Carbon::createFromFormat('Y-m-d', $req->start_date )->addWeek() ]  );
        }

        //pagination
        if ( $req->limit !=null && $req->start != null ){
            $do = $do->paginate($req->limit - $req->start );
        }else{
            $do = $do->paginate(15);
        }

        //jika jumlah $do > 0 maka $message = data found, otherwise data not found;
        if (count($do) > 0) {
            $message = 'Data found';
        }else{
            $message = 'Data not found';
        }

    	/*return [
    		'_meta'=>[
					'message'=>$message,
					'count'=> count($do)
				],
    		'data'=> $do->data
    	];

        $do['_meta'] = [
            'message'=>$message,
            'count'=> count($do)
        ];*/

        return $do;
    }
    
    public function show(Request $req){
    	$id = $req->id;
		$do = Daily_output::find($id);
		

		if ($do != null)
		{
			$message = 'Data found';
		}else{
            $message = 'Data not found';
        }



        return [
            '_meta'=>[
                'message'=> $message ,
                'count'=> count($do)
            ],

            'data'=> $do
        ]; 
    }

    public function store(Request $req)
    {
    	# code...
    	$line_name = $req->line_name;
    	$time = $req->time;
    	$minute = $req->minute;
    	$target_sop = $req->target_sop;
    	$osc_output = $req->osc_output;
    	$plus_minus = $req->plus_minus;
    	$lost_hour = $req->lost_hour;
    	$delay_type = $req->delay_type;
    	$problem = $req->problem;
    	$dic = $req->dic; //department in charge
    	$action = $req->action;
    	$users_id = $req->users_id;
    	$shift = $req->shift;
    	$tanggal = $req->tanggal;

    	$Daily_output = new Daily_output;

    	$Daily_output->line_name = $line_name;
    	$Daily_output->time = $time;
    	$Daily_output->minute = $minute;
    	$Daily_output->target_sop = $target_sop;
    	$Daily_output->osc_output = $osc_output;
    	$Daily_output->plus_minus = $plus_minus;
    	$Daily_output->lost_hour = $lost_hour;
    	$Daily_output->delay_type = $delay_type;
    	$Daily_output->problem = $problem;
    	$Daily_output->dic = $dic;
    	$Daily_output->action = $action;
    	$Daily_output->users_id = $users_id;
    	$Daily_output->shift = $shift;
    	$Daily_output->tanggal = $tanggal;

    	$Daily_output->save();

    	return [
    		'_meta'=>[
    			'status'=> "SUCCESS",
    			'userMessage'=> "Data saved",
    			'count'=>count($Daily_output)
    		]
    	];		
    }

    public function delete(Request $req)
    {
    	# code...
    	$Daily_output = Daily_output::find($req->id);
        if( !empty($Daily_output) )
        {
            $Daily_output->delete();
            return [ 
	    		'_meta'=>[
	    			'status'=> "SUCCESS",
	    			'userMessage'=> "Data deleted",
	    			'count'=>count($Daily_output)
	    		]
	    	];
        }
        else
        {
            return [ 
	    		'_meta'=>[
	    			'status'=> "FAILED",
	    			'userMessage'=> "Data not found",
	    			'count'=>count($Daily_output)
	    		]
	    	];
        }

    }

    public function update(Request $req)
    {
    	# code...

        $Daily_output = Daily_output::find($req->id);
        
        $Daily_output->line_name = $req->line_name;
    	$Daily_output->time = $req->time;
    	$Daily_output->minute = $req->minute;
    	$Daily_output->target_sop = $req->target_sop;
    	$Daily_output->osc_output = $req->osc_output;
    	$Daily_output->plus_minus = $req->plus_minus;
    	$Daily_output->lost_hour = $req->lost_hour;
    	$Daily_output->delay_type = $req->delay_type;
    	$Daily_output->problem = $req->problem;
    	$Daily_output->dic = $req->dic;
    	$Daily_output->action = $req->action;
    	$Daily_output->users_id = $req->users_id;
    	$Daily_output->shift = $req->shift;
    	$Daily_output->tanggal = $req->tanggal;

        if ( $Daily_output != null ){
            $Daily_output->save();
            return [ 
                '_meta'=>[
                    'status'=> "SUCCESS",
                    'userMessage'=> "Data updated",
                    'count'=>count($Daily_output)
                ],
                 'data'=>$Daily_output
            ];
        }
    }
}
