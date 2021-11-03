<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('имя сотрудника');
			$table->integer('employee_position_id')->default(0)->index()->comment('должность сотрудника');
			$table->integer('location_id')->default(0)->index()->comment('локация сотрудника');
			$table->text('data_json')->default('')->comment('фото сотрудника');
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
        Schema::dropIfExists('employees');
    }
}
