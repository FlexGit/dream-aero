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
			$table->string('name')->comment('имя');
			$table->string('phone', 50)->comment('номер телефона');
			$table->string('email')->comment('e-mail');
			$table->integer('product_id')->default(0)->index()->comment('продукт');
			$table->integer('certificate_id')->default(0)->index()->comment('сертификат');
			$table->integer('duration')->default(0)->comment('продолжительность полета');
			$table->integer('amount')->default(0)->comment('стоимость');
			$table->integer('city_id')->default(0)->index()->comment('город, в котором будет осуществлен полет');
			$table->integer('location_id')->default(0)->index()->comment('локация, на которой будет осуществлен полет');
			$table->integer('promo_id')->default(0)->index()->comment('акция');
			$table->integer('promocode_id')->default(0)->index()->comment('промокод');
			$table->boolean('is_certificate_purchase')->default(false)->index()->comment('покупка сертификата');
			$table->boolean('is_unified')->default(false)->index()->comment('сертификат действует во всех городах');
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
        Schema::dropIfExists('deals');
    }
}
