<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cookie;
use Session;
use Illuminate\Support\Facades\Hash;
use App\Utils\Utils;
use App\Models\Admin_group;

class Admin_member extends Model
{
    protected $table = 'adm_members';
	public $incrementing = false;
	
	public static function set_password($psw,$id) { 
		return Hash::make($psw . ':' . $id);
	}
	
	public static function isManager($admin=1) { 
		   
		$path = basename(url()->current(),".php"); //當前路徑名稱
		$path = str_replace('get_','',$path); //檢查是否 get_XXXX;
		$path = str_replace('_pt','',$path);
		
		$cookie = Cookie::get('dataCookie'); //cookie data
		$cookie_datas = json_decode(Utils::decrypt($cookie, config('bbpro.key')));
		$group_id = Session::get('ownerGroup');
		
		$myGroup = Admin_group::where('group_id',$group_id)->where('group_status',1)->select('group_master','group_setting')->first();
		
		if(!$cookie_datas || !$path || !$group_id || !$myGroup || !Session::has('ownerLevel') || !Session::get('ownerLevel') >= $admin)
			return 0;
		
		if($path!='owner' && $path!='get_owner' && $path!='owner_pt')
		{
			
			if($myGroup->group_setting)
				$set_arrs = json_decode($myGroup->group_setting);
			else
				$set_arrs = array();	
			
			if(!$myGroup->group_master && !in_array($path,$set_arrs))
			return 0;
		}
		
		return 1;	
	}
}