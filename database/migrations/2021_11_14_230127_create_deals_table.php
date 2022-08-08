<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
			$table->string('number')->nullable()->comment('номер');
			$table->integer('status_id')->default(0)->index()->comment('статус');
			$table->integer('contractor_id')->default(0)->index()->comment('контрагент');
			$table->integer('city_id')->default(0)->index()->comment('город, в котором будет осуществлен полет');
			$table->string('name')->comment('имя');
			$table->string('phone', 50)->comment('номер телефона');
			$table->string('email')->comment('e-mail');
			$table->string('uuid')->nullable()->comment('uuid');
			$table->integer('user_id')->default(0)->index()->comment('пользователь');
			$table->integer('bill_location_id')->default(0)->index()->comment('локация счета');
			$table->string('source', 25)->nullable()->comment('источник');
			$table->string('roistat')->nullable()->comment('номер визита Roistat');
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
        Schema::dropIfExists('deals');
    }
}
