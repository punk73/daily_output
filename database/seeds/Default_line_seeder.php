<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Default_line;

class Default_line_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::all();
        foreach ($user as $key => $value) {
        	$user_id = $value->id;
        	$line_id = (int) substr( $value->name , -2 ); //get last two char of string **ma17 = 17 and so on
        	if ($line_id == 0) {
        		$line_id = (int) substr( $value->name , -1 ); //get last onechar of string ** ma2 = 2;	
        	}

        	$default_line = new Default_line;
        	$default_line->user_id = $user_id;
        	$default_line->line_id = $line_id;
        	$default_line->save();

        	var_dump([
        		'user_id' => $user_id,
        		'line_id' => $line_id,
        		'user_name' => $value->name
        	]); 

        }
    }
}
