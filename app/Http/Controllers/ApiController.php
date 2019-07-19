<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utils\Utils;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
use DB;
use URL;

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

		$btn = json_encode(array('url'=>env('APP_URL').'/set_forgot_passwd?data='.$data,'name'=>'重設密碼'));
		Mail::to($request->id)->queue(new toMail(env('APP_URL'),$btn,$user->first_name,'BounBang 幫棒 – 忘記密碼','按一下下方的按鈕重設您 BounBang 幫棒帳戶的密碼'));

		return array('is_tomail'=>((Session::has('veri_id')?1:0)));
	}

	public function get_profile(Request $request)
    {
		$user = User::where('usr_id',Session::get('usrID'))->select('id','usr_status','first_name','last_name','open_offer_setting','usr_id','phone_number','phone_nat_code','sex','email','usr_photo','email_validated')->first();;
		if(!$user)
			return 'error';

		$addrs = Member_addr_recode::where('u_id',$user->id)->get();

		$user->addr = ((isset($addrs))?$addrs:'');
		$user->password = '';
		$user->open_offer_setting = ((!$user->open_offer_setting)?0:$user->open_offer_setting);
		$user->sex = ((!$user->sex)?0:$user->sex);
		$user->phone_nat_code = ((!$user->phone_nat_code)?'886':$user->phone_nat_code);

		return array('user'=>((isset($user))?$user:''));

	}

	public function set_veri_mail(Request $request)
    {
		$user = User::where('email',$request->id)->select('email_valid_key')->first();
		if(!$user)
			return 'error';

		if($user->email_valid_key)
			$key = $user->email_valid_key;
		else
			$key = Utils::create_name_id(time());

		session()->put('veri_id', $key);
		$setting = Setting::select('email_veri_subj','email_veri_body')->first();

		$data = base64_encode(Utils::encrypt('{"email":"'.$request->id.'","key":"'.$key.'"}', config('bbpro.iv')));
		$btn = json_encode(array('url'=>env('APP_URL').'/veri_mail?data='.$data,'name'=>'完成驗證'));
		Mail::to($request->id)->queue(new toMail(env('APP_URL'),$btn,$user->last_name.$user->first_name,((isset($setting) && $setting->email_veri_subj)?$setting->email_veri_subj:'BounBang 幫棒 - 會員註冊驗證信'),((isset($setting) && $setting->email_veri_body)?$setting->email_veri_body:'歡迎加入 BounBang 幫棒家族<br />您正在進行電子郵件信箱設定，請盡快完成電子郵件信箱驗證。<br />請點擊下面"按鈕"開始驗證Email作業')));

		return array('is_tomail'=>((Session::has('veri_id')?1:0)));

	}

	public function is_existed(Request $request)
    {
		return User::where('email',$request->id)->count();
	}

	public function set_latlng(Request $request)
	{
		$lat = $request->lat;
		$lng = $request->lng;

		Session::put('lat', $lat);
		Session::put('lng', $lng);

		return response()->json(['success' => true]);
	}

	public function search_offer(Request $request)
	{
		// 之後要補keyword
		$lat = $request->lat;
		$lng = $request->lng;
		$distance = $request->distance / 1000;
		$main_servicetype = $request->main_servicetype;
		$sub_servicetype = $request->sub_servicetype;
		// DB::enableQueryLog();
		$where = "(olo.service_type LIKE '*[$main_servicetype,%' OR olo.service_type LIKE '%,$main_servicetype,%' OR olo.service_type LIKE '%,$main_servicetype]*' OR olo.service_type LIKE '*[$main_servicetype]*')";

		if(isset($sub_servicetype)) {
			foreach ($sub_servicetype as $key => $value) {
				$where .= " OR (olo.service_type LIKE '*[$value,%' OR olo.service_type LIKE '%,$value,%' OR olo.service_type LIKE '%,$value]*' OR olo.service_type LIKE '*[$value]*')";
			}
		}

		$offer = DB::table('OfferListObj AS olo')->select(DB::raw('olo.id, ( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') )* sin( radians( lat ) ) ) ) AS distance, lat, lng, usr_photo, last_name, first_name, usr_id'))->join('users AS u', 'olo.mem_id', '=', 'u.id')->whereRaw($where)->having('distance','<', $distance)->orderBy('distance')->get();
		// // 取得所有 Query
		// $queries = DB::getQueryLog();

		// // 顯示最後一個 SQL 語法
		// $last_query = end($queries);

		// var_dump($last_query);

		$loc = [];
		foreach ($offer as $key => $value) {
			$loc[$key]['addr'] = [$value->lat, $value->lng];
			$loc[$key]['text'] = '<div  class="user_map"><div class="map-up"><div class="up-face"><img src="' . URL::to('/') . '/images/' . $value->usr_photo . '"></div><div class="up-left"><span class="user-name">' . $value->last_name . $value->first_name . '</span><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i><span class="text-success"> 距離：</span><span class="text-danger">' . (int)($value->distance * 1000) . '</span>公尺</div></div></div><div class="map-score"><div class="income"><span class="text-success"> 受雇次數：</span><span class="text-danger">21</span>次</div><div class="income"><span class="text-success"> 當地導遊：</span><span class="text-danger">500元</span>/小時</div><div class="income"><span class="text-success">其他服務：</span>居家清掃、水電工程、木工...<a href="' . URL::to('/') . '/web/helper_detail/' . $value->usr_id . '/' . (int)($value->distance * 1000) .'">詳細說明</a><div class="map-bt"><a href="#" class="lmbt" data-toggle="modal"              data-target="#exampleModalLong">線上諮詢</a><a href="#" class="rmbt" data-toggle="modal"              data-target="#exampleModalLong">雇用</a></div></div></div>';
			$loc[$key]['icon'] = URL::to('/') . '/images/mark.png';
			$loc[$key]['newLabel'] = '<img src="' . URL::to('/') . '/images/' . $value->usr_photo . '" style="border-radius:50%;width:30px;height:30px;margin-top: -95px;border: 2px solid #b30b06;">';
		}
		$response['loc'] = $loc;
		return response()->json($response);
	}

	public function search_offer_list(Request $request)
	{
		// 之後要補keyword
		$lat = $request->lat;
		$lng = $request->lng;
		$distance = $request->distance / 1000;
		$main_servicetype = $request->main_servicetype;
		$sub_servicetype = $request->sub_servicetype;
		// DB::enableQueryLog();
		$where = "(olo.service_type LIKE '*[$main_servicetype,%' OR olo.service_type LIKE '%,$main_servicetype,%' OR olo.service_type LIKE '%,$main_servicetype]*' OR olo.service_type LIKE '*[$main_servicetype]*')";

		if(isset($sub_servicetype)) {
			foreach ($sub_servicetype as $key => $value) {
				$where .= " OR (olo.service_type LIKE '*[$value,%' OR olo.service_type LIKE '%,$value,%' OR olo.service_type LIKE '%,$value]*' OR olo.service_type LIKE '*[$value]*')";
			}
		}

		$offer = DB::table('OfferListObj AS olo')->select(DB::raw('olo.id, ( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') )* sin( radians( lat ) ) ) ) AS distance, lat, lng, usr_photo, last_name, first_name, usr_id, price, price_type'))->join('users AS u', 'olo.mem_id', '=', 'u.id')->whereRaw($where)->having('distance','<', $distance)->orderBy('distance')->get();
		// // 取得所有 Query
		// $queries = DB::getQueryLog();

		// // 顯示最後一個 SQL 語法
		// $last_query = end($queries);

		// var_dump($last_query);

		$loc = [];
		foreach ($offer as $key => $value) {
			$loc[$key] = '<div class="list-box"> <div class="list-left"> <span class="b-face"><img src="' . URL::to('/') . '/images/' . $value->usr_photo . '"></span> </div> <a href="' . URL::to('/') . '/web/helper_detail/' . $value->usr_id . '/' . (int)($value->distance * 1000) . '" class="list-right"> <div class="list-name">' . $value->last_name . $value->first_name . '</div> <div  class="list-comm"> <span class="list-start"> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> </span> <span class="avg">4.9</span> </div> <div class="list-ds"><span class="show-m">距離：</span>' . (int)($value->distance * 1000) . '公尺</div> <div class="list-dl"><span class="show-m">受雇次數：</span>256次</div > <div class="list-dl"><span class="show-m">工作時數：</span>125/小時</div > <div class="list-dl"><span class="show-m">服務項目：</span>水電工程</div> <div class="list-dl"><span class="show-m">價格：</span>' . $value->price . '/' . $value->price_type . '</div> <div class="list-types"><img src="images/work1.jpg"><img src="images/works.jpg"></div> </a> <div class="list-bt"> <a href="#" class="lask" data-toggle="modal" data-target="#exampleModalLong">詢問</a> <a href="#" class="lhire"  data-toggle="modal" data-target="#exampleModalLong">雇用</a> </div> </div>';
		}
		$response['loc'] = $loc;
		return response()->json($response);
	}
}