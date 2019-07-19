<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
		 $tableName = 'users';
		 
		 Schema::create($tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->increments('id');
            $table->string('usr_id', 32)->unique()->comment('帳號');
			$table->tinyInteger('usr_status')->comment('用戶狀態');
			$table->string('password', 128)->comment('密碼');
			$table->string('email', 128)->comment('電子信箱');
			$table->string('first_name', 24)->nullable()->comment('名');
            $table->string('last_name', 24)->nullable()->comment('姓');
			$table->string('nickname', 32)->nullable()->comment('暱稱');
			$table->tinyInteger('sex')->nullable()->comment('性別 0未定1男2女');
            $table->string('phone_nat_code', 16)->nullable()->comment('國碼');
			$table->string('phone_number', 32)->nullable()->comment('電話號碼');
            $table->string('referral_code', 48)->nullable()->comment('推薦碼');
			$table->string('email_valid_key', 32)->nullable()->comment('email驗證碼');
			$table->string('phone_valid_key', 32)->nullable()->comment('手機驗證碼');
			$table->string('nationality', 16)->nullable()->comment('國籍');
			$table->string('id_type', 10)->nullable()->comment('身分證類別');
			$table->string('id_number', 20)->nullable()->comment('身分證號碼');
			$table->string('id_photo', 64)->nullable()->comment('身分證照片');
			$table->string('usr_corp_id', 48)->nullable()->comment('用戶所屬公司組織');
			$table->string('usr_photo', 64)->nullable()->comment('user照片');
			$table->string('lang', 100)->nullable()->comment('語言');
			$table->string('referral_from', 32)->nullable()->comment('推薦者帳號');
			$table->tinyInteger('email_validated')->nullable()->comment('email 0未驗1已驗');
			$table->tinyInteger('phone_validated')->nullable()->comment('手機 0未驗1已驗');
			$table->unsignedInteger('kyc_validated')->nullable()->comment('用戶身分證件認證_後台審核');
			$table->tinyInteger('able2offer')->nullable()->comment('不同值意義見文件說明');
			$table->unsignedInteger('two_step_authentication')->nullable()->comment('');
			$table->string('where_know_us', 64)->nullable()->comment('那裡得知我們');
			$table->string('FB_login_token', 192)->nullable()->unique()->comment('使用者的facebook id');
            $table->string('Google_login_token', 192)->nullable()->unique()->comment('使用者的google id');
            $table->string('Line_login_token', 192)->nullable()->unique()->comment('使用者的line id');
			$table->string('cookie_id', 64)->nullable()->comment('使用者cookie id');
			$table->date('birthday')->nullable()->comment('生日');
			$table->tinyInteger('student')->nullable()->comment('0一般1學生');
			$table->string('school', 64)->nullable()->comment('學校');
			$table->string('department', 32)->nullable()->comment('科系');
			$table->unsignedInteger('annual_revenue')->nullable()->comment('年所得');
			$table->unsignedInteger('annual_reward')->nullable()->comment('年回饋金');
			$table->unsignedInteger('customer_avg_rate')->nullable()->comment('平均星數');
			$table->unsignedInteger('offers_total_rate')->nullable()->comment('接案數');
			$table->unsignedInteger('total_served_case')->nullable()->comment('已完成件數');
			$table->unsignedInteger('total_served_hours')->nullable()->comment('已服務時數');
			$table->string('served_area', 48)->nullable()->comment('服務區域');
			$table->text('service_type')->nullable()->comment('服務說明');
			$table->text('personal_brief')->nullable()->comment('個人簡介');
			$table->tinyInteger('open_offer_setting')->nullable()->comment('0關閉1開啟');
			$table->text('AlbumPicKeyList2S3')->nullable()->comment('作品照片');
			$table->text('LicensePicKeyList2S3')->nullable()->comment('證照照片');
			$table->json('history_revenue_string')->nullable()->comment('歷史收入資料');
			$table->json('history_expense_string')->nullable()->comment('歷史花費資料');
			$table->json('history_reward_string')->nullable()->comment('歷史回饋金資料');
			$table->rememberToken();
            $table->timestamps();
		});

        DB::statement("ALTER TABLE `$tableName` comment '會員資料表'");
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
