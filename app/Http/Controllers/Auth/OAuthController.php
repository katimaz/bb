<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Merchant;
use Auth;
use Exception;
use Socialite;
use Google_Client;
use Google_Service_Oauth2;
use App\Utils\Utils;
use Cookie;
use Session;

class OAuthController extends Controller
{

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function redirectToGoogle()
    {
        $gclient = new Google_Client();
		
		$gclient->setAuthConfig(array('client_id'=>config('services.google.client_id'),'client_secret'=>config('services.google.client_secret')));
		$gclient->setAccessType('offline'); // offline access
		$gclient->setIncludeGrantedScopes(true); // incremental auth
		$gclient->addScope(array(Google_Service_Oauth2::USERINFO_EMAIL, Google_Service_Oauth2::USERINFO_PROFILE));
		$gclient->setRedirectUri(config('services.google.redirect')); // 寫憑證設定：「已授權的重新導向 URI 」的網址
		$google_login_url = $gclient->createAuthUrl(); // 取得要點擊登入的網址
		return redirect($google_login_url);
		///return Socialite::driver('google')->redirect();
    }

    public function redirectToLine(Request $request)
    {
        $state = $this->quickRandom();
        $nonce = $this->quickRandom();
		$request->session()->put('state', $state);
        $request->session()->put('nonce', $nonce);
		
		$url = 'https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id='.config('services.line.client_id').'&redirect_uri='.config('services.line.redirect').'&state='.$state.'&scope=openid%20profile%20email&nonce='.$nonce;

        return redirect($url);
    }

    public function handleFacebookCallback(Request $request)
    {
       try {
            $user = Socialite::driver('facebook')->user();
			
			$name =  $user->getName();
			if(mb_strlen( $name, "utf-8")==2 || mb_strlen( $name, "utf-8")==3)
			{
				$loginData['last_name'] = mb_substr($name,0,1);
				$loginData['first_name'] = mb_substr($name,1);	
			}elseif(mb_strlen( $name, "utf-8")==4)
			{
				$loginData['last_name'] = mb_substr($name,0,2);
				$loginData['first_name'] = mb_substr($name,2);	
			}else
			{
				$loginData['last_name'] = NULL;
				$loginData['first_name'] = NULL;	
			}
				
			$loginData['email'] = $user->getEmail();
            $loginData['FB_login_token'] = $user->getId();
            $loginData['avatar'] = $user->getAvatar();

            $input = new User;
            $result = $input->fbLogin($loginData);
			if(!$result)
				return View('web/error', array('message' => '很抱歉，您無帳號權限喔! 請重新登入'));
			
			if($result->usr_status==-1)
			  return View('web/error_message', array('message' => '您的資格權限已暫時被停權，請與官方聯絡!', 'goUrl'=>'/contact_us'));
			
			Auth::loginUsingId($result->id);
			
			session()->put('uID', $result->id);
			session()->put('usrID', $result->usr_id);
			session()->put('usrStatus', $result->usr_status);
			session()->put('profile', array('first'=>$result->first_name,'last'=>$result->last_name,'nick'=>$result->nickname,'photo'=>$result->usr_photo));
			session()->put('offer', ((isset($result) && $result->usr_type)?true:false));
			
			if(isset($result) && $result->cookie_id)
			{
				if($result->cookie_id != Cookie::get('BB_cookie_id'))
				{
					Cookie::queue(
						Cookie::forever('BB_cookie_id', $result->cookie_id)
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
			
			if(!isset($result) || $result->usr_status==0)
				return redirect('/web/profile');
			else	  
			{
				$url = ((Cookie::has('BB_login_cookie'))?Cookie::get('BB_login_cookie'):'/web/map');
				Cookie::queue(Cookie::forget('BB_login_cookie'));
				return redirect($url);		  
			}
		
		} catch (Exception $e) {

			return redirect('login');
		}
	}

    public function handleGoogleCallback()
    {
        $client = new Google_Client();
    	try {
            $client = new Google_Client();
			
			$oauth = new Google_Service_Oauth2($client);
			$profile = $oauth->userinfo->get();
			dd($profile);
			 
			$name =  $user->getName();
			if(mb_strlen( $name, "utf-8")==2 || mb_strlen( $name, "utf-8")==3)
			{
				$loginData['last_name'] = mb_substr($name,0,1);
				$loginData['first_name'] = mb_substr($name,1);	
			}elseif(mb_strlen( $name, "utf-8")==4)
			{
				$loginData['last_name'] = mb_substr($name,0,2);
				$loginData['first_name'] = mb_substr($name,2);	
			}else
			{
				$loginData['last_name'] = NULL;
				$loginData['first_name'] = NULL;	
			}
				
			$loginData['email'] = $user->getEmail();
            $loginData['Google_login_token'] = $user->getId();
            $loginData['avatar'] = $user->getAvatar();

            $input = new User;
            $result = $input->googleLogin($loginData);
			if(!$result)
				return View('web/error', array('message' => '很抱歉，您無帳號權限喔! 請重新登入'));
			
			if($result->usr_status==-1)
			  return View('web/error_message', array('message' => '您的資格權限已暫時被停權，請與官方聯絡!', 'goUrl'=>'/contact_us'));
			
			Auth::loginUsingId($result->id);
			
			session()->put('uID', $result->id);
			session()->put('usrID', $result->usr_id);
			session()->put('usrStatus', $result->usr_status);
			session()->put('profile', array('first'=>$result->first_name,'last'=>$result->last_name,'nick'=>$result->nickname,'photo'=>$result->usr_photo));
			session()->put('offer', ((isset($result) && $result->usr_type)?true:false));
			
			if(isset($result) && $result->cookie_id)
			{
				if($result->cookie_id != Cookie::get('BB_cookie_id'))
				{
					Cookie::queue(
						Cookie::forever('BB_cookie_id', $result->cookie_id)
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
			
			if(!isset($result) || $result->usr_status==0)
				return redirect('/web/profile');
			else	  
			{
				$url = ((Cookie::has('BB_login_cookie'))?Cookie::get('BB_login_cookie'):'/web/map');
				Cookie::queue(Cookie::forget('BB_login_cookie'));
				return redirect($url);		  
			}  
		
        } catch (Exception $e) {

			//echo $e->getMessage();
			return redirect('login');

        }
    }

    public function handleLineCallback(Request $request)
    {
		if ($request->has('code') && $request->has('state')) {

            if ($request->input('state') == $request->session()->get('state')) {
                $data = array(
                    'grant_type' => 'authorization_code',
                    'code' => $request->input('code'),
                    'redirect_uri' => config('services.line.redirect'),
                    'client_id' => config('services.line.client_id'),
                    'client_secret' => config('services.line.client_secret'),
                );
				//dd($data);
				$curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.line.me/oauth2/v2.1/token",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => http_build_query($data),
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/x-www-form-urlencoded",
                    ),
                ));

                $response = curl_exec($curl);
				
                $err = curl_error($curl);
				curl_close($curl);

                if ($err) {
//                echo "cURL Error #:" . $err;

                    //return redirect('oauth');
					return redirect('login');
                } else {
                    $accessData = json_decode($response);
					
                    if (!isset($accessData->access_token) || !isset($accessData->id_token)) {
                        //return redirect('oauth');
						return redirect('login');
                    }

                    $idToken = explode(".", $accessData->id_token);

                    //增加PHP傳說中安全Base64處理 By Ken
                    $userRawData = str_replace(array('-','_'),array('+','/'), $idToken[1]);
                    $mod4 = strlen($userRawData) % 4;
                    if ($mod4) {
                        $userRawData .= substr('====', $mod4);
                    }
                    
                    $user = json_decode(base64_decode($userRawData));
					if ($user->nonce == $request->session()->get('nonce')) {
                        $status = $this->getLineProfile($accessData, $user);
					}
                }
            }

        }
		
		
		if(!isset($status) || $status==0)
			return redirect('/web/profile');
		else	  
		{
			$url = ((Cookie::has('BB_login_cookie'))?Cookie::get('BB_login_cookie'):'/web/map');
			Cookie::queue(Cookie::forget('BB_login_cookie'));
			return redirect($url);		  
		}
    
	}

    private function getLineProfile($accessData, $member) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.line.me/v2/profile",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$accessData->access_token,
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if($err) 
           return redirect('login');
        else 
		{
            $profile = json_decode($response);
			if(!$profile)
				return redirect('login');
			
			if (isset($member->email)){ // 使用者可以不授權email
                $loginData['email'] = $member->email;
            }else
				 $loginData['email'] = '';
            
			if(mb_strlen( $profile->displayName, "utf-8")==2 || mb_strlen( $profile->displayName, "utf-8")==3)
			{
				$loginData['last_name'] = mb_substr($profile->displayName,0,1);
				$loginData['first_name'] = mb_substr($profile->displayName,1);	
			}elseif(mb_strlen( $profile->displayName, "utf-8")==4)
			{
				$loginData['last_name'] = mb_substr($profile->displayName,0,2);
				$loginData['first_name'] = mb_substr($profile->displayName,2);	
			}else
			{
				$loginData['last_name'] = NULL;
				$loginData['first_name'] = NULL;	
			}
				
			$loginData['Line_login_token'] = $profile->userId;
            $loginData['avatar'] = ((isset($profile->pictureUrl))?$profile->pictureUrl:'');

            $input = new User;
            $result = $input->lineLogin($loginData);
			if(!$result)
				return View('web/error', array('message' => '很抱歉，您無帳號權限喔! 請重新登入'));
			
			if($result->usr_status==-1)
			  return View('web/error_message', array('message' => '您的資格權限已暫時被停權，請與官方聯絡!', 'goUrl'=>'/contact_us'));
			
			Auth::loginUsingId($result->id);
			
			session()->put('uID', $result->id);
			session()->put('usrID', $result->usr_id);
			session()->put('usrStatus', $result->usr_status);
			session()->put('profile', array('first'=>$result->first_name,'last'=>$result->last_name,'nick'=>$result->nickname,'photo'=>$result->usr_photo));
			session()->put('offer', ((isset($result) && $result->usr_type)?true:false));
			
			if(isset($result) && $result->cookie_id)
			{
				if($result->cookie_id != Cookie::get('BB_cookie_id'))
				{
					Cookie::queue(
						Cookie::forever('BB_cookie_id', $result->cookie_id)
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
			
			return $result->usr_status;
		}
    }

    /**
     * Generate a "random" alpha-numeric string.
     *
     * Should not be considered sufficient for cryptography, etc.
     *
     * @param  int  $length
     * @return string
     */
    private function quickRandom($length = 8)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }
}