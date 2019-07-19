<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'videos';		
		Schema::create($tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
			
			$table->increments('id');
			$table->string('video_id', 32)->unique()->comment('影片ID');
			$table->string('title', 128)->nullable()->comment('影片名稱');
			$table->string('youtube_id', 128)->nullable()->comment('Youtube ID');
			$table->string('vimeo_id', 128)->nullable()->comment('Vimeo ID');
			$table->tinyInteger('status')->comment('0下架 1上架');
			$table->timestamps();
        });
		
		 DB::statement("ALTER TABLE `$tableName` comment '首頁影片資料表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
