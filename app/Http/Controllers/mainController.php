<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Daily_output;
use App\User;
use DB;
use Carbon\Carbon;
use Excel;

class mainController extends Controller
{
    //
    public function index(Request $req){
    	
        $do = DB::table('daily_outputs');

        //kalau get ada parameter start_date, maka yang muncul adalah between start_date s/d start_date->addWeek()

        // parameter tanggal
        if( $req->start_date != null ){

            $do = $do->whereBetween('tanggal', [Carbon::createFromFormat('Y-m-d', $req->start_date ), 
                Carbon::createFromFormat('Y-m-d', $req->start_date )->addWeek() ]  );
        }

        /*parameter tanggal*/
        if( $req->tanggal != null ){

            $do = $do->where('tanggal','=', $req->tanggal ) ;
            // return $req->tanggal;
        }

        if ($req->shift != null) {
            # code...
            $do = $do->where('shift', '=', $req->shift);
        }

        if ($req->line_name != null) {
            # code...
            $do = $do->where('line_name', '=', $req->line_name);
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

    public function download5 (Request $req){
        $do = DB::table('daily_outputs');

        $do = $do->get();
        
        $fname = 'DAILY OUTPUT CONTROL SHEET.csv';

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=$fname");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        $fp = fopen("php://output", "w");
        
        $headers = 'Line,Time,minute,tgt SOP,OSC Output,+/-,Lost Hour,Board Delay,Part Delay,EQP Trouble,Quality Prob,Bal. Prob,OTHERS,Support (must Zero),Change model,Problem, DIC,action,Leader'."\n";

        fwrite($fp,$headers);

        foreach ($do as $key => $value) {
            # code...
            $username = User::find($value->users_id);
            $username = $username['name'];
             // return $username;
            $row = [
                $value->line_name,
                $value->time,
                $value->minute,
                $value->target_sop,
                $value->osc_output,
                $value->plus_minus,
                $value->lost_hour,
                $value->board_delay,
                $value->part_delay,
                $value->eqp_trouble,
                $value->quality_problem_delay,
                $value->bal_problem,
                $value->others,
                $value->support,
                $value->change_model,
                $value->problem,
                $value->dic,
                $value->action,
                $username    
            ];
            
            fputcsv($fp, $row);
        }

        fclose($fp);    
    }

    public function download (Request $req)
    {
        # code...
        $do = DB::table('daily_outputs');
        
        if($req->tanggal != null){
            $do= $do->where('tanggal','=', $req->tanggal);
        }

        $do = $do->get();


        $data = [];
        foreach ($do as $key => $value) {
            # code...
            $username = User::find($value->users_id);
            $username = $username['name'];

            $row = [
                "Line"=> $value->line_name,
                "Time"=> $value->time,
                "minute"=> $value->minute,
                "tgt SOP"=> $value->target_sop,
                "OSC Output"=> $value->osc_output,
                "+ / -"=> $value->plus_minus,
                "Lost Hour"=> $value->lost_hour,
                "Board Delay"=> $value->board_delay,
                "Part Delay"=> $value->part_delay,
                "EQP Trouble"=> $value->eqp_trouble,
                "Quality Prob"=> $value->quality_problem_delay,
                "Bal Prob"=> $value->bal_problem,
                "OTHERS"=> $value->others,
                "Support (must Zero)"=> $value->support,
                "Change model"=> $value->change_model,
                "Problem"=> $value->problem,
                "DIC"=> $value->dic,
                "action"=> $value->action,
                "Leader"=> $username    
            ];

            $data[] = $row;
        }
        $range = 'A1:S'.(count($data)+1);
        // return $data;
        Excel::create('DAILY OUTPUT CONTROL SHEET', function($excel  ) use ($data, $range ){
            // Call writer methods header_remove()
            $excel->sheet('daily output', function($sheet) use($data, $range) {
                
                /*setting default font style*/
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Calibri',
                        'size'      =>  8
                    )
                ));
                

                /*write data to excel*/
                $sheet->fromArray($data)
                      ->setBorder($range , 'thin');
                $sheet->row(1, function($row) { 
                    $row->setBackground('#CCCCCC');
                    $row->setFont(array(
                        'family'     => 'Calibri',
                        'size'       => '8',
                        'bold'       =>  true
                    ));
                });
                
                //set background color for column delay type
                $sheet->cells('I1:P1', function($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#f4a445');
                    $cells->setTextRotation(90);
                })->setWidth(array(
                    'I' => 5 ,
                    'J' => 5 ,
                    'K' => 5 ,
                    'L' => 5 ,
                    'M' => 5 ,
                    'N' => 5 ,
                    'O' => 5 ,
                    'P' => 5 ,
                ));

                // freeze column header
                $sheet->freezeFirstRow();
            });
        })->download('xls');

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
        if(!$Daily_output->board_delay){$Daily_output->board_delay = 0; }
        if(!$Daily_output->part_delay){$Daily_output->part_delay = 0; }
        if(!$Daily_output->eqp_trouble){$Daily_output->eqp_trouble = 0; }
        if(!$Daily_output->quality_problem_delay){$Daily_output->quality_problem_delay = 0; }
        if(!$Daily_output->bal_problem){$Daily_output->bal_problem = 0; }
        if(!$Daily_output->others){$Daily_output->others = 0; }
        if(!$Daily_output->support){$Daily_output->support = 0; }
        if(!$Daily_output->change_model){$Daily_output->change_model = 0; }
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
            if(!$Daily_output->board_delay){$Daily_output->board_delay = 0; }
            if(!$Daily_output->part_delay){$Daily_output->part_delay = 0; }
            if(!$Daily_output->eqp_trouble){$Daily_output->eqp_trouble = 0; }
            if(!$Daily_output->quality_problem_delay){$Daily_output->quality_problem_delay = 0; }
            if(!$Daily_output->bal_problem){$Daily_output->bal_problem = 0; }
            if(!$Daily_output->others){$Daily_output->others = 0; }
            if(!$Daily_output->support){$Daily_output->support = 0; }
            if(!$Daily_output->change_model){$Daily_output->change_model = 0; }
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
