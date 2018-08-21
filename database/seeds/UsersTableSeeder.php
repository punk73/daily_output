<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Default_line;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i <= 25 ; $i++) {
        	$no = $i;//str_pad($i, 2, '0'); 
        	$name = 'ma'.$no;
        	$password = $name.$name;

        	$user = new User([
        		'name' => $name, 
        		'password' => $password,
        	]);

        	$user->save();

            $defaultline = new Default_line();
            $defaultline->user_id = $user->id;
            $defaultline->line_id = $user->id;
            $defaultline->save();

        }
    }
}
