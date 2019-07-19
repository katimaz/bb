<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'invoices';		
		Schema::create($tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
			
			$table->increments('id');
			$table->unsignedInteger('u_id')->comment('對應到使用者資料表的id');
			$table->foreign('u_id')->references('id')->on('users');
			$table->tinyInteger('InvoiceStatus')->comment('1開立 2作廢');
			$table->string('InvoiceNumber', 32)->unique()->comment('發票號碼');
			$table->string('MerchantOrderNo', 32)->unique()->comment('自訂編號');
			$table->string('InvoiceTransNo', 32)->unique()->comment('平台電子發票的編號');
			$table->string('TransNum', 32)->nullable()->comment('金流平台交易序號');
			$table->string('Status', 4)->comment('1即時 2等待 3預約');
			$table->string('Category', 8)->comment('發票種類 B2B B2C');
			$table->string('BuyerName', 48)->comment('買受人名稱');
			$table->string('BuyerUBN', 12)->nullable()->comment('買受人統一編號');
			$table->string('BuyerEmail', 64)->comment('買受人Email');
			$table->string('BuyerAddress', 128)->nullable()->comment('買受人地址');
			$table->string('CarrierType', 2)->nullable()->comment('載具類別 0=手機條碼具 1=自然人憑證條碼 2=ezPay發票載具');
			$table->string('CarrierNum', 64)->nullable()->comment('載具編號');
			$table->unsignedInteger('LoveCode')->nullable()->comment('捐贈碼');
			$table->string('PrintFlag', 2)->nullable()->comment('索取紙本發票 Y=索取 N=不索取');
			$table->string('TaxType', 2)->nullable()->comment('課稅別 1=應稅 2=零稅率 3=免稅 9=混和應稅');
			$table->float('TaxRate')->nullable()->comment('稅率');
			$table->unsignedInteger('TotalAmt')->comment('發票金額');
			$table->string('RandomNum', 16)->comment('發票防偽隨機碼');
			$table->dateTime('CreateTime')->comment('開立發票時間');
			$table->dateTime('InvalidTime')->nullable()->comment('發票作廢時間');
			$table->string('InvalidReason', 72)->nullable()->comment('作廢原因');
			$table->date('CreateStatusTime')->nullable()->comment('預計開立日期,格式YYYY-MM-DD');
			$table->string('BarCode', 50)->comment('發票條碼');
			$table->string('QRcodeL', 200)->comment('發票QRCode(左)');
			$table->string('QRcodeR', 200)->comment('發票QRCode(右)');
			$table->unsignedInteger('RemainAmt')->nullable()->comment('可折讓金額');
			$table->string('Comment', 72)->nullable()->comment('備註');
			$table->timestamps();
        });
		
		 DB::statement("ALTER TABLE `$tableName` comment '發票開立資料表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
