<?php

use Illuminate\Database\Seeder;

class initAdminUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('adm_groups')->insert(array(
		   'group_id' => '3013aedd5cf74f3b',
		   'group_name' => '管理群組',
		   'group_status' => 1,
		   'group_master' => 1,
		));
		
		DB::table('adm_members')->insert(array(
		   'adm_account' => 'polo',
		   'adm_name' => 'polo.huang',
		   'adm_status' => 1,
		   'adm_password' =>'$2y$10$WosstbWmQ5DIY4IRb7m7BeGGkH/jfDi2ERRBL8qdtGb4AGJG1MNGy',
		   'adm_group' => '1',
		));
    }
	
	/*
		php artisan db:seed --class=initAdminUser  //執行此
		
		新增admins 語法
		1. php artisan db:seed
		2. php artisan db:seed --class=initAdminUser
		
		返回重新 php artisan migrate:refresh --seed
	*/

}
