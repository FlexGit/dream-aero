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
			$table->integer('order_status_id')->default(0)->index()->comment('статус заказа');
			$table->integer('contractor_id')->default(0)->index()->comment('контрагент, совершивший заказ');
			$table->integer('location_id')->default(0)->index()->comment('локация, на которой будет осуществлен полет');
			$table->timestamp('filght_at')->nullable()->comment('дата и время полета');
			$table->integer('tariff_id')->default(0)->index()->comment('тариф, по которому будет осуществлен полет');
			$table->timestamp('invite_sent_at')->nullable()->comment('последняя дата отправки приглашения на e-mail');
			$table->integer('created_by_user_id')->default(0)->index()->comment('пользователь, создавший заказ');
			$table->integer('updated_by_user_id')->default(0)->index()->comment('пользователь, изменивший последним заказ');
			$table->text('data_json')->default('')->comment('дополнительная информация: комментарий к заказу');
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
