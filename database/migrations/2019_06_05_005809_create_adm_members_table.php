<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        $tableName = 'adm_members';		
		Schema::create($tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
			
			$table->increments('id');
			$table->string('adm_account', 32)->unique()->comment('帳號');
			$table->tinyInteger('adm_status')->comment('0停權 1正常');
			$table->string('adm_password', 128)->comment('密碼');
            $table->string('adm_name', 32)->comment('名稱');
            $table->string('adm_email', 100)->nullable()->comment('電子信箱');
            $table->unsignedInteger('adm_group')->comment('對應到群組資料表的id');
			$table->foreign('adm_group')->references('id')->on('adm_groups');
			$table->rememberToken();
            $table->timestamps();
        });
		
		 DB::statement("ALTER TABLE `$tableName` comment '管理者資料表'");
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_members');
    }
}
