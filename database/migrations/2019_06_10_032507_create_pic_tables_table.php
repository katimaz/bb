<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePicTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'pic_tables';		
		Schema::create($tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
			
			$table->increments('id');
			$table->string('pic_id', 32)->unique()->comment('照片ID');
			$table->tinyInteger('pic_status')->comment('0下架 1上架');
			$table->tinyInteger('pic_type')->comment('類別:1首頁頂部 2多圖片 3Login');
			$table->string('home_frontpage_pic', 128)->nullable()->comment('照片名稱');
			$table->timestamps();
        });
		
		 DB::statement("ALTER TABLE `$tableName` comment '照片輪播資料表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pic_tables');
    }
}
