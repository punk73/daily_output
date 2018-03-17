<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(Daily_output_control_seeder::class);
        //$this->call(Lost_time_control_seeder::class);
        $this->call(Default_line_seeder::class);
    }
}
