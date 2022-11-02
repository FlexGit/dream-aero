<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
			$table->id();
			$table->date('scheduled_at')->nullable()->comment('дата записи');
			$table->string('schedule_type', 255)->nullable()->comment('тип записи');
			$table->integer('user_id')->default(0)->index()->comment('пользователь');
			$table->integer('location_id')->default(0)->index()->comment('локация');
			$table->integer('flight_simulator_id')->default(0)->index()->comment('авиатренажер');
			$table->time('start_at')->nullable()->comment('время начала события');
			$table->time('stop_at')->nullable()->comment('время окончания события');
			$table->text('comment')->nullable()->comment('комментарий');
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
        Schema::dropIfExists('schedules');
    }
}
