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
			$table->integer('deal_position_id')->default(0)->index()->comment('позиция сделки');
			$table->integer('extra_time')->default(0)->comment('дополнительное время');
			$table->integer('city_id')->default(0)->index()->comment('город, в котором будет осуществлен полет');
			$table->integer('location_id')->default(0)->index()->comment('локация, на которой будет осуществлен полет');
			$table->timestamp('start_at')->nullable()->comment('дата и время начала события');
			$table->timestamp('stop_at')->nullable()->comment('дата и время окончания события');
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
        Schema::dropIfExists('deal_positions');
    }
}
