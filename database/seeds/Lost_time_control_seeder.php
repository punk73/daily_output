<?php

use Illuminate\Database\Seeder;

class Lost_time_control_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = ['06-07', '07-08', '08-09', '09-10', '10-11','11-12', '12-13','13-14', '14-15','15-16', '16-17','17-18', '18-19', '20-21','22-23'];

        $line_name = [
        	'FA 21',
        	'FA 20',
        	'FA 26',
        	'FA 23',
        	'FA 25',
        ];

        for ($i=0; $i < 50 ; $i++) { 
            # code...
            DB::table('lost_times')->insert([
                
                'line_name'=>$line_name[ ceil( rand(0,4) ) ],
                'shift'=>'A',
                'time'=>$time[ ceil( rand(0,14) ) ],
                'problem'=> str_random(50),
                'lost_time'=>ceil( rand(0,60) ),
                'cause'=> str_random(30),
                'action'=> str_random(50),
                'tanggal'=>'2018-01-10',
                'followed_by'=> str_random(30),
                'users_id'=>1
            ]); 
        }
    }
}
