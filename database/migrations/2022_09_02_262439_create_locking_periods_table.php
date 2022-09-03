<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLockingPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locking_periods', function (Blueprint $table) {
			$table->id();
			$table->integer('location_id')->default(0)->index()->comment('локация');
			$table->integer('user_id')->default(0)->index()->comment('пользователь');
			$table->timestamp('start_at')->nullable()->comment('дата начала периода');
			$table->timestamp('stop_at')->nullable()->comment('дата окончания периода');
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
        Schema::dropIfExists('locking_periods');
    }
}
