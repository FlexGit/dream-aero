<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatformDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_data', function (Blueprint $table) {
			$table->id();
			$table->integer('location_id')->default(0)->index()->comment('локация');
			$table->integer('flight_simulator_id')->default(0)->index()->comment('авиатренажер');
			$table->date('data_at')->nullable()->comment('дата, на которую представлены данные');
			$table->time('total_up')->nullable()->comment('данные платформы: общее время в поднятом и подвижном состоянии');
			$table->time('user_total_up')->nullable()->comment('данные пользователя: общее время в поднятом и подвижном состоянии');
			$table->time('in_air_no_motion')->nullable()->comment('данные платформы: общее время в поднятом и неподвижном состоянии');
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
        Schema::dropIfExists('platform_data');
    }
}
