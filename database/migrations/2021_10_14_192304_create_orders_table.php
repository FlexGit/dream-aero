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
			$table->string('number')->comment('номер заказа');
			$table->integer('status_id')->default(0)->index()->comment('статус заказа');
			$table->integer('contractor_id')->default(0)->index()->comment('контрагент, совершивший заказ');
			$table->integer('tariff_id')->default(0)->index()->comment('тариф');
			$table->integer('city_id')->default(0)->index()->comment('город, в котором будет осуществлен полет');
			$table->integer('location_id')->default(0)->index()->comment('локация, на которой будет осуществлен полет');
			$table->timestamp('flight_at')->nullable()->comment('дата и время полета');
			$table->timestamp('invite_sent_at')->nullable()->comment('последняя дата отправки приглашения на e-mail');
			$table->boolean('is_certificate_order')->default(false)->index()->comment('заказ сертификата');
			$table->string('certificate_number')->comment('номер сертификата');
			$table->timestamp('certificate_expire_at')->nullable()->comment('срок окончания действия сертификата');
			$table->timestamp('certificate_sent_at')->nullable()->comment('последняя дата отправки сертификата на e-mail');
			$table->integer('created_by_user_id')->default(0)->index()->comment('пользователь, создавший заказ');
			$table->integer('updated_by_user_id')->default(0)->index()->comment('пользователь, изменивший последним заказ');
			$table->text('data_json')->nullable()->comment('дополнительная информация: комментарий к бронированию, имя получателя сертификата, адрес доставки сертификата, комментарий по доставке сертификата');
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
        Schema::dropIfExists('orders');
    }
}
