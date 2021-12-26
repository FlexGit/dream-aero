<?php

use App\Models\EmployeePosition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('имя');
            $table->timestamps();
			$table->softDeletes();
        });
	
		$items = [
			'0' => [
				'name' => 'Администратор',
			],
			'1' => [
				'name' => 'Пилот',
			],
			'2' => [
				'name' => 'Бортпроводник',
			],
		];
	
		foreach ($items as $item) {
			$employeePosition = new EmployeePosition();
			$employeePosition->name = $item['name'];
			$employeePosition->save();
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_positions');
    }
}
