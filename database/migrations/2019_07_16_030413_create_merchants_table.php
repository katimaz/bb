<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        $tableName = 'merchants';		
		Schema::create($tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
			
			$table->increments('id');
			$table->unsignedInteger('u_id')->unique()->comment('對應到使用者資料表的id');
			$table->foreign('u_id')->references('id')->on('users');
			$table->tinyInteger('MerchantClass')->comment('1個人 2企業');
			$table->string('MemberUnified', 16)->comment('會員證號 個人身分字號 企業統一編號');
			$table->string('ManagerID', 16)->nullable()->comment('企業代表人 身分證字號1;A123456789 居留證號2;AC12345678 稅籍編號3;19420712RO');
			$table->string('IDCardDate', 8)->nullable()->comment('身份證發證日期個人必填 例:1040101');
			$table->string('IDCardPlace', 16)->nullable()->comment('身份證發證地點例:北市');
			$table->tinyInteger('IDPic')->nullable()->comment('身分證照片 0=有照片 1=無照片');
			$table->tinyInteger('IDFrom')->nullable()->comment('身分證領補換 1=初發 2=補證 3=換發');
			$table->string('MemberName', 64)->comment('會員名稱 企業公司名稱，會員個人姓名');
			$table->string('MemberPhone', 16)->comment('商店聯絡電話例：0x-000111 或 09xx-000111');
			$table->string('ManagerName', 24)->comment('管理者中文姓名 若企業代表人為外國籍，無中文姓 名，請帶入英文姓名');
			$table->string('ManagerNameE', 24)->comment('管理者英文姓名 例：Xiao ming,Wang');
			$table->string('ManagerMobile', 12)->comment('管理者 行動電話號碼');
			$table->string('ManagerEmail', 64)->comment('管理者 E-mail');
			$table->string('MerchantID', 16)->comment('商店代號 格式為金流合作推廣商代號(3碼，限為大寫英文字)+自訂編號(最長12碼，限為數字)。');
			$table->string('MerchantName', 24)->comment('商店中文名稱');
			$table->string('MerchantNameE', 128)->comment('商店英文名稱');
			$table->string('MerchantWebURL', 128)->comment('商店網址');
			$table->string('MerchantAddrCity', 16)->comment('聯絡地址－城市');
			$table->string('MerchantAddrArea', 16)->comment('聯絡地址- 地區');
			$table->string('MerchantAddrCode', 8)->comment('聯絡地址－郵遞區號');
			$table->string('MerchantAddr', 64)->comment('聯絡地址－路名及門牌號碼');
			$table->string('NationalE', 24)->comment('設立登記營業國家(英文名)例：Taiwan');
			$table->string('CityE', 24)->comment('設立登記營業城市 (英文名)例：Taipei City');
			$table->tinyInteger('MerchantType')->comment('販售商品型態 1=實體商品 2=服務 3=虛擬商品 4=票劵');
			$table->string('BusinessType', 8)->comment('商品類別');
			$table->string('MerchantDesc', 256)->comment('商店簡介 字數為255字以內。');
			$table->string('BankCode', 4)->comment('金融機構代碼 例：台北富邦商業銀行，則填入012');
			$table->string('SubBankCode', 8)->comment('金融機構分行代 碼例：台北富邦商業銀行農安分行，則填入2216');
			$table->string('BankAccount', 32)->comment('會員金融機構帳戶帳號 帳號戶名需與會員名稱相同');
			$table->tinyInteger('CreditAutoType')->nullable()->comment('信用卡自動請款 1自動請款，0為手動請款');
			$table->unsignedInteger('CreditLimit')->nullable()->comment('信用卡30天收款額度');
			$table->string('PaymentType', 256)->nullable()->comment('啟用支付方式 若需設定多個支付方式，請使 用”|”符號連接 例：CVS:1|CREDIT:0');
			$table->string('AgreedFee', 256)->nullable()->comment('交易手續費 若需設定多個支付方式，請使 用”|”符號連接 例：CVS:30|CREDIT:0.03 表示設定超 商代碼繳費每筆交易手續費為30元，信用卡每筆交易手續費為3%');
			$table->string('AgreedDay', 256)->nullable()->comment('撥款天數 若需設定多個支付方式，請使 用”|”符號連接 例：CVS:7|CREDIT:4 表示設定超商代 碼繳費7天後撥款，信用卡4天後撥款');
			$table->tinyInteger('MerchantStatus')->nullable()->comment('商店營運狀態 1營運中，2暫停');
			$table->string('MerchantHashKey', 32)->nullable()->comment('商店HashKey');
			$table->string('MerchantIvKey', 32)->nullable()->comment('商店Hash IV');
			$table->datetime('Date')->nullable()->comment('合作商店啟用或修改信用卡啟用狀態的日期時間');
			$table->string('UseInfo', 4)->nullable()->comment('信用卡一次付清支付 ON=啟用 OFF=不啟用');
			$table->string('CreditInst', 4)->nullable()->comment('信用卡分期付款支付方式啟用狀態 ON=啟用 OFF=不啟用');
			$table->string('CreditRed', 4)->nullable()->comment('信用卡紅利折抵支付方式啟用狀態 ON=啟用 OFF=不啟用');
			$table->timestamps();
		});
		
		DB::statement("ALTER TABLE `$tableName` comment '合作商店資料表'");
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchants');
    }
}
