<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Quality;
use DB;

class QualityController extends Controller
{
    public function index(Request $request){
        $Quality = Quality::select(DB::raw(
            "DATE001, 
            MONTH001,
            YEAR001,
            LINE001,
            SHIFT001,
            sum( IM_CODE ) as IM_CODE, 
            sum(PCB_CODE) as PCB_CODE,
            sum(DESIGN_CODE) as DESIGN_CODE,
            sum(MECHANISM_CODE) as MECHANISM_CODE,
            sum(ELECTRICAL_CODE) as ELECTRICAL_CODE, 
            sum(MECHANICAL_CODE) as MECHANICAL_CODE,
            sum(FINAL_ASSY_CODE) as FINAL_ASSY_CODE,
            sum(OTHERS_CODE) as OTHERS_CODE,
            sum(QTY_REJECT) as QTY_REJECT,
            sum(QTY_REJECT) as AFTER_REPAIR_QTY"

        ))->groupBy('LINE001')
          ->groupBy('SHIFT001')
          // ->groupBy('DEFECTIVE_CAUSE')
          // ->groupBy('PLACE_DISPOSAL')
          // ->groupBy('SYMPTOM')
          ->groupBy('DATE001')
          ->groupBy('MONTH001')
          ->groupBy('YEAR001');



        //setup parameter
            if (isset($request->tanggal)) {
                # code...
                $tanggal = $request->tanggal;
                $tmp = explode('-', $tanggal); //pisah string jadi array 
                
                $request->year = $tmp[0];
                $request->month = $tmp[1];
                $request->date = $tmp[2];

            }

            if (isset($request->date)) {
                # code...
                $Quality = $Quality->where('DATE001', $request->date );
            }else{
                $request->date = date('d');
                $Quality = $Quality->where('DATE001', $request->date  );
            }


            if (isset($request->month)) {
                # code...
                $Quality = $Quality->where('MONTH001', $request->month );
            }else{
                $request->month = date('m');
                $Quality = $Quality->where('MONTH001', $request->month );
            }

            if (isset($request->year)) {
                # code...
                $Quality = $Quality->where('YEAR001', $request->year );
            }else{
                $request->year = date('Y');
                $Quality = $Quality->where('YEAR001', $request->year );
            }

            if (isset($request->shift)) {
                # code...
                $Quality = $Quality->where('SHIFT001', $request->shift );
            }

            if (isset($request->line_name)) {
                # code...
                $Quality = $Quality->where('LINE001', $request->line_name );
            }
        //end setup

        $Quality = $Quality->orderBy('LINE001')->orderBy('SHIFT001', 'asc')->get();    
        
        // return $Quality;
        // return $key;

        return [
            'message' => 'OK',
            'date'=> $request->date . $request->month . $request->year ,
            'count' => count($Quality),
            // 'data' => $Quality,
            'data' => $Quality
        ];
        //return (array) $Quality;
    }

    public function data(Request $request){ //ini untuk extract data yang belum bener
        $data = $this->index($request);
        //return $data;
        $result =[];
        foreach ($data['line'] as $key => $value) {
            foreach ($value as $kunci => $val) {
                $val['shift'] = str_replace(' ', '', $kunci );
                $val['line_name'] = str_replace(' ', '', $key );
                $result[] = $val;
            }

        }

        return[
            'message'   => 'OK',
            'count'     => count($result),
            'data'      => $result
        ];
    }

    public function getDIC(Request $request){
        $data = $this->index($request);
        $SMT = 0;
        $PCB_CODE = 0;
        $DESIGN_CODE = 0;
        $MECHANISM_CODE = 0;
        $ELECTRICAL_CODE = 0;
        $MECHANICAL_CODE = 0;
        $FINAL_ASSY_CODE = 0;
        $OTHERS_CODE = 0;

        // return $data;

        foreach ($data['data'] as $key => $value) {
            # code...
            $SMT = $SMT + $value['SMT'];
            $PCB_CODE = $PCB_CODE + $value['PCB_CODE'];
            $DESIGN_CODE = $DESIGN_CODE + $value['DESIGN_CODE'];
            $MECHANISM_CODE = $MECHANISM_CODE + $value['MECHANISM_CODE'];
            $ELECTRICAL_CODE = $ELECTRICAL_CODE + $value['ELECTRICAL_CODE'];
            $MECHANICAL_CODE = $MECHANICAL_CODE + $value['MECHANICAL_CODE'];
            $FINAL_ASSY_CODE = $FINAL_ASSY_CODE + $value['FINAL_ASSY_CODE'];
            $OTHERS_CODE = $OTHERS_CODE + $value['OTHERS_CODE'];
        }

        $total = [
            "SMT"=> $SMT,
            "PCB_CODE"=> $PCB_CODE,
            "DESIGN_CODE"=> $DESIGN_CODE,
            "MECHANISM_CODE"=> $MECHANISM_CODE,
            "ELECTRICAL_CODE"=> $ELECTRICAL_CODE,
            "MECHANICAL_CODE"=> $MECHANICAL_CODE,
            "FINAL_ASSY_CODE"=> $FINAL_ASSY_CODE,
            "OTHERS_CODE"=> $OTHERS_CODE
        ];

        $result = [];
        $tmp = [];

        foreach ($total as $key => $value) {
            # code...
            if ($value != 0) {
                # code...
                $tmp['name'] = $key;
                $tmp['total'] = $value;
                $result[] = $tmp;
            }
        }

        return [
            'message' => 'OK',
            'count' => count($result),
            'data'=>$result
        ];
    }
}
