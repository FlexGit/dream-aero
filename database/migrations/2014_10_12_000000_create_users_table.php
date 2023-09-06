<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
			$table->string('lastname')->nullable()->comment('фамилия');
            $table->string('name')->comment('имя');
			$table->string('middlename')->nullable()->comment('отчество');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
			$table->string('role')->default('admin');
			$table->integer('city_id')->index()->default(0)->comment('город');
			$table->integer('location_id')->index()->default(0)->comment('локация');
			$table->integer('flight_simulator_id')->index()->default(0)->comment('авиатренажер');
			$table->string('phone', 50)->nullable()->comment('телефон');
			$table->date('birthdate')->nullable()->comment('дата рождения');
			$table->string('position')->nullable()->comment('должность');
			$table->boolean('is_reserved')->default(0)->comment('признак запасного сотрудника');
			$table->boolean('is_official')->default(0)->comment('признак официального трудоустройства');
			$table->tinyInteger('enable')->index()->default(1);
			$table->text('data_json')->nullable()->comment('дополнительная информация');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
