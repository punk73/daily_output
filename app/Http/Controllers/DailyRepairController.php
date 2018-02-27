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

            if ( isset($request->line_name) && $request->line_name != "" ) { //di set dan tidak empty string
                $daily_repair = $daily_repair->where('line_name', $request->line_name ) ;
            }
        //end setup

        $daily_repair = $daily_repair->get();
        $qualityController = app('App\Http\Controllers\QualityController')->index($request); //run Quality Controller data method
        $data = $qualityController['data']->toArray();
        // return $data;
        //kalau masih kosong, isi.
        if ( $daily_repair->isEmpty() ) {
            $tmp = $this->save($data, $tanggal ); //save data dari qualityController to table daily_repair;
            // return $tmp;
        }else{ //untuk cek apakah ada update
            // return $qualityController;
            //kalau tidak kosong, alias ada isinya

            $tmp = $this->cekUpdate($data, $tanggal, $daily_repair->toArray() );
            
            // return $tmp;
            // return 'ada';
        }
        // return 'oaudsf';
        $daily_repair = Daily_repair::where('tanggal', $tanggal)->orderBy('line_name')->orderBy('shift','asc');

        if ( isset($request->line_name) && $request->line_name != "" ) { //di set dan tidak empty string
            # code...
            $daily_repair = $daily_repair->where('line_name', $request->line_name ) ;
        }

        //pagination
        if ( $request->limit !=null){
            $daily_repair = $daily_repair->paginate($request->limit);
        }else{
            $daily_repair = $daily_repair->paginate(15);
        }

        // return $daily_repair;
        //jika jumlah $do > 0 maka $message = data found, otherwise data not found;
        if (count($daily_repair) > 0) {
            $message = 'Data found';
        }else{
            $message = 'Data not found';
        }

        $additional_message = collect(['_meta'=> [
            'message'=>$message,
            'count'=> count($daily_repair)
        ] ]);
        //adding additional message
        $daily_repair = $additional_message->merge($daily_repair);
        $daily_repair = $daily_repair->toArray();

        return $daily_repair;
    }

    public function getPerLine(Request $request){
        $this->index($request); //biar data nya up to date
        $daily_repair = DB::table('Daily_repairs');
        //setup parameter
            if (isset($request->tanggal)) {
                $tanggal = $request->tanggal;
            }else{
                $tanggal = date('Y-m-d');
            }
            
            $daily_repair = $daily_repair->where('tanggal', $tanggal ); //pasti per tanggal hari ini
        //end setup

        $daily_repair = $daily_repair
                        ->select(DB::raw('CAST(sum(AFTER_REPAIR_QTY) as INT ) as AFTER_REPAIR_QTY,
                         CAST(sum(TOTAL_REPAIR_QTY) as INT ) as TOTAL_REPAIR_QTY ,
                         tanggal,
                         line_name '))
                        ->where('tanggal', $tanggal )
                        ->groupBy('line_name')
                        ->groupBy('tanggal')
                        ->get();

        return 
        [
            'message' => 'OK',
            'count' => count($daily_repair), 
            'data'=>   $daily_repair
        ];
    }

    public function save(array $obj, $tanggal){ //kalau belum ada, masuk kesini
        //error handler
        if (! is_array($obj) ) {
            return false;
        }

        //hapus semua dily repair per tanggal $tanggal, lalu isi ulang. *ga bisa deng, nanti kalau aada yang udah diupdate gmn?
        //doing save        
        foreach ($obj as $key => $value) {
            # code... // isi $key = 0 1 2 3 dst
            $value['shift'] = str_replace(" ", "", $value["SHIFT001"] );
            $value['line_name'] = str_replace(" ", "", $value["LINE001"] );
            $value['SMT'] = str_replace(" ", "", $value["IM_CODE"] );

            // hapus property yg tidak perlu
            unset($value["DATE001"]);
            unset($value["MONTH001"]);
            unset($value["YEAR001"]);
            unset($value["LINE001"]);
            unset($value["SHIFT001"]);
            unset($value["QTY_REJECT"]);
            unset($value["IM_CODE"]);
                    // return $value;
            $daily_repair = null; 
            
            // return $value;
            //disini cek apa harus buat baru, atau Daily_repair::find();

            $daily_repair = new Daily_repair; //buat object daily repair baru untuk disave
            
            foreach ($value as $kunci => $nilai ) {
                // return $daily_repair;

                $daily_repair->$kunci = $nilai; //
                
            }
            //set value that is not exist in Quality controller
            $daily_repair->tanggal = $tanggal;
            $daily_repair->MA = 0;
            $daily_repair->PCB = 0;
            //$daily_repair->major_problem = '-'; //karena skrd dari qualities sdh pnya major problem
            $daily_repair->TOTAL_REPAIR_QTY = $daily_repair->AFTER_REPAIR_QTY;
            
            
            $daily_repair->save();
        }
    }

    public function cekUpdate(array $obj, $tanggal ,array  $currentData ){
        //foreach di $obj
            //compare apa nilai nya sama antara $obj->AFTER_REPAIR_QTY dgn $currentData->AFTER_REPAIR_QTY
            //jika ya, skip.
            //jika tidak, update.
            //jika curentData[i] == undefined (belum terinput di saat input pertama)
            //input.
        //
        foreach ($obj as $key => $value) {
            // return $value['AFTER_REPAIR_QTY'];
            // return $currentData[$key]->AFTER_REPAIR_QTY;
            $value['shift'] = str_replace(" ", "", $value["SHIFT001"] );
            $value['line_name'] = str_replace(" ", "", $value["LINE001"] );
            $value['SMT'] = str_replace(" ", "", $value["IM_CODE"] );
            // $value['tanggal'] = $value["YEAR001"] . $value["MONTH001"] . $value["DATE001"]; 
            //prepare to edit
            unset($value["DATE001"]);
            unset($value["MONTH001"]);
            unset($value["YEAR001"]);
            unset($value["LINE001"]);
            unset($value["SHIFT001"]);
            unset($value["QTY_REJECT"]);
            unset($value["IM_CODE"]);

            if ( isset( $currentData[$key] ) ) { //jika current data jumlah nya sama dengan $obj
                // return $value;
                //compare apa nilai nya sama antara $obj->AFTER_REPAIR_QTY dgn $currentData->AFTER_REPAIR_QTY
                if ($value['AFTER_REPAIR_QTY'] != $currentData[$key]->AFTER_REPAIR_QTY  ) { //jika after repair qty nya beda
                    # EDIT

                    $id  = $currentData[$key]->id;
                    $daily_repair = Daily_repair::find($id);
                    // return $daily_repair;
                    
                    /*foreach ($value as $kunci => $nilai) {
                        $daily_repair->$kunci = $nilai;
                    }*/
                    $daily_repair->line_name = $value['line_name'];
                    $daily_repair->shift = $value['shift'];
                    // $daily_repair->tanggal = $value['tanggal'];
                    $daily_repair->SMT = $value['SMT'];
                    $daily_repair->PCB_CODE = $value['PCB_CODE'];
                    $daily_repair->DESIGN_CODE = $value['DESIGN_CODE'];
                    $daily_repair->MECHANISM_CODE = $value['MECHANISM_CODE'];
                    $daily_repair->ELECTRICAL_CODE = $value['ELECTRICAL_CODE'];
                    $daily_repair->MECHANICAL_CODE = $value['MECHANICAL_CODE'];
                    $daily_repair->FINAL_ASSY_CODE = $value['FINAL_ASSY_CODE'];
                    $daily_repair->OTHERS_CODE = $value['OTHERS_CODE'];
                    $daily_repair->AFTER_REPAIR_QTY = $value['AFTER_REPAIR_QTY'];
                    $daily_repair->MA = $currentData[$key]->MA;
                    $daily_repair->PCB = $currentData[$key]->PCB;
                    $daily_repair->TOTAL_REPAIR_QTY = ( $value['AFTER_REPAIR_QTY'] + $currentData[$key]->MA +  
                        $currentData[$key]->PCB ) ;
                    $daily_repair->major_problem = $currentData[$key]->major_problem;

                    // return $daily_repair;
                    $daily_repair->save();
                    // return $daily_repair;
                }

            }else{ //jika $key tidak ada di $current data

                // return $value;
                $daily_repair = new Daily_repair;

                foreach ($value as $kunci => $nilai) {

                    $daily_repair->$kunci = $nilai;
                }
                // return $value;
                //set value that is not exist in Quality controller
                $daily_repair->tanggal = $tanggal;
                $daily_repair->MA = 0;
                $daily_repair->PCB = 0;
                $daily_repair->major_problem = $value['major_problem'];
                $daily_repair->TOTAL_REPAIR_QTY = $value['AFTER_REPAIR_QTY'];

                $daily_repair->save();
            }
        }

        // return 'akhir';
        return [
            'data' => $obj,
            'tanggal' =>$tanggal,
            'currentData' => $currentData
        ];
    }

    public function store(Request $request){
    	
        $params = $request->all();
        $daily_repair = new Daily_repair;

        $daily_repair->line_name = $request->line_name;
        $daily_repair->shift = $request->shift;
        $daily_repair->users_id = $request->users_id;
        $daily_repair->tanggal = $request->tanggal;
        $daily_repair->SMT = $request->SMT;
        $daily_repair->PCB_CODE = $request->PCB_CODE;
        $daily_repair->DESIGN_CODE = $request->DESIGN_CODE;
        $daily_repair->MECHANISM_CODE = $request->MECHANISM_CODE;
        $daily_repair->ELECTRICAL_CODE = $request->ELECTRICAL_CODE;
        $daily_repair->MECHANICAL_CODE = $request->MECHANICAL_CODE;
        $daily_repair->FINAL_ASSY_CODE = $request->FINAL_ASSY_CODE;
        $daily_repair->OTHERS_CODE = $request->OTHERS_CODE;
        $daily_repair->AFTER_REPAIR_QTY = $request->AFTER_REPAIR_QTY;        
        $daily_repair->MA = $request->MA;
        $daily_repair->PCB = $request->PCB;
        $daily_repair->TOTAL_REPAIR_QTY = $request->TOTAL_REPAIR_QTY;
        $daily_repair->major_problem = $request->major_problem;


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

    public function show($id){
        $daily_repair = Daily_repair::find($id);

        if ( !$daily_repair ) {
            $message = 'Data not found';
        }else{
            $message = 'Data Found';
        }

        return [
            'message' => $message,
            'count' => count($daily_repair),
            'data' => $daily_repair
        ];
    }

    public function update(Request $request){
    	$daily_repair = Daily_repair::find($request->id);
        // $daily_repair = $daily_repair->toArray();
        // $params = $request->all();

        // return $params;
        
        if( !empty($daily_repair) )
        {   
            $daily_repair->line_name = $request->line_name;
            $daily_repair->shift = $request->shift;
            $daily_repair->users_id = $request->users_id;
            $daily_repair->tanggal = $request->tanggal;
            $daily_repair->SMT = $request->SMT;
            $daily_repair->PCB_CODE = $request->PCB_CODE;
            $daily_repair->DESIGN_CODE = $request->DESIGN_CODE;
            $daily_repair->MECHANISM_CODE = $request->MECHANISM_CODE;
            $daily_repair->ELECTRICAL_CODE = $request->ELECTRICAL_CODE;
            $daily_repair->MECHANICAL_CODE = $request->MECHANICAL_CODE;
            $daily_repair->FINAL_ASSY_CODE = $request->FINAL_ASSY_CODE;
            $daily_repair->OTHERS_CODE = $request->OTHERS_CODE;
            $daily_repair->AFTER_REPAIR_QTY = $request->AFTER_REPAIR_QTY;   
            $daily_repair->MA = $request->MA;
            $daily_repair->PCB = $request->PCB;
            $daily_repair->TOTAL_REPAIR_QTY = $request->TOTAL_REPAIR_QTY;
            $daily_repair->major_problem = $request->major_problem;
            
            
            $daily_repair->save();

            $status = 'SUCCESS';
            $message = 'Data Updated';
        }else{
            $status = 'FAILED';
            $message = 'Data not Updated';
        }

        return [ 
            '_meta'=>[
                'status'=> $status,
                'userMessage'=> $message,
                'count'=>count($daily_repair)
            ],
            'data' => $daily_repair
        ];
    }

    public function getPerMonth(Request $request){


        $daily_repair = Daily_repair::select(DB::raw(
            "sum(AFTER_REPAIR_QTY) as AFTER_REPAIR_QTY,
             sum(TOTAL_REPAIR_QTY) as TOTAL_REPAIR_QTY,
             tanggal
              "));


        if (isset($request->month) ) {
            $month = $request->month;
        }else{
            $month = date('m');
        }

        if (isset($request->year) && $request->year != "" ) {
            $year = $request->year;
        }else{
            $year = date('Y');
        }

        // $arrayLineName = [19]; //[18,19,20,21,22,23,24,25];

        $daily_repair = $daily_repair->whereMonth('tanggal', '=', $month );
        $daily_repair = $daily_repair->whereYear('tanggal', '=', $year );

        // $daily_repair = $daily_repair->whereIn('line_name', $arrayLineName);
        $daily_repair = $daily_repair->orderBy('tanggal')
        ->groupBy('tanggal')
        ->get();

        foreach ($daily_repair as $key => $value) {
            $value['AFTER_REPAIR_QTY'] = (int) $value['AFTER_REPAIR_QTY'];
            $value['TOTAL_REPAIR_QTY'] = (int) $value['TOTAL_REPAIR_QTY'];
            $value['name'] = $value['tanggal']. "tt" ;
            
        }



        return [
            'status' => 'OK',
            'month' => $month,
            'year' => $year,
            'count' => count($daily_repair),
            'data'=>    $daily_repair
        ];
    }

   

    
}
