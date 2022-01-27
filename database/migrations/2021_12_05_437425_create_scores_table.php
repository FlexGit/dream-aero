<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
			$table->integer('score')->default(0)->comment('количество баллов');
			$table->string('type')->nullable()->index()->comment('количество баллов');
			$table->integer('contractor_id')->default(0)->index()->comment('контрагент');
			$table->integer('event_id')->default(0)->index()->comment('событие');
			$table->integer('duration')->default(0)->comment('длительность полета');
			$table->integer('user_id')->default(0)->index()->comment('пользователь');
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
        Schema::dropIfExists('scores');
    }
}
