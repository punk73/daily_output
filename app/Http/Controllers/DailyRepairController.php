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
            // return $qualityController;
            foreach ($qualityController['data'] as $key => $value) {
                # code...
                //prepare variable
                

                //Input $this->Store()
                $tmp = $this->save($value);
                return $tmp;
            }
        }

        return [
            'message'=>'OK',
            'count' => count($daily_repair),
            'data'=>    $daily_repair
        ];
    }

    public function save(array $obj){
        //doing save
        
        //error handler
        if (! is_array($obj) ) {
            return false;
        }

        foreach ($obj as $key => $value) {
            # code... // isi $key = 0 1 2 3 dst
            $daily_repair = new Daily_repair; //buat object daily repair baru untuk disave
            foreach ($value as $kunci => $nilai ) {
                # code...
                $daily_repair->$kunci = $nilai; //
            }


        }
        //return obj result
        return $obj;
    }

    public function store(Request $request){
    	
    }

    public function destroy(Request $request){
    	
    }

    public function update(Request $request){
    	
    }
}
