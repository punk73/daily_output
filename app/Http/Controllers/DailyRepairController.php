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
        $qualityController = app('App\Http\Controllers\QualityController')->index($request); //run Quality Controller data method
        $data = $qualityController['data']->toArray();
        // return $data;
        // return $data;
        //kalau masih kosong, isi.
        if ( $daily_repair->isEmpty() ) {
            
            //kirim daily output sebagi parameter, nanti di fungsi save, di cek, apakah datanya msh up to date atau tidak.
            //jika masih, lewat. jika tidak update. jika tidak ada, tambah.
            $tmp = $this->save($data, $tanggal ); //save data dari qualityController to table daily_repair;
            // return $tmp;
        }else{ //untuk cek apakah ada update
            // return $qualityController;
            //kalau tidak kosong, alias ada isinya
            $tmp = $this->cekUpdate($data, $tanggal, $daily_repair->toArray() );
            // return $tmp;
        }
        // return 'oaudsf';
        $daily_repair = Daily_repair::where('tanggal', $tanggal)->orderBy('line_name')->orderBy('shift','asc');
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

    public function save(array $obj, $tanggal){
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
            $daily_repair->major_problem = '-';
            $daily_repair->TOTAL_REPAIR_QTY = $daily_repair->AFTER_REPAIR_QTY;
            
            
            $daily_repair->save();
        }

        

    }



    public function cekUpdate(array $obj, $tanggal ,array  $currentData ){
        
        //foreach di current data,
        //compare apa curentData[i]->AFTER_REPAIR_QTY == $obj[i]->AFTER_REPAIR_QTY *AFTER_REPAIR_QTY dipilih karena jumlah dr msg code.
        //jika tidak, update
        //jika iya skip.

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
            
            if ( isset( $currentData[$key] ) ) { //jika current data jumlah nya sama dengan $obj
                
                //prepare to edit
                //compare apa nilai nya sama antara $obj->AFTER_REPAIR_QTY dgn $currentData->AFTER_REPAIR_QTY
                if ($value['AFTER_REPAIR_QTY'] != $currentData[$key]->AFTER_REPAIR_QTY  ) { //jika after repair qty nya beda
                    # EDIT

                    $id  = $currentData[$key]->id;
                    $daily_repair = Daily_repair::find($id);
                    // return [$value, $currentData ];
                    // return $daily_repair;
                    foreach ($value as $kunci => $nilai) {
                        # code...
                        if (isset($daily_repair->$kunci)) {
                            # code...
                            $daily_repair->$kunci = $nilai;
                        }
                    }
                    $daily_repair->save();
                }

            }else{ //jika $key tidak ada di $current data

                $daily_repair = new Daily_repair;

                foreach ($value as $kunci => $nilai) {
                    # code...
                    if (isset($daily_repair->kunci)) {
                        # code...
                        $daily_repair->$kunci = $nilai;
                    }
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
