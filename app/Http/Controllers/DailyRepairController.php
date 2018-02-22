<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Curl;
use Ixudra\Curl\Facades\Curl;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Daily_repair;
use DB; //database


class DailyRepairController extends Controller
{   
    public function index(Request $request){
        $daily_repair = DB::table('daily_repairs');

        //setup parameter
            if (isset($request->tanggal)) {
                $tanggal = $request->tanggal;
            }else{
                $tanggal = date('Y-m-d');
            }
            
            $daily_repair = $daily_repair->where('tanggal', $tanggal ); //pasti per tanggal hari ini
        //end setup

        $daily_repair = $daily_repair->get();
        

        if ( $daily_repair->isEmpty() ) {
            //get data dari controller QualityController
            $qualityController = app('App\Http\Controllers\QualityController')->data($request); //run Quality Controller data method
            $data = $qualityController['data'];
            $this->save($data, $tanggal ); //save data dari qualityController to table daily_repair;
            $daily_repair = Daily_repair::where('tanggal', $tanggal)->get();
        }

        return [
            'message'=>'OK',
            'count' => count($daily_repair),
            'data'=>    $daily_repair
        ];
    }

    public function save(array $obj, $tanggal){
        //error handler
        if (! is_array($obj) ) {
            return false;
        }

        //doing save        
        foreach ($obj as $key => $value) {
            # code... // isi $key = 0 1 2 3 dst
            
            $daily_repair = null; 
            $daily_repair = new Daily_repair; //buat object daily repair baru untuk disave
            foreach ($value as $kunci => $nilai ) {
                $daily_repair->$kunci = $nilai; //
            }
            //set value that is not exist in Quality controller
            $daily_repair->tanggal = $tanggal;
            $daily_repair->MA = 0;
            $daily_repair->PCB = 0;
            $daily_repair->major_problem = '-';
            $daily_repair->TOTAL_REPAIR_QTY = $daily_repair->AFTER_REPAIR_QTY;
            
            
            $daily_repair->save();
        }

    }

    public function store(Request $request){
    	
        $params = $request->all();
        $daily_repair = new Daily_repair;

        foreach ($params as $key => $value) {
            /*if ( empty( $value ) ) {
                # code...
                continue;
            }*/

            $daily_repair->$key = $value;
        }


        $daily_repair->save();

        return [
            'message' => 'OK',
            'count'=> count($daily_repair),
            'data'=>$daily_repair
        ];
    }

    public function destroy(Request $request){
    	$Daily_repair = Daily_repair::find($request->id);

        if( !empty($Daily_repair) )
        {
            $Daily_repair->delete();
            return [ 
                '_meta'=>[
                    'status'=> "SUCCESS",
                    'userMessage'=> "Data deleted",
                    'count'=>count($Daily_repair)
                ]
            ];
        }
        else
        {
            return [ 
                '_meta'=>[
                    'status'=> "FAILED",
                    'userMessage'=> "Data not found",
                    'count'=>count($Daily_repair)
                ]
            ];
        }
    }

    public function update(Request $request){
    	$daily_repair = Daily_repair::find($request->id);

        $params = $request->all();
        
        if( !empty($daily_repair) )
        {
            foreach ($params as $key => $value) {
                if (isset($daily_repair->$key )) { //cek agar yg $daily_repair tidak ambil column yg tidak tersedia, contoh _dc dr extjs
                    # code...
                    $daily_repair->$key = $value;
                }
            }
            $daily_repair->save();
            
        }

        return [ 
            '_meta'=>[
                'status'=> "SUCCESS",
                'userMessage'=> "Data Updated",
                'count'=>count($daily_repair)
            ],
            'data' => $daily_repair
        ];
    }
}
