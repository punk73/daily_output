<?php

use Illuminate\Database\Seeder;
use App\User;

class Defaultlines extends Seeder
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
        	$id = $value['id'];
        }
    }
}
