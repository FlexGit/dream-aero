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
			$table->string('number')->nullable()->comment('номер платежа');
			$table->integer('bill_id')->default(0)->index()->comment('счет, по которому совершен платеж');
			$table->integer('status_id')->default(0)->index()->comment('статус');
			$table->integer('payment_method_id')->default(0)->index()->comment('способ платежа');
			$table->integer('amount')->default(0)->comment('сумма платежа');
			$table->timestamp('performed_at')->nullable()->comment('дата проведения платежа шлюзом или ОФД');
			$table->text('data_json')->nullable()->comment('дополнительная информация: ОФД - номер смены, состав позиций, номер ФД, №пп, оператор. Шлюз - ');
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
        Schema::dropIfExists('payments');
    }
}
