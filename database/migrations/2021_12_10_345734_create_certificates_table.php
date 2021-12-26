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
			$table->string('number')->nullable()->comment('номер сертификата');
			$table->integer('status_id')->default(0)->index()->comment('статус');
			$table->integer('contractor_id')->default(0)->index()->comment('контрагент');
			$table->integer('product_id')->default(0)->index()->comment('продукт');
			$table->integer('city_id')->default(0)->index()->comment('город');
			$table->timestamp('expire_at')->nullable()->comment('срок окончания действия сертификата');
			$table->boolean('is_unified')->default(false)->index()->comment('сертификат действует во всех городах');
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
        Schema::dropIfExists('certificates');
    }
}
