<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 25 ; $i++) {
        	$no = str_pad($i, 2, '0'); 
        	$name = 'ma'.$no;
        	$password = $name.$name;

        	$user = new User([
        		'name' => $name, 
        		'password' => $password,
        	]);

        	$user->save();
        }
    }
}
