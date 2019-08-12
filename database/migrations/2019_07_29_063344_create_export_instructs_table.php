<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExportInstructsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'export_instructs';		
		Schema::create($tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
			
			$table->increments('id');
			$table->string('MerchantID', 16)->comment('合作商店ID');
			$table->string('MerchantOrderNo', 24)->comment('商店訂單編號');
			$table->unsignedInteger('Amount')->comment('扣款金額');
			$table->timestamps();
		});
		
		DB::statement("ALTER TABLE `$tableName` comment '合作商店撥款指示資料表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('export_instructs');
    }
}
