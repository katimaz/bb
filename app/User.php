<?php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Utils\Utils;
use File;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array(
        'usr_id','usr_status','email','first_name','last_name','password','FB_login_token','Google_login_token','Line_login_token','cookie_id','open_offer_setting','usr_photo'
	);
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = array(
        'password', 'remember_token',
    );

    public function fbLogin($input)
    {
        if(!static::where('FB_login_token', $input['FB_login_token'])->orWhere('email',$input['email'])->count())
		{
            $chk=true; 
			while($chk==true) {
				  $account = Utils::create_name_id(time());;
				  if(!static::where('usr_id', $account)->count())
					$chk=false;
			}
			$input['usr_id'] = $account;
			$input['usr_status'] = 0;
			$input['open_offer_setting'] = 0;
			$input['password'] = Utils::set_password(time(),trim($account));
			if($input['avatar'])
			{
				$this->create_storage();
				$avatar = Utils::getAvatarFileName($account);
				$input['usr_photo'] = $avatar;
				$this->createFbAvatar($avatar, $input['avatar']);
			}
			//dd($input);
			return static::create($input);
		}elseif(static::where('email', $input['email'])->count())
		{
			static::where('email', $input['email'])->update(array('FB_login_token'=>$input['FB_login_token']));
			$userData = static::where('email', $input['email'])->first();
		}else
			$userData = static::where('FB_login_token', $input['FB_login_token'])->first();

        if($input['avatar'] && !$userData->usr_photo)
		{
			$avatar = Utils::getAvatarFileName($userData->usr_id);
			static::where('FB_login_token', $input['FB_login_token'])->update(array('usr_photo'=>$avatar)); 
			$this->createFbAvatar($avatar, $input['avatar']);
		}
		return $userData;
	}

    public function googleLogin($input)
    {
        if(!static::where('Google_login_token', $input['Google_login_token'])->orWhere('email',$input['email'])->count())
		{
            
			/*$chk=true; 
			while($chk==true) {
				  $account = Utils::create_name_id(time());;
				  if(!static::where('usr_id', $account)->count())
					$chk=false;
			}*/
			$input['usr_id'] = $account;
			$input['usr_status'] = 0;
			$input['open_offer_setting'] = 0;
			$input['password'] = Utils::set_password(time(),trim($account));
			if($input['avatar'])
			{
				$this->create_storage();
				$avatar = Utils::getAvatarFileName($account);
				$input['usr_photo'] = $avatar;
				$this->createFbAvatar($avatar, $input['avatar']);
			}
			return static::create($input);
		}elseif(static::where('email', $input['email'])->count())
		{
			static::where('email', $input['email'])->update(array('Google_login_token'=>$input['Google_login_token']));
			$userData = static::where('email', $input['email'])->first();
		}else
			$userData = static::where('Google_login_token', $input['Google_login_token'])->first();

        if($input['avatar'] && !$userData->usr_photo)
		{
			$avatar = Utils::getAvatarFileName($userData->usr_id);
			static::where('Google_login_token', $input['Google_login_token'])->update(array('usr_photo'=>$avatar)); 
			$this->createFbAvatar($avatar, $input['avatar']);
		}
		return $userData;
	}

    public function lineLogin($input)
    {
        if(!static::where('Line_login_token', $input['Line_login_token'])->orWhere('email',$input['email'])->count()) 
		{
            $chk=true; 
			while($chk==true) {
				  $account = Utils::create_name_id(time());;
				  if(!static::where('usr_id', $account)->count())
					$chk=false;
			}
			$input['usr_id'] = $account;
			$input['usr_status'] = 0;
			$input['open_offer_setting'] = 0;
			$input['password'] = Utils::set_password(time(),trim($account));
			if($input['avatar'])

			{
				$this->create_storage();
				$avatar = Utils::getAvatarFileName($account);
				$input['usr_photo'] = $avatar;
				$this->createLineAvatar($avatar, $input['avatar']);
			}
			return static::create($input);
        }elseif(static::where('email', $input['email'])->count())
		{
			static::where('email', $input['email'])->update(array('Line_login_token'=>$input['Line_login_token']));
			$userData = static::where('email', $input['email'])->first();	
		}else
			$userData = static::where('Line_login_token', $input['Line_login_token'])->first();

        if($input['avatar'] && !$userData->usr_photo)
		{
			$avatar = Utils::getAvatarFileName($userData->usr_id);
			static::where('Line_login_token', $input['Line_login_token'])->update(array('usr_photo'=>$avatar)); 
			$this->createLineAvatar($avatar, $input['avatar']);
		}
		return $userData;
    }
	private function create_storage(){
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
	}
	private function createFbAvatar($avatar, $inputAvatarUrl) {
        // 使用者大頭像
        $avatarBigLocalPath = storage_path('files/pic/avatar/photoBig/'.$avatar);
        $avatarUrlArr = explode("?", $inputAvatarUrl);
		$avatarUrl = $avatarUrlArr[0] . "?type=normal";
		$fileContents = file_get_contents($avatarUrl);
		File::put($avatarBigLocalPath, $fileContents);

        // 使用者小頭像
        $avatarSmallLocalPath = storage_path('files/pic/avatar/photoSmall/'.$avatar);
        $avatarUrlArr = explode("?", $inputAvatarUrl);
		$avatarUrl = $avatarUrlArr[0] . "?type=normal";
		$fileContents = file_get_contents($avatarUrl);
		File::put($avatarSmallLocalPath, $fileContents);
    }

    private function createGoogleAvatar($avatar, $inputAvatarUrl) {
        // 使用者大頭像
		$avatarBigLocalPath = storage_path('files/pic/avatar/photoBig/'.$avatar);
        $avatarUrlArr = explode("?", $inputAvatarUrl);
		$avatarUrl = $avatarUrlArr[0] . "?sz=600";
		$fileContents = file_get_contents($avatarUrl);
		File::put($avatarBigLocalPath, $fileContents);

        // 使用者小頭像
        $avatarSmallLocalPath = storage_path('files/pic/avatar/photoSmall/'.$avatar);
        $avatarUrlArr = explode("?", $inputAvatarUrl);
		$avatarUrl = $avatarUrlArr[0] . "?sz=200";
		$fileContents = file_get_contents($avatarUrl);
		File::put($avatarSmallLocalPath, $fileContents);
	}

    private function createLineAvatar($avatar, $inputAvatarUrl) {
        // 使用者大頭像
        $avatarBigLocalPath = storage_path('files/pic/avatar/photoBig/'.$avatar);
        if (!File::exists($avatarBigLocalPath)) {
            $fileContents = file_get_contents($inputAvatarUrl);
            File::put($avatarBigLocalPath, $fileContents);
            Utils::ImageResize($avatarBigLocalPath, $avatarBigLocalPath, 600, 600);
        }

        // 使用者小頭像
        $avatarSmallLocalPath = storage_path('files/pic/avatar/photoSmall/'.$avatar);
	    Utils::ImageResize($avatarBigLocalPath, $avatarSmallLocalPath, 200, 200);
    }
}