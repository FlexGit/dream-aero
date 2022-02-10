<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->float('value')->comment('значение');
			$table->integer('count')->comment('количество голосов');
			$table->integer('content_id')->default(0)->index()->comment('материал');
			$table->boolean('is_active')->default(false)->index()->comment('признак активности');
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
        Schema::dropIfExists('ratings');
    }
}
