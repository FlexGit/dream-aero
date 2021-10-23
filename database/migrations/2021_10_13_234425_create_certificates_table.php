<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
			$table->string('number')->comment('номер сертификата');
			$table->integer('order_id')->default(0)->index()->comment('заказ, которому привязан сертификат');
			$table->string('ordered_for')->default('')->comment('имя получателя сертификата');
			$table->timestamp('expired_at')->nullable()->comment('срок окончания действия сертификата');
			$table->timestamp('sent_at')->nullable()->comment('последняя дата отправки сертификата на e-mail');
			$table->text('data_json')->default('')->comment('дополнительная информация: адрес доставки сертификата, комментарий по доставке сертификата');
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
        Schema::dropIfExists('certificates');
    }
}
