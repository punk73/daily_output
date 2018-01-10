<?php

use Illuminate\Database\Seeder;
//use DB;

class Daily_output_control_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $time = ['06-07', '07-08', '08-09', '09-10', '10-11','11-12', '12-13','13-14', '14-15','15-16', '16-17','17-18', '18-19', '20-21','22-23'];

        for ($i=0; $i < 50 ; $i++) { 
            # code...
            DB::table('daily_outputs')->insert([
                /*'name' => str_random(10),
                'email' => str_random(10).'@gmail.com',
                'password' => bcrypt('secret'),*/
                'line_name'=>6,
                'time'=>$time[ ceil( rand(0,14) ) ],
                'minute'=>60,
                'target_sop'=>ceil( rand(0,100) ),
                'plus_minus'=>-80,
                'lost_hour'=>-0.70,
                'delay_type'=> str_random(30),
                'problem'=> str_random(50),
                'dic'=> str_random(2),
                'action'=> str_random(50),
                'users_id'=>1,
                'shift'=>'A',
                'tanggal'=>'2018-01-10',
                'osc_output'=>'25'
            ]); 
        }

               
    }
}
