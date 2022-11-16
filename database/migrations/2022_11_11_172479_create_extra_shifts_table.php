<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_shifts', function (Blueprint $table) {
			$table->id();
			$table->integer('user_id')->default(0)->index()->comment('пользователь');
			$table->integer('location_id')->default(0)->index()->comment('локация');
			$table->integer('flight_simulator_id')->default(0)->index()->comment('авиатренажер');
			$table->date('period')->nullable()->comment('период');
			$table->timestamps();
			$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extra_shifts');
    }
}
