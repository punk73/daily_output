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

        if( $req->tanggal != null ){

            $do = $do->where('tanggal','=', $req->tanggal ) ;
            // return $req->tanggal;
        }

        //pagination
        if ( $req->limit !=null){
            $do = $do->paginate($req->limit);
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

        $additional_message = collect(['_meta'=> [
                    'message'=>$message,
                    'count'=> count($do)
                ] ]);
        //adding additional message
        $do = $additional_message->merge($do);
        //$do is object, need to changes to array first!
        $do = $do->toArray();

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

    	$Daily_output = new Daily_output;
    	$Daily_output->line_name = $req->input('line_name', null);
    	$Daily_output->time = $req->input('time', null);
    	$Daily_output->minute = $req->input('minute', null);
    	$Daily_output->target_sop = $req->input('target_sop', null);
    	$Daily_output->osc_output = $req->input('osc_output', null);
    	$Daily_output->plus_minus = $req->input('plus_minus', null);
    	$Daily_output->lost_hour = $req->input('lost_hour', null);
        //new column
        $Daily_output->board_delay = $req->input('board_delay', null);
        $Daily_output->part_delay = $req->input('part_delay', null);
        $Daily_output->eqp_trouble = $req->input('eqp_trouble', null);
        $Daily_output->quality_problem_delay = $req->input('quality_problem_delay', null);
        $Daily_output->bal_problem = $req->input('bal_problem', null);
        $Daily_output->others = $req->input('others', null);
        $Daily_output->support = $req->input('support', null);
        $Daily_output->change_model = $req->input('change_model', null);
        //end new column
    	$Daily_output->delay_type = $req->input('delay_type', null);
        $Daily_output->problem = $req->input('problem', null);
    	$Daily_output->dic = $req->input('dic', null);
    	$Daily_output->action = $req->input('action', null);
    	$Daily_output->users_id = $req->input('users_id', null);
    	$Daily_output->shift = $req->input('shift', null);
    	$Daily_output->tanggal = $req->input('tanggal', null);

        if(!$Daily_output->minute){$Daily_output->minute = null; }
        if(!$Daily_output->target_sop){$Daily_output->target_sop = null; }
        if(!$Daily_output->osc_output){$Daily_output->osc_output = null; }
        if(!$Daily_output->plus_minus){$Daily_output->plus_minus = null; }
        if(!$Daily_output->lost_hour){$Daily_output->lost_hour = null; }
        if(!$Daily_output->board_delay){$Daily_output->board_delay = null; }
        if(!$Daily_output->part_delay){$Daily_output->part_delay = null; }
        if(!$Daily_output->eqp_trouble){$Daily_output->eqp_trouble = null; }
        if(!$Daily_output->quality_problem_delay){$Daily_output->quality_problem_delay = null; }
        if(!$Daily_output->bal_problem){$Daily_output->bal_problem = null; }
        if(!$Daily_output->others){$Daily_output->others = null; }
        if(!$Daily_output->support){$Daily_output->support = null; }
        if(!$Daily_output->change_model){$Daily_output->change_model = null; }
        if(!$Daily_output->users_id){$Daily_output->users_id = null; }

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
        //new column
        if( !empty($req->board_delay) )
        {$Daily_output->board_delay = $req->board_delay;}
        else {$Daily_output->board_delay = 0;}
        
        if( !empty($req->part_delay) )
        {$Daily_output->part_delay = $req->part_delay;}
        else{$Daily_output->part_delay = 0;}

        if( !empty($req->eqp_trouble) )
        {$Daily_output->eqp_trouble = $req->eqp_trouble;}
        else{$Daily_output->eqp_trouble = 0;}

        if( !empty($req->quality_problem_delay) )
        {$Daily_output->quality_problem_delay = $req->quality_problem_delay;}
        else{$Daily_output->quality_problem_delay = 0;}

        if( !empty($req->bal_problem) )
        {$Daily_output->bal_problem = $req->bal_problem;}
        else{$Daily_output->bal_problem = 0;}
        
        if( !empty($req->others) )
        {$Daily_output->others = $req->others;} 
        else{$Daily_output->others = 0;}
        
        if( !empty($req->support) )
        {$Daily_output->support = $req->support;}
        else{$Daily_output->support = 0;}

        
        if( !empty($req->change_model) )
        {$Daily_output->change_model = $req->change_model;}
        else{$Daily_output->change_model = 0;}

        //end new column
    	$Daily_output->delay_type = $req->delay_type;
    	$Daily_output->problem = $req->problem;
    	$Daily_output->dic = $req->dic;
    	$Daily_output->action = $req->action;
    	$Daily_output->users_id = $req->users_id;
    	$Daily_output->shift = $req->shift;
    	$Daily_output->tanggal = $req->tanggal;

        //error handler
            if(!$Daily_output->minute){$Daily_output->minute = null; }
            if(!$Daily_output->target_sop){$Daily_output->target_sop = null; }
            if(!$Daily_output->osc_output){$Daily_output->osc_output = null; }
            if(!$Daily_output->plus_minus){$Daily_output->plus_minus = null; }
            if(!$Daily_output->lost_hour){$Daily_output->lost_hour = null; }
            if(!$Daily_output->board_delay){$Daily_output->board_delay = null; }
            if(!$Daily_output->part_delay){$Daily_output->part_delay = null; }
            if(!$Daily_output->eqp_trouble){$Daily_output->eqp_trouble = null; }
            if(!$Daily_output->quality_problem_delay){$Daily_output->quality_problem_delay = null; }
            if(!$Daily_output->bal_problem){$Daily_output->bal_problem = null; }
            if(!$Daily_output->others){$Daily_output->others = null; }
            if(!$Daily_output->support){$Daily_output->support = null; }
            if(!$Daily_output->change_model){$Daily_output->change_model = null; }
            if(!$Daily_output->users_id){$Daily_output->users_id = null; }
        //END ERROR HANDLER

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
