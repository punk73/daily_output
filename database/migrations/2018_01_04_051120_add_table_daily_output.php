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
            $table->string('line_name', 15)->nullable();
            $table->string('time', 15)->nullable(); // 16-17 or 06-07
            $table->integer('minute')->nullable(); //50 menit, 40 menit
            $table->integer('target_sop')->nullable();
            $table->integer('osc_output')->nullable();
            $table->integer('plus_minus')->nullable();
            $table->float('lost_hour', 8, 2)->nullable();

            $table->float('board_delay', 8, 2)->nullable()->default(0);
            $table->float('part_delay', 8, 2)->nullable()->default(0);
            $table->float('eqp_trouble', 8, 2)->nullable()->default(0);
            $table->float('quality_problem_delay', 8, 2)->nullable()->default(0);
            $table->float('bal_problem', 8, 2)->nullable()->default(0);
            $table->float('others', 8, 2)->nullable()->default(0);
            $table->float('support', 8, 2)->nullable()->default(0);
            $table->float('change_model', 8, 2)->nullable()->default(0);
            

            $table->string('delay_type', 80)->nullable();
            $table->text('problem')->nullable();
            $table->string('dic', 30)->nullable(); //department in charge
            $table->text('action')->nullable();
            $table->integer('users_id')->nullable();
            $table->string('shift', 5)->nullable();
            $table->date('tanggal')->nullable();
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
