<?php
namespace App\Utils;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\RegisterWelcome;
use Illuminate\Support\Facades\Mail;
use App\User;
use Auth;
use Cookie;
use Session;
use PhpRedis;
use App\Models\Admin_group;
use App\Models\Admin_member;
use App\Models\Ezpay;
use App\Models\NewebPay;
use App\Models\Newebpay_mpg;
use App\Models\Invoice;
use App\Models\Invoice_allowance;

class Utils
{
	
  public static function v4() {
	return sprintf ( '%04x%04x%04x%04x%04x%04x%04x%04x', mt_rand ( 0, 0xffff ), mt_rand ( 0, 0xffff ), 
			  mt_rand ( 0, 0xffff ), mt_rand ( 0, 0x0fff ) | 0x4000, mt_rand ( 0, 0x3fff ) | 0x8000, 
			  mt_rand ( 0, 0xffff ), mt_rand ( 0, 0xffff ), mt_rand ( 0, 0xffff ) ).self::create_name_id(time());
  }

  public static function create_name_id($sn) {

	  return sprintf ( '%04x%04x%04x', mt_rand ( 0, 0xffff ), mt_rand ( 0, 0xffff ), $sn );
  
  }

  public static function getQRCodeUrlFromGoogleChart($data, $width = '250', $error_correction_level = 'M', $margin = 0) {

	  $data = urlencode ( $data );
	  return self::GOOGLE_CHART_API_URL . "cht=qr&chs=$width" . "x" .
			   "$width&chl=$data&chld=$error_correction_level|$margin";
  
  }
  
  public static function set_password($psw,$id) { 
	  return Hash::make($psw . ':' . $id);
  }
  
  public static function get_rand() { 
	  $str="qwertyuiopasdfghjklzxcvbnm1234567890";
	  return substr(str_shuffle($str),5,8);
  }
  
  public static function get_video($video_url) { 
	  
	  	$videoArr = explode('?',$video_url);
		
		if(count($videoArr)>1)
		{
		  $arr = explode('&',$videoArr[1]);
		  $videoArr1 = explode('=',$arr[count($arr)-1]);
		  $video = $videoArr1[1];	
		}else
		  $video = $video_url;
		
		return $video;
  }
  
  public static function getResizePercent($source_w, $source_h, $inside_w, $inside_h)
  {
	  if ($source_w < $inside_w && $source_h < $inside_h) {
		  return 1; // Percent = 1, 如果都比預計縮圖的小就不用縮
	  }
  
	  $w_percent = $inside_w / $source_w;
	  $h_percent = $inside_h / $source_h;
  
	  return ($w_percent > $h_percent) ? $h_percent : $w_percent;
  }
  
  public static function ImageResize($from_filename, $save_filename, $in_width=800, $in_height=800, $quality=100,$cut=false)
  {
	  $allow_format = array('jpeg', 'png', 'gif');
	  $sub_name = $t = '';
  	
	  // Get new dimensions
	  $img_info = getimagesize($from_filename);
	  $width    = $img_info['0'];
	  $height   = $img_info['1'];
	  $imgtype  = $img_info['2'];
	  $imgtag   = $img_info['3'];
	  $bits     = $img_info['bits'];
	  $channels = ((isset($img_info['channels']) && $img_info['channels'])?$img_info['channels']:3);
	  $mime = ((isset($img_info['mime']))?$img_info['mime']:'');
	  
	  list($t, $sub_name) = explode('/', $mime);
	  if ($sub_name == 'jpg') {
		  $sub_name = 'jpeg';
	  }
  
	  if (!in_array($sub_name, $allow_format)) {
		  return false;
	  }
  
	  // 取得縮圖在此範圍內的比例
	  
	  $percent = Utils::getResizePercent($width, $height, $in_width, $in_height);
	 
	  $new_width  = $width * $percent;
	  $new_height = $height * $percent;
  
	  // Resample
	  if($sub_name=='jpeg')
	  {
			$src = imagecreatefromjpeg($from_filename);
			$image_new = imagecreatetruecolor($new_width, $new_height);
			imagecopyresampled($image_new, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	  }
	  
	  if($sub_name=='png')
	  {
			$src = imagecreatefrompng($from_filename);
			$image_new = imagecreatetruecolor($new_width, $new_height);
			imagecopyresampled($image_new, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	  }
	  
	  if($sub_name=='gif')
	  {
			$src = imagecreatefromgif($from_filename);
			$image_new = imagecreatetruecolor($new_width, $new_height);
			imagecopyresampled($image_new, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	  }
	  imagejpeg($image_new, $save_filename, $quality);
	  if($cut)
	  {
		  $newwidth = ((imagesx($image_new)>imagesy($image_new))?imagesy($image_new):imagesx($image_new));
		  $newheight = ((imagesy($image_new)>imagesx($image_new))?imagesx($image_new):imagesy($image_new));
		  $new_img = imagecreatetruecolor($newwidth, $newheight);
		  imagecopyresampled($new_img, $image_new, 0, 0, 0, 0, $newwidth, $newheight, imagesx($image_new), imagesy($image_new));
		  imagejpeg($new_img , $save_filename, $quality);                
		  imagedestroy($new_img);
	 }
  }

  public static function cutphoto($o_photo,$d_photo,$width,$height,$x,$y)
  {
	  $temp_img = ((strpos($o_photo,".png"))?imagecreatefrompng($o_photo):imagecreatefromjpeg($o_photo));
	  $o_width   = imagesx($temp_img);                                 //取得原图宽
	  $o_height = imagesy($temp_img);                                 //取得原图高
	  //判断处理方法

	  if($width>$o_width || $height>$o_height)
	  {         								//原图宽或高比规定的尺寸小,进行压缩
		  $newwidth=$o_width;
		  $newheight=$o_height;
		  if($o_width>$width){
			  $newwidth=$width;
			  $newheight=$o_height*$width/$o_width;
		  }
		   if($newheight>$height){
			   $newwidth=$newwidth*$height/$newheight;
			   $newheight=$height;
		   }
		   //缩略图片
		   $new_img = imagecreatetruecolor($newwidth, $newheight);
		   imagecopyresampled($new_img, $temp_img, 0, 0, 0, 0, $newwidth, $newheight, $o_width, $o_height);
		   imagejpeg($new_img , $d_photo);
		   imagedestroy($new_img);

		   $temp_img = ((strpos($d_photo,".png"))?imagecreatefrompng($d_photo):imagecreatefromjpeg($d_photo));
		   //$temp_img = imagecreatefromjpeg($d_photo);

		   $o_width   = imagesx($temp_img);                                 //取得缩略图宽
		   $o_height = imagesy($temp_img);
		   //裁剪图片
		   $new_imgx = imagecreatetruecolor($width,$height);
		   imagecopyresampled($new_imgx,$temp_img,0,0,$x,$y,$width,$height,$width,$height);
		   imagejpeg($new_imgx , $d_photo);
		   imagedestroy($new_imgx);
	  }else
	  {                                                                                 //原图宽与高都比规定尺寸大,进行压缩后裁剪
		  if($o_height*$width/$o_width>$height){         //先确定width与规定相同,如果height比规定大,则ok
			   $newwidth=$width;
			   $newheight=$o_height*$width/$o_width;
			   //$x=0;
			   //$y=($newheight-$height)/2;
		   }else
		   {
			   $newwidth=$o_width*$height/$o_height;
			   $newheight=$height;
			   //$x=($newwidth-$width)/2;
			   //$y=0;
		  }
		   //缩略图片
		   $new_img = imagecreatetruecolor($newwidth, $newheight);
		   imagecopyresampled($new_img, $temp_img, 0, 0, 0, 0, $newwidth, $newheight, $o_width, $o_height);
		   imagejpeg($new_img , $d_photo);
		   imagedestroy($new_img);

		   $temp_img = ((imagecreatefromjpeg($d_photo))?imagecreatefromjpeg($d_photo):imagecreatefrompng($d_photo));
		   $o_width   = imagesx($temp_img);                                 	//取得缩略图宽
		   $o_height = imagesy($temp_img); 									//取得缩略图高


			//裁剪图片
		   $new_imgx = imagecreatetruecolor($width,$height);
		   imagecopyresampled($new_imgx,$temp_img,0,0,$x,$y,$width,$height,$width,$height);
		   imagejpeg($new_imgx , $d_photo);
		   imagedestroy($new_imgx);

	  }
  }

  public static function toArea($city_id)  
  {
	  return  DB::table('areaClass')->where("city_id","=",$city_id)->select('area_twName','area_id')->get();
  }
  
  public static function serviceAreaInfo($x)
  {
	  $Name = array( 1=>"基隆","台北","宜蘭","桃園","新竹","苗栗","台中","彰化","南投","雲林","嘉義","台南","高雄","屏東","花蓮","台東","澎湖","金門","馬祖");	
	  if($x=='count')
		  return count($Name);
	  else			
		  return $Name[$x];	
  }
  
  public static function encrypt($data, $key) {
	  
	  $encryption_key = base64_decode($key);
	  $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

	  $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
	  
	  return base64_encode($encrypted . '::' . $iv);
  }
  
  public static function decrypt($data, $key) {
	
	if(!$data || $data==NULL)
		return false;
	$encryption_key = base64_decode($key);
	list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
	return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
  
  }

  public static function sms_encrypt($data, $key, $iv)
  {
	  //$message = urlencode($data);
	  $message = $data;
	  //Pkcs7
	  $blocksize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	  $len = strlen($message); //取得字符串长度
	  $pad = $blocksize - ($len % $blocksize); //取得补码的长度
	  $message .= str_repeat(chr($pad), $pad); //用ASCII码为补码长度的字符， 补足最后一段
	  //AES CBC
	  $xcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $message, MCRYPT_MODE_CBC, $iv);
	  //Base64
	  //綠界那邊需要encode兩次，可使用之後再來找原因
	  $encrypted = base64_encode($xcrypt);
	  $encrypted = base64_encode($encrypted);
	  return $encrypted; 	
  }
  
  public static function stripPkcs7Padding($string) {
	  $slast = ord(substr($string, -1));
	  $slastc = chr($slast);
	  $pcheck = substr($string, -$slast);
  
	  if (preg_match("/$slastc{" . $slast . "}/", $string)) {
		  $string = substr($string, 0, strlen($string) - $slast);
		  return $string;
	  } else {
		  return false;
	  }
  }
  
  public static function smsSend($smsMessage, $phoneNo){
	  
	  $gwSMSAPITPID = 'TP0000002';
	  $gwSMSAPIKey = 'F8BA309E42AC0394';
	  $gwSMSAPIIV = '441010271F089FBD';
	  $gwSMSAPIUrl = 'https://sms.ecpay.com.tw/api/Send';
	  
	  $gwSmsAry = array();
	  $gwSmsAry["MessageData"] = urlencode($smsMessage); 
	  $gwSmsAry["ForeignFlag"] = false; 
	  $gwSmsAry["PhoneNo"] = $phoneNo;
	  $data = json_encode($gwSmsAry);
	  $encrypted = Utils::sms_encrypt($data, $gwSMSAPIKey, $gwSMSAPIIV);
	  //$decrypted = Utils::decrypt($encrypted, $gwSMSAPIKey, $gwSMSAPIIV);
	  
	  $reVal = null;

	  $ch = curl_init();
	  curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
	  curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($ch, CURLOPT_POST, true);
	  curl_setopt($ch, CURLOPT_URL, $gwSMSAPIUrl);
	  $PostDataAry = array('ThirdPartyID'=>$gwSMSAPITPID,	'EncryValues'=>$encrypted);
	  curl_setopt($ch, CURLOPT_POST, 1);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($PostDataAry) );
	  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,20);
	  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	  $reVal = json_decode(curl_exec($ch));
	  curl_close($ch);
	  return $reVal;
  
  }
  
  public static function base64_to_jpeg($base64_string, $output_file) {
   
	  $ifp = fopen($output_file, "wb"); 
	  $data = explode(',', $base64_string);
	  fwrite($ifp, base64_decode(((count($data)>1)?$data[1]:$data[0]))); 
	  fclose($ifp); 
	  return $output_file;
  
  }
  
  public static function urldecodeDotNetToPhp($dotNetUrlEncode)
  {
	  $phpUrlEncode = $dotNetUrlEncode;
	  // 取代dotNet 與 Php urldecode 差異的字元
	  $phpUrlEncode = str_replace('-', '%2d', $phpUrlEncode);
	  $phpUrlEncode = str_replace('_', '%5f', $phpUrlEncode);
	  $phpUrlEncode = str_replace('.', '%2e', $phpUrlEncode);
	  $phpUrlEncode = str_replace('!', '%21', $phpUrlEncode);
	  $phpUrlEncode = str_replace('*', '%2a', $phpUrlEncode);
	  $phpUrlEncode = str_replace('(', '%28', $phpUrlEncode);
	  $phpUrlEncode = str_replace(')', '%29', $phpUrlEncode);

	  $urldecode = urldecode($phpUrlEncode);
	  return $urldecode;
  }
  
  public static function urlencodePhpToDotNet($phpData)
  {
	  $phpUrlEncode = urlencode($phpData);

	  $dotNetUrlEncode = $phpUrlEncode;
	  // 取代Php urlencode 與 dotNet 差異的字元
	  $dotNetUrlEncode = str_replace('%2d', '-', $dotNetUrlEncode);
	  $dotNetUrlEncode = str_replace('%5f', '_', $dotNetUrlEncode);
	  $dotNetUrlEncode = str_replace('%2e', '.', $dotNetUrlEncode);
	  $dotNetUrlEncode = str_replace('%21', '!', $dotNetUrlEncode);
	  $dotNetUrlEncode = str_replace('%2a', '*', $dotNetUrlEncode);
	  $dotNetUrlEncode = str_replace('%28', '(', $dotNetUrlEncode);
	  $dotNetUrlEncode = str_replace('%29', ')', $dotNetUrlEncode);

	  return $dotNetUrlEncode;
  }
  
  public static function set_log($cookie_id, $eid=NULL)
  {
	if(!file_exists(storage_path()."/files"))
		mkdir(storage_path()."/files", 0775);
	if(!file_exists(storage_path()."/files/text"))
		mkdir(storage_path()."/files/text", 0777);
	if(!file_exists(storage_path()."/files/text/analysis"))
		mkdir(storage_path()."/files/text/analysis", 0777);	
			
	//dd($cookie_id.' '.$str);
	
	$file = fopen(storage_path().'/files/text/analysis/'.date("Ymd").'.txt', "a+");
	$str = '';
	
	while (!feof($file)) {
		$str .= fgets($file);
	}
	
	if($eid)
	{
		if($str=='')
		{
			$logArr[] = array('uid'=>$cookie_id,'eid'=>array($eid));
			fwrite($file, json_encode($logArr));
		}else
		{
			$file = fopen(storage_path().'/files/text/analysis/'.date("Ymd").'.txt', "w");
			$logArr = json_decode($str);
			if(isset($logArr) && $logArr)
			{
				$chk = 1;
				
				foreach($logArr as $log)
				{
					if($log->uid == $cookie_id)
					{
						if(!in_array($eid, $log->eid))
							array_push($log->eid, $eid);
						$chk = 0;
					}	
				}
				if($chk)
					$logArr[] = array('uid'=>$cookie_id,'eid'=>array($eid));
				fwrite($file, json_encode($logArr));
			}
		}	
	}
	fclose($file);
  
  }
  
  public static function check_cookie()
  {
	//dd(Session::all());
	//dd(Cookie::get(Auth::getRecallerName()));
	//var_dump(Session::all());
	if(Auth::check())
	{
	  	//dd(Cookie::get(Auth::getRecallerName()));
		if(!Session::has('userID'))
		{
			$rememberArr = explode("|",Cookie::get(Auth::getRecallerName()));
			$user = User::where('id',$rememberArr[0])->where('password',$rememberArr[2])->where('remember_token',$rememberArr[1])->select('id','account','level','nickname')->first();
			if(isset($user))
			{
				   
				  session()->put('uID', $user->id);
				  session()->put('userID', $user->account);
				  session()->put('type', $user->level);
				  session()->put('userName', $user->nickname);
				  
				  $table_cookie = User::where('account',$user->account)->select('cookie_id')->first();
				  if(isset($table_cookie) && $table_cookie->cookie_id)
				  {
					  if($table_cookie->cookie_id != Cookie::get('bb_cookie_id'))
					  {
						Cookie::queue(
							Cookie::forever('bb_cookie_id', $table_cookie->cookie_id)
						);	
					  }		
				  }else
					  User::where('account',$user->account)->update(array('cookie_id'=>Cookie::get('bb_cookie_id')));
			
				 //return redirect('/');
			}
		}elseif(User::where('account',Session::get('userID'))->where('status',0)->count())
		{
			Auth::logout();
			return redirect('/');	
		}elseif(!User::where('account',Session::get('userID'))->where('level',Session::get('type'))->count())
		{
			$user = User::where('account',Session::get('userID'))->select('level')->first();
			session()->put('type', $user->level);	
		}
	}		
	
	if(!Cookie::has('bb_cookie_id'))
	{	
	  Cookie::queue(
		  Cookie::forever('bb_cookie_id', uniqid())
	  ); 	
	}	
	
	$urlArr = explode('?', $_SERVER["REQUEST_URI"]);
	if(count($urlArr)>1)
	{
		$url_0_arr = explode('/',$urlArr[0]);
		$class1 = $url_0_arr[count($url_0_arr)-1];
		$url_1_arr = explode('=',$urlArr[1]);
		if(count($url_1_arr)>1)
			$class2 = $url_1_arr[1]; 
	}elseif($_SERVER["REQUEST_URI"]=='/')
		$class1 = $_SERVER["REQUEST_URI"];
	else	
	{
		$url_0_arr = explode('/',$urlArr[0]);
		$class1 = $url_0_arr[count($url_0_arr)-1];
	}
	
	$text = ((Cookie::has('bb_cookie_id'))?Cookie::get('bb_cookie_id'):'none').','.((Auth::check())?Session::get('uID'):'none').','.((isset($class1))?$class1:'none').((isset($class2))?','.$class2:',none');
	
	if(!file_exists(storage_path()."/files"))
		mkdir(storage_path()."/files", 0775);
	if(!file_exists(storage_path()."/files/text"))
		mkdir(storage_path()."/files/text", 0777);
	if(!file_exists(storage_path()."/files/text/analysis"))
		mkdir(storage_path()."/files/text/analysis", 0777);
	
	$file = fopen(storage_path().'/files/text/analysis/'.date("YmdH").'_'.env('MACHINE_NAME').'.txt', "a+");
	fwrite($file, $text.' '.PHP_EOL);
	fclose($file);
	
  }
  
  
  public static function inxClass($x)
  {
	 $arr = array(100=>"/","register","login_pt","account_pt","owner","logout");
	 if(in_array($x, $arr))
	 {
	 	return array_search($x, $arr);
	 }else
	 	return 999;
	 
  }
  
  public static function get_setting()
  {
	 return array('users'=>'會員管理','helper'=>'好幫手管理','publishs'=>'服務刊登管理','transfer_records'=>'交易管理','accountings'=>'帳務管理系統','marketings'=>'行銷內容管理','settings'=>'資訊頁面管理','promotions'=>'折扣促銷管理','videos'=>'影片管理','datas'=>'數據統計與分析','flows'=>'外部流量、廣告設定','logs'=>'日誌(Text file)','customers'=>'客服 Portal'); 
  }
  
  public static function links()
  {
  	$handler = opendir(storage_path().'/files/text/analysis');
	while(($filename = readdir($handler)) !== false)
	{
	  if($filename != "." && $filename != "..") {
		$nameArr = explode('_',$filename);
		if($nameArr[0]>date("YmdH",strtotime("-30 day")))
			$files[] = $filename ;
	  }
	}
	closedir($handler);
	$indexArrs = array();
	foreach ($files as $value){
		
	  $file = fopen(storage_path().'/files/text/analysis/'.$value, "r");
	  while (!feof($file)) {
		  $arr = explode(" ",fgets($file));
		  if(isset($arr) && count($arr)>3 && $arr[1] == Cookie::get('bb_cookie_id') && $arr[2]==102)
		  {
			 if(!is_null($arr[3]) && strlen($arr[3])>10)
				$indexArrs[] = $arr[3];
		  }
	  }
	  fclose($file);
	}
	$sort_count = array_count_values($indexArrs);
	$views = array();
	$event_ids = array();
	foreach($sort_count as $key => $value)
	{
		if(isset($key))
		{
			$views[] = json_decode('{"cun":"'.$value.'","eid":"'.$key.'"}');
			$event_ids[] = $key;
		}
	}
	
	rsort($views);
	
	return json_decode('{"views":'.json_encode($views).',"event_ids":'.json_encode($event_ids).'}');
	 
  }
  
  //取得群組id
  public static function get_groupid($id) {
      
      $group = Admin_group::where('group_id',$id)->select('id')->first();
	  return $group->id;
  }
  
  //取得職員 id
  public static function get_adminid($id) {
      
      $admin = Admin_member::where('adm_account',$id)->select('id')->first();
	  return $admin->id;
  }
  
  
  // 取得使用者頭像檔名
  public static function getAvatarFileName($userAccount) {
      $salt = substr(md5($userAccount), -16);
      return md5($userAccount.$salt).'.jpg';
  }

  // 取得檔名
  public static function getFileName($userAccount, $subDot) {
      $salt = substr(md5($userAccount), -16);
      return md5($userAccount.$salt) . '.' . $subDot;
  }

  public static function getAvatar($userAccount) {
      $salt = substr(md5($userAccount), -16);
      
	  $avatar = glob(storage_path().'/files/pic/avatar/photoSmall/'.md5($userAccount.$salt).'.jpg');
	  
	  if(count($avatar))
		{
		  $photoArr = explode("/",$avatar[0]);
		  $name = $photoArr[count($photoArr)-1];
		  
		  $imgsize = getimagesize(storage_path().'/files/pic/avatar/photoSmall/'.$photoArr[count($photoArr)-1]);
		  
		  if(isset($imgsize))
		  {
			if($imgsize[0]>=$imgsize[1])
				$size = 1;
			else
				$size = 2;  
		  }
		}else
		{
			$name = NULL;
			$size = NULL;
		}
		return array('name'=>$name,'size'=>$size);
  }
  
  public static function getPhoto($mode,$userAccount,$sub) {
      
	$imgArr =  glob(storage_path().'/files/pic/'.$mode.'/photoBig/'.$userAccount.(($sub)?'_'.$sub.'*':'*')); 
	if(count($imgArr))
	{
		rsort($imgArr);
		$photoArr = explode('/',$imgArr[0]);
		
		$name = '/'.$mode.'/photoBig/'.$photoArr[count($photoArr)-1];
		$imgsize = getimagesize(storage_path().'/files/pic/'.$name);
		if(isset($imgsize))
		{
		  if($imgsize[0]>=$imgsize[1])
			  $size = 1;
		  else
			  $size = 2;  
		}
	}else
	{
		$name = NULL;
		$size = NULL;	
	}
	return array('name'=>$name,'size'=>$size);
  }
  
  public static function AddLink2Text($str) {
//	 $str = preg_replace("#(http://[0-9a-z._/?=&;]+)#i","<a href=\"\\1\" target=\"_blank\">\\1</a>", $str);
//	 $str = preg_replace("#(https://[0-9a-z._/?=&;]+)#i","<a href=\"\\1\" target=\"_blank\">\\1</a>", $str);
//   $str = preg_replace("#([0-9a-z._]+@[0-9a-z._?=]+)#i","<a href=\"mailto:\\1\">\\1</a>", $str);

     $url = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
     $str = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $str);

     return $str;
  }
  
  public static function get_redis()
  {
	  //緩存一段時間才更新時間，首次執行後請更新網頁，超過過期時間才會更新新時間
	  $key = 'tags_class';
	  $ttlTime = 300; //過期時間 單位:秒
	  $main_tags = Tag::where('type', 1)->where('on', 1)->select('name')->orderBy('order')->get();
	  foreach($main_tags as $main_tag)
	  {
		 $main_tag->search_type = '9999';
		 $main_tag->search_condition = ''; 
	  }
	  
	  $areas = EventArea::where('on',1)->select('area_name','search_type','search_condition')->get();
	  foreach($areas as $area)
	  {
		 $thisvalue = json_decode('{"name":"'.$area->area_name.'","search_type":"'.$area->search_type.'","search_condition":"'.join(",",json_decode($area->search_condition)).'"}');
		 
		 if(isset($thisvalue))
		 	$main_tags[] = json_decode('{"name":"'.$area->area_name.'","search_type":"'.$area->search_type.'","search_condition":"'.join(",",json_decode($area->search_condition)).'"}'); 
	  }
	  
	  //不存在才設定
	  if(!PhpRedis::exists($key)){
		  //設定Key:Value和過期時間
		 PhpRedis::setex($key,$ttlTime,json_encode($main_tags));
	  }
	  //取得設定的值
	  //$value = PhpRedis::get($key);
	  return json_decode(PhpRedis::get($key));
  }
  
   public static function get_area() {
      $data = array(
		  '基市'=>'基隆市',
		  '北市'=>'台北市',
		  '新北市'=>'新北市',
		  '北縣'=>'台北縣',
		  '桃市'=>'桃園市',
		  '桃縣'=>'桃園縣',
		  '竹市'=>'新竹市',
		  '竹縣'=>'新竹縣',
		  '苗縣'=>'苗栗縣',
		  '中市'=>'台中市',
		  '中縣'=>'台中縣',
		  '彰縣'=>'彰化縣',
		  '投縣'=>'南投縣',
		  '雲縣'=>'雲林縣',
		  '義市'=>'嘉義市',
		  '嘉縣'=>'嘉義縣',
		  '南市'=>'台南市',
		  '南縣'=>'台南縣',
		  '高市'=>'高雄市',
		  '高縣'=>'高雄縣',
		  '屏縣'=>'屏東縣',
		  '宜縣'=>'宜蘭縣',
		  '花縣'=>'花蓮縣',
		  '東縣'=>'台東縣',
		  '連江'=>'連江縣',
		  '金門'=>'金門縣',
		  '澎縣'=>'澎湖縣'
	  );
	  return $data;
  }
  
  public static function get_area_zipcode() {
      $data = array(
		   '基隆市' =>array('English'=>'Keelung','Zip'=>array('仁愛區'=> '200', '信義區'=> '201', '中正區'=> '202', '中山區'=> '203', '安樂區'=> '204', '暖暖區'=> '205', '七堵區'=> '206')),
		   
		   '臺北市' => array('English'=>'Taipei','Zip'=>array('中正區'=> '100', '大同區'=> '103', '中山區'=> '104', '松山區'=> '105', '大安區'=> '106', '萬華區'=> '108', '信義區'=> '110', '士林區'=> '111', '北投區'=> '112', '內湖區'=> '114', '南港區'=> '115', '文山區'=> '116')),
		   
		   '新北市' =>array('English'=>'New Taipei','Zip'=>array('萬里區'=> '207', '金山區'=> '208', '板橋區'=> '220', '汐止區'=> '221', '深坑區'=> '222', '石碇區'=> '223','瑞芳區'=> '224', '平溪區'=> '226', '雙溪區'=> '227', '貢寮區'=> '228', '新店區'=> '231', '坪林區'=> '232','烏來區'=> '233', '永和區'=> '234', '中和區'=> '235', '土城區'=> '236', '三峽區'=> '237', '樹林區'=> '238','鶯歌區'=> '239', '三重區'=> '241', '新莊區'=> '242', '泰山區'=> '243', '林口區'=> '244', '蘆洲區'=> '247','五股區'=> '248', '八里區'=> '249', '淡水區'=> '251', '三芝區'=> '252', '石門區'=> '253')),
		 
		  '宜蘭縣' =>array('English'=>'Yilan','Zip'=>array('宜蘭市'=> '260', '頭城鎮'=> '261', '礁溪鄉'=> '262', '壯圍鄉'=> '263', '員山鄉'=> '264', '羅東鎮'=> '265','三星鄉'=> '266', '大同鄉'=> '267', '五結鄉'=> '268', '冬山鄉'=> '269', '蘇澳鎮'=> '270', '南澳鄉'=> '272','釣魚臺列嶼'=> '290')),
		  
		  '桃園市' => array('English'=>'Taoyuan','Zip'=>array('中壢區'=> '320', '平鎮區'=> '324', '龍潭區'=> '325', '楊梅區'=> '326', '新屋區'=> '327', '觀音區'=> '328','桃園區'=> '330', '龜山區'=> '333', '八德區'=> '334', '大溪區'=> '335', '復興區'=> '336', '大園區'=> '337','蘆竹區'=> '338')),
		  
		  '新竹市' =>array('English'=>'Hsinchu','Zip'=>array('東區'=> '300', '北區'=> '300', '香山區'=> '300')),
		  
		  '新竹縣' =>array('English'=>'Hsinchu','Zip'=>array('竹北市'=> '302', '湖口鄉'=> '303', '新豐鄉'=> '304', '新埔鎮'=> '305', '關西鎮'=> '306', '芎林鄉'=> '307','寶山鄉'=> '308', '竹東鎮'=> '310', '五峰鄉'=> '311', '橫山鄉'=> '312', '尖石鄉'=> '313', '北埔鄉'=> '314','峨眉鄉'=> '315')),
		  
		'苗栗縣' =>array('English'=>'Miaoli','Zip'=>array('竹南鎮'=> '350', '頭份市'=> '351', '三灣鄉'=> '352', '南庄鄉'=> '353', '獅潭鄉'=> '354', '後龍鎮'=> '356','通霄鎮'=> '357', '苑裡鎮'=> '358', '苗栗市'=> '360', '造橋鄉'=> '361', '頭屋鄉'=> '362', '公館鄉'=> '363','大湖鄉'=> '364', '泰安鄉'=> '365', '銅鑼鄉'=> '366', '三義鄉'=> '367', '西湖鄉'=> '368', '卓蘭鎮'=> '369')),
		
		'臺中市' =>array('English'=>'Taichung','Zip'=>array('中區'=> '400', '東區'=> '401', '南區'=> '402', '西區'=> '403', '北區'=> '404', '北屯區'=> '406', '西屯區'=> '407', '南屯區'=> '408','太平區'=> '411', '大里區'=> '412', '霧峰區'=> '413', '烏日區'=> '414', '豐原區'=> '420', '后里區'=> '421','石岡區'=> '422', '東勢區'=> '423', '和平區'=> '424', '新社區'=> '426', '潭子區'=> '427', '大雅區'=> '428','神岡區'=> '429', '大肚區'=> '432', '沙鹿區'=> '433', '龍井區'=> '434', '梧棲區'=> '435', '清水區'=> '436','大甲區'=> '437', '外埔區'=> '438', '大安區'=> '439')),
		  
		  '彰化縣' =>array('English'=>'Changhua','Zip'=>array('彰化市'=> '500', '芬園鄉'=> '502', '花壇鄉'=> '503', '秀水鄉'=> '504', '鹿港鎮'=> '505', '福興鄉'=> '506','線西鄉'=> '507', '和美鎮'=> '508', '伸港鄉'=> '509', '員林市'=> '510', '社頭鄉'=> '511', '永靖鄉'=> '512','埔心鄉'=> '513', '溪湖鎮'=> '514', '大村鄉'=> '515', '埔鹽鄉'=> '516', '田中鎮'=> '520', '北斗鎮'=> '521','田尾鄉'=> '522', '埤頭鄉'=> '523', '溪州鄉'=> '524', '竹塘鄉'=> '525', '二林鎮'=> '526', '大城鄉'=> '527','芳苑鄉'=> '528', '二水鄉'=> '530')),
		  '南投縣' =>array('English'=>'Nantou','Zip'=>array('南投市'=> '540', '中寮鄉'=> '541', '草屯鎮'=> '542', '國姓鄉'=> '544', '埔里鎮'=> '545', '仁愛鄉'=> '546','名間鄉'=> '551', '集集鎮'=> '552', '水里鄉'=> '553', '魚池鄉'=> '555', '信義鄉'=> '556', '竹山鎮'=> '557','鹿谷鄉'=> '558')), 
		  
		  '雲林縣' =>array('English'=>'Yunlin','Zip'=>array('斗南鎮'=> '630', '大埤鄉'=> '631', '虎尾鎮'=> '632', '土庫鎮'=> '633', '褒忠鄉'=> '634', '東勢鄉'=> '635','臺西鄉'=> '636', '崙背鄉'=> '637', '麥寮鄉'=> '638', '斗六市'=> '640', '林內鄉'=> '643', '古坑鄉'=> '646','莿桐鄉'=> '647', '西螺鎮'=> '648', '二崙鄉'=> '649', '北港鎮'=> '651', '水林鄉'=> '652', '口湖鄉'=> '653','四湖鄉'=> '654', '元長鄉'=> '655')),
		  
		  '嘉義市' =>array('English'=>'Chiayi','Zip'=>array('東區'=> '600', '西區'=> '600')),
		  
		  '嘉義縣' =>array('English'=>'Chiayi','Zip'=>array('番路鄉'=> '602', '梅山鄉'=> '603', '竹崎鄉'=> '604', '阿里山'=> '605', '中埔鄉'=> '606', '大埔鄉'=> '607','水上鄉'=> '608', '鹿草鄉'=> '611', '太保市'=> '612', '朴子市'=> '613', '東石鄉'=> '614', '六腳鄉'=> '615','新港鄉'=> '616', '民雄鄉'=> '621', '大林鎮'=> '622', '溪口鄉'=> '623', '義竹鄉'=> '624', '布袋鎮'=> '625')), 
		  
		  '臺南市' =>array('English'=>'Tainan','Zip'=>array('中西區'=> '700', '東區'=> '701', '南區'=> '702', '北區'=> '704', '安平區'=> '708', '安南區'=> '709','永康區'=> '710', '歸仁區'=> '711', '新化區'=> '712', '左鎮區'=> '713', '玉井區'=> '714', '楠西區'=> '715','南化區'=> '716', '仁德區'=> '717', '關廟區'=> '718', '龍崎區'=> '719', '官田區'=> '720', '麻豆區'=> '721','佳里區'=> '722', '西港區'=> '723', '七股區'=> '724', '將軍區'=> '725', '學甲區'=> '726', '北門區'=> '727','新營區'=> '730', '後壁區'=> '731', '白河區'=> '732', '東山區'=> '733', '六甲區'=> '734', '下營區'=> '735','柳營區'=> '736', '鹽水區'=> '737', '善化區'=> '741', '大內區'=> '742', '山上區'=> '743', '新市區'=> '744','安定區'=> '745')),
		  
		  '高雄市' =>array('English'=>'Kaohsiung','Zip'=>array('新興區'=> '800', '前金區'=> '801', '苓雅區'=> '802', '鹽埕區'=> '803', '鼓山區'=> '804', '旗津區'=> '805','前鎮區'=> '806', '三民區'=> '807', '楠梓區'=> '811', '小港區'=> '812', '左營區'=> '813','仁武區'=> '814', '大社區'=> '815', '東沙群島'=> '817', '南沙群島'=> '819', '岡山區'=> '820', '路竹區'=> '821','阿蓮區'=> '822', '田寮區'=> '823','燕巢區'=> '824', '橋頭區'=> '825', '梓官區'=> '826', '彌陀區'=> '827', '永安區'=> '828', '湖內區'=> '829','鳳山區'=> '830', '大寮區'=> '831', '林園區'=> '832', '鳥松區'=> '833', '大樹區'=> '840', '旗山區'=> '842','美濃區'=> '843', '六龜區'=> '844', '內門區'=> '845', '杉林區'=> '846', '甲仙區'=> '847', '桃源區'=> '848','那瑪夏區'=> '849', '茂林區'=> '851', '茄萣區'=> '852')),
		  
		  '屏東縣' =>array('English'=>'Pingtung','Zip'=>array('屏東市'=> '900', '三地門鄉'=> '901', '霧臺鄉'=> '902', '瑪家鄉'=> '903', '九如鄉'=> '904', '里港鄉'=> '905','高樹鄉'=> '906', '鹽埔鄉'=> '907', '長治鄉'=> '908', '麟洛鄉'=> '909', '竹田鄉'=> '911', '內埔鄉'=> '912','萬丹鄉'=> '913', '潮州鎮'=> '920', '泰武鄉'=> '921', '來義鄉'=> '922', '萬巒鄉'=> '923', '崁頂鄉'=> '924','新埤鄉'=> '925', '南州鄉'=> '926', '林邊鄉'=> '927', '東港鎮'=> '928', '琉球鄉'=> '929', '佳冬鄉'=> '931','新園鄉'=> '932', '枋寮鄉'=> '940', '枋山鄉'=> '941', '春日鄉'=> '942', '獅子鄉'=> '943', '車城鄉'=> '944','牡丹鄉'=> '945', '恆春鎮'=> '946', '滿州鄉'=> '947')),
		  
		  '臺東縣' =>array('English'=>'Taitung','Zip'=>array('臺東市'=> '950', '綠島鄉'=> '951', '蘭嶼鄉'=> '952', '延平鄉'=> '953', '卑南鄉'=> '954', '鹿野鄉'=> '955','關山鎮'=> '956', '海端鄉'=> '957', '池上鄉'=> '958', '東河鄉'=> '959', '成功鎮'=> '961', '長濱鄉'=> '962','太麻里鄉'=> '963', '金峰鄉'=> '964', '大武鄉'=> '965', '達仁鄉'=> '966')),
		  
		  '花蓮縣' =>array('English'=>'Hualien','Zip'=>array('花蓮市'=> '970', '新城鄉'=> '971', '秀林鄉'=> '972', '吉安鄉'=> '973', '壽豐鄉'=> '974', '鳳林鎮'=> '975', '光復鄉'=> '976', '豐濱鄉'=> '977', '瑞穗鄉'=> '978', '萬榮鄉'=> '979', '玉里鎮'=> '981', '卓溪鄉'=> '982','富里鄉'=> '983')),
		  
		  '澎湖縣' =>array('English'=>'Penghu','Zip'=>array('馬公市'=> '880', '西嶼鄉'=> '881', '望安鄉'=> '882', '七美鄉'=> '883', '白沙鄉'=> '884', '湖西鄉'=> '885')),
		  
		  '金門縣' =>array('English'=>'Kinmen','Zip'=>array('金沙鎮'=> '890', '金湖鎮'=> '891', '金寧鄉'=> '892', '金城鎮'=> '893', '烈嶼鄉'=> '894', '烏坵鄉'=> '896')),
		  
		  '連江縣' =>array('English'=>'Lienchiang','Zip'=>array('南竿鄉'=> '209', '北竿鄉'=> '210', '莒光鄉'=> '211', '東引鄉'=> '212'))
	  );
	  return $data;
  }
  
  public static function get_BusinessType() {
      $data = array(
		  '4225'=>'倉儲服務',
		  '4722'=>'旅行社',
		  '4812'=>'電話通訊設備及服務',
		  '4899'=>'有線電視',
		  '5045'=>'3C商品',
		  '5094'=>'寶石/黃金/珠寶貴重物',
		  '5192'=>'書報雜誌',
		  '5261'=>'園藝用品',
		  '5399'=>'一般商品買賣',
		  '5422'=>'冷凍食品',
		  '5462'=>'西點麵包',
		  '5499'=>'食品名特產',
		  '5699'=>'服飾配件',
		  '5732'=>'電器行',
		  '5812'=>'餐廳',
		  '5941'=>'運動商品 ',
		  '5946'=>'攝影用品',
		  '5963'=>'直銷',
		  '4225'=>'倉儲服務',
		  '4722'=>'旅行社',
		  '4812'=>'電話通訊設備及服務',
		  '4899'=>'有線電視',
		  '5045'=>'3C商品',
		  '5094'=>'寶石/黃金/珠寶貴重物',
		  '5192'=>'書報雜誌',
		  '5261'=>'園藝用品',
		  '5399'=>'一般商品買賣',
		  '5422'=>'冷凍食品',
		  '5462'=>'西點麵包',
		  '5499'=>'食品名特產',
		  '5699'=>'服飾配件',
		  '5732'=>'電器行',
		  '5812'=>'餐廳',
		  '5941'=>'運動商品',
		  '5946'=>'攝影用品',
		  '5963'=>'直銷',
		  '5977'=>'化妝/美容保養產品',
		  '5992'=>'花店',
		  '5995'=>'寵物用品',
		  '7011'=>'飯店/民宿',
		  '7261'=>'喪葬服務及用品',
		  '7298'=>'美容美體服務',
		  '7311'=>'廣告服務',
		  '7372'=>'網路資訊服務',
		  '7392'=>'諮詢服務',
		  '7519'=>'休閒交通工具租借',
		  '7996'=>'樂區 / 博覽會',
		  '7999'=>'娛樂休閒服務',
		  '8220'=>'學校',
		  '8299'=>'補習/教學服務',
		  '8398'=>'社會福利團體',
		  '8651'=>'政治團體',
		  '8661'=>'宗教團體',
		  '8999'=>'其他專業服務'
	  );
	  return $data;
  }
  
  public static function get_bank_code() {
			   
	  $data = json_decode('[{"code":"004","name":"臺灣銀行"},{"code":"005","name":"臺灣土地銀行"},{"code":"006","name":"合作金庫商業銀行"},{"code":"007","name":"第一商業銀行"},{"code":"008","name":"華南商業銀行"},{"code":"009","name":"彰化商業銀行"},{"code":"011","name":"上海商業儲蓄銀行"},{"code":"012","name":"台北富邦商業銀行"},{"code":"013","name":"國泰世華商業銀行"},{"code":"016","name":"高雄銀行"},{"code":"017","name":"兆豐國際商業銀行"},{"code":"021","name":"花旗(台灣)商業銀行"},{"code":"050","name":"臺灣中小企業銀行"},{"code":"052","name":"渣打國際商業銀行"},{"code":"053","name":"台中商業銀行"},{"code":"054","name":"京城商業銀行"},{"code":"101","name":"瑞興商業銀行"},{"code":"102","name":"華泰商業銀行"},{"code":"103","name":"臺灣新光商業銀行"},{"code":"108","name":"陽信商業銀行"},{"code":"114","name":"基隆第一信用合作社"},{"code":"115","name":"基隆市第二信用合作社"},{"code":"118","name":"板信商業銀行"},{"code":"119","name":"淡水第一信用合作社"},{"code":"130","name":"新竹第一信用合作社"},{"code":"132","name":"新竹第三信用合作社"},{"code":"146","name":"台中市第二信用合作社"},{"code":"147","name":"三信商業銀行"},{"code":"162","name":"彰化第六信用合作社"},{"code":"165","name":"彰化縣鹿港信用合作社"},{"code":"215","name":"花蓮第一信用合作社"},{"code":"216","name":"花蓮第二信用合作社"},{"code":"600","name":"財團法人全國農漁業及金融資訊中心"},{"code":"700","name":"中華郵政股份有限公司"},{"code":"803","name":"聯邦商業銀行"},{"code":"805","name":"遠東國際商業銀行"},{"code":"806","name":"元大商業銀行"},{"code":"807","name":"永豐商業銀行"},{"code":"808","name":"玉山商業銀行"},{"code":"809","name":"凱基商業銀行"},{"code":"812","name":"台新國際商業銀行"},{"code":"815","name":"日盛國際商業銀行"},{"code":"816","name":"安泰商業銀行"},{"code":"822","name":"中國信託商業銀行"},{"code":"910","name":"財團法人農漁會聯合資訊中心"},{"code":"928","name":"板橋區農會電腦共用中心"},{"code":"951","name":"新北市農會附設北區農會電腦共同利用中心"},{"code":"952","name":"財團法人農漁會南區資訊中心"},{"code":"997","name":"中華民國信用合作社聯合社南區聯合資訊處理中心"}]
');
	  return $data;
  }
  
}
