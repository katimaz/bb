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
use App\Models\NeedListObj;
use App\Models\Users;
use App\Models\einvoice_info;
use App\Models\collection_info;
use App\Models\member_notify_setting;
use App\Models\OfferListObj;
use App\Models\olo_img;
use App\Models\olo_license_img;
use App\Models\olo_video;
use App\Models\olo_food;
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
		$user = User::where('usr_id',Session::get('usrID'))->select('id','usr_status','first_name','last_name','usr_type', 'open_offer_setting','usr_id','phone_number','phone_nat_code','sex','email','usr_photo','email_validated')->first();;
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
		$btn = json_encode(array('url'=>env('APP_URL').'/veri_mail?data='.$data,'name'=>'完成驗證'));
		
		Mail::to($request->id)->queue(new toMail(env('APP_URL'),$btn,$user->last_name.$user->first_name,((isset($setting) && $setting->email_veri_subj)?$setting->email_veri_subj:'BounBang 幫棒 - 會員註冊驗證信'),((isset($setting) && $setting->email_veri_body)?$setting->email_veri_body:'歡迎加入 BounBang 幫棒家族<br />您正在進行電子郵件信箱設定，請盡快完成電子郵件信箱驗證。<br />請點擊下面"按鈕"開始驗證Email作業')));
		
		return array('is_tomail'=>(($user->email_valid_key)?1:0));
		
	}

	public function is_existed(Request $request)
    {
		$num = User::where('usr_id','!=',Session::get('usrID'))->where('email',$request->id)->count();
		return ((isset($num) && $num)?$num:0);
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

		$user = User::where('usr_id',Session::get('usrID'))->first();
		if($user->open_offer_setting == "0") {
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
		} else {
			$where = "(nlo.service_type LIKE '*[$main_servicetype,%' OR nlo.service_type LIKE '%,$main_servicetype,%' OR nlo.service_type LIKE '%,$main_servicetype]*' OR nlo.service_type LIKE '*[$main_servicetype]*')";

			if(isset($sub_servicetype)) {
				foreach ($sub_servicetype as $key => $value) {
					$where .= " OR (nlo.service_type LIKE '*[$value,%' OR nlo.service_type LIKE '%,$value,%' OR nlo.service_type LIKE '%,$value]*' OR nlo.service_type LIKE '*[$value]*')";
				}
			}

			$offer = DB::table('NeedListObj AS nlo')->select(DB::raw('nlo.id, ( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') )* sin( radians( lat ) ) ) ) AS distance, lat, lng, usr_photo, last_name, first_name, usr_id'))->join('users AS u', 'nlo.mem_id', '=', 'u.id')->whereRaw($where)->having('distance','<', $distance)->orderBy('distance')->get();
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
		$user = User::where('usr_id',Session::get('usrID'))->first();
		if($user->open_offer_setting == "0") {
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
		} else {
			$where = "(nlo.service_type LIKE '*[$main_servicetype,%' OR nlo.service_type LIKE '%,$main_servicetype,%' OR nlo.service_type LIKE '%,$main_servicetype]*' OR nlo.service_type LIKE '*[$main_servicetype]*')";

			if(isset($sub_servicetype)) {
				foreach ($sub_servicetype as $key => $value) {
					$where .= " OR (nlo.service_type LIKE '*[$value,%' OR nlo.service_type LIKE '%,$value,%' OR nlo.service_type LIKE '%,$value]*' OR nlo.service_type LIKE '*[$value]*')";
				}
			}

			$offer = DB::table('NeedListObj AS nlo')->select(DB::raw('nlo.id, ( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') )* sin( radians( lat ) ) ) ) AS distance, lat, lng, usr_photo, last_name, first_name, usr_id, price, price_type'))->join('users AS u', 'nlo.mem_id', '=', 'u.id')->whereRaw($where)->having('distance','<', $distance)->orderBy('distance')->get();
			// // 取得所有 Query
			// $queries = DB::getQueryLog();

			// // 顯示最後一個 SQL 語法
			// $last_query = end($queries);

			// var_dump($last_query);

			$loc = [];
			foreach ($offer as $key => $value) {
				$loc[$key] = '<div class="list-box"> <div class="list-left"> <span class="b-face"><img src="' . URL::to('/') . '/images/' . $value->usr_photo . '"></span> </div> <a href="' . URL::to('/') . '/web/helper_detail/' . $value->usr_id . '/' . (int)($value->distance * 1000) . '" class="list-right"> <div class="list-name">' . $value->last_name . $value->first_name . '</div> <div  class="list-comm"> <span class="list-start"> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> </span> <span class="avg">4.9</span> </div> <div class="list-ds"><span class="show-m">距離：</span>' . (int)($value->distance * 1000) . '公尺</div> <div class="list-dl"><span class="show-m">受雇次數：</span>256次</div > <div class="list-dl"><span class="show-m">工作時數：</span>125/小時</div > <div class="list-dl"><span class="show-m">服務項目：</span>水電工程</div> <div class="list-dl"><span class="show-m">價格：</span>' . $value->price . '/' . $value->price_type . '</div> <div class="list-types"><img src="images/work1.jpg"><img src="images/works.jpg"></div> </a> <div class="list-bt"> <a href="#" class="lask" data-toggle="modal" data-target="#exampleModalLong">詢問</a> <a href="#" class="lhire"  data-toggle="modal" data-target="#exampleModalLong">雇用</a> </div> </div>';
			}
		}

		$response['loc'] = $loc;
		return response()->json($response);
	}

	public function set_need(Request $request)
	{
		$need_list_obj = new NeedListObj;

		$need_list_obj->mem_id = session()->get('uID');
		$need_list_obj->budget =  $request->budget;
		$need_list_obj->budget_type =  $request->budget_type;
		$need_list_obj->frequency =  $request->frequency;
		// $need_list_obj->date =  $request->date;
		$need_list_obj->datetime_from =  $request->s_dt . ' ' . $request->time;
		$need_list_obj->datetime_end =  $request->e_dt . ' ' . $request->time;
		$need_list_obj->available_daytime_enum =  ($request->available_daytime_enum == null) ? '0' : $request->available_daytime_enum;
		$need_list_obj->weekday_enum =  ($request->week == null) ? '0' : $request->week;
		$need_list_obj->monthday_enum =  ($request->monthday_enum == null) ? '0' : $request->monthday_enum;
		$need_list_obj->service_type = $request->service_type;
		$need_list_obj->keyword = $request->keyword;
		$need_list_obj->need_description = $request->need_description;
		$need_list_obj->lat = $request->lat;
		$need_list_obj->lng = $request->lng;
		if($request->mem_addr == '') {
			$gmap = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . session()->get('lat') . ',' . session()->get('lng')  . '&language=zh-TW&key=AIzaSyABW4BgnHQyCb11qpo3kx6t97BwxgG1k18');
			$location = json_decode($gmap);
			$need_list_obj->mem_addr = $location->results[0]->formatted_address;
		} else {
			$need_list_obj->mem_addr = $request->mem_addr;
		}
		// $need_list_obj->total =  $request->total;

		$need_list_obj->save();

		// email
		$users = Users::where('id', '=', session()->get('uID'))->get();
		$user = $users[0];

		$btn = json_encode(array('url'=>env('APP_URL'),'name'=>'連結幫棒'));
		Mail::to($user->email)->queue(new toMail(env('APP_URL'), $btn, $user->nickname, 'BounBang幫棒 – 服務鈴(配對)送出通知 - [' . $need_list_obj->id . ' , <項目名稱> ' . $need_list_obj->budget . ']', $user->nickname . ' 您好，<br> 您的服務鈴(配對)工作已刊登 - [' . $need_list_obj->id . ' , <項目名稱> ' . $need_list_obj->budget . ']。 合適的好幫手們很快會跟您聯絡。您也可到管理我的需求 – 配對做查詢與管理。
		'));
		return response()->json(['msg' => '已發送請求']);
	}

	public function set_invoice(Request $request)
	{
		$einvoice = new einvoice_info;
		$einvoice->title = $request->title;
		$einvoice->number = $request->number;
		$einvoice->corp_or_personal = $request->crop_or_personal;
		$einvoice->mem_id = session()->get('uID');
		$einvoice->save();

		return response()->json([]);
	}

	public function del_invoice(Request $request)
	{
		$id = $request->id;
		$deletedRows = einvoice_info::find($id)->delete();

		return response()->json([]);
	}

	public function set_bank(Request $request)
	{
		$collection_info = new collection_info;
		$collection_info->bank_title = $request->bank_title;
		$collection_info->bank_id = $request->bank_id;
		$collection_info->account_name = $request->account_name;
		$collection_info->account_no = $request->account_no;
		$collection_info->mem_id = session()->get('uID');
		$collection_info->save();

		return response()->json([]);
	}

	public function del_bank(Request $request)
	{
		$id = $request->id;
		$deletedRows = collection_info::find($id)->delete();

		return response()->json([]);
	}

	public function set_notify(Request $request)
	{
		$member_notify_setting = member_notify_setting::where('mem_id', '=', session()->get('uID'))->get();
		if($member_notify_setting->first()) {
			$set = [
				'setting_email_notify1' => $request->setting_email_notify1,
				'setting_email_notify2' => $request->setting_email_notify2,
				'setting_email_notify3' => $request->setting_email_notify3,
				'setting_email_notify4' => $request->setting_email_notify4,
			];
			member_notify_setting::where('mem_id', '=', session()->get('uID'))->update($set);
		} else {
			$member_notify_setting = new member_notify_setting;
			$member_notify_setting->setting_email_notify1 = $request->setting_email_notify1;
			$member_notify_setting->setting_email_notify2 = $request->setting_email_notify2;
			$member_notify_setting->setting_email_notify3 = $request->setting_email_notify3;
			$member_notify_setting->setting_email_notify4 = $request->setting_email_notify4;
			$member_notify_setting->mem_id = session()->get('uID');
			$member_notify_setting->save();
		}

		return response()->json([]);
	}

	public function set_helper(Request $request)
	{
		$open_offer_setting = 0;
		if(isset($request->open_offer_setting)) {
			$open_offer_setting = $request->open_offer_setting;
		}
		$set = [
			'personal_brief' => $request->personal_brief,
			'open_offer_setting' => $open_offer_setting
		];
		User::where('usr_id', session()->get('usrID'))->update($set);

		$mar = Member_addr_recode::where('id', '=', $request->address)->get();

		$set = [];
		$set = [
			'mem_addr' => $mar[0]->city . $mar[0]->nat . $mar[0]->addr,
		];
		OfferListObj::where('mem_id', session()->get('uID'))->update($set);

		return response()->json(['msg' => '成功']);
	}

	public function get_olo(Request $request)
	{
		$olo = OfferListObj::where('id', $request->id)->get();
		$olo_license_img = olo_license_img::where('olo_id', $request->id)->get();
		$olo_img = olo_img::where('olo_id', $request->id)->get();
		$olo_video = olo_video::where('olo_id', $request->id)->get();
		$olo_food = olo_food::where('olo_id', $request->id)->get();

		return response()->json(['olo' => $olo[0], 'olo_license_img' => $olo_license_img, 'olo_img' => $olo_img, 'olo_video' => $olo_video, 'olo_food' => $olo_food]);
	}

	public function set_olo(Request $request)
	{
		$olo_id = $request->id;
		// 計價方式
		$price_type = $request->price_type;
		$price = $request->price;
		// 服務簡介
		$offer_description = $request->offer_description;
		// 最高學歷
		$education = $request->education;
		$set = [
			'price' => $price,
			'price_type' => $price_type,
			'offer_description' => $offer_description,
			'education' => $education
		];
		OfferListObj::where('id', $olo_id)->update($set);

		// 證照照片
		if(isset($request->old_license_img)) {
			$old_license_img = $request->old_license_img;
			$old_license_img_str = implode(',', $old_license_img);
			olo_license_img::whereRaw("id NOT IN ($old_license_img_str) AND olo_id = $olo_id")->delete();
		} else {
			olo_license_img::whereRaw("olo_id = $olo_id")->delete();
		}

		if($request->file('license_img') !== null) {
			if(!file_exists(storage_path()."/files"))
				mkdir(storage_path()."/files", 0775);
			if(!file_exists(storage_path()."/files/pic"))
				mkdir(storage_path()."/files/pic", 0775);
			if(!file_exists(storage_path()."/files/pic/license_img"))
				mkdir(storage_path()."/files/pic/license_img", 0775);
			if(!file_exists(storage_path()."/files/pic/license_img/photoBig"))
				mkdir(storage_path()."/files/pic/license_img/photoBig", 0775);
			if(!file_exists(storage_path()."/files/pic/license_img/photoSmall"))
				mkdir(storage_path()."/files/pic/license_img/photoSmall", 0775);

			foreach ($request->file('license_img') as $key => $value) {
				$time = strval(time());
				$file = $value;
				$file_name = strtolower($value->getClientOriginalName());
				$subDot = strtolower($value->getClientOriginalExtension());
				if($subDot == 'jpeg' || $subDot == 'jpg' || $subDot == 'JPG') {
					$Fn_name = Utils::getFileName($file_name.$time, $subDot);
					$path = storage_path()."/files/pic/license_img/photoBig/";
					$value->move($path, $Fn_name);
					//chmod($path.$Fn_name, 0775);
					Utils::ImageResize($path.$Fn_name, $path.$Fn_name, 600,600,72);

					copy($path.$Fn_name, storage_path()."/files/pic/license_img/photoSmall/".$Fn_name);
					//chmod(storage_path()."/files/pic/license_img/photoSmall/".$Fn_name, 0775);
					Utils::ImageResize(storage_path()."/files/pic/license_img/photoSmall/".$Fn_name, storage_path()."/files/pic/license_img/photoSmall/".$Fn_name, 200,200,72);

					$olo_license_img_db = new olo_license_img;
					$olo_license_img_db->olo_id = $olo_id;
					$olo_license_img_db->img = $Fn_name;
					$olo_license_img_db->save();
				} else {
					// return View('web/error_message', array('message' => '錯誤的影像格式，請使用JPG圖檔!', 'goUrl'=>'/register'));
					return response()->json(['success' => false, 'msg' => '錯誤的影像格式，請使用JPG圖檔!']);
				}
			}
		}

		// 作品照片
		if(isset($request->old_img)) {
			$old_img = $request->old_img;
			$old_img_str = implode(',', $old_img);
			olo_img::whereRaw("id NOT IN ($old_img_str) AND olo_id = $olo_id")->delete();
		} else {
			olo_img::whereRaw("olo_id = $olo_id")->delete();
		}

		if($request->file('img') !== null) {
			if(!file_exists(storage_path()."/files"))
				mkdir(storage_path()."/files", 0775);
			if(!file_exists(storage_path()."/files/pic"))
				mkdir(storage_path()."/files/pic", 0775);
			if(!file_exists(storage_path()."/files/pic/img"))
				mkdir(storage_path()."/files/pic/img", 0775);
			if(!file_exists(storage_path()."/files/pic/img/photoBig"))
				mkdir(storage_path()."/files/pic/img/photoBig", 0775);
			if(!file_exists(storage_path()."/files/pic/img/photoSmall"))
				mkdir(storage_path()."/files/pic/img/photoSmall", 0775);

			foreach ($request->file('img') as $key => $value) {
				$time = strval(time());
				$file = $value;
				$file_name = strtolower($value->getClientOriginalName());
				$subDot = strtolower($value->getClientOriginalExtension());
				if($subDot == 'jpeg' || $subDot == 'jpg' || $subDot == 'JPG') {
					$Fn_name = Utils::getFileName($file_name.$time, $subDot);
					$path = storage_path()."/files/pic/img/photoBig/";
					$value->move($path, $Fn_name);
					//chmod($path.$Fn_name, 0775);
					// dd(mime_content_type($path.$Fn_name));
					Utils::ImageResize($path.$Fn_name, $path.$Fn_name, 600,600,72);

					copy($path.$Fn_name, storage_path()."/files/pic/img/photoSmall/".$Fn_name);
					//chmod(storage_path()."/files/pic/img/photoSmall/".$Fn_name, 0775);
					Utils::ImageResize(storage_path()."/files/pic/img/photoSmall/".$Fn_name, storage_path()."/files/pic/img/photoSmall/".$Fn_name, 200,200,72);

					$olo_img_db = new olo_img;
					$olo_img_db->olo_id = $olo_id;
					$olo_img_db->img = $Fn_name;
					$olo_img_db->save();
				} else {
					return response()->json(['success' => false, 'msg' => '錯誤的影像格式，請使用JPG圖檔!']);
				}
			}
		}

		// yt影片
		$olo_video = $request->olo_video;
		$olo_video_id = $request->olo_video_id;

		if(isset($request->old_video)) {
			$old_video = $request->old_video;
			$old_video_str = implode(',', $old_video);
			olo_video::whereRaw("id NOT IN ($old_video_str) AND olo_id = $olo_id")->delete();
		} else {
			olo_video::whereRaw("olo_id = $olo_id")->delete();
		}

		if(isset($olo_video_id)) {
			foreach ($olo_video_id as $key => $value) {
				if($value != 0) {
					olo_video::where('id', $value)->update(['url' => $olo_video[$key]]);
				} else {
					$olo_video_db = new olo_video;
					$olo_video_db->url = $olo_video[$key];
					$olo_video_db->olo_id = $olo_id;
					$olo_video_db->save();
				}
			}
		}

		// 家常菜
		$food_title = $request->food_title;
		$food_price = $request->food_price;
		$olo_food_id = $request->olo_food_id;
		$food_img = [];

		if(isset($request->old_food)) {
			$old_food = $request->old_food;
			$old_food_str = implode(',', $old_food);
			olo_food::whereRaw("id NOT IN ($old_food_str) AND olo_id = $olo_id")->delete();
		} else {
			olo_food::whereRaw("olo_id = $olo_id")->delete();
		}

		if($request->file('food_img') !== null) {
			if(!file_exists(storage_path()."/files"))
				mkdir(storage_path()."/files", 0775);
			if(!file_exists(storage_path()."/files/pic"))
				mkdir(storage_path()."/files/pic", 0775);
			if(!file_exists(storage_path()."/files/pic/img"))
				mkdir(storage_path()."/files/pic/img", 0775);
			if(!file_exists(storage_path()."/files/pic/img/photoBig"))
				mkdir(storage_path()."/files/pic/img/photoBig", 0775);
			if(!file_exists(storage_path()."/files/pic/img/photoSmall"))
				mkdir(storage_path()."/files/pic/img/photoSmall", 0775);

			foreach ($request->file('food_img') as $key => $value) {
				$time = strval(time());
				$file = $value;
				$file_name = strtolower($value->getClientOriginalName());
				$subDot = strtolower($value->getClientOriginalExtension());
				if($subDot == 'jpeg' || $subDot == 'jpg' || $subDot == 'JPG') {
					$Fn_name = Utils::getFileName($file_name.$time, $subDot);
					$path = storage_path()."/files/pic/img/photoBig/";
					$value->move($path, $Fn_name);
					//chmod($path.$Fn_name, 0775);
					// dd(mime_content_type($path.$Fn_name));
					Utils::ImageResize($path.$Fn_name, $path.$Fn_name, 600,600,72);

					copy($path.$Fn_name, storage_path()."/files/pic/img/photoSmall/".$Fn_name);
					//chmod(storage_path()."/files/pic/img/photoSmall/".$Fn_name, 0775);
					Utils::ImageResize(storage_path()."/files/pic/img/photoSmall/".$Fn_name, storage_path()."/files/pic/img/photoSmall/".$Fn_name, 200,200,72);

					$food_img[$key] = $Fn_name;
				} else {
					return response()->json(['success' => false, 'msg' => '錯誤的影像格式，請使用JPG圖檔!']);
				}
			}
		}
		foreach ($olo_food_id as $key => $value) {
			if($value != 0) {
				if(isset($food_img[$key])) {
					olo_food::where('id', $value)->update(['title' => $food_title[$key], 'price' => $food_price[$key], 'img' => $food_img[$key]]);
				} else {
					olo_food::where('id', $value)->update(['title' => $food_title[$key], 'price' => $food_price[$key]]);
				}
			} else {
				$olo_food_db = new olo_food;
				$olo_food_db->img = $food_img[$key];
				$olo_food_db->title = $food_title[$key];
				$olo_food_db->price = $food_price[$key];
				$olo_food_db->olo_id = $olo_id;
				$olo_food_db->save();
			}
		}

		return response()->json(['success' => true]);
	}

	public function add_olo(Request $request)
	{
		$service_type_sub = $request->service_type_sub;
		$service_type = '*[' . $request->service_type_main . ',' . $service_type_sub . ']*';
		$mar = Member_addr_recode::where('id', '=', $request->address)->get();

		$mem_addr = $mar[0]->city . $mar[0]->nat . $mar[0]->addr;

		// class_flag = 0, 1, 2
		//
		$class_flag = 0;

		if(isset($request->service_type_sub) && $request->service_type_sub == '美味家常菜') {
			$class_flag = 1;
		}
		if($request->service_type_main == '創意市集' || $request->service_type_main == '二手平台') {
			$class_flag = 1;
		}
		if($request->service_type_main == '專業設計' || $request->service_type_main == '文字工作' || $request->service_type_main == '專業顧問') {
			$class_flag = 2;
		}

		$offer_title = $request->service_type_main;
		if(isset($request->service_type_sub)) {
			$offer_title = $request->service_type_sub;
		}

		$olo = new OfferListObj;
		$olo->mem_id = session()->get('uID');
		$olo->service_type = $service_type;
		$olo->mem_addr = $mem_addr;
		$olo->lat = $mar[0]->lat;
		$olo->lng = $mar[0]->lng;
		$olo->status = 4;
		$olo->class_flag = $class_flag;
		$olo->offer_title = $offer_title;
		$olo->save();

		return response()->json(['success' => true, 'class_flag' => $class_flag, 'offer_title' => $offer_title, 'id' => $olo->id]);
	}

	public function change()
	{
		$user = User::find(session()->get('uID'));
		if($user->usr_type == '1') {
			User::where('id', '=', session()->get('uID'))->update(['usr_type' => 0]);
			session()->put('usr_type', 0);
		} else {
			User::where('id', '=', session()->get('uID'))->update(['usr_type' => 1]);
			session()->put('usr_type', 1);
		}

		return response()->json([]);
	}
}
