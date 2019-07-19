<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberAddrRecodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'member_addr_recodes';		
		Schema::create($tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
			
			$table->increments('id');
			$table->unsignedInteger('u_id')->comment('對應到使用者資料表的id');
			$table->foreign('u_id')->references('id')->on('users');
			$table->string('city', 32)->nullable()->comment('城市');
			$table->string('nat', 32)->nullable()->comment('鄉鎮');
			$table->string('zip', 16)->nullable()->comment('郵政區號');
			$table->string('addr', 100)->nullable()->comment('住址');
			$table->string('lat', 32)->nullable()->comment('經度');
			$table->string('lng', 32)->nullable()->comment('經度');
			$table->timestamps();
        });
		
		 DB::statement("ALTER TABLE `$tableName` comment '住址、GPS資料表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_addr_recodes');
    }
}
