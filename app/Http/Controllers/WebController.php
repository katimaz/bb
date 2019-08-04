<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utils\Utils;
use App\Notifications\Notify;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Mail\toMail;
use App\Mail\RegisterWelcome;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\Models\Video;
use App\Models\Pic_table;
use App\Models\Member_addr_recode;
use App\Models\Ezpay;
use App\Models\NewebPay;
use App\Models\Newebpay_mpg;
use App\Models\Invoice;
use App\Models\Invoice_allowance;
use App\Models\OfferListObj;
use App\Models\Users;
use App\Models\einvoice_info;
use App\Models\collection_info;
use App\Models\member_notify_setting;
use Session;
use App\User;
use Auth;
use Cookie;
use DB;
use URL;

class WebController extends Controller
{
    public function login(Request $request)
	{
		Utils::check_cookie();
		$login_cookie = base64_decode($request->p);
		if(isset($login_cookie))
		{
		   Cookie::queue('owed_login_cookie', $login_cookie, 10);
		}

		return view('login');
	}

	public function logout(Request $request)
    {
    	Auth::logout();

		$request->session()->flush();
		return redirect('/');

	}

	public function index(Request $request)
    {
		$video = Video::where('status',1)->select('youtube_id','vimeo_id')->first();
		if(isset($video) && $video->youtube_id)
		{
			$video_type = 1;
			$videoid = Utils::get_video($video->youtube_id);

		}elseif(isset($video) && $video->vimeo_id)
		{
			$video_type = 2;
			$videoid = Utils::get_video($video->youtube_id);
		}else
		{
			$video_type = 0;
			$videoid = '';
		}
		$pics = Pic_table::where('pic_status',1)
			->where(function($query){
			  $query->orWhere('pic_type',1);
			  $query->orWhere('pic_type',2);
			})
			->select('home_frontpage_pic','pic_status','pic_type')->get();

		$top_pics = array();
		$center_pics = array();
		foreach($pics as $pic)
		{
			if($pic->pic_type==1)
				$top_pics[] = $pic->home_frontpage_pic;
			if($pic->pic_type==2)
				$center_pics[] = $pic->home_frontpage_pic;
		}

		return View('web/index', array('home'=>1,'top_pics'=>$top_pics,'center_pics'=>$center_pics,'videoid'=>$videoid,'videotype'=>$video_type));
	}

	public function login_pt(Request $request)
    {
		if ($request->has('email') && $request->email && $request->has ( 'password' ))
	   	{
		  if(!User::where('email', '=', $request->email)->count()){
			  return View('web/error', array('message' => '很抱歉，帳密有問題喔!'));
		  }else
		  {
			  $user = User::where('email', '=', trim($request->email))->select('id','usr_id','password','usr_status','last_name','first_name','phone_number','email','remember_token','cookie_id')->first();
			  $password = trim($request->password) . ":" . $user->usr_id;

			  $request->session()->flush();
			  if (Auth::attempt(array('email' => trim($request->email), 'password' => $password),((isset($request->remember))?true:false)))
			  {
				  session()->put('uID', $user->id);
				  session()->put('usrID', $user->usr_id);
				  session()->put('usrStatus', $user->usr_status);
				  session()->put('usrName', array('first'=>$user->first_name,'last'=>$user->last_name));
				  session()->put('usrPhoto', $user->usr_photo);


				  if(isset($user) && $user->cookie_id)
				  {
					  if($user->cookie_id != Cookie::get('BB_cookie_id'))
					  {
						Cookie::queue(
							Cookie::forever('BB_cookie_id', $user->cookie_id)
						);
					  }
				  }else
				  {
					  $new_cookie = Utils::v4();
					  User::where('usr_id',Session::get('usrID'))->update(array('cookie_id'=>$new_cookie));
					  Cookie::queue(
						  Cookie::forever('BB_cookie_id', $new_cookie)
					  );
				  }
				  if(!$user->usr_status)
					  return redirect('/');
				  else
				  {
					  $url = ((Cookie::has('BB_login_cookie'))?Cookie::get('BB_login_cookie'):'/');
					  Cookie::queue(Cookie::forget('BB_login_cookie'));
					  return redirect($url);
				  }

			  }else
			 	return View('web/error', array('message' => '很抱歉，帳密有問題喔!', 'data'=>''));


			  if($user->usr_status<0)
			  	return View('web/error', array('message' => '很抱歉，您的權限不足請聯絡官方服務人員!', 'data'=>''));
			}
		}else
			return View('web/error', array('message' => '很抱歉，帳密有問題喔!'));
	}

	public function signup_pt(Request $request)
    {

		//Utils::check_cookie();
		$validator = Validator::make($request->all(), array(
			'email' => 'required|email',
			'password' => 'required|string|min:6|max:20',
			'chk_password' => 'required_with:password|same:password|min:6|max:20'
		));

		if ($validator->fails()) {
		  return View('web/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/signup'));
		}

		if(User::where('email',$request->email)->count())
			return View('web/error_message', array('message' => '您所填寫的Email帳號已有人使用喔!!', 'goUrl'=>'/signup'));

		$account = Utils::create_name_id(time());

		$input = new User;
		$input->usr_id = $account;
		$input->password = Utils::set_password(trim($request->password),trim($account));
		$input->usr_status = 0;
		$input->email = trim($request->email);

		$input->save();

		$user = User::where('usr_id',$account)->first();
		if(!$user)
			return View('web/error_message', array('message' => '錯誤了!!帳號申請未成功', 'goUrl'=>'/'));

		$request->session()->flush();
		$password = trim($request->password) . ":" . $user->usr_id;
		if(Auth::attempt(array('email' => $user->email, 'password' => $password),true))
		{
			session()->put('uID', $user->id);
			session()->put('usrID', $user->usr_id);
			session()->put('usrStatus', $user->usr_status);
			session()->put('usrName', array('first'=>'','last'=>''));
		}

		//寄發歡迎加入信件
		$setting = Setting::select('welcome_email_subj','welcome_email_body')->first();
		Mail::to($user->email)->queue(new RegisterWelcome(env('APP_URL'),((isset($user))?$user->first_name:''),((isset($setting) && $setting->welcome_email_subj)?$setting->welcome_email_subj:'歡迎加入BounBang幫棒家族'),((isset($setting)&&$setting->welcome_email_body)?$setting->welcome_email_body:'BounBang幫棒, 您的好幫手。')));

		if($user->cookie_id)
		{
			if($user->cookie_id != Cookie::get('BB_cookie_id'))
			{
			  Cookie::queue(
				  Cookie::forever('BB_cookie_id', $user->cookie_id)
			  );
			}
		}else
			User::where('usr_id',Session::get('usrID'))->update(array('cookie_id'=>Cookie::get('BB_cookie_id')));

		return redirect('/web/profile');

	}

	public function register_pt(Request $request)
    {

		$user = User::where('usr_id',Session::get('usrID'))->select('usr_id','email_validated','usr_status')->first();
		if(!$user)
			return View('web/error', array('message' => '很抱歉，無此帳號權限喔! 請重新登入'));

		$validator = Validator::make($request->all(), array(
			'first_name' => 'string|max:30',
			'last_name' => 'string|max:30',
			'email' => 'required|email',
		));
		if ($validator->fails()) {
		  return View('web/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/web/profile'));
		}
		if($request->email!=$request->old_email && User::where('email',$request->email)->where('email','!=',$request->old_email)->count())
			return View('web/error_message', array('message' => '您所填寫的郵件帳號已有人使用喔!!', 'goUrl'=>'/register'));

		if(!file_exists(storage_path()."/files"))
			mkdir(storage_path()."/files", 0775);
		if(!file_exists(storage_path()."/files/pic"))
			mkdir(storage_path()."/files/pic", 0775);
		if(!file_exists(storage_path()."/files/pic/avatar"))
			mkdir(storage_path()."/files/pic/avatar", 0775);
		if(!file_exists(storage_path()."/files/pic/avatar/photoBig"))
			mkdir(storage_path()."/files/pic/avatar/photoBig", 0775);
		if(!file_exists(storage_path()."/files/pic/avatar/photoSmall"))
			mkdir(storage_path()."/files/pic/avatar/photoSmall", 0775);


		$account = $user->usr_id;
		if($request->file('avatar'))
		{
			$time = strval(time());
			$file = $request->file('avatar');
			$subDot = strtolower($request->avatar->getClientOriginalExtension());
			if($subDot=='jpeg' || $subDot=='jpg' || $subDot=='JPG')
			{
				$Fn_name = Utils::getAvatarFileName($account);
				$path = storage_path()."/files/pic/avatar/photoBig/";
				$request->file('avatar')->move($path, $Fn_name);
				//chmod($path.$Fn_name, 0775);
				Utils::ImageResize($path.$Fn_name, $path.$Fn_name, 600,600,72);

				copy($path.$Fn_name, storage_path()."/files/pic/avatar/photoSmall/".$Fn_name);
				//chmod(storage_path()."/files/pic/avatar/photoSmall/".$Fn_name, 0775);
				Utils::ImageResize(storage_path()."/files/pic/avatar/photoSmall/".$Fn_name, storage_path()."/files/pic/avatar/photoSmall/".$Fn_name, 200,200,72);
				$input['usr_photo'] = $Fn_name;
				session()->put('usrPhoto', $Fn_name);
			}else
				return View('web/error_message', array('message' => '錯誤的影像格式，請使用JPG圖檔!', 'goUrl'=>'/register'));
		}

		//$input = new User;
		if(isset($request->password) && $request->password && $request->password==$request->chk_password)
			$input['password'] = Utils::set_password(trim($request->password),trim($account));


		$input['open_offer_setting'] = $request->open_offer_setting;
		$input['last_name'] = $request->last_name;
		$input['first_name'] = $request->first_name;
		if($user->email_validated)
			$input['usr_status'] = 1;
		$input['sex'] = $request->sex;
		$input['phone_nat_code'] = $request->phone_nat_code;
		$input['phone_number'] = $request->phone_number;
		if(trim($request->email)!=trim($request->old_email))
		{
			$input['email'] = trim($request->email);
			$input['usr_status'] = 0;
			$input['email_validated'] = 0;
		}
		User::where('usr_id',$account)->update($input);

		$new_user = User::where('usr_id',$account)->select('id','usr_id','usr_status','email','last_name','first_name')->first();
		if(!$new_user)
			return View('web/error', array('message' => '很抱歉，無此帳號權限喔! 請重新登入'));

		$address = json_decode($request->all_address);
		$ids = array();
		$member_addrs = Member_addr_recode::where('u_id',$new_user->id)->select('id')->get();
		if(isset($member_addrs))
		{
			foreach($member_addrs as $member_addr)
			{
				$ids[] = $member_addr->id;
			}
		}
		if(isset($address) && count($address))
		{
			$cun = 0;
			foreach($address as $addr)
			{
				if($cun<count($ids))
				{
					unset($input);
					$input['u_id'] = $new_user->id;
					$input['city'] = $addr->county;
					$input['nat'] = $addr->nat;
					$input['zip'] = $addr->zipcode;
					$input['addr'] = $addr->addr;
					$input['lat'] = $addr->lat;
					$input['lng'] = $addr->lng;
					Member_addr_recode::where('id',$ids[$cun])->update($input);
				}else
				{
					$input = new Member_addr_recode;
					$input->u_id = $new_user->id;
					$input->city = $addr->county;
					$input->nat = $addr->nat;
					$input->zip = $addr->zipcode;
					$input->addr = $addr->addr;
					$input->lat = $addr->lat;
					$input->lng = $addr->lng;
					$input->save();
				}
				$cun++;
			}
		}
		$num = count($ids)-count($address);
		for($i=0;$i<$num;$i++)
		{
			$id = $ids[count($ids)-$i-1];
			Member_addr_recode::where('id',$id)->delete();
		}

		session()->put('uID', $new_user->id);
		session()->put('usrID', $new_user->usr_id);
		session()->put('usrStatus', $new_user->usr_status);
		session()->put('usrName', array('first'=>$new_user->first_name,'last'=>$new_user->last_name));

		if($new_user->usr_status && $new_user->usr_status!=$user->usr_status)
		{
			$setting = Setting::select('email_veri_comp_subj','email_veri_comp_body')->first();
			$btn = json_encode(array('url'=>env('APP_URL'),'name'=>'連結幫棒'));
			Mail::to($new_user->email)->queue(new toMail(env('APP_URL'),$btn,((isset($new_user))?$new_user->first_name:''),((isset($setting) && $setting->email_veri_comp_subj)?$setting->email_veri_comp_subj:'BounBang 幫棒 – 您已完成註冊驗證信'),((isset($setting) && $setting->email_veri_comp_body)?$setting->email_veri_comp_body:'歡迎加入 BounBang 幫棒家族，您已完成電子郵件信箱設定<br />歡迎您由此進入幫棒')));
			return redirect('/web/map');
		}else
			return redirect('/web/profile');

	}

	public function profile(Request $request)
    {

		//Utils::check_cookie();
		$user = User::where('usr_id',Session::get('usrID'))->first();
		if(!$user)
		{
		  return View('web/error_message', array('message' => '你的等級不足喔，請洽網站管理員!', 'goUrl'=>'/'));
		}
		return View('web/profile', array('user'=>$user));

	}

	public function map(Request $request)
    {
		//Utils::check_cookie();
		$user = User::where('usr_id',Session::get('usrID'))->first();

		if(!$user)
		{
		  return View('web/error_message', array('message' => '你的等級不足喔，請洽網站管理員!', 'goUrl'=>'/'));
		}

		$keyword = $request->keyword;
		$distance = 10;
		$lat = Session::get('lat');
		$lng = Session::get('lng');

		// 用戶
		if($user->open_offer_setting == "0") {
			// 如果有輸入關鍵字搜尋
			if($keyword != '') {
				$offer = DB::table('OfferListObj AS olo')->select(DB::raw('olo.id, ( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') )* sin( radians( lat ) ) ) ) AS distance, lat, lng, usr_photo, last_name, first_name, u.usr_id'))->join('users AS u', 'olo.mem_id', '=', 'u.id')->where('offer_title', 'like', "%$keyword%")->having('distance','<', $distance)->orderBy('distance')->get();
			} else {
				$offer = DB::table('OfferListObj AS olo')->select(DB::raw('olo.id, ( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') )* sin( radians( lat ) ) ) ) AS distance, lat, lng, usr_photo, last_name, first_name, u.usr_id'))->join('users AS u', 'olo.mem_id', '=', 'u.id')->having('distance','<', $distance)->orderBy('distance')->get();
			}

			$loc = [];
			foreach ($offer as $key => $value) {
				$loc[$key]['addr'] = [$value->lat, $value->lng];
				$loc[$key]['text'] = '<div  class="user_map"><div class="map-up"><div class="up-face"><img src="' . URL::to('/') . '/images/' . $value->usr_photo . '"></div><div class="up-left"><span class="user-name">' . $value->last_name . $value->first_name . '</span><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i><span class="text-success"> 距離：</span><span class="text-danger">' . (int)($value->distance * 1000) . '</span>公尺</div></div></div><div class="map-score"><div class="income"><span class="text-success"> 受雇次數：</span><span class="text-danger">21</span>次</div><div class="income"><span class="text-success"> 當地導遊：</span><span class="text-danger">500元</span>/小時</div><div class="income"><span class="text-success">其他服務：</span>居家清掃、水電工程、木工...<a href="' . URL::to('/') . '/web/helper_detail/' . $value->usr_id . '/' . (int)($value->distance * 1000) . '">詳細說明</a><div class="map-bt"><a href="#" class="lmbt" data-toggle="modal"              data-target="#exampleModalLong">線上諮詢</a><a href="#" class="rmbt" data-toggle="modal"              data-target="#exampleModalLong">雇用</a></div></div></div>';
				$loc[$key]['icon'] = URL::to('/') . '/images/mark.png';
				$loc[$key]['newLabel'] = '<img src="' . URL::to('/') . '/images/' . $value->usr_photo . '" style="border-radius:50%;width:30px;height:30px;margin-top: -95px;border: 2px solid #b30b06;">';
			}
		} else {
			// 幫手
			// 如果有輸入關鍵字搜尋
			if($keyword != '') {
				$offer = DB::table('NeedListObj AS nlo')->select(DB::raw('nlo.id, ( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') )* sin( radians( lat ) ) ) ) AS distance, lat, lng, usr_photo, last_name, first_name, u.usr_id'))->join('users AS u', 'nlo.mem_id', '=', 'u.id')->where('need_title', 'like', "%$keyword%")->having('distance','<', $distance)->orderBy('distance')->get();
			} else {
				$offer = DB::table('NeedListObj AS nlo')->select(DB::raw('nlo.id, ( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') )* sin( radians( lat ) ) ) ) AS distance, lat, lng, usr_photo, last_name, first_name, u.usr_id'))->join('users AS u', 'nlo.mem_id', '=', 'u.id')->having('distance','<', $distance)->orderBy('distance')->get();
			}

			$loc = [];
			foreach ($offer as $key => $value) {
				$loc[$key]['addr'] = [$value->lat, $value->lng];
				$loc[$key]['text'] = '<div  class="user_map"><div class="map-up"><div class="up-face"><img src="' . URL::to('/') . '/images/' . $value->usr_photo . '"></div><div class="up-left"><span class="user-name">' . $value->last_name . $value->first_name . '</span><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i><span class="text-success"> 距離：</span><span class="text-danger">' . (int)($value->distance * 1000) . '</span>公尺</div></div></div><div class="map-score"><div class="income"><span class="text-success"> 受雇次數：</span><span class="text-danger">21</span>次</div><div class="income"><span class="text-success"> 當地導遊：</span><span class="text-danger">500元</span>/小時</div><div class="income"><span class="text-success">其他服務：</span>居家清掃、水電工程、木工...<a href="' . URL::to('/') . '/web/helper_detail/' . $value->usr_id . '/' . (int)($value->distance * 1000) . '">詳細說明</a><div class="map-bt"><a href="#" class="lmbt" data-toggle="modal"              data-target="#exampleModalLong">線上諮詢</a><a href="#" class="rmbt" data-toggle="modal"              data-target="#exampleModalLong">雇用</a></div></div></div>';
				$loc[$key]['icon'] = URL::to('/') . '/images/mark.png';
				$loc[$key]['newLabel'] = '<img src="' . URL::to('/') . '/images/' . $value->usr_photo . '" style="border-radius:50%;width:30px;height:30px;margin-top: -95px;border: 2px solid #b30b06;">';
			}
		}

		return View('web/map', array('user'=>$user, 'loc' => $loc, 'keyword' => $keyword));

	}

	public function list(Request $request)
    {
		//Utils::check_cookie();
		$user = User::where('usr_id',Session::get('usrID'))->first();

		if(!$user)
		{
		  return View('web/error_message', array('message' => '你的等級不足喔，請洽網站管理員!', 'goUrl'=>'/'));
		}

		$keyword = $request->keyword;
		$distance = 10;
		$lat = Session::get('lat');
		$lng = Session::get('lng');

		// 用戶
		if($user->open_offer_setting == "0") {
			if($keyword != '') {
				$offer = DB::table('OfferListObj AS olo')->select(DB::raw('olo.id, ( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') )* sin( radians( lat ) ) ) ) AS distance, lat, lng, usr_photo, last_name, first_name, u.usr_id'))->join('users AS u', 'olo.mem_id', '=', 'u.id')->where('offer_title', 'like', "%$keyword%")->having('distance','<', $distance)->orderBy('distance')->get();
			} else {
				$offer = DB::table('OfferListObj AS olo')->select(DB::raw('olo.id, ( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') )* sin( radians( lat ) ) ) ) AS distance, lat, lng, usr_photo, last_name, first_name, u.usr_id, price, price_type'))->join('users AS u', 'olo.mem_id', '=', 'u.id')->having('distance','<', $distance)->orderBy('distance')->get();
			}

			$loc = [];
			foreach ($offer as $key => $value) {
				$loc[$key] = $value;
				// $loc[$key] = '<div class="list-box"> <div class="list-left"> <span class="b-face"><img src="' . URL::to('/') . '/images/' . $value->usr_photo . '"></span> </div> <a href="' . URL::to('/') . '/web/helper_detail/' . $value->usr_id . '/' . (int)($value->distance * 1000) . '" class="list-right"> <div class="list-name">' . $value->last_name . $value->first_name . '</div> <div  class="list-comm"> <span class="list-start"> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> </span> <span class="avg">4.9</span> </div> <div class="list-ds"><span class="show-m">距離：</span>' . (int)($value->distance * 1000) . '公尺</div> <div class="list-dl"><span class="show-m">受雇次數：</span>256次</div > <div class="list-dl"><span class="show-m">工作時數：</span>125/小時</div > <div class="list-dl"><span class="show-m">服務項目：</span>水電工程</div> <div class="list-dl"><span class="show-m">價格：</span>' . $value->price . '/' . $value->price_type . '</div> <div class="list-types"><img src="images/work1.jpg"><img src="images/works.jpg"></div> </a> <div class="list-bt"> <a href="#" class="lask" data-toggle="modal" data-target="#exampleModalLong">詢問</a> <a href="#" class="lhire"  data-toggle="modal" data-target="#exampleModalLong">雇用</a> </div> </div>';
			}
		} else {
			if($keyword != '') {
				$offer = DB::table('NeedListObj AS nlo')->select(DB::raw('nlo.id, ( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') )* sin( radians( lat ) ) ) ) AS distance, lat, lng, usr_photo, last_name, first_name, u.usr_id'))->join('users AS u', 'nlo.mem_id', '=', 'u.id')->where('offer_title', 'like', "%$keyword%")->having('distance','<', $distance)->orderBy('distance')->get();
			} else {
				$offer = DB::table('NeedListObj AS nlo')->select(DB::raw('nlo.id, ( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') )* sin( radians( lat ) ) ) ) AS distance, lat, lng, usr_photo, last_name, first_name, u.usr_id, budget, budget_type'))->join('users AS u', 'nlo.mem_id', '=', 'u.id')->having('distance','<', $distance)->orderBy('distance')->get();
			}

			$loc = [];
			foreach ($offer as $key => $value) {
				$loc[$key] = $value;
				// $loc[$key] = '<div class="list-box"> <div class="list-left"> <span class="b-face"><img src="' . URL::to('/') . '/images/' . $value->usr_photo . '"></span> </div> <a href="' . URL::to('/') . '/web/helper_detail/' . $value->usr_id . '/' . (int)($value->distance * 1000) . '" class="list-right"> <div class="list-name">' . $value->last_name . $value->first_name . '</div> <div  class="list-comm"> <span class="list-start"> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> </span> <span class="avg">4.9</span> </div> <div class="list-ds"><span class="show-m">距離：</span>' . (int)($value->distance * 1000) . '公尺</div> <div class="list-dl"><span class="show-m">受雇次數：</span>256次</div > <div class="list-dl"><span class="show-m">工作時數：</span>125/小時</div > <div class="list-dl"><span class="show-m">服務項目：</span>水電工程</div> <div class="list-dl"><span class="show-m">價格：</span>' . $value->price . '/' . $value->price_type . '</div> <div class="list-types"><img src="images/work1.jpg"><img src="images/works.jpg"></div> </a> <div class="list-bt"> <a href="#" class="lask" data-toggle="modal" data-target="#exampleModalLong">詢問</a> <a href="#" class="lhire"  data-toggle="modal" data-target="#exampleModalLong">雇用</a> </div> </div>';
			}
		}


		return View('web/list', array('user'=>$user, 'loc' => $loc, 'keyword' => $keyword));

	}

	public function helper_detail($usr_id = '', $distance = 0)
	{
		// 使用者資訊
		$user = Users::where('usr_id', $usr_id)->first()->get();
		// 服務
		$olo = OfferListObj::where('mem_id', $user[0]->id)->get();
		// 評價
		$service_rate = [];

		return View('web/helper_detail', ['distance' => $distance, 'user' => $user[0], 'olo' => $olo]);
	}

	public function einvoice_info()
	{
		$mem_id = session()->get('uID');
		$rs = einvoice_info::where('mem_id', '=', $mem_id)->get();
		return View('web/invoice', array('datalist' => $rs));
	}

	public function collection_info()
	{
		$mem_id = session()->get('uID');
		$rs = collection_info::where('mem_id', '=', $mem_id)->get();
		return View('web/payment', array('collection' => $rs));
	}

	public function set_notify()
	{
		$mem_id = session()->get('uID');
		$rs = member_notify_setting::where('mem_id', '=', $mem_id)->get();
		return View('web/notification', array('datalist' => $rs));
	}
	// 幫手 身份認證
	public function certification(Request $request)
	{
		if(count($request->all()) > 0) {
			$user = User::where('usr_id',Session::get('usrID'))->select('usr_id')->first();

			// 更新資料
			if(!file_exists(storage_path()."/files"))
				mkdir(storage_path()."/files", 0775);
			if(!file_exists(storage_path()."/files/pic"))
				mkdir(storage_path()."/files/pic", 0775);
			if(!file_exists(storage_path()."/files/pic/avatar"))
				mkdir(storage_path()."/files/pic/avatar", 0775);
			if(!file_exists(storage_path()."/files/pic/avatar/photoBig"))
				mkdir(storage_path()."/files/pic/avatar/photoBig", 0775);
			if(!file_exists(storage_path()."/files/pic/avatar/photoSmall"))
				mkdir(storage_path()."/files/pic/avatar/photoSmall", 0775);


			$account = $user->usr_id;
			if($request->file('id_photo'))
			{
				$time = strval(time());
				$file = $request->file('id_photo');
				$subDot = strtolower($request->id_photo->getClientOriginalExtension());
				if($subDot=='jpeg' || $subDot=='jpg' || $subDot=='JPG')
				{
					$Fn_name = Utils::getAvatarFileName($account);
					$path = storage_path()."/files/pic/avatar/photoBig/";
					$request->file('id_photo')->move($path, $Fn_name);
					//chmod($path.$Fn_name, 0775);
					Utils::ImageResize($path.$Fn_name, $path.$Fn_name, 600,600,72);

					copy($path.$Fn_name, storage_path()."/files/pic/avatar/photoSmall/".$Fn_name);
					//chmod(storage_path()."/files/pic/avatar/photoSmall/".$Fn_name, 0775);
					Utils::ImageResize(storage_path()."/files/pic/avatar/photoSmall/".$Fn_name, storage_path()."/files/pic/avatar/photoSmall/".$Fn_name, 200,200,72);
					$input['id_photo'] = $Fn_name;
				}else
					return View('web/error_message', array('message' => '錯誤的影像格式，請使用JPG圖檔!', 'goUrl'=>'/register'));
			}

			if($request->file('id_photo2'))
			{
				$time = strval(time());
				$file = $request->file('id_photo2');
				$subDot = strtolower($request->id_photo2->getClientOriginalExtension());
				if($subDot=='jpeg' || $subDot=='jpg' || $subDot=='JPG')
				{
					$Fn_name = Utils::getAvatarFileName($account);
					$path = storage_path()."/files/pic/avatar/photoBig/";
					$request->file('id_photo2')->move($path, $Fn_name);
					//chmod($path.$Fn_name, 0775);
					Utils::ImageResize($path.$Fn_name, $path.$Fn_name, 600,600,72);

					copy($path.$Fn_name, storage_path()."/files/pic/avatar/photoSmall/".$Fn_name);
					//chmod(storage_path()."/files/pic/avatar/photoSmall/".$Fn_name, 0775);
					Utils::ImageResize(storage_path()."/files/pic/avatar/photoSmall/".$Fn_name, storage_path()."/files/pic/avatar/photoSmall/".$Fn_name, 200,200,72);
					$input['id_photo2'] = $Fn_name;
				}else
					return View('web/error_message', array('message' => '錯誤的影像格式，請使用JPG圖檔!', 'goUrl'=>'/register'));
			}

			$set = [
				'id_name' => $request->id_name,
				'id_number' => $request->id_number,
				'id_year' => $request->id_year,
				'id_month' => $request->id_month,
				'id_day' => $request->id_day,
				'id_type' => $request->id_type,
			];
			if(isset($input['id_photo'])) {
				$set['id_photo'] = $input['id_photo'];
			}
			if(isset($input['id_photo2'])) {
				$set['id_photo2'] = $input['id_photo2'];
			}
			Users::where('id', '=', session()->get('uID'))->update($set);

			return redirect('web/certification');
		} else {
			$user = Users::where('id', '=', session()->get('uID'))->get();
			return View('web/certification', ['user' => $user[0]]);
		}

	}

	// job-detail
	public function job_detail($usr_id = '', $distance = 0)
	{
		// 使用者資訊
		$user = Users::where('usr_id', $usr_id)->first()->get();
		// 服務
		$nlo = NeedListObj::where('mem_id', $user[0]->id)->get();
		// 評價
		$service_rate = [];

		return View('web/job_detail', ['distance' => $distance, 'user' => $user[0], 'nlo' => $nlo]);
	}

	public function veri_mail(Request $request)
    {
		$data = json_decode(Utils::decrypt(base64_decode($request->data), config('bbpro.iv')));
		if(!$data)
			return View('web/error_message', array('message' => 'Email驗證不成功，請重新執行驗證一次', 'goUrl'=>'/web/profile'));
		if(User::where('email',$data->email)->where('email_validated',1)->where('email_valid_key','!=','')->count())
			return View('web/error_message', array('message' => 'Email驗證已完成，無須再驗證!', 'goUrl'=>'/web/profile'));
		if(Session::get('veri_id')!=$data->key)
			return View('web/error_message', array('message' => 'Email驗證不成功，請重新執行驗證一次', 'goUrl'=>'/web/profile'));

		$user = User::where('email',$data->email)->select('first_name','last_name','phone_number','usr_id','usr_status')->first();
		if(!$user)
			return View('web/error_message', array('message' => 'Email驗證不成功，請重新執行驗證一次', 'goUrl'=>'/web/profile'));

		if($user->first_name!=''&&$user->last_name!=''&& $user->phone_number!='')
			$input['usr_status'] = 1;
		$input['email_validated'] = 1;
		$input['email_valid_key'] = Session::get('veri_id');
		User::where('email',$data->email)->update($input);
		$request->session()->forget('veri_id');

		if(isset($input['usr_status']))
		{
			$setting = Setting::select('email_veri_comp_subj','email_veri_comp_body')->first();
			$btn = json_encode(array('url'=>env('APP_URL'),'name'=>'連結幫棒'));
			Mail::to($data->email)->queue(new toMail(env('APP_URL'), $btn,$user->last_name.$user->first_name, ((isset($setting) && $setting->email_veri_comp_subj) ? $setting->email_veri_comp_subj : 'BounBang 幫棒 – 您已完成註冊驗證信'), ((isset($setting) && $setting->email_veri_comp_body) ? $setting->email_veri_comp_body : '歡迎加入 BounBang 幫棒家族，您已完成電子郵件信箱設定<br />歡迎您由此進入幫棒')));
		}
		return redirect('/web/profile');

	}

	public function test()
	{
		Mail::to('iamgodc@gmail.com')->queue(new toMail(env('APP_URL'), 'test', 'BounBang 幫棒 – 您已完成註冊驗證信', '歡迎加入 BounBang 幫棒家族，您已完成電子郵件信箱設定<br />歡迎您由此進入幫棒'));
	}

	public function set_forgot_passwd(Request $request)
    {
		$data = json_decode(Utils::decrypt(base64_decode($request->data), config('bbpro.iv')));
		if(!$data)
			return View('web/error_message', array('message' => 'Email驗證不成功，請重新執行一次', 'goUrl'=>'/web/forgot'));
		if(Session::get('veri_id')!=$data->key)
			return View('web/error_message', array('message' => '驗證碼已經有效時間已過，請重新執行一次變更密碼', 'goUrl'=>'/forgot'));

		$user = User::where('email',$data->email)->select('first_name','last_name','usr_id')->first();
		if(!$user)
			return View('web/error_message', array('message' => '您的權限不足，請重新執行一次', 'goUrl'=>'/forgot'));

		$request->session()->forget('veri_id');

		return View('web/set_forgot_passwd', array('user'=>$user));

	}

	public function set_forgot_passwd_pt(Request $request)
    {

		$validator = Validator::make($request->all(), array(
			'id' => 'required',
			'password' => 'required|string|min:6|max:20',
			'chk_password' => 'required_with:password|same:password|min:6|max:20'
		));

		if ($validator->fails()) {
		  return View('web/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/forgot'));
		}


		$user = User::where('usr_id',$request->id)->update(array('password'=>Utils::set_password(trim($request->password),trim($request->id))));

		return redirect('/login');

	}

	public function term_of_use(Request $request)
    {

		if($request->has('back'))
			$back = $request->get('back');
		$use = Setting::select('term_of_use')->first();
		return View('web/term_of_use', array('term_of_use'=>((isset($use))?$use->term_of_use:NULL),'back'=>((isset($back))?$back:NULL)));

	}

	public function how2help_post_list(Request $request)
    {
		if($request->has('back'))
			$back = $request->get('back');
		$use = Setting::select('how2_help_post_list')->first();
		return View('web/how2help_post_list', array('how2help_post_list'=>((isset($use))?$use->how2_help_post_list:NULL),'back'=>((isset($back))?$back:NULL)));

	}

	public function newebPay_return_url(Request $request)
    {
		$newebPay = new NewebPay();
		if(!count($request->all()))
		{
			$request->session()->flush();
			return View('admin/error', array('message' => '回傳值錯誤，請稍後在試!!'));
		}

		$result = $newebPay->newebPay_return($request);
		if(isset($result) && $result!='error')
		{
			if($result->Status!='SUCCESS')
				$message = $result->Message;

			$noArr = explode('_',$result->Result->MerchantOrderNo);
			if(count($noArr)>1&&$noArr[0]=='BB')
				return redirect('/admin/transfer_records?item=newebPay&action=manage&message='.((isset($message))?$message:''));
			else
				return redirect('/');
		}else
			return redirect('/');
	}

	public function newebPay_notify_url(Request $request)
    {
		$newebPay = new NewebPay();
		if(!count($request->all()))
		{
			$request->session()->flush();
			return View('admin/error', array('message' => '回傳值錯誤，請稍後在試!!'));
		}
		$result = $newebPay->newebPay_return($request);
		//Notify::via('notify','此訂單返回為 : NOTIFY_URL 前往查看 : ! <'.env('APP_URL').'/admin/transfer_records?item=newebPay&action=manage'.'|Click here> for details!');
	}

	public function newebPay_customer_url(Request $request)
    {
		$newebPay = new NewebPay();
		if(!count($request->all()))
		{
			$request->session()->flush();
			return View('admin/error', array('message' => '回傳值錯誤，請稍後在試!!'));
		}

		$result = $newebPay->newebPay_customer($request);

		//Notify::via('notify','此訂單返回為 : CUSTOMER_URL 前往查看 : ! <'.env('APP_URL').'/admin/transfer_records?item=newebPay&action=manage'.'|Click here> for details!');
		/*$noArr = explode('_',$result->Result->MerchantOrderNo);
		if(count($noArr)>1&&$noArr[0]=='BB')
			return redirect('/admin/transfer_records?item=newebPay&action=manage');
		else
			return redirect('/');*/
	}

	public function newebPay_back_url(Request $request)
    {
		return redirect('/');
	}

	public function newebPay_creditCancel_url(Request $request)
    {

		if($request->Status=='SUCCESS')
		{
			$data = json_decode($request->Result);
			$merchant_id = env('newebPay_merchant_id');
			$mer_key = env('newebPay_HashKey');
			$mer_iv = env('newebPay_HashIV');
			$check_code_str = 'HashIV='.$mer_iv.'&Amt='.$data->Amt.'&MerchantID='.$merchant_id.'&MerchantOrderNo='.$data->MerchantOrderNo.'&TradeNo='.$data->TradeNo.'&HashKey='.$mer_key;
			$check_code = strtoupper(hash("sha256", $check_code_str));
			if($data->Result->CheckCode==$check_code)
			{
				$input['TradeStatus'] = 3;
				Newebpay_mpg::where('MerchantOrderNo',$data->MerchantOrderNo)->update($input);
			}else
				Log::alert("藍新驗證碼失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
		}

	}

}