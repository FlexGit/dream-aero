<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatformLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_logs', function (Blueprint $table) {
			$table->id();
			$table->integer('platform_data_id')->default(0)->index();
			$table->string('action_type', 50)->index()->nullable()->comment('тип действия');
			$table->timestamp('start_at')->nullable()->comment('время начала действия');
			$table->timestamp('stop_at')->nullable()->comment('время окончания действия');
			$table->timestamp('duration')->nullable()->comment('длительность');
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
        Schema::dropIfExists('platform_logs');
    }
}
