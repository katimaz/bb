<?php
namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Utils\Utils;
use App\Notifications\Notify;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin_member;
use App\Models\Admin_group;
use App\Models\Setting;
use App\Models\Video;
use App\Models\Pic_table;
use App\Models\Ezpay;
use App\Models\NewebPay;
use App\Models\Newebpay_mpg;
use App\Models\Invoice_track;
use App\Models\Invoice;
use App\Models\Invoice_allowance;
use App\Models\Merchant;
use App\Models\ChargeInstruct;
use App\Models\ExportInstruct;
use App\User;
use Cookie;
use DB;

class AdminController extends Controller
{
   
   	public function adm_logout(Request $request)
    {
    	
		$request->session()->flush();
		return redirect('adm_login');
	}
	
	public function adm_login_pt(Request $request)
    {
        
		if ($request->has('account') && $request->input('account') && $request->has('password'))
		 {
			$owner = Admin_member::join('adm_groups','adm_members.adm_group','=','adm_groups.id')
				->where('adm_members.adm_account', '=', trim($request->account))->select('adm_members.id','adm_members.adm_status','adm_members.adm_account','adm_members.adm_password','adm_members.adm_name','adm_members.adm_email','adm_groups.group_id','adm_groups.group_name','adm_groups.group_master','adm_groups.group_manager')->first();
			if(!$owner || !$owner->adm_status){
				return View('admin/error', array('message' => '很抱歉，帳密有問題喔!!'));
			}else
			{
				
				if (!Hash::check(trim($request->password).":".$request->account, $owner->adm_password )) {
					return View('admin/error', array('message' => '很抱歉，帳密有問題喔!', 'data'=>''));
				}
				$request->session()->flush();
				$request->session()->put('ownerID', $owner->adm_account);
				$request->session()->put('ownerGroup', $owner->group_id);
				$request->session()->put('ownerLevel', (((int)$owner->group_master==1)?9:(($owner->group_manager==$owner->adm_account)?7:1))); //紀錄是管理者(9)或是群組主管(7)其他則為(0)
				$request->session()->put('ownerName', $owner->adm_name);
				$request->session()->put('groupName', $owner->group_name);
				
				$data = Utils::encrypt('{"SessionId":"'.$request->session()->getId().'", "Account":"'.$owner->adm_account.'", "Group":"'.$owner->group_id.'"}', config('bbpro.key'));
				
				//Cookie::queue('dataCookie',$data, 28800);
				Cookie::queue(Cookie::make('dataCookie', $data, 28800));
				
				return redirect('admin/owner');
			}
		}else
			return View('admin/error', array('message' => '很抱歉，帳密有問題喔!'));
			
	}
	
	public function owner(Request $request)
    {
    	if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		
		return View('admin/owner');
			
	}
	
	public function owner_pt(Request $request)
	{
		if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		
		if($request->modifyPassword)
			$valArr = array('group_id' => 'required', 'adm_account' => 'required|string|max:32','adm_name' => 'required|string|max:32', 'password' => 'required|string|min:6','chk_password' => 'required_with:password|same:password|min:6');
		else
			$valArr = array('group_id' => 'required', 'adm_account' => 'required|string|max:32','adm_name' => 'required|string|max:32');	
		$validator = Validator::make($request->all(), $valArr );
	
		if ($validator->fails()) {
			return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/managers','mode'=>$mode));
		}
		
		if(Admin_member::where('adm_account','!=',$request->old_account)->where('adm_account',$request->adm_account)->count())
			return View('admin/error_message', array('message' => '此帳號已有人註冊，請重新填寫!', 'goUrl'=>'/admin/managers','mode'=>$mode));
				
		$input['adm_account'] = $request->adm_account;
		$input['adm_name'] = $request->adm_name;
		$input['adm_email'] = ($request->adm_email)?$request->adm_email:'';
		if($request->modifyPassword)
			$input['adm_password'] = Admin_member::set_password(trim($request->password),trim($request->adm_account));
		
		Admin_member::where('adm_account', $request->old_account)->update($input);
		
		
		return redirect('admin/owner');
	}
	
	public function managers(Request $request)
    {
    	if(!Admin_member::isManager(7))
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		
		$mode = ($request->has('mode'))?$request->mode:'';
		
		if($mode=='edit')
		{
			return View('admin/managers', array('mode'=>$mode,'id'=>$request->id));
		}elseif($mode=='del')
		{
			if(Session::get('ownerLevel')!=9)
				return View('admin/error_message', array('message' => '你的權限似乎不足喔!!', 'goUrl'=>'/admin/managers','mode'=>''));	
			
			$id = $request->id;
			
			Admin_member::where('adm_account','=',$id)->delete();
			
			return redirect('admin/managers');
				
		}else
			return View('admin/managers', array('mode'=>$mode));
		
	}
	
	public function managers_pt(Request $request)
	{
		if(!Admin_member::isManager(9))
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		
		$mode = ($request->has('mode'))?$request->mode:'';
		if($mode=='add')
		{
			if(Admin_member::where('adm_account','=',$request->adm_account)->count())
				return View('admin/error_message', array('message' => '此帳號已有人註冊，請重新填寫!', 'goUrl'=>'/admin/managers','mode'=>$mode));
			
			$validator = Validator::make($request->all(), array(
				'group_id' => 'required',
				'adm_account' => 'required|string|max:32',
				'adm_name' => 'required|string|max:32',
				//'email' => 'required|string|email|max:255|unique:users',
				'password' => 'required|string|min:6',
				'chk_password' => 'required_with:password|same:password|min:6'
			));
		
			if ($validator->fails()) {
				return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/managers','mode'=>$mode));
			}
			
			$input = new Admin_member;
			
			$input->adm_status = 1;
			$input->adm_account = $request->adm_account;
			$input->adm_password = Admin_member::set_password(trim($request->password),trim($request->adm_account));
			$input->adm_name = $request->adm_name;
			$input->adm_group = Utils::get_groupid($request->group_id);
			$input->adm_email = ($request->adm_email)?$request->adm_email:'';
			$input->save();
			
			return redirect('admin/managers');
			
		}elseif($mode=='edit')
		{
			if($request->modifyPassword)
				$valArr = array('group_id' => 'required', 'adm_account' => 'required|string|max:32','adm_name' => 'required|string|max:32', 'password' => 'required|string|min:6','chk_password' => 'required_with:password|same:password|min:6');
			else
				$valArr = array('group_id' => 'required', 'adm_account' => 'required|string|max:32','adm_name' => 'required|string|max:32');	
			$validator = Validator::make($request->all(), $valArr );
		
			if ($validator->fails()) {
				return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/managers','mode'=>$mode));
			}
			
			if(Admin_member::where('adm_account','!=',$request->old_account)->where('adm_account',$request->adm_account)->count())
				return View('admin/error_message', array('message' => '此帳號已有人註冊，請重新填寫!', 'goUrl'=>'/admin/managers','mode'=>$mode));
			
			$input['adm_status'] = ((isset($request->sdm_status))?1:0);
			$input['adm_account'] = $request->adm_account;
			$input['adm_name'] = $request->adm_name;
			$input['adm_group'] = Utils::get_groupid($request->group_id);
			$input['adm_email'] = ($request->adm_email)?$request->adm_email:'';
			if($request->modifyPassword)
				$input['adm_password'] = Admin_member::set_password(trim($request->password),trim($request->adm_account));
			
			Admin_member::where('adm_account', $request->old_account)->update($input);
			
			
			return redirect('admin/managers?mode=edit&id='.$request->adm_account);
		}
	}
	
	public function groups(Request $request)
    {
    	//Log::emergency('payload={"text": "{A very important thing has occurred! <https://alert-system.com/alerts/1234|Click here> for details!"}');
		//Notify::via('notify','訂單編號 : 333<br />支付方式 : 44444<br />前往查看 : ! <'.url('/').'/admin/transfer_records?item=newebPay&action=manage'.'|Click here> for details!');
		if(!Admin_member::isManager(9))
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		
		$mode = ($request->has('mode'))?$request->mode:'';
		
		if($mode=='edit')
		{
			return View('admin/groups', array('mode'=>$mode,'id'=>$request->id));
		}elseif($mode=='del')
		{
			if(Session::get('ownerLevel')!=9)
				return View('admin/error_message', array('message' => '你的權限似乎不足喔!!', 'goUrl'=>'/admin/groups','mode'=>''));	
			
			$id = $request->id;
			
			Admin_group::where('group_id','=',$id)->delete();
			
			return redirect('admin/groups');
				
		}else
			return View('admin/groups', array('mode'=>$mode));
		
	}
	
	public function groups_pt(Request $request)
	{
		if(!Admin_member::isManager(9))
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		$mode = ($request->has('mode'))?$request->mode:'';
		if($mode=='add')
		{
			$validator = Validator::make($request->all(), array(
				'group_name' => 'required|string|max:64',
				'group_setting' => 'required',
			));
		
			if ($validator->fails()) {
				return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/groups','mode'=>$mode));
			}
			
			$settings = explode(',',$request->group_setting);
			
			$input = new Admin_group;
			$input->group_status = 1;
			$input->group_id = Utils::create_name_id(time());
			$input->group_name = $request->group_name;
			$input->group_master = 0;
			$input->group_manager = $request->group_manager;
			$input->group_setting = json_encode($settings);
			$input->save();
			
			return redirect('admin/groups');
			
		}elseif($mode=='edit')
		{
			$validator = Validator::make($request->all(), array(
				'group_name' => 'required|string|max:64',
				'group_setting' => 'required',
			));
		
			if ($validator->fails()) {
				return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/groups','mode'=>$mode));
			}
			
			$settings = explode(',',$request->group_setting);
			$input['group_status'] = ((isset($request->group_status))?1:0);
			$input['group_name'] = $request->group_name;
			$input['group_manager'] = $request->group_manager;
			$input['group_setting'] = json_encode($settings);;
			
			Admin_group::where('group_id', $request->group_id)->update($input);
			
			
			return redirect('admin/groups?mode=edit&id='.$request->group_id);
		}
	}
	
	public function serviceFee(Request $request)
    {
    	if(!Admin_member::isManager(9))
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		
		$setting = Setting::select('service_fee')->first();
		
		return View('admin/serviceFee', array('fee'=>((isset($setting))?$setting->service_fee:'')));
	}
	
	public function serviceFee_pt(Request $request)
	{
		if(!Admin_member::isManager(9))
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		$validator = Validator::make($request->all(), array(
			'serviceFee' => 'required|numeric|between:10,50',
		));
		
		if ($validator->fails()) {
			return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/serviceFee'));
		}
				
		if(!Setting::count())
		{
			$input = new Setting;
			$input->service_fee = $request->serviceFee;
			$input->save();	
		}else
			Setting::where('id',1)->update(array('service_fee'=>$request->serviceFee));
			
		return redirect('admin/serviceFee');
		
	}
	
	public function transfer_records(Request $request)
    {
    	//dd(basename(url()->current(),".php"));
		if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		
		//dd($request->all());
		$item = ($request->has('item'))?$request->item:'';
		$action = ($request->has('action'))?$request->action:'';
		$status = ($request->has('status'))?$request->status:'';
		$message = ($request->has('message') && $request->get('message'))?$request->message:'';
		
		return View('admin/transfer_records', array('item'=>$item,'action'=>$action,'status'=>$status,'message'=>$message));
		
	}
	
	public function transfer_records_pt(Request $request)
	{
		if(!Admin_member::isManager() && !Auth::check())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		$item = ($request->has('item'))?$request->item:'';
		$action = ($request->has('action'))?$request->action:'';
		
		if($item=='newebPay')
		{
			$newebPay = new NewebPay();
			$ezpay = new Ezpay();
			//dd($request->all());
			if($action=='mpg_gateway')
			{
				$validator = Validator::make($request->all(), array(
					'usr_id' => 'required|max:32',
					'Email' => 'required|max:128',
					'MerchantID' => 'required|max:15',
					'MerchantOrderNo' => 'required|max:32',
					'Amt' => 'required|integer|min:1',
				
				));
			
				if ($validator->fails()) {
					return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/transfer_records','item'=>$item,'action'=>$action));
				}
				$post_data_array = array(
					'usr_id' => $request->usr_id, //會員ID
					'MerchantID' => $request->MerchantID, //合作商店ID
					'MerchantOrderNo' => $request->MerchantOrderNo, //自訂商品編號
					'Amt' => $request->Amt, //交易金額
					'ItemDesc' => $request->ItemDesc, //商品資訊50字內
					'Email' => $request->Email //會員Email
				);
				$newebPay->create_mpg_form($post_data_array);
				
			}elseif($action=='cancel')
			{
				$transfer = Newebpay_mpg::join('merchants','newebpay_mpgs.MerchantID','=','merchants.MerchantID')->where('MerchantOrderNo',$request->id)->first();
				if(!$transfer)
					return View('admin/error_message', array('message' => '無合作商店資料喔!!', 'goUrl'=>'/admin/transfer_records','item'=>$item,'action'=>$action));
				if(isset($transfer->TradeNo) && $transfer->TradeNo)	
					$invoice = Invoice::where('TransNum',$transfer->TradeNo)->where('InvoiceStatus',1)->first();
				
				$post_data_array = array(//post_data 欄位資料            
					 "RespondType" => "JSON",             
					 "Version" => "1.0",
					 "Amt" => $transfer->Amt,              
					 "TimeStamp" => time(), //請以 time() 格式            
					 "MerchantOrderNo" => $transfer->MerchantOrderNo,             
					 "IndexType" => 1,
					 "NotifyURL" => env('newebPay_creditCancel_url')
				);
				$result = $newebPay->credit_card_cancel($post_data_array,$transfer,$invoice);
				if($result['Status']!='SUCCESS')
					$message = $result['Message'];
				return redirect('admin/transfer_records?item='.$item.'&action=manage&message='.((isset($message))?$message:''));
			}elseif($action=='credit_close')
			{
				$validator = Validator::make($request->all(), array(
					'MerchantOrderNo' => 'required|max:32',
					'TradeNo' => 'required|max:32',
					'Amt' => 'required|min:1',
				));
				
				if ($validator->fails()) {
					return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/transfer_records','item'=>$item));
				}
				
				$result = $newebPay->send_credit_close($request);
				if($result['Status']!='SUCCESS')
					$message = 	$result['Message'];
				return redirect('admin/transfer_records?item='.$item.'&action=credit_close&message='.((isset($message))?$message:''));	
			}
		}
	}
	
	public function accountings(Request $request)
    {
    	//dd(basename(url()->current(),".php"));
		if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		
		//dd($request->all());
		$item = ($request->has('item'))?$request->item:'';
		$action = ($request->has('action'))?$request->action:'';
		$mode = ($request->has('mode'))?$request->mode:'';
		$status = ($request->has('status'))?$request->status:'';
		$message = ($request->has('message'))?$request->message:'';
		$id = ($request->has('id'))?$request->id:'';
		
		if($item=='invoice_tracks' && $action=='manage')
		{
			$ezpay = new Ezpay();
			$ezpay->query_term();
		}
		return View('admin/accountings', array('item'=>$item,'action'=>$action,'id'=>$id,'status'=>$status,'message'=>$message));
		
	}
	
	public function accountings_pt(Request $request)
	{
		if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		$item = ($request->has('item'))?$request->item:'';
		$mode = ($request->has('mode'))?$request->mode:'';
		$action = ($request->has('action'))?$request->action:'';
		if($item=='systemAccount')
		{
			$newebPay = new NewebPay();
			if($action=='merchant_create')
			{
				$validator = Validator::make($request->all(), array(
					'usr_id' => 'required|max:32',
					'MerchantID' => 'required|max:15',
				
				));
				if ($validator->fails()) {
					return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/accountings','item'=>$item));
				}
				
				$user = User::where('usr_id',$request->usr_id)->select('id')->first();
				if(!$user)
					return View('admin/error_message', array('message' => '無會員資料喔!!', 'goUrl'=>'/admin/accountings','item'=>$item));
				$paymodes = array('CREDIT','WEBATM','VACC','CVS','BARCODE');
				$pays = explode('|',$request->PaymentType);
				$PaymentArr = array();
				foreach($paymodes as $paymode)
				{
					if(in_array($paymode,$pays))
						$PaymentArr[] = $paymode.':1';
					else
						$PaymentArr[] = $paymode.':0';	
				}
				$merchant = Merchant::where('u_id',$user->id)->select('MerchantID')->first();
				if(!$merchant && $request->mode=='add')  //新增合作商家
				{
					//dd($request->all());
					$post_data_array = array(
						'Version' => '1.2',
						'TimeStamp' => time(),
						'MerchantID' => $request->MerchantID,
						'ManagerID' => (($request->MerchantClass==2)?$request->Manager_id.';'.$request->Manager_id_number:''),
						'IDCardDate' => (($request->MerchantClass==1)?str_pad($request->ID_year,3,'0',STR_PAD_LEFT).$request->ID_month.$request->ID_day:''),
						'MemberUnified' => $request->MemberUnified,
						'IDCardPlace' => (($request->MerchantClass==1)?$request->IDCardPlace:''),
						'IDPic' => (($request->MerchantClass==1)?$request->IDPic:''),
						'IDFrom' => (($request->MerchantClass==1)?$request->IDFrom:''),
						'MemberName' => $request->MemberName,
						'MemberPhone' => $request->telHead.'-'.$request->telValue,
						'ManagerName' => $request->ManagerName,
						'ManagerNameE' => $request->ManagerNameE,
						'LoginAccount' => 'BB'.time(),
						'ManagerMobile' => $request->ManagerMobile,
						'ManagerEmail' => $request->ManagerEmail,
						'MerchantName' => $request->MerchantName,
						'MerchantNameE' => $request->MerchantNameE,
						'MerchantWebURL' => $request->MerchantWebURL,
						'MerchantAddrCity' => $request->MerchantAddrCity,
						'MerchantAddrArea' => $request->MerchantAddrArea,
						'MerchantAddrCode' => $request->MerchantAddrCode,
						'MerchantAddr' => $request->MerchantAddr,
						'NationalE' => $request->NationalE,
						'CityE' => $request->CityE,
						'MerchantType' => $request->MerchantType,
						'BusinessType' => $request->BusinessType,
						'MerchantDesc' => $request->MerchantDesc,
						'BankCode' => $request->BankCode,
						'SubBankCode' => $request->SubBankCode,
						'BankAccount' => $request->BankAccount,
						'CreditAutoType' => $request->CreditAutoType,
						'CreditLimit' => $request->CreditLimit,
						'PaymentType' => join('|',$PaymentArr)
					);
					//dd($post_data_array);
					$result = $newebPay->merchantCreate($post_data_array);
					$result_data = json_decode($result);
					//dd($result_data);
					if($result_data->status=='SUCCESS')
					{
						$input = new Merchant;
						if($request->MerchantClass==2)
							$input->ManagerID = $request->Manager_id.';'.$request->Manager_id_number;
						else
						{
							$input->IDCardDate = $request->ID_year.$request->ID_month.$request->ID_day;
							$input->IDCardPlace = $request->IDCardPlace;
							$input->IDPic = $request->IDPic;
							$input->IDFrom = $request->IDFrom;
						}	
						$input->u_id = $user->id;
						$input->MerchantID = $result_data->result->MerchantID;
						$input->MerchantClass = $request->MerchantClass;
						$input->MemberUnified = $request->MemberUnified;
						$input->MemberName = $request->MemberName;
						$input->MemberPhone = $request->telHead.'-'.$request->telValue;
						$input->ManagerName = $request->ManagerName;
						$input->ManagerNameE = $request->ManagerNameE;
						$input->ManagerMobile = $request->ManagerMobile;
						$input->ManagerEmail = $request->ManagerEmail;
						$input->MerchantName = $request->MerchantName;
						$input->MerchantNameE = $request->MerchantNameE;
						$input->MerchantWebURL = $request->MerchantWebURL;
						$input->MerchantAddrCity = $request->MerchantAddrCity;
						$input->MerchantAddrArea = $request->MerchantAddrArea;
						$input->MerchantAddrCode = $request->MerchantAddrCode;
						$input->MerchantAddr = $request->MerchantAddr;
						$input->NationalE = $request->NationalE;
						$input->CityE = $request->CityE;
						$input->MerchantType = $request->MerchantType;
						$input->BusinessType = $request->BusinessType;
						$input->MerchantDesc = $request->MerchantDesc;
						$input->BankCode = $request->BankCode;
						$input->SubBankCode = $request->SubBankCode;
						$input->BankAccount = $request->BankAccount;
						$input->CreditAutoType = $request->CreditAutoType;
						$input->CreditLimit = $request->CreditLimit;
						$input->PaymentType = join('|',$PaymentArr);
						$input->MerchantStatus = 1;
						$input->MerchantHashKey = $result_data->result->MerchantHashKey;
						$input->MerchantIvKey = $result_data->result->MerchantIvKey;
						$input->save();
					}
					if($result_data->status!='SUCCESS')
					{
						//Cookie::queue('tempCookie',json_encode($request->all()), 3600);
						Cookie::queue(Cookie::make('tempCookie', json_encode($request->all()), 3600));
						$message = $result_data->message;	
						return redirect('admin/accountings?item='.$item.'&action='.$action.'&message='.((isset($message))?$message:''));
					}
					return redirect('admin/accountings?item='.$item.'&action=merchant_manager');	
				}else if($request->mode=='edit') //修改合作商家
				{
					$post_data_array = array(
						'Version' => '1.2',
						'MerchantID' => $request->MerchantID,
						'MemberPhone' => $request->telHead.'-'.$request->telValue,
						'MerchantAddrCity' => $request->MerchantAddrCity,
						'MerchantAddrArea' => $request->MerchantAddrArea,
						'MerchantAddrCode' => $request->MerchantAddrCode,
						'MerchantAddr' => $request->MerchantAddr,
						'BankCode' => $request->BankCode,
						'SubBankCode' => $request->SubBankCode,
						'BankAccount' => $request->BankAccount,
						'CreditAutoType' => $request->CreditAutoType,
						'CreditLimit' => $request->CreditLimit,
						'MerchantStatus' => $request->MerchantStatus
						
					);
					//dd($post_data_array);
					$result = $newebPay->merchantModify($post_data_array);
					$result_data = json_decode($result);
					//dd($result_data);
					if($result_data->status=='SUCCESS')
					{
						$input['MemberName'] = $request->MemberName;
						$input['MemberPhone'] = $request->telHead.'-'.$request->telValue;
						$input['MerchantAddrCity'] = $request->MerchantAddrCity;
						$input['MerchantAddrArea'] = $request->MerchantAddrArea;
						$input['MerchantAddrCode'] = $request->MerchantAddrCode;
						$input['MerchantAddr'] = $request->MerchantAddr;
						$input['BankCode'] = $request->BankCode;
						$input['SubBankCode'] = $request->SubBankCode;
						$input['BankAccount'] = $request->BankAccount;
						$input['CreditAutoType'] = $request->CreditAutoType;
						$input['CreditLimit'] = $request->CreditLimit;
						$input['PaymentType'] = join('|',$PaymentArr);
						$input['MerchantStatus'] = $request->MerchantStatus;
						Merchant::where('u_id',$user->id)->update($input);
					}else
						$message = $result_data->message;
					//$merchantId = $merchant->MerchantID;
					
					return redirect('admin/accountings?item='.$item.'&action=merchant_manager&message='.((isset($message))?$message:''));
				}
			}elseif($action=='FeeInstruct')
			{
				$validator = Validator::make($request->all(), array(
					'MerchantID' => 'required|max:15',
					'MerchantOrderNo' => 'required|max:24',
					'Amount' => 'required|integer'
				
				));
				if ($validator->fails()) {
					return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/accountings','item'=>$item,'action'=>$action));
				}
				
				if($request->feeMode=='ChargeInstruct')
				{
					$transfer = Newebpay_mpg::where('MerchantOrderNo',$request->MerchantOrderNo)->select('Amt')->first();
					if(!$transfer)
						return View('admin/error_message', array('message' => '查無該筆交易資料喔!!', 'goUrl'=>'/admin/accountings','item'=>$item,'action'=>$action));
					
					$setting = Setting::select('service_fee')->first();
					$serviceFee = ((isset($setting) && $setting->service_fee)?$setting->service_fee:20);
					$fee = round($transfer->Amt*$serviceFee*0.01);
					if($request->Amount>$fee)
						return View('admin/error_message', array('message' => '扣款金額不得大於可扣款金額!!', 'goUrl'=>'/admin/accountings','item'=>$item,'action'=>$action));	
					
					$post_data_array = array(
						'Version' => '1.0',
						'TimeStamp' => time(),
						'MerchantID' => $request->MerchantID,
						'MerTrade' => $request->MerchantOrderNo,
						'BalanceType' => $request->BalanceType,
						'FeeType' => $request->FeeType,
						'Amount' => $request->Amount,
						
					);
					//dd($post_data_array);
					$result = $newebPay->ChargeInstruct($post_data_array);
					$result_data = json_decode($result);
					if($result_data->Status=='SUCCESS')
					{
						$input = new ChargeInstruct;
						$input->MerchantID = $request->MerchantID;
						$input->Amount = $request->Amount;
						$input->FeeType = $request->FeeType;
						$input->BalanceType = $request->BalanceType;
						$input->MerTrade = $result_data->MerTrade;
						$input->FundTime = $result_data->FundTime;
						$input->ExeNo = $result_data->ExeNo;
						$input->save();
					}
					
					$message = $result_data->Message;	
				}elseif($request->feeMode=='ExportInstruct')
				{
					$transfer = Newebpay_mpg::where('MerchantOrderNo',$request->MerchantOrderNo)->select('Amt')->first();
					if(!$transfer)
						return View('admin/error_message', array('message' => '查無該筆交易資料喔!!', 'goUrl'=>'/admin/accountings','item'=>$item,'action'=>$action));
					$setting = Setting::select('service_fee')->first();
					$serviceFee = ((isset($setting) && $setting->service_fee)?$setting->service_fee:20);
					//$fee = round($transfer->Amt*$serviceFee*0.01);
					if($request->Amount>$transfer->Amt)
						return View('admin/error_message', array('message' => '撥款金額不得大於可撥款金額!!', 'goUrl'=>'/admin/accountings','item'=>$item,'action'=>$action));	
					
					$post_data_array = array(
						'Version' => '1.0',
						'TimeStamp' => time(),
						'MerchantID' => $request->MerchantID,
						'MerchantOrderNo' => $request->MerchantOrderNo,
						'Amount' => $request->Amount
					);
					//dd($post_data_array);
					$result = $newebPay->ExportInstruct($post_data_array);
					$result_data = json_decode($result);
					dd($result_data);
					if($result_data->Status=='SUCCESS')
					{
						$input = new ChargeInstruct;
						$input->MerchantID = $request->MerchantID;
						$input->MerchantOrderNo = $request->MerchantOrderNo;
						$input->Amount = $request->Amount;
						$input->save();
					}
					
					$message = $result_data->Message;		
				}
				
				return redirect('admin/accountings?item='.$item.'&action='.$action.'&message='.((isset($message))?$message:''));
					
			}
		}elseif($item=='invoice_tracks')
		{
			$ezpay = new EzPay();
			$company_id = env('ezPay_invoice_track_company_id');
			if($action=='create')
			{
				$validator = Validator::make($request->all(), array(
					'Year' => 'required|max:8',
					'Term' => 'required|max:4',
					'AphabeticLetter' => 'required|max:8',
					'StartNumber' => 'required|max:16',
					'EndNumber' => 'required|max:16',
					'Type' => 'required|max:4'
				));
			
				if ($validator->fails()) {
					return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/accountings','item'=>$item));
				}
				
				$post_data_array = array(//post_data 欄位資料     
					'RespondType' => 'JSON', //回傳格式     
					'Version' => '1.0', //串接程式版本     
					'TimeStamp' => time(), //時間戳記     
					'Year' => $request->Year, //發票年度     
					'Term' => $request->Term, //發票期別     
					'AphabeticLetter' => $request->AphabeticLetter, //字軌英文代碼     
					'StartNumber' => $request->StartNumber, //發票起始號碼     
					'EndNumber' => $request->EndNumber, //發票結束號碼     
					'Type' => $request->Type //發票類別 
				); 
				$url = 'https://cinv.ezpay.com.tw/Api_number_management/createNumber';
				$result = $ezpay->inv_track_post($url,$post_data_array);
				$result_data = json_decode($result['web_info']);
				if($result_data->Status=='SUCCESS')
				{
					
					$check_code_array = array(     
						'AphabeticLetter' => $result_data->Result->AphabeticLetter, //發票字軌     
						'CompanyId' => $company_id, //會員編號     
						'EndNumber' => $result_data->Result->EndNumber, //發票結束號碼     
						'ManagementNo' => $result_data->Result->ManagementNo, //字軌流水號     
						'StartNumber' => $result_data->Result->StartNumber //發票起始號碼 
					);
					
					$check_code = $ezpay->check_track_code($check_code_array);
					
					if($check_code==$result_data->Result->CheckCode)
					{
						$input = new Invoice_track;
						$input->ManagementNo = $result_data->Result->ManagementNo;
						$input->Year = $result_data->Result->Year;
						$input->Term = $result_data->Result->Term;
						$input->AphabeticLetter = $result_data->Result->AphabeticLetter;
						$input->StartNumber = $result_data->Result->StartNumber;
						$input->EndNumber = $result_data->Result->EndNumber;
						$input->LastNumber = (int)$result_data->Result->LastNumber;
						$input->Type = $result_data->Result->Type;
						$input->Flag = $result_data->Result->Flag;
						$input->save();
						
					}else
					{
						$result_data->Result->Status = 'ERROR0001';
						$result_data->Result->Message = '驗證碼錯誤!!';	
					}	
				}	 
				return redirect('admin/accountings?item='.$item.'&action=manage&message='.(($result_data->Status!='SUCCESS')?$result_data->Message:''));
			}elseif($action=='manage')
			{
				$validator = Validator::make($request->all(), array(
					'Year' => 'required|max:8',
					'Flag' => 'required|max:4',
				));
			
				if ($validator->fails()) {
					return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/accountings','item'=>$item));
				}
				
				$post_data_array = array(//post_data 欄位資料     
					'RespondType' => 'JSON', //回傳格式     
					'Version' => '1.0', //串接程式版本     
					'TimeStamp' => time(), //時間戳記     
					'ManagementNo' => $request->ManagementNo, //平台序號
					'Year' => $request->Year, //發票年度     
					'Flag' => $request->Flag //字軌狀態 
				); 
				
				$url = 'https://cinv.ezpay.com.tw/Api_number_management/manageNumber';
				$result = $ezpay->inv_track_post($url,$post_data_array);
				$result_data = json_decode($result['web_info']);
				if($result_data->Status=='SUCCESS')
				{
					
					$check_code_array = array(     
						'AphabeticLetter' => $result_data->Result->AphabeticLetter, //發票字軌     
						'CompanyId' => $company_id, //會員編號     
						'EndNumber' => $result_data->Result->EndNumber, //發票結束號碼     
						'ManagementNo' => $result_data->Result->ManagementNo, //字軌流水號     
						'StartNumber' => $result_data->Result->StartNumber //發票起始號碼 
					); 
					
					$check_code = $ezpay->check_track_code($check_code_array);
					if($check_code==$result_data->Result->CheckCode)
					{
						DB::transaction(function () use($result_data) {
							Invoice_track::where('Flag','!=',2)
								->where('Year',$result_data->Result->Year)
								->where('Term',$result_data->Result->Term)
								//->where('AphabeticLetter',$result_data->Result->AphabeticLetter)
								->where('ManagementNo','!=',$result_data->Result->ManagementNo)
								->update(array('Flag'=>0));
							
							$input['Year'] = $result_data->Result->Year;
							$input['Flag'] = $result_data->Result->Flag;
							$input['LastNumber'] = (int)$result_data->Result->LastNumber;
							Invoice_track::where('ManagementNo',$result_data->Result->ManagementNo)->update($input);
						});
						
					}else
					{
						$result_data->Result->Status = 'ERROR0001';
						$result_data->Result->Message = 'CheckCode不符';	
					}	
				}	 
				return redirect('admin/accountings?item='.$item.'&action='.$action.'&message='.(($result_data->Status!='SUCCESS')?$result_data->Message:''));
			}
		}elseif($item=='invoices')
		{
			$ezpay = new EzPay();
			$merchant_id = env('ezPay_invoice_merchant_id');
			if($action=='create')
			{
				$validator = Validator::make($request->all(), array(
					'u_id' => 'required|max:32',
					'BuyerEmail' => 'required|max:128',
					'MerchantOrderNo' => 'required|max:32',
					'Status' => 'required|max:4',
					'Category' => 'required|max:8'
				));
			
				if ($validator->fails()) {
					return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/accountings','item'=>$item));
				}
				
				$user = User::where('usr_id',$request->u_id)->select('id')->first();
				if(!$user)
					return View('admin/error_message', array('message' => '您的權限似乎不足喔!!', 'goUrl'=>'/admin/accountings','item'=>$item));
				
				$ItemNames = array();
				$ItemCounts = array();
				$ItemUnits = array();
				$ItemPrices = array();
				$ItemAmts = array();
				for($i=0;$i<$request->count;$i++)
				{
					$ItemNames[] = $request['ItemName'.$i];
					$ItemCounts[] = $request['ItemCount'.$i];
					$ItemUnits[] = $request['ItemUnit'.$i];
					$ItemPrices[] = $request['ItemPrice'.$i];
					$ItemAmts[] = $request['ItemAmt'.$i];	
				}
				$post_data_array = array(//post_data 欄位資料            
					 "RespondType" => "JSON",             
					 "Version" => "1.4",             
					 "TimeStamp" => time(), //請以 time() 格式            
					 "TransNum" => ((isset($request->TransNum))?$request->TransNum:''),             
					 "MerchantOrderNo" => $request->MerchantOrderNo, 
					 "BuyerName" => $request->BuyerName,             
					 "BuyerUBN" => ((isset($request->BuyerUBN))?$request->BuyerUBN:NULL), 
					 "BuyerAddress" => ((isset($request->BuyerAddress))?$request->BuyerAddress:''),             
					 "BuyerEmail" => $request->BuyerEmail,             
					 "Category" => $request->Category,           
					 "TaxType" => $request->TaxType,             
					 "TaxRate" => $request->TaxRate,             
					 "Amt" => $request->Amt,             
					 "TaxAmt" => $request->TaxAmt,             
					 "TotalAmt" => $request->TotalAmt,             
					 "CarrierType" => ((isset($request->CarrierType))?$request->CarrierType:NULL), 
					 "CarrierNum" => ((isset($request->CarrierType) && $request->CarrierType)?rawurlencode($request->u_id):NULL),
					 "LoveCode" => ((isset($request->LoveCode))?$request->LoveCode:NULL),             
					 "PrintFlag" => ((isset($request->PrintFlag))?$request->PrintFlag:'N'), 
					 "ItemName" => join('|',$ItemNames), //多項商品時，以「|」分開 
					 "ItemCount" => join('|',$ItemCounts), //多項商品時，以「|」分開 
					 "ItemUnit" => join('|',$ItemUnits), //多項商品時，以「|」分開 
					 "ItemPrice" => join('|',$ItemPrices), //多項商品時，以「|」分開 
					 "ItemAmt" => join('|',$ItemAmts), //多項商品時，以「|」分開 
					 "Comment" => ((isset($request->Comment))?$request->Comment:''), 
					 "Status" => $request->Status, //1=立即開立，0=待開立，3=延遲開立             
					 "CreateStatusTime" => ((isset($request->CreateStatusTime))?$request->CreateStatusTime:NULL), 
					 "NotifyEmail" => "1", //1=通知，0=不通知 
				); 
				
				$url = 'https://cinv.pay2go.com/API/invoice_issue';
				$result = $ezpay->invoice_post($url, $post_data_array);
				if($result=='error')
					return View('admin/error_message', array('message' => '查無此合作商店資料', 'goUrl'=>'/admin/accountings','item'=>$item, 'action'=>$action));
				
				$result_data = json_decode($result['web_info']);
				if($result_data->Status=='SUCCESS')
				{
					$deArr = json_decode($result_data->Result);
					$check_code_array = array(     
						"MerchantID" => $deArr->MerchantID,//商店代號     
						"MerchantOrderNo" => $deArr->MerchantOrderNo,  //商店自訂單號(訂單編號)     
						"InvoiceTransNo" => $deArr->InvoiceTransNo,  //智付寶電子發票開立序號     
						"TotalAmt" => $deArr->TotalAmt,  //發票金額    
						"RandomNum" => $deArr->RandomNum  //發票防偽隨機碼 
					);
					$check_code = $ezpay->check_invoice_code($check_code_array);
					if($check_code==$deArr->CheckCode)
					{
						$input = new Invoice;
						
						$input->u_id = $user->id;
						$input->InvoiceStatus = 1;
						$input->InvoiceTransNo = $deArr->InvoiceTransNo;
						$input->TransNum = $request->TransNum;
						$input->MerchantOrderNo = $request->MerchantOrderNo;
						$input->Status = $request->Status;
						$input->Category = $request->Category;
						$input->BuyerName = $request->BuyerName;
						$input->BuyerUBN = $request->BuyerUBN;
						$input->BuyerEmail = $request->BuyerEmail;
						$input->BuyerUBN = $request->BuyerUBN;
						$input->BuyerAddress = $request->BuyerAddress;
						$input->CarrierType = $request->CarrierType;
						$input->CarrierNum = $request->CarrierNum;
						$input->LoveCode = $request->LoveCode;
						$input->PrintFlag = $request->PrintFlag;
						$input->TaxType = $request->TaxType;
						$input->TaxRate = $request->TaxRate;
						$input->TotalAmt = (int)$deArr->TotalAmt;
						$input->InvoiceNumber = $deArr->InvoiceNumber;
						$input->RandomNum = $deArr->RandomNum;
						$input->CreateTime = $deArr->CreateTime;
						$input->BarCode = ((isset($deArr->BarCode))?$deArr->BarCode:'');
						$input->QRcodeL = ((isset($deArr->QRcodeL))?$deArr->QRcodeL:'');
						$input->QRcodeR = ((isset($deArr->QRcodeR))?$deArr->QRcodeR:'');
						$input->Comment = $request->Comment;
						$input->save();
					}else
					{
						$result_data->Status = 'ERROR0001';
					}	
				}
				if(isset($request->MerchantID))
					return redirect('admin/accountings?item='.$item.'&action=transfer&message='.(($result_data->Status!='SUCCESS')?$result_data->Message:''));
				else
					return redirect('admin/accountings?item='.$item.'&action=manage&message='.(($result_data->Status!='SUCCESS')?$result_data->Message:''));
						
			}elseif($action=='invalid') //發票作廢
			{
				$validator = Validator::make($request->all(), array(
					'InvalidReason' => 'required|max:70',
					'InvoiceNumber' => 'required|max:32'
				));
			
				if ($validator->fails()) {
					return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/accountings','item'=>$item));
				}
				
				$post_data_array = array(//post_data 欄位資料            
					 "RespondType" => "JSON",             
					 "Version" => "1.0",             
					 "TimeStamp" => time(), //請以 time() 格式            
					 "InvoiceNumber" => $request->InvoiceNumber,             
					 "InvalidReason" => $request->InvalidReason, 
				); 
				
				//dd($post_data_array);
				$url = 'https://cinv.pay2go.com/API/invoice_invalid';
				$result = $ezpay->set_invalid($url,$post_data_array);
				
				return redirect('admin/accountings?item='.$item.'&action=manage&message='.(($result)?$result:''));
					
			}elseif($action=='allowance')
			{
				$validator = Validator::make($request->all(), array(
					'BuyerEmail' => 'required|max:64',
					'InvoiceNo' => 'required|max:32',
					'MerchantOrderNo' => 'required|max:32'
				));
			
				if ($validator->fails()) {
					return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/accountings','item'=>$item));
				}
				
				$ItemNames = array();
				$ItemCounts = array();
				$ItemUnits = array();
				$ItemPrices = array();
				$ItemAmts = array();
				$ItemTaxAmts = array();
				for($i=0;$i<$request->count;$i++)
				{
					$ItemNames[] = $request['ItemName'.$i];
					$ItemCounts[] = $request['ItemCount'.$i];
					$ItemUnits[] = $request['ItemUnit'.$i];
					$ItemPrices[] = $request['ItemPrice'.$i];
					$ItemAmts[] = $request['ItemAmt'.$i];
					$ItemTaxAmts[] = $request['ItemTaxAmt'.$i];	
				}
				
				$post_data_array = array(//post_data 欄位資料            
					 "RespondType" => "JSON",             
					 "Version" => "1.3",             
					 "TimeStamp" => time(), //請以 time() 格式            
					 "InvoiceNo" => $request->InvoiceNo,             
					 "MerchantOrderNo" => $request->MerchantOrderNo,
					 "ItemName" => join("|",$ItemNames),
					 "ItemCount" => join("|",$ItemCounts),
					 "ItemUnit" => join("|",$ItemUnits),
					 "ItemPrice" => join("|",$ItemPrices),
					 "ItemAmt" => join("|",$ItemAmts),
					 "ItemTaxAmt" => join("|",$ItemTaxAmts),
					 "TotalAmt" => $request->TotalAmt,
					 "Status" => $request->Status,
					 "BuyerEmail" => $request->BuyerEmail 
				); 
				$url = 'https://cinv.pay2go.com/API/allowance_issue';
				$result = $ezpay->invoice_post($url,$post_data_array);
				$result_data = json_decode($result['web_info']);
				if($result_data->Status=='SUCCESS')
				{
					$invoice = Invoice::where('InvoiceNumber',$request->InvoiceNo)->select('MerchantOrderNo','InvoiceTransNo','TotalAmt','RandomNum')->first();
					
					$deArr = json_decode($result_data->Result);
					$check_code_array = array(     
						"MerchantID" => $merchant_id,//商店代號     
						"MerchantOrderNo" => $invoice->MerchantOrderNo,  //商店自訂單號(訂單編號)     
						"InvoiceTransNo" => $invoice->InvoiceTransNo,  //智付寶電子發票開立序號     
						"TotalAmt" => $invoice->TotalAmt,  //發票金額    
						"RandomNum" => $invoice->RandomNum  //發票防偽隨機碼 
					);
					$check_code = $ezpay->check_invoice_code($check_code_array);
					if($check_code==$deArr->CheckCode)
					{
						
						$input = new Invoice_allowance;
						
						$input->Status = $request->Status;
						$input->InvoiceNumber = $request->InvoiceNo;
						$input->MerchantOrderNo = $request->MerchantOrderNo;
						$input->ItemName = join("|",$ItemNames);
						$input->ItemCount = join("|",$ItemCounts);
						$input->ItemUnit = join("|",$ItemUnits);
						$input->ItemPrice = join("|",$ItemPrices);
						$input->ItemAmt = join("|",$ItemAmts);
						$input->ItemTaxAmt = join("|",$ItemTaxAmts);
						$input->AllowanceNo = $deArr->AllowanceNo;
						$input->AllowanceAmt = $deArr->AllowanceAmt;
						$input->save();
						
						Invoice::where('InvoiceNumber',$request->InvoiceNo)->update(array('RemainAmt'=>$deArr->RemainAmt));
					}else
					{
						$result_data->Status = 'ERROR0001';
					}	
				}
				return redirect('admin/accountings?item='.$item.'&action=manage&message='.(($result_data->Status!='SUCCESS')?$result_data->Message:''));	
			}elseif($action=='confirm')
			{
				$validator = Validator::make($request->all(), array(
					'MerchantOrderNo' => 'required|max:32',
					'AllowanceStatus' => 'required|max:4',
					'AllowanceNo' => 'required|max:32',
					'TotalAmt' => 'required|max:10'
				));
			
				if ($validator->fails()) {
					return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/accountings','item'=>$item));
				}
				
				$post_data_array = array(//post_data 欄位資料            
					 "RespondType" => "JSON",             
					 "Version" => "1.0",             
					 "TimeStamp" => time(), //請以 time() 格式            
					 "AllowanceStatus" => $request->AllowanceStatus,             
					 "AllowanceNo" => $request->AllowanceNo,
					 "MerchantOrderNo" => $request->MerchantOrderNo,
					 "TotalAmt" => $request->TotalAmt
				); 
				
				$url = 'https://cinv.pay2go.com/API/allowance_touch_issue';
				$result = $ezpay->invoice_post($url,$post_data_array);
				$result_data = json_decode($result['web_info']);
				if($result_data->Status=='SUCCESS')
				{
					$invoice = Invoice::where('InvoiceNumber',$request->InvoiceNo)->select('MerchantOrderNo','InvoiceTransNo','TotalAmt','RandomNum','RemainAmt')->first();
					
					$deArr = json_decode($result_data->Result);
					$check_code_array = array(     
						"MerchantID" => $merchant_id,//商店代號     
						"MerchantOrderNo" => $invoice->MerchantOrderNo,  //商店自訂單號(訂單編號)     
						"InvoiceTransNo" => $invoice->InvoiceTransNo,  //智付寶電子發票開立序號     
						"TotalAmt" => $invoice->TotalAmt,  //發票金額    
						"RandomNum" => $invoice->RandomNum  //發票防偽隨機碼 
					);
					$check_code = $ezpay->check_invoice_code($check_code_array);
					if($check_code==$deArr->CheckCode)
					{
						
						if($request->AllowanceStatus=='C')
						{
							Invoice_allowance::where('AllowanceNo', $request->AllowanceNo)->update(array('Status'=>1));
							Invoice::where('InvoiceNumber',$request->InvoiceNo)->update(array('RemainAmt'=>$deArr->RemainAmt));
						}else
						{
							Invoice_allowance::where('AllowanceNo', $request->AllowanceNo)->delete();
							Invoice::where('InvoiceNumber',$request->InvoiceNo)->update(array('RemainAmt'=>($invoice->RemainAmt+$request->TotalAmt)));	
						}
					}else
					{
						$result_data->Status = 'ERROR0001';
					}	
				}
				return redirect('admin/accountings?item='.$item.'&action=manage&message='.(($result_data->Status!='SUCCESS')?$result_data->Message:''));	
			}
 	
		}
	}
	
	public function settings(Request $request)
    {
    	if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		
		return View('admin/settings', array('id'=>(($request->has('id'))?$request->id:'')));
			
	}
	
	public function settings_pt(Request $request)
	{
		if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
			
		$setting = Setting::first();
		
		$id = $request->id;
		if(isset($id) && $id)
		{
			if(!$setting)
				$input = new Setting;
			
			switch($id)
			{
				case 'term_of_use':
			  		if(!$setting)
						$input->term_of_use = $request->data_body;
					else
						$input['term_of_use'] = $request->data_body; 	
					break;
				case 'privacy':
			  		if(!$setting)
						$input->privacy = $request->data_body;
					else
						$input['privacy'] = $request->data_body; 	
					break;
				case 'how2_help_post_list':
			  		if(!$setting)
						$input->how2_help_post_list = $request->data_body;
					else
						$input['how2_help_post_list'] = $request->data_body; 	
					break;
				case 'profit_share_post_list':
			  		if(!$setting)
						$input->profit_share_post_list = $request->data_body;
					else
						$input['profit_share_post_list'] = $request->data_body; 	
					break;
				case 'tutorial_post_list':
			  		if(!$setting)
						$input->tutorial_post_list = $request->data_body;
					else
						$input['tutorial_post_list'] = $request->data_body; 	
					break;
				case 'aboutus_post':
			  		if(!$setting)
						$input->aboutus_post = $request->data_body;
					else
						$input['aboutus_post'] = $request->data_body; 	
					break;
				case 'GA_code':
			  		if(!$setting)
						$input->GA_code = $request->data_body;
					else
						$input['GA_code'] = $request->data_body; 	
					break;
				case 'Mixpanel_Code':
			  		if(!$setting)
						$input->Mixpanel_Code = $request->data_body;
					else
						$input['Mixpanel_Code'] = $request->data_body; 	
					break;
				case 'referral_FB_msg':
			  		if(!$setting)
						$input->referral_FB_msg = $request->data_body;
					else
						$input['referral_FB_msg'] = $request->data_body; 	
					break;	
				case 'welcome_email':
			  		if(!$setting)
					{
						$input->welcome_email_subj = $request->data_subject;
						$input->welcome_email_body = $request->data_body;
					}else
					{
						$input['welcome_email_subj'] = $request->data_subject;
						$input['welcome_email_body'] = $request->data_body;
					}
					break;
				case 'referral_email':
			  		if(!$setting)
					{
						$input->referral_email_subj = $request->data_subject;
						$input->referral_email_body = $request->data_body;
					}else
					{
						$input['referral_email_subj'] = $request->data_subject;
						$input['referral_email_body'] = $request->data_body;
					}
					break;
				case 'email_veri':
			  		if(!$setting)
					{
						$input->email_veri_subj = $request->data_subject;
						$input->email_veri_body = $request->data_body;
					}else
					{
						$input['email_veri_subj'] = $request->data_subject;
						$input['email_veri_body'] = $request->data_body;
					}
					break;
				case 'email_veri_comp':
			  		if(!$setting)
					{
						$input->email_veri_comp_subj = $request->data_subject;
						$input->email_veri_comp_body = $request->data_body;
					}else
					{
						$input['email_veri_comp_subj'] = $request->data_subject;
						$input['email_veri_comp_body'] = $request->data_body;
					}
					break;
				case 'email_account_del':
			  		if(!$setting)
					{
						$input->email_account_del_subj = $request->data_subject;
						$input->email_account_del_body = $request->data_body;
					}else
					{
						$input['email_account_del_subj'] = $request->data_subject;
						$input['email_account_del_body'] = $request->data_body;
					}
					break;
				case 'email_reward':
			  		if(!$setting)
					{
						$input->email_reward_subj = $request->data_subject;
						$input->email_reward_body = $request->data_body;
					}else
					{
						$input['email_reward_subj'] = $request->data_subject;
						$input['email_reward_body'] = $request->data_body;
					}
					break;													  
			
			}
		}
		if(!$setting)
		{
			
			$input->save();
			
			return redirect('admin/settings');
			
		}else
		{
			Setting::where('id', 1)->update($input);
			
			return redirect('admin/settings?id='.$request->id);
		}
	}
	
	public function users(Request $request)
    {
    	if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		
		$mode = (($request->has('mode'))?$request->get('mode'):'');
		if($mode=='edit')
			return View('admin/users', array('mode'=>$mode,'id'=>$request->get('id')));
		else
			return View('admin/users', array('mode'=>$mode));
		
	}
	
	public function users_pt(Request $request)
    {
    	if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		
		$mode = (($request->has('mode'))?$request->get('mode'):'');
		
		if($mode=='edit')
		{
			$valArr = array(
				'first_name' => 'required|string|max:24',
				'last_name' => 'required|string|max:24'
			);
			$validator = Validator::make($request->all(), $valArr );
		
			if ($validator->fails()) {
				return View('admin/error_message', array('message' => '輸入表單欄位驗證錯誤!!', 'goUrl'=>'/admin/users','mode'=>$mode, 'id'=>$request->id));
			}
			$input['first_name'] = $request->first_name;
			$input['last_name'] = $request->last_name;
			$input['nickname'] = $request->nickname;
			$input['phone_nat_code'] = $request->phone_nat_code;
			$input['phone_number'] = $request->photo_number;
			$input['usr_status'] = $request->usr_status;
			$input['sex'] = $request->sex;
			
			User::where('usr_id', $request->id)->update($input);
			
			return redirect('admin/users?mode=edit&id='.$request->id);
		}
	}
	
	public function videos(Request $request)
    {
    	if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		
		$mode = (($request->has('mode'))?$request->get('mode'):'');
		if($mode=='edit')
		{
			return View('admin/videos', array('mode'=>$mode,'id'=>$request->id));
		}elseif($mode=='del')
		{
			$id = $request->id;
			
			Video::where('video_id','=',$id)->delete();
			
			return redirect('admin/videos');
				
		}else
			return View('admin/videos', array('mode'=>$mode));
			
	}
	
	public function videos_pt(Request $request)
	{
		if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		
		$mode = ($request->has('mode'))?$request->mode:'';
		if($mode=='add')
		{
			$input = new Video;
			$input->status = (($request->has('status'))?1:0);
			$input->video_id = Utils::create_name_id(time());
			$input->title = $request->title;
			$input->youtube_id = (($request->has('youtube_id'))?$request->youtube_id:'');
			$input->vimeo_id = (($request->has('vimeo_id'))?$request->vimeo_id:'');
			$input->save();
			
			return redirect('admin/videos');
			
		}elseif($mode=='edit')
		{
			
			$input['status'] = (($request->has('status'))?1:0);
			$input['title'] = $request->title;
			$input['youtube_id'] = (($request->has('youtube_id'))?$request->youtube_id:'');
			$input['vimeo_id'] = (($request->has('vimeo_id'))?$request->vimeo_id:'');
			
			Video::where('video_id', $request->video_id)->update($input);
			
			return redirect('admin/videos?mode=edit&id='.$request->video_id);
		}
	}
	
	public function marketings(Request $request)
    {
    	if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		$item = (($request->has('item'))?$request->get('item'):'');
		$mode = (($request->has('mode'))?$request->get('mode'):'');
		$type = (($request->has('type'))?$request->get('type'):'');
		if($item=='pic_tables')
		{
			if($mode=='edit')
			{
				return View('admin/pic_tables', array('item'=>$item,'mode'=>$mode,'type'=>$type,'id'=>$request->id));
			}elseif($mode=='del')
			{
				$id = $request->id;
				
				Pic_table::where('pic_id','=',$id)->delete();
				
				return redirect('admin/marketings?item=pic_tables&type='.$type);
					
			}else
				return View('admin/pic_tables', array('mode'=>$mode,'type'=>$type));
		}
			
	}
	
	public function marketings_pt(Request $request)
	{
		if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return View('admin/error', array('message' => '您的權限似乎不足喔!!'));
		}
		
		$item = ($request->has('item'))?$request->item:'';
		$mode = ($request->has('mode'))?$request->mode:'';
		$type = ($request->has('type'))?$request->type:'';
		
		if(!file_exists(storage_path()."/files"))
			mkdir(storage_path()."/files", 0775);
		if(!file_exists(storage_path()."/files/pic"))
			mkdir(storage_path()."/files/pic", 0775);	
		if(!file_exists(storage_path()."/files/pic/home/"))
			mkdir(storage_path()."/files/pic/home", 0775);
		if(!file_exists(storage_path()."/files/pic/home/photoBig"))
			mkdir(storage_path()."/files/pic/home/photoBig", 0775);
		if(!file_exists(storage_path()."/files/pic/home/photoSmall"))
			mkdir(storage_path()."/files/pic/home/photoSmall", 0775);	
				
		if($item=='pic_tables')
		{
			if($mode=='add')
			{
				$pic_id = Utils::create_name_id(time());
				
				if($request->file('photo'))
				{
					$file = $request->file('photo');
					$subDot = strtolower($request->photo->getClientOriginalExtension());
					if($subDot=='jpeg' || $subDot=='jpg' || $subDot=='JPG')
					{
						$Fn_name = $pic_id.'.'.$subDot;
						$path = storage_path()."/files/pic/home/photoBig/";
						$request->file('photo')->move($path, $pic_id.'.'.$subDot);
						//chmod($path.$pic_id.'.'.$subDot, 0775);
						//Utils::ImageResize($path.$pic_id.'.'.$subDot, $path.$pic_id.'.'.$subDot, 1200,1200,72);
						
						$path2 = storage_path()."/files/pic/home/photoSmall/";
						copy($path.$pic_id.'.'.$subDot, $path2.$pic_id.'.'.$subDot);
						Utils::ImageResize($path2.$pic_id.'.'.$subDot, $path2.$pic_id.'.'.$subDot, 500,500,72);
					}else
						return View('admin/error_message', array('message' => '錯誤的影像格式，請使用JPG圖檔!', 'goUrl'=>'/admin/marketings?item=pic_tables&type='.$typs));
				}
				$input = new Pic_table;
				$input->pic_id = $pic_id;
				$input->pic_status = (($request->has('pic_status'))?1:0);
				$input->pic_type = $request->type;
				$input->home_frontpage_pic = ((isset($Fn_name))?$Fn_name:'');
				$input->save();
				
				return redirect('admin/marketings?item=pic_tables&type='.$type);
				
			}elseif($mode=='edit')
			{
				
				$pic_id = $request->pic_id;
				
				if($request->file('photo'))
				{
					$file = $request->file('photo');
					$subDot = strtolower($request->photo->getClientOriginalExtension());
					if($subDot=='jpeg' || $subDot=='jpg' || $subDot=='JPG')
					{
						$Fn_name = $pic_id.'.'.$subDot;
						$path = storage_path()."/files/pic/home/photoBig/";
						$request->file('photo')->move($path, $pic_id.'.'.$subDot);
						
						$path2 = storage_path()."/files/pic/home/photoSmall/";
						copy($path.$pic_id.'.'.$subDot, $path2.$pic_id.'.'.$subDot);
						Utils::ImageResize($path2.$pic_id.'.'.$subDot, $path2.$pic_id.'.'.$subDot, 500,500,72);
					}else
						return View('admin/error_message', array('message' => '錯誤的影像格式，請使用JPG圖檔!', 'goUrl'=>'/admin/marketings?item=pic_tables&type='.$type));
				}
				
				$input['pic_status'] = (($request->has('pic_status'))?1:0);
				$input['pic_type'] = $request->type;
				$input['home_frontpage_pic'] = ((isset($Fn_name) && $request->up_photo)?$Fn_name:(($request->old_photo)?$request->old_photo:''));
				Pic_table::where('pic_id', $pic_id)->update($input);
				
				return redirect('admin/marketings?item=pic_tables&mode=edit&type='.$type.'&id='.$pic_id);
			}
		}
	}
	
	//-------------------------------------------------ajax
	
	public function get_owner(Request $request)
    {
    	if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return 'failed';
		}
		
		$owner = Admin_member::join('adm_groups','adm_members.adm_group','=','adm_groups.id')
		  ->select('adm_members.id','adm_members.adm_account','adm_members.adm_status','adm_members.adm_name','adm_members.adm_email','adm_members.created_at','adm_groups.group_id')
		  ->where('adm_account', Session::get('ownerID'))
		  ->first();
		
		$groups = Admin_group::where('group_status',1)->where('group_id',$owner->group_id)->select('group_id','group_name')->get();
		return array('owner'=>((isset($owner))?$owner:''),'groups'=>((isset($groups))?$groups:''));
	}
	
	public function get_managers(Request $request)
    {
    	if(!Admin_member::isManager(7))
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return 'failed';
		}
		
		$mode = (($request->has('mode'))?$request->mode:'');
		$groups = Admin_group::where('group_status',1)->select('group_id','group_name')->get();
		if($mode=='edit')
		{
			$manager = Admin_member::join('adm_groups','adm_members.adm_group','=','adm_groups.id')
			  ->select('adm_members.id','adm_members.adm_account','adm_members.adm_status','adm_members.adm_name','adm_members.adm_email','adm_members.created_at','adm_groups.group_id')
			  ->where('adm_account',$request->id)
			  ->first();
			return array('manager'=>((isset($manager))?$manager:''),'groups'=>((isset($groups))?$groups:''));  
		}else
		{
			$level = Session::get('ownerLevel');
			if($level>=7)
			{
			  $managers = Admin_member::join('adm_groups','adm_members.adm_group','=','adm_groups.id')
				  ->select('adm_members.id','adm_members.adm_account','adm_members.adm_name','adm_members.adm_email','adm_members.created_at','adm_groups.group_name','adm_groups.group_master','adm_groups.group_manager')
				  ->where(function($query) use($level){
					  if($level<9)
						$query->where('adm_groups.group_id',Session::get('ownerGroup'));
				  })->get();
			}
			 
			return array('managers'=>((isset($managers))?$managers:''),'groups'=>((isset($groups))?$groups:''));
		}
	}
	
	public function get_users(Request $request) 
    {
    	if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return 'error';
		}
		
		
		$mode = (($request->has('mode'))?$request->mode:'');
		if($mode=='edit')
		{
			$user = User::where('usr_id',$request->id)->select('id','usr_id','usr_status','first_name','last_name','nickname','email','phone_number','phone_nat_code','email_validated','open_offer_setting','usr_photo','sex','created_at')->first();
				
			if($user->addr)
				$user->addr = json_decode($user->addr);
			return array('user'=>((isset($user))?$user:''));  
		}else if($mode=='search'||($mode=='turn'&&$request->text!=''))
		{
			$text = trim($request->text);
			$users = User::where('first_name',$text)
				->orWhere('last_name',$text)
				->orWhere('nickname',$text)
				->orWhere('usr_id',$text)
				->orWhere('email',$text)
				->orWhere('phone_number',$text)
				->orWhere('nationality',$text)
				->orWhere('id_number',$text)
				->orderBy('created_at','desc')->select('id','usr_id','usr_status','first_name','last_name','email','phone_number','phone_nat_code','email_validated','created_at')->paginate(30);
				
			return array('users'=>((isset($users))?$users:''));
		}else
		{
			$users = User::orderBy('created_at','desc')->select('id','usr_id','usr_status','first_name','last_name','nickname','email','phone_number','phone_nat_code','email_validated','FB_login_token','Line_login_token','Google_login_token','created_at')->paginate(30);
			
			return array('users'=>((isset($users))?$users:''));
		}
	}
	
	public function chk_account_repeat(Request $request) //檢查新增帳號是否重複
    {
    	
		if(!Admin_member::isManager(7))
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return 'failed';
		}
		
		return Admin_member::where('adm_account',$request->id)->count();
	}
	
	public function get_groups(Request $request)
    {
    	if(!Admin_member::isManager(7))
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return 'failed';
		}
		
		$mode = (($request->has('mode'))?$request->mode:'');
		$admins = Admin_member::where('adm_status',1)->select('id','adm_account','adm_name')->get();
		$gets = Utils::get_setting();
		$settings = array();
		foreach($gets as $key => $value)
		{
			$settings[] = array('id'=>$key,'val'=>$value);
		}
		if($mode=='edit')
		{
			$group = Admin_group::where('group_id',$request->id)->select('id','group_status','group_id','group_name','group_manager','group_setting','created_at')->first();
			if($group->group_setting)
					$group->group_setting = json_decode($group->group_setting);
			if(isset($group))
				$group_members = Admin_member::where('adm_group',$group->id)->select('adm_name','adm_account')->get();
			else
				$group_members = array();	
			return array('group'=>((isset($group))?$group:''),'admins'=>((isset($admins))?$admins:''),'settings'=>((isset($settings))?$settings:''),'members'=>((isset($group_members))?$group_members:''));  
		}else
		{
			$groups = Admin_group::where('group_master',0)->select('id','group_status','group_id','group_name','group_manager','group_setting','created_at')->get();
			if(isset($groups) && count($groups))
			{
				foreach($groups as $group)
				{
					if($group->group_manager)
					{
						$manager = Admin_member::where('id',$group->group_manager)->select('adm_name')->first();
						if(isset($manager))
							$group->group_manager = $manager->adm_name;	
					}
					$arr = array();
					if($group->group_setting)
					{
						$sets = json_decode($group->group_setting);	
						foreach($sets as $set)
						{
							if(isset($gets[$set]) && $gets[$set])
								$arr[] = $gets[$set];	
						}
					}
					$group->group_setting = $arr;
				}
			}
			return array('groups'=>((isset($groups))?$groups:''),'admins'=>((isset($admins))?$admins:''),'settings'=>((isset($settings))?$settings:''));
		}
	}
	
	public function get_system(Request $request) //檢查新增帳號是否重複
    {
    	$settings = Admin_group::where('group_id', Session::get('ownerGroup'))->select('group_setting')->first();
		if(isset($settings) && $settings->group_setting)
			return json_decode($settings->group_setting);
		else
			return array();
	}
	
	public function get_settings(Request $request)
    {
    	if(!Admin_member::isManager(7))
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return 'failed';
		}
		
		$id = $request->id;
		if(isset($id) && $id)
		{
			$item_data = Setting::first();
			switch($id)
			{
				case 'term_of_use':
			  		if(isset($item_data))
					{
						$data_subject = '';
						$data_body = $item_data->term_of_use;
					}
					$item_name = '使用者條款';	
			  		$item = 0;
					break;
				case 'privacy':
			  		if(isset($item_data))
					{
						$data_subject = '';
						$data_body = $item_data->privacy;
					}
					$item_name = '隱私權政策';	
			  		$item = 0;
					break;
				case 'how2_help_post_list':
			  		if(isset($item_data))
					{
						$data_subject = '';
						$data_body = $item_data->how2_help_post_list;
					}
					$item_name = '好幫手條款';	
			  		$item = 0;
					break;
				case 'profit_share_post_list':
			  		if(isset($item_data))
					{
						$data_subject = '';
						$data_body = $item_data->profit_share_post_list;
					}
					$item_name = '利潤共享';	
			  		$item = 0;
					break;
				case 'tutorial_post_list':
			  		if(isset($item_data))
					{
						$data_subject = '';
						$data_body = $item_data->tutorial_post_list;
					}
					$item_name = '好幫手教學';	
			  		$item = 0;
					break;
				case 'aboutus_post':
			  		if(isset($item_data))
					{
						$data_subject = '';
						$data_body = $item_data->aboutus_post;
					}
					$item_name = '關於我門';	
			  		$item = 0;
					break;
				case 'GA_code':
			  		if(isset($item_data))
					{
						$data_subject = '';
						$data_body = $item_data->GA_code;
					}
					$item_name = 'GA code';	
			  		$item = 0;
					break;
				case 'Mixpanel_Code':
			  		if(isset($item_data))
					{
						$data_subject = '';
						$data_body = $item_data->Mixpanel_Code;
					}
					$item_name = 'Mixpanel code';	
			  		$item = 0;
					break;
				case 'referral_FB_msg':
			  		if(isset($item_data))
					{
						$data_subject = '';
						$data_body = $item_data->referral_FB_msg;
					}
					$item_name = '分享到FB文字訊息';	
			  		$item = 0;
					break;	
				case 'welcome_email':
			  		if(isset($item_data))
					{
						$data_subject = $item_data->welcome_email_subj;
						$data_body = $item_data->welcome_email_body;
					}
					$item_name = '歡迎加入';	
			  		$item = 1;
					break;
				case 'referral_email':
			  		if(isset($item_data))
					{
						$data_subject = $item_data->referral_email_subj;
						$data_body = $item_data->referral_email_body;
					}
					$item_name = '推薦他人';	
			  		$item = 1;
					break;
				case 'email_veri':
			  		if(isset($item_data))
					{
						$data_subject = $item_data->email_veri_subj;
						$data_body = $item_data->email_veri_body;
					}
					$item_name = 'Email認證';	
			  		$item = 1;
					break;
				case 'email_veri_comp':
			  		if(isset($item_data))
					{
						$data_subject = $item_data->email_veri_comp_subj;
						$data_body = $item_data->email_veri_comp_body;
					}
					$item_name = 'Email認證完成';	
			  		$item = 1;
					break;
				case 'email_account_del':
			  		if(isset($item_data))
					{
						$data_subject = $item_data->email_account_del_subj;
						$data_body = $item_data->email_account_del_body;
					}
					$item_name = '刪除帳戶';	
			  		$item = 1;
					break;
				case 'email_reward':
			  		if(isset($item_data))
					{
						$data_subject = $item_data->email_reward_subj;
						$data_body = $item_data->email_reward_body;
					}
					$item_name = '獲得紅利';	
			  		$item = 1;
					break;													  
			
			}
			return array('data_subject'=>((isset($item_data))?$data_subject:''),'data_body'=>((isset($item_data))?$data_body:''),'item_name'=>$item_name,'item'=>$item);	
		}
		
	}
	
	public function get_videos(Request $request) 
    {
    	if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return 'error';
		}
		
		$mode = (($request->has('mode'))?$request->mode:'');
		if($mode=='edit')
		{
			$video = Video::where('video_id',$request->id)->select('id','status','video_id','title','youtube_id','vimeo_id','created_at')->first();
				
			return array('video'=>((isset($video))?$video:''));  
		}else
		{
			$videos = Video::select('id','title','status','video_id','youtube_id','vimeo_id','created_at')->get();
			
			return array('videos'=>((isset($videos))?$videos:''));
		}
	}
	
	public function get_marketings(Request $request) 
    {
    	if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return 'error';
		}
		$item = (($request->has('item'))?$request->item:'');
		$mode = (($request->has('mode'))?$request->mode:'');
		$type = (($request->has('type'))?$request->type:'');
		if($item=='pic_tables')
		{
			if($mode=='edit')
			{
				$pic_table = Pic_table::where('pic_id',$request->id)->select('id','pic_id','pic_status','home_frontpage_pic','created_at')->first();
				
				$old_photo = $pic_table->home_frontpage_pic;
				if($pic_table->home_frontpage_pic)
					$pic_table->home_frontpage_pic = '/home/'.(($type==1)?'big':'small').'/'.$pic_table->home_frontpage_pic;
				
				return array('pic_table'=>((isset($pic_table))?$pic_table:''),'old_photo'=>$old_photo);  
			}else
			{
				$pic_tables = Pic_table::where('pic_type',$type)->select('id','pic_id','pic_status','pic_type','home_frontpage_pic','created_at')->get();
				
				foreach($pic_tables as $pic_table)
				{
					if(!$pic_table->pic_status)
						$pic_table->pic_status = '<span class="text-danger">下架中</span>';
					elseif($pic_table->pic_type==1)
						$pic_table->pic_status = '<span class="text-primary">頂部大圖 上架中</span>';
					elseif($pic_table->pic_type==2)
						$pic_table->pic_status = '<span class="text-success">首頁多圖 上架中</span>';
					elseif($pic_table->pic_type==3)
						$pic_table->pic_status = '<span class="text-success">LOGIN圖</span>';
					elseif($pic_table->pic_type==4)
						$pic_table->pic_status = 'NEWS圖';
					elseif($pic_table->pic_type==5)
						$pic_table->pic_status = 'POST圖';						
				}
				
				return array('pic_tables'=>((isset($pic_tables))?$pic_tables:''));
			}
		}
	}
	
	public function get_transfer_records(Request $request) 
    {
    	if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return 'error';
		}
		$item = (($request->has('item'))?$request->item:'');
		$action = (($request->has('action'))?$request->action:'');
		$message = (($request->has('message') && $request->get('message'))?$request->message:'');
		
		if($item=='newebPay')
		{
			$newebPay = new NewebPay();
			if($action=='mpg_gateway')
			{
				$create_transfer = array('MerchantOrderNo'=>'','BuyerName'=>'','Amt'=>0,'ItemDesc'=>'','Email'=>'');
				$title = '手動開立交易新增';	
			}elseif($action=='search')
			{
				$start_date = (($request->has('start_date') && $request->get('start_date'))?$request->get('start_date'):'');
				$end_date = (($request->has('end_date') && $request->get('end_date'))?$request->get('end_date'):'');
				$status = (($request->has('tradeStatus') && $request->get('tradeStatus')!='')?$request->get('tradeStatus'):'');
				$text = (($request->has('text') && $request->get('text'))?trim($request->get('text')):'');
				
				$search_date = array('start'=>$start_date,'end'=>$end_date);
				
				$mpgs = new Newebpay_mpg;
				$transfers = $mpgs->mpgs_search($search_date, $text, $status); //搜尋交易單資料
				$title = '交易搜尋管理';
					
			}elseif($action=='manage' || !$action)
			{	
				$transfers = Newebpay_mpg::join('merchants','newebpay_mpgs.MerchantID','=','merchants.MerchantID')
					  ->select('newebpay_mpgs.MerchantOrderNo','newebpay_mpgs.TradeStatus','newebpay_mpgs.TradeNo','newebpay_mpgs.PaymentType','newebpay_mpgs.Amt','newebpay_mpgs.ItemDesc','newebpay_mpgs.Email','newebpay_mpgs.EscrowBank','newebpay_mpgs.PayTime','newebpay_mpgs.MerchantID','merchants.MemberName','merchants.MerchantName','newebpay_mpgs.FundTime')
					  ->orderBy('newebpay_mpgs.created_at','desc')
					  ->paginate(30);
					  
				//$transfers = Newebpay_mpg::orderBy('created_at','desc')->paginate(30);
				$title = '交易查詢管理';
			}elseif($action=='del')
			{	
				Newebpay_mpg::where('MerchantOrderNo',$request->id)->delete();
				$transfers = Newebpay_mpg::orderBy('created_at','desc')->paginate(30);
				$title = '交易查詢管理';
			}elseif($action=='detail')
			{
				$transfer = Newebpay_mpg::join('merchants','newebpay_mpgs.MerchantID','=','merchants.MerchantID')->where('newebpay_mpgs.MerchantOrderNo',$request->id)->first();
				if(!$transfer)
					return 'error';
				if($transfer->TradeNo)
					$invoice = Invoice::where('TransNum',$transfer->TradeNo)->first(); //發票資料
				
				$result = $newebPay->query_tradeInfo($transfer); //查詢交易單並更新內容
				if($result['Status']!='SUCCESS')
						$message = $result['Message'];	
				return array('transfer'=>((isset($result['transfer']))?$result['transfer']:''),'invoice'=>((isset($invoice))?$invoice:''),'message'=>((isset($message))?$message:''));
				
			}elseif($action=='credit_close')
			{	
				$credits = Newebpay_mpg::join('merchants','newebpay_mpgs.MerchantID','=','merchants.MerchantID')
					->where('newebpay_mpgs.TradeStatus',1)
					->where('newebpay_mpgs.PaymentType','CREDIT')
					->where('newebpay_mpgs.Auth','!=','')
					->orderBy('newebpay_mpgs.created_at','desc')
					->paginate(30);
				$title = '信用卡請退款作業';
			}elseif($action=='merchant_search')
			{
				//return $request->all();
				$merchants = Merchant::where('MemberUnified',$request->text)
					->orWhere('ManagerID',$request->text)
					->orWhere('MemberName','like','%'.$request->text.'%')
					->orWhere('MemberPhone',$request->text)
					->orWhere('ManagerName',$request->text)
					->orWhere('ManagerMobile',$request->text)
					->orWhere('ManagerEmail','like','%'.$request->text.'%')
					->orWhere('MerchantName','like','%'.$request->text.'%')
					->paginate(30);
				return array('merchants'=>((isset($merchants))?$merchants:''));
			}
			
			if(isset($transfers))
			{
				foreach($transfers as $transfer)
				{
					$invo = Invoice::where('TransNum',$transfer->TradeNo)->select('InvoiceStatus')->first();
					if(isset($invo))
						$transfer->InvoiceStatus = $invo->InvoiceStatus;
					else
						$transfer->InvoiceStatus = 0;		
				}	
			}
			return array('transfers'=>((isset($transfers))?$transfers:''),'create_transfer'=>((isset($create_transfer))?$create_transfer:''),'title'=>((isset($title))?$title:''),'credits'=>((isset($credits))?$credits:''));
		}
	}
	
	public function get_accountings(Request $request) 
    {
    	if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return 'error';
		}
		$item = (($request->has('item'))?$request->item:'');
		$mode = (($request->has('mode'))?$request->mode:'');
		$action = (($request->has('action'))?$request->action:'');
		$Year = (($request->has('Year'))?$request->Year:date("Y")-1911);
		$id = (($request->has('id'))?$request->id:'');
		
		if($item=='systemAccount')
		{
			$newebPay = new NewebPay();
			
			$zipcodes = Utils::get_area_zipcode();
			$citys = array();
			$englishs = array();
			$nats = array();
			$types = Utils::get_BusinessType();
			$min_areas = Utils::get_area();
			$banks = Utils::get_bank_code();
			foreach($zipcodes as $key => $value)
			{
				$citys[] = $key;
				//$area = $value;
				$englishs[] = $value['English'];
				$nats[] = $value['Zip'];
			}
				
			if($request->action=='merchant_create') //申請商店
			{
				if(Cookie::has('tempCookie'))
				{
					$cookie = Cookie::get('tempCookie');
					$cookieData = json_decode($cookie);
					Cookie::queue(Cookie::forget('tempCookie'));
					if($cookieData->MerchantClass==2)
					{
						$ID = $cookieData->Manager_id;
						$Number = $cookieData->Manager_id_number;
						$IDCardDate = '';
						$IDCardPlace = '';
						$IDPic = '';
						$IDFrom = '';	
					}else
					{
						$ID = '';
						$Number = '';
						$IDCardDate = $cookieData->ID_year.$cookieData->ID_month.$cookieData->ID_day;
						$IDCardPlace = $cookieData->IDCardPlace;
						$IDPic = $cookieData->IDPic;
						$IDFrom = $cookieData->IDFrom;	
					}
					$merchant = array('usr_id'=>$cookieData->usr_id,'MerchantClass'=>$cookieData->MerchantClass,'MemberUnified'=>$cookieData->MemberUnified,'ManagerID'=>array('ID'=>$ID,'Number'=>$Number),'IDCardDate'=>$IDCardDate,'IDCardPlace'=>$IDCardPlace,'IDPic'=>$IDPic,'IDFrom'=>$IDFrom,'MemberName'=>$cookieData->MemberName,'MemberPhone'=>array('head'=>$cookieData->telHead,'value'=>$cookieData->telValue),'ManagerName'=>$cookieData->ManagerName,'ManagerNameE'=>$cookieData->ManagerNameE,'ManagerMobile'=>$cookieData->ManagerMobile,'ManagerEmail'=>$cookieData->ManagerEmail,'MerchantID'=>$cookieData->MerchantID,'MerchantName'=>$cookieData->MerchantName,'MerchantNameE'=>$cookieData->MerchantNameE,'MerchantWebURL'=>$cookieData->MerchantWebURL,'MerchantAddrCity'=>$cookieData->MerchantAddrCity,'MerchantAddrArea'=>$cookieData->MerchantAddrArea,'MerchantAddrCode'=>$cookieData->MerchantAddrCode,'MerchantAddr'=>$cookieData->MerchantAddr,'NationalE'=>$cookieData->NationalE,'CityE'=>$cookieData->CityE,'MerchantType'=>$cookieData->MerchantType,'BusinessType'=>$cookieData->BusinessType,'MerchantDesc'=>$cookieData->MerchantDesc,'BankCode'=>$cookieData->BankCode,'SubBankCode'=>$cookieData->SubBankCode,'BankAccount'=>$cookieData->BankAccount,'PaymentType'=>array('CREDIT'=>1,'WEBATM'=>1,'VACC'=>1,'CVS'=>1,'BARCODE'=>1),'CreditAutoType'=>$cookieData->CreditAutoType,'CreditLimit'=>$cookieData->CreditLimit);
				}else	
					$merchant = array('usr_id'=>'','MerchantClass'=>'1','MemberUnified'=>'','ManagerID'=>array('ID'=>'','Number'=>''),'IDCardDate'=>'','IDCardPlace'=>'','IDPic'=>'','IDFrom'=>'','MemberName'=>'','MemberPhone'=>array('head'=>'','value'=>''),'ManagerName'=>'','ManagerNameE'=>'','ManagerMobile'=>'','ManagerEmail'=>'','MerchantID'=>'','MerchantName'=>'','MerchantNameE'=>'','MerchantWebURL'=>'','MerchantAddrCity'=>'','MerchantAddrArea'=>'','MerchantAddrCode'=>'','MerchantAddr'=>'','NationalE'=>'Taiwan','CityE'=>'','MerchantType'=>'2','BusinessType'=>'','MerchantDesc'=>'','BankCode'=>'','SubBankCode'=>'','BankAccount'=>'','PaymentType'=>array('CREDIT'=>1,'WEBATM'=>1,'VACC'=>1,'CVS'=>1,'BARCODE'=>1),'CreditAutoType'=>1,'CreditLimit'=>200000);
				$title = '新增合作商店';
			}elseif($request->action=='merchant_manager')
			{
				if($request->has('text') && $request->get('text'))
				{	
					$text = trim($request->get('text'));
					$merchants = Merchant::join('users','merchants.u_id','=','users.id')
						->where(function($query) use($text){
							  $query->orWhere('merchants.ManagerID','like','%'.$text.'%');
							  $query->orWhere('merchants.MemberName',$text);
							  $query->orWhere('merchants.MemberPhone',$text);
							  $query->orWhere('merchants.ManagerName',$text);
							  $query->orWhere('merchants.ManagerMobile',$text);
							  $query->orWhere('users.last_name',$text);
							  $query->orWhere('users.first_name',$text);
						})
						->orderBy('merchants.created_at','desc')
						->paginate(30);
				}else	
					$merchants = Merchant::join('users','merchants.u_id','=','users.id')->orderBy('merchants.created_at','desc')->paginate(30);
				if(isset($merchants) && count($merchants))
				{
					foreach($merchants as $merchant)
					{
						if(isset($merchant->ManagerID) && $merchant->ManagerID)
						{
							$idArr = explode(';',$merchant->ManagerID);
							$merchant->ManagerID = array('ID'=>$idArr[0],'Number'=>$idArr[1]);
						}else
							$merchant->ManagerID = array('ID'=>'','Number'=>'');
							
						if(isset($merchant->MemberPhone) && $merchant->MemberPhone)
						{
							$phoneArr = explode('-',$merchant->MemberPhone);
							$merchant->MemberPhone = array('head'=>$phoneArr[0],'value'=>$phoneArr[1]);
						}else
							$merchant->MemberPhone = array('head'=>'','value'=>'');	
						
						if(isset($merchant->PaymentType) && $merchant->PaymentType)
						{
							$types = explode('|',$merchant->PaymentType);
							$array = array();
							foreach($types as $type)
							{
								$arr = explode(':',$type);
								$array[$arr[0]] = $arr[1];	
							}
							$merchant->PaymentType = $array;;
						}else
							$merchant->PaymentType = array('CREDIT'=>1,'WEBATM'=>1,'VACC'=>1,'CVS'=>1,'BARCODE'=>1);
					}
				}
				$title = '合作商店列表';
				//return array('merchants'=>((isset($merchants))?$merchants:''),'title'=>((isset($title))?$title:''));
			}elseif($request->action=='FeeInstruct' || $request->action=='Platformfee_search')
			{
				if($request->action=='Platformfee_search' && ($request->has('id') && $request->get('id'))) //單筆查詢
				{
					$result = $newebPay->get_Platformfee_search($request->id);
					return array('platformfee'=>json_decode($result));
				}else
				{
					if($request->has('text') && $request->get('text') || $request->has('start_date') && $request->get('start_date')){	
						$start_date = (($request->has('start_date') && $request->get('start_date'))?$request->get('start_date'):'');
						$end_date = (($request->has('end_date') && $request->get('end_date'))?$request->get('end_date'):'');
						$status = (($request->has('tradeStatus') && $request->get('tradeStatus')!='')?$request->get('tradeStatus'):'');
						$text = (($request->has('text') && $request->get('text'))?trim($request->get('text')):'');
						
						$search_date = array('start'=>$start_date,'end'=>$end_date);
						
						$mpgs = new Newebpay_mpg;
						$transfers = $mpgs->mpgs_search($search_date, $text, $status); //搜尋交易單資料
						
					}else if($request->has('id') && $request->get('id')) //合作商店扣撥款指示
					{	
						$transfer = Newebpay_mpg::join('merchants','newebpay_mpgs.MerchantID','=','merchants.merchantID')
							->where('newebpay_mpgs.MerchantOrderNo',$request->get('id'))
							->select('newebpay_mpgs.MerchantOrderNo','newebpay_mpgs.Amt','newebpay_mpgs.MerchantID','merchants.MemberName','merchants.MerchantName','newebpay_mpgs.FundTime')
							->first();
						$settingFee = Setting::select('service_fee')->first();
						$serviceFee = ((isset($serviceFee))?$serviceFee->service_fee:20);
						if($request->feeMode==2)
						{
							$fee_instruct  = ChargeInstruct::join('export_instructs','charge_instructs.MerTrade','=','export_instructs.MerchantOrderNo')->where('MerTrade',$request->get('id'))->get();
							$transfer->fee_total = $transfer->Amt;
							$transfer->fee = $transfer->Amt;
						}else
						{
							$thisFee = round($transfer->Amt*$serviceFee*0.01);
							
							$fee_charges  = ChargeInstruct::where('MerTrade',$request->get('id'))->get();
							$fee = 0;
							if(isset($fee_charges))
							{
								foreach($fee_charges as $fee_charge)
								{
									if($fee_charge->BalanceType==0)
										$fee += $fee_charge->Amount;
									else
										$fee -= $fee_charge->Amount;		
								}
							}
							$fee_exports  = ExportInstruct::where('MerchantOrderNo',$request->get('id'))->get();
							
							$transfer->complete_fee = $fee;
							$transfer->fee_total = round($transfer->Amt*$serviceFee*0.01);
							$transfer->fee = round($transfer->Amt*$serviceFee*0.01)-$fee;	
						}
						return array('transfer'=>((isset($transfer))?$transfer:''),'fee_charges'=>((isset($fee_charges))?$fee_charges:''),'fee_exports'=>((isset($fee_exports))?$fee_exports:''),'serviceFee'=>$serviceFee);
					}else
						$transfers = Newebpay_mpg::join('merchants','newebpay_mpgs.MerchantID','=','merchants.MerchantID')
						->join('users','newebpay_mpgs.u_id','=','users.id')
						->where('newebpay_mpgs.TradeStatus',1)
						->select('newebpay_mpgs.MerchantOrderNo','newebpay_mpgs.PaymentType','newebpay_mpgs.Amt','newebpay_mpgs.Email','newebpay_mpgs.PayTime','newebpay_mpgs.MerchantID','merchants.MemberName','merchants.MerchantName','newebpay_mpgs.FundTime','users.usr_id','users.last_name','users.first_name')
						->orderBy('newebpay_mpgs.created_at','desc')
						->paginate(30);
					$title = (($action=='Platformfee_search')?'平台費用扣款單筆查詢':'商店訂單扣撥款作業');
						
				}
				//return array('transfers'=>((isset($transfers))?$transfers:''),'title'=>((isset($title))?$title:''));
			}elseif($request->action=='Platformfee_perday')
			{
				if($request->has('date'))
				{
					$result = $newebPay->get_Platformfee_perday($request->get('date'));
					$result_data = json_decode($result);
					if($result_data->Status=='SUCCESS')
					{
						
					}else
						$message = $result_data->Message;
				}
			}
			
			if($action == 'FeeInstruct')
			{
				foreach($transfers as $transfer)
				{
					//$ChargeInstruct = ChargeInstruct::where('MerTrade',$transfer->MerchantOrderNo)->first();
					$charge_instructs  = ChargeInstruct::where('MerTrade',$transfer->MerchantOrderNo)->select('BalanceType','Amount')->get();
					$fee = 0;
					foreach($charge_instructs as $charge_instruct)
					{
						if($charge_instruct->BalanceType==0)
							$fee += $charge_instruct->Amount;
						else
							$fee -= $charge_instruct->Amount;		
					}
					
					$export_fees  = ExportInstruct::where('MerchantOrderNo',$transfer->MerchantOrderNo)->sum('Amount');
							
					$fee_instruct = array('charge'=>$fee,'export'=>((isset($export_fees))?$export_fees:0));
					
					$transfer->fee_instruct = $fee_instruct;		
				}	
			}
			
			return array('transfers'=>((isset($transfers))?$transfers:''),'merchants'=>((isset($merchants))?$merchants:''),'merchant'=>((isset($merchant))?$merchant:''),'title'=>((isset($title))?$title:''),'citys'=>$citys,'nats'=>$nats,'englishs'=>$englishs,'types'=>((isset($types))?$types:''),'banks'=>((isset($banks))?$banks:''),'min_areas'=>((isset($min_areas))?$min_areas:''),'MaxLimit'=>200000,'amounts'=>((isset($amounts))?$amounts:''),'message'=>((isset($message))?$message:''));
		}elseif($item=='invoice_tracks')
		{
			$ezpay = new EzPay();
			if($request->action=='create')
			{
				$single_invoice_track = array('ManagementNo'=>'','Year'=>'','Term'=>'','AphabeticLetter'=>'','StartNumber'=>'','EndNumber'=>'','Type'=>'07');
				$title = '新增發票字軌';	
			}else
			{
				$invoice_tracks = Invoice_track::where('Year',$Year)->orderBy('Year','desc')->orderBy('Term','desc')->orderBy('StartNumber','desc')->get();
				foreach($invoice_tracks as $invoice_track)
				{
					if($invoice_track->Term==1)
						$tremText = '一、二月';
					elseif($invoice_track->Term==2)
						$tremText = '三、四月';
					elseif($invoice_track->Term==3)
						$tremText = '五、六月';
					elseif($invoice_track->Term==4)
						$tremText = '七、八月';
					elseif($invoice_track->Term==5)
						$tremText = '九、十月';
					elseif($invoice_track->Term==6)
						$tremText = '十一、十二月';				
					
					$invoice_track->Term = $tremText;
					$invoice_track->Type = (($invoice_track->Type==07)?'一般稅率':'特種稅率');
					$invoice_track->status = (($invoice_track->Flag==0)?'暫停':(($invoice_track->Flag==1)?'<b class="text-success">啟用</b>':'<b class="text-danger">停用</b>'));
				}
				
				if($request->action=='manage')
				{
					$title = '字軌資料管理';	
				}else
				{
					$title = '字軌資料查詢';	
				}
			}
			return array('invoice_tracks'=>((isset($invoice_tracks))?$invoice_tracks:''),'single_invoice_track'=>((isset($single_invoice_track))?$single_invoice_track:''),'title'=>((isset($title))?$title:''));
		}elseif($item=='invoices')
		{
			$ezpay = new EzPay();
			$mode = (($request->has('mode'))?$request->get('mode'):'');
			if(isset($mode) && $mode=='search') //搜尋
			{
				$start_date = (($request->has('start_date') && $request->get('start_date'))?$request->get('start_date'):'');
				$end_date = (($request->has('end_date') && $request->get('end_date')!='')?$request->get('end_date'):'');
				$status = (($request->has('tradeStatus') && $request->get('tradeStatus')!='')?$request->get('tradeStatus'):'');
				$text = (($request->has('text') && $request->get('text'))?trim($request->get('text')):'');
					
				if($action=='transfer')
				{
					$search_date = array('start'=>$start_date,'end'=>$end_date);
					$mpgs = new Newebpay_mpg;
					$transfers = $mpgs->mpgs_search($search_date, $text, $status); //搜尋交易單資料
					
					$title = '訂單搜尋管理';	
				}else
				{
					$inv = new Invoice;
					
					$search_date = array('start'=>$start_date,'end'=>$end_date);
					$invoices = $inv->invoice_search($search_date, $text, $status); //搜尋發票資料
					$title = '發票搜尋管理';
				}
				return array('invoices'=>((isset($invoices))?$invoices:''),'transfers'=>((isset($transfers))?$transfers:''),'title'=>((isset($title))?$title:''));
				
			}
			if($action=='create')
			{
				if($id)
					$transfer = Newebpay_mpg::join('users','newebpay_mpgs.u_id','=','users.id')->where('newebpay_mpgs.TradeNo',$id)->select('newebpay_mpgs.Email','newebpay_mpgs.Amt','users.usr_id','users.first_name','users.last_name')->first();
				if(isset($transfer))
				{
					$ItemPrice = round($transfer->Amt*0.2);
					$ItemAmt = round($transfer->Amt*0.2);
					$Amt = round($ItemAmt/1.05);
					$TaxAmt = $ItemAmt-$Amt;
					$TotalAmt = $Amt+$TaxAmt;
				}else
				{
					$ItemPrice = 0;
					$ItemAmt = 0;
					$Amt = 0;
					$TaxAmt = 0;
					$TotalAmt = 0;	
				}
				$arr = array();
				$arr[] = array('ItemName'=>'手續費','ItemCount'=>1,'ItemUnit'=>'筆','ItemPrice'=>$ItemPrice,'ItemAmt'=>$ItemAmt);
				$create_invoice = array('usr_id'=>((isset($transfer))?$transfer->usr_id:''),'TransNum'=>$id,'MerchantOrderNo'=>'','Status'=>'1','CarrierType'=>'2','Category'=>'B2C','Category'=>'B2C','PrintFlag'=>'N','BuyerName'=>'','BuyerEmail'=>((isset($transfer))?$transfer->Email:''),'BuyerUBN'=>'','BuyerAddress'=>'','Amt'=>$Amt,'TaxAmt'=>$TaxAmt,'TotalAmt'=>$TotalAmt,'BuyerName'=>((isset($transfer))?$transfer->last_name.$transfer->first_name:''),'details'=>$arr);
				$title = '手動開立發票新增';	
			}elseif($action=='transfer')
			{
				$mpgs = new Newebpay_mpg;
				$start_date = (($request->has('start_date') && $request->get('start_date'))?$request->get('start_date'):'');
				$end_date = (($request->has('end_date') && $request->get('end_date')!='')?$request->get('end_date'):'');
				$status = (($request->has('tradeStatus') && $request->get('tradeStatus')!='')?$request->get('tradeStatus'):'');
				$text = (($request->has('text') && $request->get('text'))?trim($request->get('text')):'');
					
				$search_date = array('start'=>$start_date,'end'=>$end_date);
				
				$transfers = $mpgs->mpgs_search($search_date, $text, $status); //搜尋交易單資料
				
				$setting = Setting::select('service_fee')->first();
				$title = '訂單開立發票作業';
			}elseif($action=='manage' || !$action)
			{	
				$inv = new Invoice;
				$start_date = (($request->has('start_date') && $request->get('start_date'))?$request->get('start_date'):'');
				$end_date = (($request->has('end_date') && $request->get('end_date')!='')?$request->get('end_date'):'');
				$status = (($request->has('tradeStatus') && $request->get('tradeStatus')!='')?$request->get('tradeStatus'):'');
				$text = (($request->has('text') && $request->get('text'))?trim($request->get('text')):'');
					
				$search_date = array('start'=>$start_date,'end'=>$end_date);
				
				$invoices = $inv->invoice_search($search_date, $text, $status); //搜尋發票資料
				
				$title = '發票資料管理';
			}elseif($action=='detail' || $action=='allowance')
			{
				$invoice = Invoice::where('InvoiceNumber',$request->id)->first();
				
				$merchant_id = env('ezPay_invoice_merchant_id');
				
				if(!$invoice)
					return 'error';
				
				$post_data_array = array(//post_data 欄位資料            
					 "RespondType" => "JSON",             
					 "Version" => "1.1",             
					 "TimeStamp" => time(), //請以 time() 格式            
					 "SearchType" => 0,             
					 "InvoiceNumber" => $request->id,
					 "TotalAmt" => $invoice->TotalAmt,
					 "RandomNum" => $invoice->RandomNum,
					 "DisplayFlag" => 0 
				); 
				
				$url = 'https://cinv.pay2go.com/API/invoice_search';
				$result = $ezpay->invoice_post($url,$post_data_array);
				$check_code_array = array(     
					"MerchantID" => $merchant_id,//商店代號     
					"MerchantOrderNo" => $invoice->MerchantOrderNo,  //商店自訂單號(訂單編號)     
					"InvoiceTransNo" => $invoice->InvoiceTransNo,  //智付寶電子發票開立序號     
					"TotalAmt" => $invoice->TotalAmt,  //發票金額    
					"RandomNum" => $invoice->RandomNum  //發票防偽隨機碼 
				);
				$check_code = $ezpay->check_invoice_code($check_code_array);
				$data = json_decode($result['web_info']);
				$data1 = json_decode($data->Result);
				if($data1->CheckCode==$check_code)
				{
					$invoice->UploadStatus = $data1->UploadStatus;
					$invoice->ItemDetails = json_decode($data1->ItemDetail);
					if($data1->InvoiceStatus!=$invoice->InvoiceStatus)
					{
						Invoice::where('InvoiceNumber',$request->id)->update(array('InvoiceStatus'=>$data1->InvoiceStatus));
						$invoice->InvoiceStatus = $data1->InvoiceStatus;
					}
				}else
					$message = '驗證碼不符!!';
				if($data->Status!='SUCCESS')
						$message = $data->Message;
						
				$allowances = Invoice_allowance::join('invoices','invoice_allowances.InvoiceNumber','=','invoices.InvoiceNumber')->where('invoice_allowances.InvoiceNumber',$invoice->InvoiceNumber)->select('invoices.RemainAmt','invoice_allowances.Status')->orderBy('invoice_allowances.created_at','desc')->get();
				if(isset($allowances) && count($allowances))
				{
					$can_allowance = false;
					foreach($allowances as $allowance)
					{
						if(!$allowance->Status)
							$can_allowance = true;	
					}
					
					$invoice->need_confirm = ((isset($can_allowance) && $can_allowance)?2:1);
					$invoice->RemainAmt = (($allowances[0]->RemainAmt)?$allowances[0]->RemainAmt:$invoice->TotalAmt);
					$invoice->allowance = 1;
				}else
				{
					$invoice->need_confirm = 0;
					$invoice->RemainAmt = $invoice->TotalAmt;
					$invoice->allowance = 0;
				}			
				return array('invoice'=>((isset($invoice))?$invoice:''),'message'=>((isset($message))?$message:''));
				
			}elseif($action=='confirm')
			{
				$allowances = Invoice_allowance::where('InvoiceNumber',$request->id)->orderBy('Status','asc')->orderBy('created_at','asc')->get();
				return array('allowances'=>((isset($allowances))?$allowances:''));
			}
			
			//return $request->all();
			
			return array('invoices'=>((isset($invoices))?$invoices:''),'create_invoice'=>((isset($create_invoice))?$create_invoice:''),'transfers'=>((isset($transfers))?$transfers:''),'service_fee'=>((isset($setting))?$setting->service_fee:''),'title'=>((isset($title))?$title:''));
		}
	}
	
	public function email_get_account(Request $request) 
    {
    	if(!Admin_member::isManager())
		{
			Log::warning("登入驗證失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			return 'error';
		}
		
		$buyer = User::where('email',$request->id)->select('id','usr_id','email','first_name','last_name','phone_number','usr_id')->first();
		if(isset($buyer))
		{
			$merchant = Merchant::where('u_id',$buyer->id)->first();
			if(isset($merchant))
			{
				if(isset($merchant->ManagerID) && $merchant->ManagerID)
				{
					$idArr = explode(';',$merchant->ManagerID);
					$merchant->ManagerID = array('ID'=>$idArr[0],'Number'=>$idArr[1]);
				}else
					$merchant->ManagerID = array('ID'=>'','Number'=>'');
					
				if(isset($merchant->MemberPhone) && $merchant->MemberPhone)
				{
					$phoneArr = explode('-',$merchant->MemberPhone);
					$merchant->MemberPhone = array('head'=>$phoneArr[0],'value'=>$phoneArr[1]);
				}else
					$merchant->MemberPhone = array('head'=>'','value'=>'');	
				
				if(isset($merchant->PaymentType) && $merchant->PaymentType)
				{
					$types = explode('|',$merchant->PaymentType);
					$array = array();
					foreach($types as $type)
					{
						$arr = explode(':',$type);
						$array[$arr[0]] = $arr[1];	
					}
					$merchant->PaymentType = $array;;
				}else
					$merchant->PaymentType = array('CREDIT'=>1,'WEBATM'=>1,'VACC'=>1,'CVS'=>1,'BARCODE'=>1);	
				
				$merchant->usr_id = $buyer->usr_id;
				$is_edit = true;
			}
		}
		return array('buyer'=>((isset($buyer))?$buyer:''),'merchant'=>((isset($merchant))?$merchant:''),'is_edit'=>((isset($is_edit))?$is_edit:''));
	}

}