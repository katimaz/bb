<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
		 $tableName = 'adm_groups';
		 
		 Schema::create($tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->increments('id');
            $table->string('group_id', 32)->unique()->comment('群組ID');
			$table->tinyInteger('group_status')->comment('狀態 0下架 1上架');
			$table->string('group_name', 64)->comment('群組名稱');
			$table->tinyInteger('group_master')->comment('0一般 1管理群組');
			$table->unsignedInteger('group_manager')->nullable()->comment('群組經理');
			$table->json('group_setting')->nullable()->comment('群組設定');
			$table->timestamps();
		});

        DB::statement("ALTER TABLE `$tableName` comment '群組(部門)資料表'");
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_groups');
    }
}
