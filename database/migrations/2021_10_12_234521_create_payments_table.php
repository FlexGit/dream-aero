<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
			$table->integer('payment_method_id')->default(0)->index()->comment('способ платежа');
			$table->integer('sum')->default(0)->comment('сумма платежа');
			$table->timestamp('performed_at')->nullable()->comment('дата проведения платежа шлюзом или ОФД');
			$table->integer('order_id')->default(0)->index()->comment('заказ, к которому привязан платеж');
			$table->text('data_json')->comment('дополнительная информация: ОФД - номер смены, состав позиций, номер ФД, №пп, оператор. Шлюз - ');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
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
        Schema::dropIfExists('payments');
    }
}
