<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deal_positions', function (Blueprint $table) {
            $table->id();
			$table->string('number')->nullable()->comment('номер позиции сделки');
			$table->integer('deal_id')->default(0)->index()->comment('сделка');
			$table->integer('status_id')->default(0)->index()->comment('статус
позиции сделки');
			$table->integer('order_id')->default(0)->index()->comment('заказ');
			$table->integer('product_id')->default(0)->index()->comment('продукт');
			$table->integer('certificate_id')->default(0)->index()->comment('сертификат');
			$table->integer('duration')->default(0)->comment('продолжительность полета');
			$table->integer('amount')->default(0)->comment('стоимость');
			$table->integer('city_id')->default(0)->index()->comment('город, в котором будет осуществлен полет');
			$table->integer('location_id')->default(0)->index()->comment('локация, на которой будет осуществлен полет');
			$table->timestamp('flight_at')->nullable()->comment('дата и время полета');
			$table->timestamp('invite_sent_at')->nullable()->comment('последняя дата отправки приглашения на e-mail');
			$table->timestamp('certificate_sent_at')->nullable()->comment('последняя дата отправки сертификата на e-mail');
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
