<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewebpayMpgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'newebpay_mpgs';		
		Schema::create($tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
			
			$table->increments('id');
			$table->unsignedInteger('u_id')->comment('對應到使用者資料表的id');
			$table->foreign('u_id')->references('id')->on('users');
			$table->string('MerchantOrderNo', 32)->unique()->comment('自訂編號');
			$table->tinyInteger('TradeStatus')->comment('狀態0=未付 1=成功 2=失敗 3=取消 ');
			$table->string('TradeNo', 24)->nullable()->comment('藍新金流交易序號');
			$table->string('PaymentType', 16)->nullable()->comment('支付方式');
			$table->unsignedInteger('Amt')->comment('訂單金額');
			$table->string('ItemDesc', 64)->comment('商品資訊');
			$table->string('Email', 128)->comment('Email');
			$table->dateTime('PayTime')->nullable()->comment('PayTime');
			$table->string('RespondType', 16)->nullable()->comment('回傳格式');
			$table->string('IP', 24)->nullable()->comment('交易 IP ');
			$table->string('EscrowBank', 16)->nullable()->comment('款項保管銀行');
			$table->string('RespondCode', 16)->nullable()->comment('金融機構回應碼');
			$table->string('Auth', 8)->nullable()->comment('授權碼');
			$table->string('Card6No', 8)->nullable()->comment('卡號前六碼');
			$table->string('Card4No', 4)->nullable()->comment('卡號末四碼');
			$table->unsignedInteger('Inst')->nullable()->comment('分期-期別');
			$table->unsignedInteger('InstFirst')->nullable()->comment('分期-首期金額');
			$table->unsignedInteger('InstEach')->nullable()->comment('分期-每期金額');
			$table->string('ECI', 2)->nullable()->comment('ECI值');
			$table->tinyInteger('TokenUseStatus')->nullable()->comment('信用卡快速結帳 使用狀態');
			$table->string('PaymentMethod', 16)->nullable()->comment('交易類別');
			$table->unsignedInteger('CloseAmt')->nullable()->comment('請款金額');
			$table->tinyInteger('CloseStatus')->nullable()->comment('請款狀態 0未請 1等待送請款至收單機構 2請款處理中 3請款完成');
			$table->unsignedInteger('BackBalance')->nullable()->comment('可退款餘額');
			$table->tinyInteger('BackStatus')->nullable()->comment('退款狀態 0未請 1等待送退款至收單機構  2退款處理中 3退款完成');
			$table->string('RespondMsg', 64)->nullable()->comment('授權結果訊息');
			$table->string('PayInfo', 64)->nullable()->comment('付款資訊');
			$table->dateTime('ExpireDate')->nullable()->comment('繳費截止日期');
			$table->date('FundTime')->nullable()->comment('預計撥款日');
			$table->string('PayBankCode', 16)->nullable()->comment('ATM 付款人金融機構 代碼');
			$table->string('PayerAccount5Code', 8)->nullable()->comment('ATM 付款人金融機構 帳號末五碼');
			$table->string('CodeNo', 32)->nullable()->comment('超商繳費代碼');
			$table->string('StoreType', 16)->nullable()->comment('超商繳費門市類別');
			$table->string('StoreID', 16)->nullable()->comment('繳費門市代號');
			$table->string('Barcode_1', 24)->nullable()->comment('第一段條碼');
			$table->string('Barcode_2', 24)->nullable()->comment('第二段條碼');
			$table->string('Barcode_3', 24)->nullable()->comment('第三段條碼');
			$table->string('PayStore', 8)->nullable()->comment('繳費超商');
			$table->string('P2GTradeNo', 32)->nullable()->comment('P2G交易序號');
			$table->string('P2GPaymentType', 16)->nullable()->comment('P2G支付方式');
			$table->unsignedInteger('P2GAmt')->nullable()->comment('P2G交易金額');
			$table->string('StoreCode', 16)->nullable()->comment('超商門市編號');
			$table->string('StoreName', 16)->nullable()->comment('超商門市名稱');
			$table->string('StoreAddr', 128)->nullable()->comment('超商門市地址');
			$table->tinyInteger('TradeType')->nullable()->comment('取件交易方式1付款3不付款');
			$table->string('CVSCOMName', 24)->nullable()->comment('取貨人姓名');
			$table->string('CVSCOMPhone', 16)->nullable()->comment('取貨人手機號碼');
			$table->timestamps();
		});
		
		DB::statement("ALTER TABLE `$tableName` comment '藍新金流資料表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('newebpay_mpgs');
    }
}
