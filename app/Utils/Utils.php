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
  
  //test_mailqueue使用 Start
  public static function test_mailqueue()
  {
	  echo "Mail Start<br>";
	  $start = date('Y-m-d H:i:s', time());
	  echo "Start Time:".$start."<br>";
	  Mail::to('slpl1206@gmail.com')->queue(new RegisterWelcome("slpl (".$start.")"));
	  echo "End Time:".date('Y-m-d H:i:s', time())."<br>";
	  echo "Mail End<br>";
  }
  //test_mailqueue使用 End
	
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
  
  public static function ImageResize($from_filename, $save_filename, $in_width=800, $in_height=800, $quality=100)
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
  
	  // 取得縮在此範圍內的比例
	  
	  $percent = Utils::getResizePercent($width, $height, $in_width, $in_height);
	 
	  $new_width  = $width * $percent;
	  $new_height = $height * $percent;
  
	  // Resample
	  $image_new = imagecreatetruecolor($new_width, $new_height);
	  
	  $image = ((strpos($from_filename,".png"))?imagecreatefrompng($from_filename):imagecreatefromjpeg($from_filename));
	  //$image = imagecreatefromjpeg($from_filename);
  
	  imagecopyresampled($image_new, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
  
	  return imagejpeg($image_new, $save_filename, $quality);
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
  
}