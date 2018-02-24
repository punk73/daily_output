<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDailyRepair extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('daily_repairs', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->string('line_name', 15)->nullable();
            $table->string('shift', 5)->nullable();
            $table->integer('users_id')->nullable();
            $table->date('tanggal')->nullable();
            
            $table->integer('SMT')->nullable();
            $table->integer('PCB_CODE')->nullable();
            $table->integer('DESIGN_CODE')->nullable();
            $table->integer('MECHANISM_CODE')->nullable();
            $table->integer('ELECTRICAL_CODE')->nullable();
            $table->integer('MECHANICAL_CODE')->nullable();
            $table->integer('FINAL_ASSY_CODE')->nullable();
            $table->integer('OTHERS_CODE')->nullable();
            $table->integer('AFTER_REPAIR_QTY')->nullable();

            $table->integer('MA')->nullable();
            $table->integer('PCB')->nullable();
            $table->integer('TOTAL_REPAIR_QTY')->nullable();

            $table->text('major_problem')->nullable();

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
        Schema::dropIfExists('daily_repairs');
    }
}
