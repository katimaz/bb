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
    protected $fillable = [
        'usr_id','usr_status','email','first_name','last_name','password','FB_login_token','Google_login_token','Line_login_token','cookie_id'
	];
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function fbLogin($input)
    {
        if(!static::where('fb_id', $input['fb_id'])->orWhere('email',$input['email'])->count())
		{
            $chk=true;
			while($chk==true) {
			  $id = Utils::createTimeId(time());
			  if(!static::where('account', $id)->count())
			  	$chk=false;
			}
			$input['account'] = $id;
			$input['level'] = 1;
			$input['status'] = 1;
			$input['record_count'] = 0;

            $this->createFbAvatar($input['account'], $input['avatar']);

			return static::create($input);
		}elseif(static::where('email', $input['email'])->count())
		{
			static::where('email', $input['email'])->update(array('fb_id'=>$input['fb_id']));
			$userData = static::where('fb_id', $input['fb_id'])->first();
		}else
			$userData = static::where('fb_id', $input['fb_id'])->first();

        $this->createFbAvatar($userData->account, $input['avatar']);

		return $userData;

		/*$userData = static::where('fb_id', $input['fb_id'])->first();

		if (is_null($userData)) {
            $input['account'] = Utils::createTimeId(time());
			return static::create($input);
        }elseif(!$userData->account)
		{
			static::where('fb_id', $input['fb_id'])->update(array('account'=>Utils::createTimeId(time())));
			$userData = static::where('fb_id', $input['fb_id'])->first();
		}*/

		// return $userData;
    }

    public function googleLogin($input)
    {
        //$userData = static::where('google_id', $input['google_id'])->first();

		if(!static::where('google_id', $input['google_id'])->orWhere('email',$input['email'])->count())
		{
            $chk=true;
			while($chk==true) {
			  $id = Utils::createTimeId(time());
			  if(!static::where('account', $id)->count())
			  	$chk=false;
			}
			$input['account'] = $id;
			$input['level'] = 1;
			$input['status'] = 1;
            $input['record_count'] = 0;

            $this->createGoogleAvatar($input['account'], $input['avatar']);

			return static::create($input);
        }elseif(static::where('email', $input['email'])->count())
		{
			static::where('email', $input['email'])->update(array('google_id'=>$input['google_id']));
			$userData = static::where('google_id', $input['google_id'])->first();
		}else
			$userData = static::where('google_id', $input['google_id'])->first();

        $this->createGoogleAvatar($userData->account, $input['avatar']);

		return $userData;
    }

    public function lineLogin($input)
    {

		if(!static::where('line_id', $input['line_id'])->orWhere('email',$input['email'])->count())
		{
            $chk=true;
			while($chk==true) {
			  $id = Utils::createTimeId(time());
			  if(!static::where('account', $id)->count())
			  	$chk=false;
			}
			$input['account'] = $id;
			$input['level'] = 1;
			$input['status'] = 1;
            $input['record_count'] = 0;

			$this->createLineAvatar($input['account'], $input['avatar']);

			return static::create($input);
        }elseif(static::where('email', $input['email'])->count())
		{
			static::where('email', $input['email'])->update(array('line_id'=>$input['line_id']));
			$userData = static::where('line_id', $input['line_id'])->first();
		}else
			$userData = static::where('line_id', $input['line_id'])->first();

        $this->createLineAvatar($userData->account, $input['avatar']);

		return $userData;
    }

	public function cookieLogin($input)
    {

		$userData = static::where($input['stage'], $input['id'])->where('tel',$input['tel'])->first();

		return ((isset($userData))?$userData:NULL);
    }

    private function createFbAvatar($userAccount, $inputAvatarUrl) {
        // 使用者大頭像
        $avatarBigLocalPath = storage_path('files/pic/avatar/photoBig/' . Utils::getAvatarFileName($userAccount));
        if (!File::exists($avatarBigLocalPath)) {
            $avatarUrlArr = explode("?", $inputAvatarUrl);
            $avatarUrl = $avatarUrlArr[0] . "?type=large";
            $fileContents = file_get_contents($avatarUrl);
            File::put($avatarBigLocalPath, $fileContents);
        }

        // 使用者小頭像
        $avatarSmallLocalPath = storage_path('files/pic/avatar/photoSmall/' . Utils::getAvatarFileName($userAccount));
        if (!File::exists($avatarSmallLocalPath)) {
            $avatarUrlArr = explode("?", $inputAvatarUrl);
            $avatarUrl = $avatarUrlArr[0] . "?type=small";
            $fileContents = file_get_contents($avatarUrl);
            File::put($avatarSmallLocalPath, $fileContents);
        }
    }

    private function createGoogleAvatar($userAccount, $inputAvatarUrl) {
        // 使用者大頭像
		$avatarBigLocalPath = storage_path('files/pic/avatar/photoBig/' . Utils::getAvatarFileName($userAccount));
        if (!File::exists($avatarBigLocalPath)) {
            $avatarUrlArr = explode("?", $inputAvatarUrl);
            $avatarUrl = $avatarUrlArr[0] . "?sz=200";
            $fileContents = file_get_contents($avatarUrl);
            File::put($avatarBigLocalPath, $fileContents);
        }

        // 使用者小頭像
        $avatarSmallLocalPath = storage_path('files/pic/avatar/photoSmall/' . Utils::getAvatarFileName($userAccount));
        if (!File::exists($avatarSmallLocalPath)) {
            $avatarUrlArr = explode("?", $inputAvatarUrl);
            $avatarUrl = $avatarUrlArr[0] . "?sz=50";
            $fileContents = file_get_contents($avatarUrl);
            File::put($avatarSmallLocalPath, $fileContents);
        }
	}

    private function createLineAvatar($userAccount, $inputAvatarUrl) {
        // 使用者大頭像
        $avatarBigLocalPath = storage_path('files/pic/avatar/photoBig/' . Utils::getAvatarFileName($userAccount));
        if (!File::exists($avatarBigLocalPath)) {
            $fileContents = file_get_contents($inputAvatarUrl);
            File::put($avatarBigLocalPath, $fileContents);
            Utils::ImageResize($avatarBigLocalPath, $avatarBigLocalPath, 200, 200);
        }

        // 使用者小頭像
        $avatarSmallLocalPath = storage_path('files/pic/avatar/photoSmall/' . Utils::getAvatarFileName($userAccount));
        if (!File::exists($avatarSmallLocalPath)) {
            Utils::ImageResize($avatarBigLocalPath, $avatarSmallLocalPath, 50, 50);
        }
    }
}
