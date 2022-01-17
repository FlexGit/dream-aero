<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_comments', function (Blueprint $table) {
            $table->id();
			$table->string('name')->comment('комментарий');
			$table->integer('event_id')->default(0)->index()->comment('событие');
			$table->integer('created_by')->default(0)->index()->comment('кто создал');
			$table->integer('updated_by')->default(0)->index()->comment('кто изменил');
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
        Schema::dropIfExists('event_comments');
    }
}
