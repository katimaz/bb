<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utils\Utils;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Mail\MailServe;
use App\Mail\toMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\Models\Video;
use App\Models\Pic_table;
use App\Models\Member_addr_recode;
use Session;
use App\User;
use Auth;
use Cookie;

class ApiController extends Controller
{
    
	public function chk_mail(Request $request)
    {
		$user = User::where('email',$request->id)->select('FB_login_token','Google_login_token','Line_login_token')->first();
		if(isset($user))
		{
			$socials = array();
			if($user->FB_login_token)
				$socials[] = 'Facebook';
			if($user->Google_login_token)
				$socials[] = 'Google';
			if($user->Line_login_token)
				$socials[] = 'Line';				
		}
		return array('checkmail'=>((isset($user))?0:1),'socials'=>((isset($socials))?$socials:''));
	}
	
	public function chk_tel(Request $request)
    {
		//preg_match("/09[0-9]{2}[0-9]{6}/", $TELEPHONE)
		return User::where('tel',$request->id)->count();
	}
	
	public function get_forgot_passwd(Request $request)
    {
		$user = User::where('email',$request->id)->select('first_name','last_name')->first();
		if(!$user)
			return 'error';
			
		$key = ((Session::has('veri_id')?Session::get('veri_id'):Utils::create_name_id(time())));
		session()->put('veri_id', $key);
		
		$data = base64_encode(Utils::encrypt('{"email":"'.$request->id.'","key":"'.$key.'"}', config('bbpro.iv')));
		
		//-------------------------寄發信件
		$mail = new MailServe();
		$email = $request->id;
		$mailType = '1-012';
		$data_info_array = array(
			'name' => ((isset($user))?$user->first_name:''),
			'btn' => array('txt'=>'&nbsp;&nbsp;重設密碼&nbsp;&nbsp;','url'=>url('/').'/set_forgot_passwd?data='.$data,'style'=>'button'),
		);
		$mail->sendEmail($mailType,$email,$data_info_array);
		//--------------------end
					
		/*$btn = json_encode(array('url'=>url('/').'/set_forgot_passwd?data='.$data,'name'=>'重設密碼'));
		Mail::to($request->id)->queue(new toMail(url('/'),$btn,$user->first_name,'BounBang 幫棒 – 忘記密碼','按一下下方的按鈕重設您 BounBang 幫棒帳戶的密碼'));*/
		
		return array('is_tomail'=>((Session::has('veri_id')?1:0)));
	}
	
	public function get_profile(Request $request)
    {
		$user = User::where('usr_id',Session::get('usrID'))->select('id','usr_status','first_name','last_name','nickname','usr_type','usr_id','phone_number','phone_nat_code','sex','email','usr_photo','email_validated')->first();
		if(!$user)
			return 'error';
		$zipcodes = Utils::get_area_zipcode();
		$citys = array();
		$areas = array();
		foreach($zipcodes as $key => $value)
		{
			$citys[] = $key;
			$areas[] = $value['Zip'];
		}
		$addrs = Member_addr_recode::where('u_id',$user->id)->get();
		$positions = array();
		if(isset($addrs) && count($addrs))
		{
			foreach($addrs as $addr)
			{
				$index = array_search($addr->city,$citys);
				$positions[] = array('city'=>$addr->city,'areas'=>$areas[$index],'nat'=>$addr->nat,'zip'=>$addr->zip,'addr'=>$addr->addr,'lat'=>$addr->lat,'lng'=>$addr->lng);
			}
		}else
		{
			$positions[] = array('city'=>'','areas'=>array(),'nat'=>'','zip'=>'','addr'=>'','lat'=>'','lng'=>'');
		}
		$user->password = '';
		$user->usr_type = ((!$user->usr_type) ? 0 : $user->usr_type);
		$user->sex = ((!$user->sex)?0:$user->sex);
		$user->phone_nat_code = ((!$user->phone_nat_code)?'886':$user->phone_nat_code);
		
		return array('user'=>((isset($user))?$user:''),'positions'=>$positions,'citys'=>((isset($citys))?$citys:''),'areas'=>((isset($areas))?$areas:''));
		
	}
	
	public function set_veri_mail(Request $request)
    {
		$key = Utils::create_name_id(time());
		User::where('email',$request->id)->update(array('email_valid_key'=>$key));
		$user = User::where('email',$request->id)->first();
		if(!$user)
			return 'error';
			
		$setting = Setting::select('email_veri_subj','email_veri_body')->first();
		
		$data = base64_encode(Utils::encrypt('{"email":"'.$request->id.'","key":"'.$key.'"}', config('bbpro.iv')));
		
		//-------------------------寄發信件
		$mail = new MailServe();
		$email = $request->id;
		$mailType = '1-007';
		$data_info_array = array(
			'name' => ((isset($user))?$user->first_name:''),
			'btn' => array('txt'=>'&nbsp;&nbsp;完成驗證&nbsp;&nbsp;','url'=>url('/').'/veri_mail?data='.$data,'style'=>'button'),
		);
		$mail->sendEmail($mailType,$email,$data_info_array);
		
		$btn = json_encode(array('url'=>url('/').'/veri_mail?data='.$data,'name'=>'完成驗證'));
		
		/*Mail::to($request->id)->queue(new toMail(url('/'),$btn,$user->last_name.$user->first_name,((isset($setting) && $setting->email_veri_subj)?$setting->email_veri_subj:'BounBang 幫棒 - 會員註冊驗證信'),((isset($setting) && $setting->email_veri_body)?$setting->email_veri_body:'歡迎加入 BounBang 幫棒家族<br />您正在進行電子郵件信箱設定，請盡快完成電子郵件信箱驗證。<br />請點擊下面"按鈕"開始驗證Email作業')));*/
		
		return array('is_tomail'=>(($user->email_valid_key)?1:0));
		
	}
	
	public function is_existed(Request $request)
    {
		$num = User::where('usr_id','!=',Session::get('usrID'))->where('email',$request->id)->count();
		return ((isset($num) && $num)?$num:0);
	}
	
	public function get_partners(Request $request)
    {
		if(!Auth::check())
			return 'error';
			
		$referrals = User::where('referral_from',Session::get('usrID'))->select('usr_id')->get();
		$ids = array();
		if(isset($referrals))
		{
			foreach($referrals as $referral)
			{
				$ids[] = $referral->usr_id;	
			}	
		}
		$partners = User::whereIn('usr_id',$ids)->get();
		foreach($partners as $partner)
		{
			$addr = Member_addr_recode::where('u_id',$partner->id)->first();
			if(isset($addr))
			{
				$partner->city = $addr->city;
				$partner->nat = $addr->nat;
			}else
			{
				$partner->city = '';
				$partner->nat = '';	
			}	
		}
		return array('partners'=>((isset($partners))?$partners:''));
	}
	
	
}
