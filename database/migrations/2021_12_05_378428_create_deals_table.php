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
			$table->string('number')->comment('номер сделки');
			$table->integer('status_id')->default(0)->index()->comment('статус сделки');
			$table->integer('contractor_id')->default(0)->index()->comment('контрагент, с которым заключена сделка');
			$table->integer('tariff_id')->default(0)->index()->comment('тариф');
			$table->integer('duration')->default(0)->comment('продолжительность полета');
			$table->integer('order_id')->default(0)->index()->comment('ссылка на заказ');
			$table->integer('certificate_id')->default(0)->index()->comment('ссылка на сертификат');
			$table->integer('city_id')->default(0)->index()->comment('город, в котором будет осуществлен полет');
			$table->integer('location_id')->default(0)->index()->comment('локация, на которой будет осуществлен полет');
			$table->timestamp('flight_at')->nullable()->comment('дата и время полета');
			//$table->timestamp('invite_sent_at')->nullable()->comment('последняя дата отправки приглашения на e-mail');
			$table->integer('created_by_user_id')->default(0)->index()->comment('пользователь, создавший сделку');
			$table->integer('updated_by_user_id')->default(0)->index()->comment('пользователь, изменивший последним сделку');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
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
        Schema::dropIfExists('deals');
    }
}
