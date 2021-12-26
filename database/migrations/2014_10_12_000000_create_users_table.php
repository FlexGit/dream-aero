<?php

use App\Models\User;
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
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
			$table->string('role')->default('manager');
			$table->integer('city_id')->index()->default(0)->comment('город');
			$table->integer('location_id')->index()->default(0)->comment('локация');
			$table->tinyInteger('enable')->index()->default(1);
            $table->rememberToken();
            $table->timestamps();
			$table->softDeletes();
        });
	
		$users = [
			'0' => [
				'name' => 'Дмитрий',
				'email' => 'webmanage@inbox.ru',
				'password' => '$2y$10$JxmanInukUGs4iJp38GHuO2B22jHhNPBbMvf2Kk.x6dvmen.OH25i',
				'role' => User::ROLE_SUPERADMIN,
				'remember_token' => '17ukgipc2dKzlVheH7a4lHuJN215T15r3GNIhxxnhxDMPzg5a4Bt23auzoDq',
			],
		];
	
		foreach ($users as $item) {
			$user = new User();
			$user->name = $item['name'];
			$user->email = $item['email'];
			$user->password = $item['password'];
			$user->role = $item['role'];
			$user->remember_token = $item['remember_token'];
			$user->save();
		}
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
