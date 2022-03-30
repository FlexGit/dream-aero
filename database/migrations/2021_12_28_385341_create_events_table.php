<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
			$table->string('event_type')->comment('тип события');
			$table->integer('contractor_id')->default(0)->index()->comment('контрагент');
			$table->integer('deal_id')->default(0)->index()->comment('сделка');
			$table->integer('deal_position_id')->default(0)->index()->comment('позиция сделки');
			$table->integer('city_id')->default(0)->index()->comment('город, в котором будет осуществлен полет');
			$table->integer('location_id')->default(0)->index()->comment('локация, на которой будет осуществлен полет');
			$table->integer('flight_simulator_id')->default(0)->index()->comment('авиатренажер, на котором будет осуществлен полет');
			$table->integer('user_id')->default(0)->index()->comment('пользователь');
			$table->timestamp('start_at')->nullable()->comment('дата и время начала события');
			$table->timestamp('stop_at')->nullable()->comment('дата и время окончания события');
			$table->integer('extra_time')->default(0)->comment('дополнительное время');
			$table->boolean('is_repeated_flight')->default(false)->index()->comment('признак повторного полета');
			$table->boolean('is_unexpected_flight')->default(false)->index()->comment('признак спонтанного полета');
			$table->boolean('is_test_flight')->default(false)->index()->comment('признак тестового полета');
			$table->string('notification_type')->nullable()->comment('способ оповещения контрагента о полете');
			$table->tinyInteger('pilot_assessment')->default(0)->comment('оценка пилота');
			$table->tinyInteger('admin_assessment')->default(0)->comment('оценка администратора');
			$table->timestamp('simulator_up_at')->nullable()->comment('дата и время подъема платформы');
			$table->timestamp('simulator_down_at')->nullable()->comment('дата и время опускания платформы');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
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
        Schema::dropIfExists('events');
    }
}
