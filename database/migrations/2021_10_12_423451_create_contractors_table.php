<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
			$table->string('name')->comment('имя');
			$table->string('phone')->nullable()->comment('основной номер телефона');
			$table->string('email')->comment('основной e-mail');
			$table->string('password');
			$table->rememberToken();
			$table->integer('city_id')->default(0)->index()->comment('город, к которому привязан контрагент');
			$table->integer('discount')->default(0)->comment('скидка');
			$table->text('data_json')->default('[]')->comment('дополнительная информация');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
			$table->timestamp('last_auth_at')->nullable()->comment('дата последней по времени авторизации');
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
        Schema::dropIfExists('contractors');
    }
}
