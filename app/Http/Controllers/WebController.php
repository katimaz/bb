<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utils\Utils;
use App\Notifications\Notify;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Mail\MailServe;
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
		return view('login',array('message'=>(($request->has('message'))?$request->get('message'):'')));
	}
	 
	public function logout(Request $request)
    {
    	Auth::logout();
		
		$request->session()->flush();
		return redirect('/');
		
	}
	
	public function index(Request $request)
    {
		//dd(basename(url()->current(),".php"));
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
		
		return View('web/'.((Session::has('offer') && Session::get('offer'))?'h-index':'index'), array('home'=>1,'top_pics'=>$top_pics,'center_pics'=>$center_pics,'videoid'=>$videoid,'videotype'=>$video_type));		
			
	}
	
	public function login_pt(Request $request)
    {
		if ($request->has('email') && $request->email && $request->has ( 'password' ))
	   	{
		  if(!User::where('email', '=', $request->email)->count()){
			  return View('/login', array('message'=>json_encode(array('title'=>'喔喔 帳戶或密碼錯誤了 !!','body'=>'您的帳戶或密碼不正確。請重新登入。'))));
		  }else
		  {
			  $user = User::where('email', '=', trim($request->email))->select('id','usr_id','password','usr_status','last_name','first_name','nickname','phone_number','email','remember_token','cookie_id','open_offer_setting','usr_photo')->first();
			  $password = trim($request->password) . ":" . $user->usr_id;
			  
			  $request->session()->flush();
			  if (Auth::attempt(array('email' => trim($request->email), 'password' => $password),((isset($request->remember))?true:false)))
			  {
				  session()->put('uID', $user->id);
				  session()->put('usrID', $user->usr_id);
				  session()->put('usrStatus', $user->usr_status);
				  session()->put('profile', array('first'=>$user->first_name,'last'=>$user->last_name,'nick'=>$user->nickname,'photo'=>$user->usr_photo));
				  session()->put('offer', ((isset($user) && $user->usr_type)?true:false));
				  
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
					  return redirect('/web/profile');
				  else	  
				  {
					  $url = ((Cookie::has('BB_login_cookie'))?Cookie::get('BB_login_cookie'):'/');
					  Cookie::queue(Cookie::forget('BB_login_cookie'));
					  return redirect($url);		  
				  }		
			  
			  }else
			  	return redirect()->action('WebController@login', array('message'=>json_encode(array('title'=>'喔喔 帳戶或密碼錯誤了 !!','body'=>'您的帳戶或密碼不正確。請重新登入。'))));
				
			  if($user->usr_status<0)
			  	return View('/login', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'很抱歉，您的權限不足請聯絡官方服務人員。'))));
			}
		}else
			return redirect()->action('WebController@login', array('message'=>json_encode(array('title'=>'喔喔 帳戶或密碼錯誤了 !!','body'=>'您的帳戶或密碼不正確。請重新登入。'))));
	}
	
	public function signup(Request $request)
	{
		return view('signup',array('message'=>(($request->has('message'))?$request->get('message'):'')));
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
		  return View('/signup', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'輸入表單欄位驗證錯誤!!'))));
		}
		
		if(User::where('email',$request->email)->count())
			return View('/signup', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'您所填寫的Email帳號已有人使用喔!!'))));
		
		$account = Utils::create_name_id(time());
		
		$input = new User;
		$input->usr_id = $account;
		$input->password = Utils::set_password(trim($request->password),trim($account));
		$input->usr_status = 0;
		$input->usr_type = 0;  /* DB usr_type doesn't allow null */
		$input->email = trim($request->email);
		
		$input->save();
		
		$user = User::where('usr_id',$account)->first();
		if(!$user)
			return View('/signup', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'帳號申請未成功!請重新申請一次!!'))));
			
		$request->session()->flush();
		$password = trim($request->password) . ":" . $user->usr_id;
		if(Auth::attempt(array('email' => $user->email, 'password' => $password),true))
		{
			session()->put('uID', $user->id);
			session()->put('usrID', $user->usr_id);
			session()->put('usrStatus', $user->usr_status);
			session()->put('profile', array('first'=>'','last'=>'','nick'=>'','usr_photo'=>''));
			session()->put('offer', ((isset($user) && $user->usr_type)?true:false));
		}
		
		//寄發歡迎加入信件
		//-------------------------寄發信件
		$mail = new MailServe();
		$email = $user->email;
		$mailType = '1-002';
		$data_info_array = array(
			'name' => ((isset($user))?$user->first_name:''),
			//'btn' => array('txt'=>'連結幫棒','url'=>url('/'),'style'=>'button'),
		);
		$mail->sendEmail($mailType,$email,$data_info_array);
		//--------------------end
		
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
	
	public function profile(Request $request)
    {
		
		$user = User::where('usr_id',Session::get('usrID'))->first();
		if(!$user)
		{
			Auth::logout();
			return redirect()->action('WebController@login', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'很抱歉，您無帳號權限喔! 請重新登入。'))));
		}
		
		return View('web/profile', array('user'=>$user,'message'=>(($request->has('message'))?$request->get('message'):''),'offer'=>(($user->open_offer_setting)?true:false)));
		
	}
	
	public function profile_pt(Request $request)
    {
		$mail = new MailServe();
		$email = 'gao@gao.com.tw';
		$mailType = '2-002';
		$user = User::where('id',1)->select('usr_photo','email','first_name','last_name','phone_number')->first()->toArray();
		$data_info_array = array(
			'name' => 'POLO',
			'no' => 'A001254896',
			'item' => '導遊服務',
			'money' => '2000元',
			'number' => '123456',
			'time' => '08:00',
			//'btn' => array('txt'=>'我的夥伴團隊','url'=>url('/').'/','style'=>'button'),
			'btn' => array('txt'=>'查看訂單','url'=>url('/').'/'),
			'btn1' => array('txt'=>'我的回饋金','url'=>url('/').'/'),
			'btn2' => array('txt'=>'推薦賺回饋','url'=>url('/').'/'),
			'array' => array('訂單'=>'A001254896','好幫手'=>array('user'=>$user),'服務名稱'=>'當地導遊','服務地點'=>'台北市','服務時間'=>'08:00~18:00')
		);
		$mail->sendEmail($mailType,$email,$data_info_array);
		if(!Auth::check())
			return redirect()->action('WebController@profile', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'很抱歉，您無帳號權限喔! 請重新登入。'))));
		
		$user = User::where('usr_id',Session::get('usrID'))->select('usr_id','email_validated','usr_status')->first();
		if(!$user)
		{
			Auth::logout();
			return redirect()->action('WebController@profile', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'很抱歉，無此帳號權限喔! 請重新登入。'))));
		}
		$validator = Validator::make($request->all(), array(
			'first_name' => 'string|max:30',
			'last_name' => 'string|max:30',
			'email' => 'required|email',
		));
		if ($validator->fails()) {
		  return redirect()->action('WebController@profile', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'輸入表單欄位驗證錯誤。'))));
		}
		if($request->email!=$request->old_email && User::where('email',$request->email)->where('email','!=',$request->old_email)->count())
			return redirect()->action('WebController@profile', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'您所填寫的郵件帳號已有人使用喔!'))));
		
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
			if($subDot=='jpeg' || $subDot=='jpg' || $subDot=='JPG' || $subDot=='png' || $subDot=='gif')
			{
				$Fn_name = Utils::getAvatarFileName($account);
				$path = storage_path()."/files/pic/avatar/photoBig/";
				$request->file('avatar')->move($path, $Fn_name);
				//chmod($path.$Fn_name, 0775);
				Utils::ImageResize($path.$Fn_name, $path.$Fn_name, 600,600,72,true);
				
				copy($path.$Fn_name, storage_path()."/files/pic/avatar/photoSmall/".$Fn_name);
				//chmod(storage_path()."/files/pic/avatar/photoSmall/".$Fn_name, 0775);
				Utils::ImageResize(storage_path()."/files/pic/avatar/photoSmall/".$Fn_name, storage_path()."/files/pic/avatar/photoSmall/".$Fn_name, 200,200,72,true);
				$input['usr_photo'] = $Fn_name;
			}else
				return redirect()->action('WebController@profile', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'錯誤的影像格式，請使用JPG,PNG,GIF圖檔!'))));
		}
		
		//$input = new User;
		if(isset($request->password) && $request->password && $request->password==$request->chk_password)
			$input['password'] = Utils::set_password(trim($request->password),trim($account));

//		session()->put('usr_type', $request->usr_type);
		$input['usr_type'] = $request->usr_type;
		$input['last_name'] = $request->last_name;
		$input['first_name'] = $request->first_name;
		$input['nickname'] = $request->nickname;
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
		
//		$new_user = User::where('usr_id',$account)->select('id','usr_id','usr_status','email','last_name','first_name','nickname','usr_type','referral_from','usr_photo')->first();
		$new_user = User::where('usr_id',$account)->select('id','usr_id','usr_status','email','last_name','first_name','nickname','usr_type','usr_photo')->first();

		if(!$new_user)
			return redirect()->action('WebController@profile', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'很抱歉，無此帳號權限喔! 請重新登入'))));
		
		$ids = array();
		$member_addrs = Member_addr_recode::where('u_id',$new_user->id)->select('id')->get();
		if(isset($member_addrs))
		{
			foreach($member_addrs as $member_addr)
			{
				$ids[] = $member_addr->id;		
			}
		}
		
		for($i=0;$i<$request->count;$i++)
		{
			if($i<count($ids))
			{
				unset($input);
				$input['u_id'] = $new_user->id;
				$input['city'] = $request['city'.$i];
				$input['nat'] = $request['nat'.$i];
				$input['zip'] = $request['zip'.$i];
				$input['addr'] = $request['addr'.$i];
				$input['lat'] = $request['lat'.$i];
				$input['lng'] = $request['lng'.$i];
				Member_addr_recode::where('id',$ids[$i])->update($input);
			}else
			{
				$input = new Member_addr_recode;
				$input->u_id = $new_user->id;
				$input->city = $request['city'.$i];
				$input->nat = $request['nat'.$i];
				$input->zip = $request['zip'.$i];
				$input->addr = $request['addr'.$i];
				$input->lat = $request['lat'.$i];
				$input->lng = $request['lng'.$i];
				$input->save();	
			}
		}
		
		$num = count($ids)-$request->count;
		for($i=0;$i<$num;$i++)
		{
			$id = $ids[count($ids)-$i-1];
			Member_addr_recode::where('id',$id)->delete();	
		}
			
		session()->put('uID', $new_user->id);
		session()->put('usrID', $new_user->usr_id);
		session()->put('usrStatus', $new_user->usr_status);
		session()->put('profile', array('first'=>$new_user->first_name,'last'=>$new_user->last_name,'nick'=>$new_user->nickname,'photo'=>$new_user->usr_photo));
		session()->put('offer', ((isset($new_user) && $new_user->usr_type)?true:false));
		
		if($new_user->usr_status && $new_user->usr_status!=$user->usr_status)
		{
			//-------------------------寄發信件
			$mail = new MailServe();
			$email = $new_user->email;
			$mailType = '1-008';
			$data_info_array = array(
				'name' => ((isset($new_user))?$new_user->first_name:''),
				'btn' => array('txt'=>'&nbsp;&nbsp;連結幫棒&nbsp;&nbsp;','url'=>url('/'),'style'=>'button'),
			);
			$mail->sendEmail($mailType,$email,$data_info_array);
			//--------------------end
			
			if($new_user->referral_from)
			{
				$referral = User::where('usr_id',$new_user->referral_from)->select('first_name','email')->first();
				if($referral)
				{
					//-------------------------寄發信件
					$mail = new MailServe();
					$email = $referral->email;
					$mailType = '1-006';
					$data_info_array = array(
						'name' => ((isset($new_user))?$new_user->first_name:''),
					);
					$mail->sendEmail($mailType,$email,$data_info_array);
					//--------------------end
				}
			}
			
			return redirect('/web/map');
		}else if($new_user->usr_status)
			return redirect('/web/map');
		else
			return redirect('/web/profile');	

	}
	
	public function veri_mail(Request $request)
    {
		$data = json_decode(Utils::decrypt(base64_decode($request->data), config('bbpro.iv')));
		if(!$data)
			return redirect()->action('WebController@profile', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'Email驗證不成功，請重新執行驗證一次'))));
		
		$veri = User::where('email',$data->email)->select('email_validated','email_valid_key','updated_at')->first();
		
		if($veri->email_validated==1)
			return redirect()->action('WebController@profile', array('message'=>json_encode(array('title'=>'喔喔 重複驗證 !!','body'=>'您已完成電子郵件信箱驗證。'))));
			
		if(!$veri || $veri->email_valid_key!=$data->key)
			return redirect()->action('WebController@profile', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'Email驗證不成功，請重新執行驗證一次'))));
		
		if($veri->updated_at<date("Y-m-d H:i:s", strtotime('-1 day')))
			return redirect()->action('WebController@profile', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'驗證碼已逾期失效，請重新執行驗證一次'))));
			
		$user = User::where('email',$data->email)->select('first_name','last_name','phone_number','usr_id','usr_status','referral_from')->first();
		if(!$user)
			return redirect()->action('WebController@profile', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'Email驗證不成功，請重新執行驗證一次'))));
			
		if($user->first_name!=''&&$user->last_name!=''&& $user->phone_number!='')
			$input['usr_status'] = 1;
		$input['email_validated'] = 1;
		$input['email_valid_key'] = Session::get('veri_id');	
		User::where('email',$data->email)->update($input);
		
		if(isset($input['usr_status']))
		{
			//-------------------------寄發信件
			$mail = new MailServe();
			$email = $user->email;
			$mailType = '1-008';
			$data_info_array = array(
				'name' => ((isset($user))?$user->first_name:''),
				'btn' => array('txt'=>'&nbsp;&nbsp;連結幫棒&nbsp;&nbsp;','url'=>url('/'),'style'=>'button'),
			);
			$mail->sendEmail($mailType,$email,$data_info_array);
			//--------------------end
			
			if($user->referral_from)
			{
				$referral = User::where('usr_id',$user->referral_from)->select('first_name','email')->first();
				if($referral)
				{
					//-------------------------寄發信件
					$mail = new MailServe();
					$email = $referral->email;
					$mailType = '1-006';
					$data_info_array = array(
						'name' => ((isset($user))?$user->first_name:''),
					);
					$mail->sendEmail($mailType,$email,$data_info_array);
					//--------------------end
				}
			}
			
			return redirect('/web/map');
		}else
			return redirect('/web/profile');
				
	}
	
	public function set_forgot_passwd(Request $request)
    {
		$data = json_decode(Utils::decrypt(base64_decode($request->data), config('bbpro.iv')));
		if(!$data)
			return redirect()->action('WebController@login', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'驗證不成功，請重新執行一次'))));
			
		if(Session::get('veri_id')!=$data->key)
			return redirect()->action('WebController@login', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'驗證碼已經有效時間已過，請重新執行一次變更密碼。'))));
			
		$user = User::where('email',$data->email)->select('first_name','last_name','usr_id')->first();
		if(!$user)
			return redirect()->action('WebController@login', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'驗證碼已經有效時間已過，請重新執行一次變更密碼。'))));
		
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
		  return redirect()->action('WebController@login', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'輸入表單欄位驗證錯誤!!'))));
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
	
	public function recommend(Request $request)
    {
		
		if(!Auth::check())
			return redirect()->action('WebController@login', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'很抱歉，您無帳號權限喔! 請重新登入。'))));
			
		$user = User::where('usr_id',Session::get('usrID'))->select('usr_id','usr_status','first_name','last_name','nickname','referral_code','referral_from')->first();
		if(!$user)
		{
			Auth::logout();
			return redirect()->action('WebController@login', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'很抱歉，您無帳號權限喔! 請重新登入。'))));
		}elseif(!$user->usr_status)
			return redirect()->action('WebController@profile', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'尚未執行驗證嗎?請先前往完成驗證程序!'))));
		
		if($user->referral_code)
			$referralCode = $user->referral_code;
		else
		{
			$referralCode = Utils::create_name_id(time());
			User::where('usr_id',$user->usr_id)->update(array('referral_code'=>$referralCode));	
		}
		$data = env('APP_URL').'/%3Fcode='.$referralCode;
		return View('web/recommend', array('user'=>$user, 'data'=>$data));
	}
	
	public static function set_qrcode(Request $request) 
	{
		$data = (($request->has('data'))?$request->get('data'):env('APP_URL'));
		$size = (($request->has('size'))?$request->get('size'):'400x400');
		$logo = (($request->has('logo'))?$request->get('logo'):env('APP_URL').'/images/logo.png');
		
		header('Content-type: image/png');
		// Get QR Code image from Google Chart API
		// http://code.google.com/apis/chart/infographics/docs/qr_codes.html
		$QR = imagecreatefrompng('https://chart.googleapis.com/chart?cht=qr&chld=H|0&chs='.$size.'&chl='.urlencode($data));
		$arrContextOptions=array(
			"ssl"=>array(
				//"cafile" => storage_path()."/ssl/ca_bundle.crt",
				"verify_peer"=> false,
				"verify_peer_name"=> false,
			),
		); 
		
		if($logo){
			$logo = imagecreatefromstring(file_get_contents($logo,false,stream_context_create($arrContextOptions)));
		
			$QR_width = imagesx($QR);
			$QR_height = imagesy($QR);
			
			$logo_width = imagesx($logo);
			$logo_height = imagesy($logo);
			
			// Scale logo to fit in the QR Code
			$logo_qr_width = $QR_width/3;
			$scale = $logo_width/$logo_qr_width;
			$logo_qr_height = $logo_height/$scale;
			
			imagecopyresampled($QR, $logo, $QR_width/3, $QR_height/3, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
		}
		imagepng($QR);
		imagedestroy($QR);  
	}
	
	public function partners(Request $request)
    {
		
		if(!Auth::check())
			return redirect()->action('WebController@login', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'很抱歉，您無帳號權限喔! 請重新登入。'))));
			
		return View('web/partners');
	}
	
	public function calendar(Request $request)
    {
		
		if(!Auth::check())
			return redirect()->action('WebController@login', array('message'=>json_encode(array('title'=>'喔喔 錯誤了 !!','body'=>'很抱歉，您無帳號權限喔! 請重新登入。'))));
			
		return View('web/calendar');
	}
	
	public function newebPay_return_url(Request $request)
    {
		$newebPay = new NewebPay();
		if(!count($request->all()))
		{
			$request->session()->flush();
			return View('web/error', array('message' => '回傳值錯誤，請稍後在試!!'));
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
			return View('web/error', array('message' => '回傳值錯誤，請稍後在試!!'));
		}
		$result = $newebPay->newebPay_return($request);
		//Notify::via('notify','此訂單返回為 : NOTIFY_URL 前往查看 : ! <'.url('/').'/admin/transfer_records?item=newebPay&action=manage'.'|Click here> for details!');
	}
	
	public function newebPay_customer_url(Request $request)
    {
		$newebPay = new NewebPay();
		if(!count($request->all()))
		{
			$request->session()->flush();
			return View('web/error', array('message' => '回傳值錯誤，請稍後在試!!'));
		}
		
		$result = $newebPay->newebPay_customer($request);
		
		//Notify::via('notify','此訂單返回為 : CUSTOMER_URL 前往查看 : ! <'.url('/').'/admin/transfer_records?item=newebPay&action=manage'.'|Click here> for details!');
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
	
	
	public function map(Request $request)
    {
		
		if(!Auth::check())
			return View('web/error', array('message' => '請先登入系統才能繼續喔!!'));
		
		$user = User::where('usr_id',Session::get('usrID'))->select('usr_status','open_offer_setting')->first();
		if(!$user || !$user->usr_status)
		  return View('web/error_message', array('message' => '還沒驗證嗎，請前往個人資訊頁面完成驗證程序!', 'goUrl'=>'/web/profile'));
		$offer = ((isset($user) && $user->open_offer_setting)?true:false);
		
		return View('web/'.(($offer)?'h-map':'map'), array('user'=>$user));
		
	}
	
	

	
}
