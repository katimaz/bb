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
use App\Models\NeedListObj;
use App\Models\OfferListObj;
use App\Models\Users;
use App\Models\einvoice_info;
use App\Models\collection_info;
use App\Models\member_notify_setting;
use App\Models\member_favorite;
use Session;
use App\User;
use Auth;
use Cookie;
use DB;
use URL;

class MemberController extends Controller
{
    public function management()
    {
		// 會員的需求列表
		$nlo = NeedListObj::where('mem_id', '=', session()->get('uID'))->get();
		$distance = 50;
		foreach ($nlo as $key => $value) {
			if($value->budget_type == '小時') {
				$nlo[$key]->total = $value->budget * $value->available_daytime_enum;
			} elseif($value->budget_type == '每件') {
				$nlo[$key]->total = $value->budget;
			}
			// 該需求要回應有興趣的列表
			$nlo[$key]->mf = member_favorite::select('olo.*', 'u.last_name', 'u.first_name', 'u.usr_photo', 'u.usr_id', DB::raw("( 6371 * acos( cos( radians(".$value->lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$value->lng.") ) + sin( radians(".$value->lat.") ) * sin( radians( lat ) ) ) ) AS distance"))->where('need_id', '=', $value->id)->join('OfferListObj AS olo', 'offer_id', '=', 'olo.id')->join('users AS u', 'u.id', '=', 'olo.mem_id')->having('distance', '<', $distance)->get();
		}

		return View('web/management', ['nlo' => $nlo]);
	}

	public function ask($offer_id, $need_id = 0)
	{
		$lat = Session::get('lat');
		$lng = Session::get('lng');
		if(empty($lat)) {
			$lat = '25.0477756';
			$lng = '121.5127512';
		}
		if(empty($lng)) {
			$lat = '25.0477756';
			$lng = '121.5127512';
		}
		// OfferListObj::select(DB::raw("( 6371 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$lng.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) ) AS distance", 'service_type', 'service_type_sub'));


		if($need_id > 0) {

		}

	}
}

