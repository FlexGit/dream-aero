<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
			$table->string('number')->nullable()->comment('номер заказа');
			$table->integer('status_id')->default(0)->index()->comment('статус заказа');
			$table->integer('contractor_id')->default(0)->index()->comment('контрагент, совершивший заказ');
			$table->string('name')->comment('имя');
			$table->string('phone')->comment('номер телефона');
			$table->string('email')->comment('e-mail');
			$table->integer('city_id')->default(0)->index()->comment('город, в котором будет осуществлен полет');
			$table->integer('location_id')->default(0)->index()->comment('локация, на которой будет осуществлен полет');
			$table->integer('product_id')->default(0)->index()->comment('продукт');
			$table->integer('amount')->default(0)->comment('стоимость');
			$table->integer('duration')->default(0)->comment('продолжительность полета');
			$table->integer('promocode_id')->default(0)->index()->comment('промокод');
			$table->integer('certificate_id')->default(0)->index()->comment('сертификат');
			$table->timestamp('flight_at')->nullable()->comment('дата и время полета');
			$table->boolean('is_certificate_order')->default(false)->index()->comment('заказ сертификата');
			$table->boolean('is_unified')->default(false)->index()->comment('сертификат действует во всех городах');
			$table->string('source')->nullable()->comment('источник');
			$table->text('data_json')->nullable()->comment('дополнительная информация');
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
        Schema::dropIfExists('orders');
    }
}
