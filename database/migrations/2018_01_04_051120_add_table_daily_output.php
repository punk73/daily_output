<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableDailyOutput extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_outputs', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->string('line_name', 15);
            $table->string('time', 15); // 16-17 or 06-07
            $table->integer('minute'); //50 menit, 40 menit
            $table->integer('target_sop');
            $table->integer('osc_output');
            $table->integer('plus_minus');
            $table->float('lost_hour', 8, 2);
            $table->string('delay_type', 80);
            $table->text('problem');
            $table->string('dic', 30); //department in charge
            $table->text('action');
            $table->integer('users_id');
            $table->string('shift', 5);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_outputs', function (Blueprint $table) {
            //
        });
    }
}
