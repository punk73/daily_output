<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LostTimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('lost_times', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->string('line_name', 15)->nullable();
            $table->string('shift', 5)->nullable();
            $table->string('time', 15)->nullable(); // 16-17 or 06-07
            $table->text('problem')->nullable();
            $table->float('lost_time', 8, 2)->nullable()->default(0);
            $table->text('cause')->nullable();
            $table->text('action')->nullable();
            $table->date('tanggal')->nullable();
            $table->text('followed_by')->nullable(); //50 menit, 40 menit
            $table->integer('users_id')->nullable();
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
        //
        Schema::dropIfExists('lost_times');
    }
}
