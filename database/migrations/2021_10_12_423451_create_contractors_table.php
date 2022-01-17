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
			$table->string('lastname')->nullable()->comment('фамилия');
			$table->datetime('birthdate')->nullable()->comment('дата рождения');
			$table->string('phone')->nullable()->comment('основной номер телефона');
			$table->string('email')->comment('основной e-mail');
			$table->string('password')->nullable()->comment('пароль в md5');
			$table->rememberToken();
			$table->integer('city_id')->default(0)->index()->comment('город, к которому привязан контрагент');
			$table->integer('discount_id')->default(0)->comment('скидка');
			$table->integer('user_id')->default(0)->index()->comment('пользователь');
			$table->boolean('is_active')->default(true)->index()->comment('признак активности');
			$table->timestamp('last_auth_at')->nullable()->comment('дата последней по времени авторизации');
			$table->string('source')->nullable()->comment('источник');
			$table->string('uuid')->nullable()->comment('uuid');
			$table->boolean('is_subscribed')->default(true)->index()->comment('подписан на рассылку');
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
        Schema::dropIfExists('contractors');
    }
}
