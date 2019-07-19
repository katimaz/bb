<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'settings';		
		Schema::create($tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
			
			$table->increments('id');
			$table->text('term_of_use')->nullable()->comment('服務條款');
			$table->text('privacy')->nullable()->comment('隱私權政策');
			$table->text('how2_help_post_list')->nullable()->comment('幫棒！您的好幫手');
			$table->text('profit_share_post_list')->nullable()->comment('利潤共享');
			$table->text('tutorial_post_list')->nullable()->comment('好幫手教學');
			$table->text('aboutus_post')->nullable()->comment('關於我門');
			$table->string('GA_code', 192)->nullable()->comment('GA code');
            $table->string('Mixpanel_Code', 192)->nullable()->comment('Mixpanel code');
			$table->string('welcome_email_subj', 192)->nullable()->comment('歡迎加入的主旨');
			$table->text('welcome_email_body')->nullable()->comment('歡迎加入的內文');
			$table->string('referral_email_subj', 192)->nullable()->comment('推薦他人的主旨');
			$table->text('referral_email_body')->nullable()->comment('推薦他人的內文');
			$table->string('referral_FB_msg', 192)->nullable()->comment('分享到FB添加文字訊息');
			$table->string('email_veri_subj', 192)->nullable()->comment('Email認證主旨');
			$table->text('email_veri_body')->nullable()->comment('Email認證內文');
			$table->string('email_veri_comp_subj', 192)->nullable()->comment('Email認證完成的主旨');
			$table->text('email_veri_comp_body')->nullable()->comment('Email認證完成的內文');
			$table->string('email_account_del_subj', 192)->nullable()->comment('刪除帳戶的主旨');
			$table->text('email_account_del_body')->nullable()->comment('刪除帳戶的內文');
			$table->string('email_reward_subj', 192)->nullable()->comment('獲得紅利的主旨');
			$table->text('email_reward_body')->nullable()->comment('獲得紅利的內文');
			$table->timestamps();
        });
		
		 DB::statement("ALTER TABLE `$tableName` comment '管理者資料表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
