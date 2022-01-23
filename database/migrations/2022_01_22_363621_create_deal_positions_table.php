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
			$table->integer('deal_id')->default(0)->index()->comment('сделка');
			$table->integer('product_id')->default(0)->index()->comment('продукт');
			$table->integer('certificate_id')->default(0)->index()->comment('сертификат');
			$table->integer('duration')->default(0)->comment('продолжительность полета');
			$table->integer('amount')->default(0)->comment('стоимость');
			$table->integer('currency_id')->default(0)->index()->comment('валюта');
			$table->integer('city_id')->default(0)->index()->comment('город, в котором будет осуществлен полет');
			$table->integer('location_id')->default(0)->index()->comment('локация, на которой будет осуществлен полет');
			$table->integer('flight_simulator_id')->default(0)->index()->comment('авиатренажер');
			$table->integer('promo_id')->default(0)->index()->comment('акция');
			$table->integer('promocode_id')->default(0)->index()->comment('промокод');
			$table->boolean('is_certificate_purchase')->default(false)->index()->comment('покупка сертификата');
			$table->timestamp('flight_at')->nullable()->comment('дата и время полета');
			$table->timestamp('invite_sent_at')->nullable()->comment('последняя дата отправки приглашения на e-mail');
			$table->timestamp('certificate_sent_at')->nullable()->comment('последняя дата отправки сертификата на e-mail');
			$table->string('source', 25)->nullable()->comment('источник');
			$table->integer('user_id')->default(0)->index()->comment('пользователь');
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
