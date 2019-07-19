<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'invoice_tracks';		
		Schema::create($tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
			
			$table->increments('id');
			$table->string('ManagementNo', 32)->comment('平台配發的編號');
			$table->string('Year', 8)->comment('民國年');
			$table->string('Term', 4)->comment('1(一二)2(三四)3(五六)4(七八)5(九十)6(十一二)月');
			$table->string('AphabeticLetter', 8)->comment('字軌英文代碼');
			$table->string('StartNumber', 16)->comment('發票起始號碼');
			$table->string('EndNumber', 16)->comment('發票結束號碼');
			$table->string('Type', 8)->comment('發票類別 07一般 08特種');
			$table->tinyInteger('Flag')->comment('0暫停 1啟用 2停用');
			$table->unsignedInteger('LastNumber')->nullable()->comment('剩餘數量');
			$table->timestamps();
        });
		
		 DB::statement("ALTER TABLE `$tableName` comment '發票字軌資料表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_tracks');
    }
}
