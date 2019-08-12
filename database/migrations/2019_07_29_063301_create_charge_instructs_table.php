<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargeInstructsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'charge_instructs';		
		Schema::create($tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
			
			$table->increments('id');
			$table->string('MerchantID', 16)->comment('合作商店ID');
			$table->string('MerTrade', 24)->comment('商店訂單編號');
			$table->unsignedInteger('Amount')->comment('金額');
			$table->tinyInteger('FeeType')->comment('費用類別0=平台手續費1=佣金費用2=退款費用3=物流費用4=其他費用');
			$table->tinyInteger('BalanceType')->comment('交易正負值0=收取 1=退還');
			$table->string('ExeNo', 16)->comment('處理流水號');
			$table->string('FundTime', 16)->nullable()->comment('預計撥款日');
			$table->timestamps();
		});
		
		DB::statement("ALTER TABLE `$tableName` comment '合作商店扣款指示資料表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charge_instructs');
    }
}
