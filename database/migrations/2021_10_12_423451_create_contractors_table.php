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
			$table->string('phone')->comment('основной номер телефона');
			$table->string('email')->comment('основной e-mail');
			$table->timestamp('email_verified_at')->nullable();
			$table->string('password');
			$table->rememberToken();
			$table->integer('city_id')->default(0)->index()->comment('город, к которому привязан контрагент');
			$table->text('data_json')->default('')->comment('дополнительная информация');
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
