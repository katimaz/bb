<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceAllowancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'invoice_allowances';		
		Schema::create($tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
			
			$table->increments('id');
			$table->string('Status', 4)->comment('0未傳出 1已傳出 2取消');
			$table->string('InvoiceNumber', 32)->comment('折讓的原發票號碼');
			$table->string('MerchantOrderNo', 32)->comment('自訂編號');
			$table->string('ItemName', 128)->comment('折讓名稱');
			$table->string('ItemCount', 64)->nullable()->comment('折讓數量');
			$table->string('ItemUnit', 64)->nullable()->comment('折讓單位');
			$table->string('ItemPrice', 64)->nullable()->comment('折讓單價');
			$table->string('ItemAmt', 64)->nullable()->comment('折讓小計');
			$table->string('ItemTaxAmt', 64)->nullable()->comment('折讓稅額');
			$table->string('AllowanceNo', 32)->comment('折讓號碼');
			$table->unsignedInteger('AllowanceAmt')->comment('折讓總金額');
			$table->timestamps();
		});
		
		DB::statement("ALTER TABLE `$tableName` comment '折讓資料表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_allowances');
    }
}
