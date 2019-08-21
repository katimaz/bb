<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Notifications\Notify;
use App\Models\Newebpay_mpg;
use App\Models\Invoice;
use App\Models\Ezpay;
use App\User;

class NewebPay extends Model
{
   	public $partner_id;
	public $partner_key;
	public $partner_iv;
	
	public function __construct()
    {
        $this->partner_id = env('newebPay_partner_id');
		$this->partner_key = env('newebPay_partner_HashKey');
		$this->partner_iv = env('newebPay_partner_HashIV');
	}
	
	public function curl_work($url = '', $parameter = '') 
	{     
		$curl_options = array( 
			CURLOPT_URL => $url,         
			CURLOPT_HEADER => false,         
			CURLOPT_RETURNTRANSFER => true,         
			CURLOPT_USERAGENT => 'Google Bot',         
			CURLOPT_FOLLOWLOCATION => true,         
			CURLOPT_SSL_VERIFYPEER => FALSE,         
			CURLOPT_SSL_VERIFYHOST => FALSE,         
			CURLOPT_POST => '1',         
			CURLOPT_POSTFIELDS => $parameter     
		);     
		$ch = curl_init(); 
    	curl_setopt_array($ch, $curl_options);     
		$result = curl_exec($ch);
		$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);     
		$curl_error = curl_errno($ch);     
		curl_close($ch);     
		$return_info = array(         
			'url' => $url,         
			'sent_parameter' => $parameter,         
			'http_status' => $retcode,         
			'curl_error_no' => $curl_error,         
			'web_info' => $result     
		);     
		return $return_info; 
	} 
	
	public function create_mpg_aes_encrypt($post_data_array=array(),$key='',$iv='') {         
		$data_str = '';
		if (!empty($post_data_array)) { 
            //將參數經過 URL ENCODED QUERY STRING 
            $data_str = http_build_query($post_data_array);         
		}
		$TradeInfo = trim(bin2hex(openssl_encrypt($this->addpadding($data_str), 'AES-256-CBC', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv)));
		$TradeSha = strtoupper(hash("sha256", 'HashKey='.$key.'&'.$TradeInfo.'&HashIV='.$iv));
		 
		return array('TradeInfo'=>$TradeInfo,'TradeSha'=>$TradeSha);
	} 
	
	public function addpadding($string = '', $blocksize = 32) {         
		$len = strlen($string);         
		$pad = $blocksize - ($len % $blocksize);         
		$string .= str_repeat(chr($pad), $pad);         
		return $string;      
	} 
	
	public function create_aes_decrypt($parameter="",$key="",$iv="") {         
		 return json_decode($this->strippadding(openssl_decrypt(hex2bin($parameter),'AES-256-CBC', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv))); 
		     
	} 
	
	public function strippadding($string) {         
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
	
	public function query_tradeInfo($transfer) 
	{
		//交易資料經 AES 加密後取得 TradeInfo
		$data_array = array(//post_data 欄位資料            
			 'MerchantOrderNo' => $transfer->MerchantOrderNo,
			 'Amt' =>  $transfer->Amt,
		);  
		$TradeSha = $this->get_tradeSha($data_array,$transfer->MerchantID,$transfer->MerchantHashKey,$transfer->MerchantIvKey);
		
		$post_data_array = array(//post_data 欄位資料            
			 'MerchantID' =>  $transfer->MerchantID, 
			 "RespondType" => "JSON",             
			 "Version" => "1.1",             
			 "TimeStamp" => time(), //請以 time() 格式            
			 'MerchantOrderNo' => $transfer->MerchantOrderNo,
			 'Amt' =>  $transfer->Amt,
			 "CheckValue" => $TradeSha,
		); 
				
		$url = 'https://ccore.newebpay.com/API/QueryTradeInfo';
		$post_data_str = http_build_query($post_data_array); 
		$result = $this->curl_work($url, $post_data_str); //背景送出
		$data = json_decode($result['web_info']);
		//return $result['web_info'];
		if(isset($data->Result->TradeNo))
		{
			$data_array = array(//post_data 欄位資料            
				 'MerchantOrderNo' => $transfer->MerchantOrderNo,
				 'Amt' =>  $transfer->Amt,
				 'TradeNo' => $data->Result->TradeNo,
			);
			
			$check_code = $this->get_check_code($data_array,$transfer->MerchantID,$transfer->MerchantHashKey,$transfer->MerchantIvKey);
			if($data->Result->CheckCode==$check_code)
			{
				$input['TradeStatus'] = $data->Result->TradeStatus;
				$input['TradeNo'] = $data->Result->TradeNo;
				$input['Amt'] = $data->Result->Amt;
				if($data->Result->PayTime!='0000-00-00 00:00:00')
					$input['PayTime'] = $data->Result->PayTime;
				$input['PaymentType'] = $data->Result->PaymentType;
				if($data->Result->FundTime!='0000-00-00')
					$input['FundTime'] = $data->Result->FundTime;
				
				if($data->Result->PaymentType=='CREDIT')
				{
					$input['RespondCode'] = $data->Result->RespondCode;
					$input['Auth'] = $data->Result->Auth;
					$input['ECI'] = $data->Result->ECI;
					$input['Inst'] = $data->Result->Inst;
					$input['CloseAmt'] = $data->Result->CloseAmt;
					$input['CloseStatus'] = $data->Result->CloseStatus;
					$input['BackBalance'] = $data->Result->BackBalance;
					$input['BackStatus'] = $data->Result->BackStatus;
					$input['RespondMsg'] = $data->Result->RespondMsg;
					$input['PaymentMethod'] = $data->Result->PaymentMethod;
				}
				Newebpay_mpg::where('MerchantOrderNo',$transfer->MerchantOrderNo)->update($input);
				$transfer->TradeStatus = $data->Result->TradeStatus;
				$transfer->PaymentType = $data->Result->PaymentType;
				$transfer->TradeNo = $data->Result->TradeNo;
				$transfer->FundTime = $data->Result->FundTime;
				
				if($data->Result->PaymentType=='CREDIT')
				{
					$transfer->RespondCode = $data->Result->RespondCode;
					$transfer->CloseAmt	= $data->Result->CloseAmt;
					$transfer->CloseStatus = $data->Result->CloseStatus;
					$transfer->BackStatus = $data->Result->BackStatus;
					$transfer->BackBalance = $data->Result->BackBalance;
					$transfer->RespondMsg = $data->Result->RespondMsg;
					$transfer->PaymentMethod = $data->Result->PaymentMethod;
					$transfer->PaymentType = $data->Result->PaymentType;
					
				}elseif($data->Result->PaymentType=='CVS'||$data->Result->PaymentType=='BARCODE'||$data->Result->PaymentType=='VACC')
				{
					$transfer->PayInfo	= $data->Result->PayInfo;
					if($data->Result->ExpireDate!=$transfer->ExpireDate || $data->Result->TradeNo!=$transfer->TradeNo)
					{
						
						$input['ExpireDate'] = $data->Result->ExpireDate;
						$input['TradeNo'] = $data->Result->TradeNo;
						$input['PaymentType'] = $data->Result->PaymentType;
						Newebpay_mpg::where('MerchantOrderNo',$transfer->MerchantOrderNo)->update($input);
						
						$transfer->ExpireDate	= $data->Result->ExpireDate;
						$transfer->TradeNo	= $data->Result->TradeNo;
						$transfer->PayTime	= $data->Result->PayTime;
						$transfer->PaymentType	= $data->Result->PaymentType;
					}
				}
			}		
		}
		return array('transfer'=>((isset($transfer))?$transfer:''),'Status'=>$data->Status,'Message'=>$data->Message);
	} 
	
	public function credit_card_cancel($post_data_array=array(),$transfer=array(),$invoice='') 
	{
		if(!count($post_data_array))
			return array('Status'=>'ERROR-0','Message'=>'無參數值!');
		if(!isset($transfer) || !$transfer->MerchantHashKey)
			return array('Status'=>'ERROR-1','Message'=>'無合作商店參數!');
		
		$url = 'https://ccore.newebpay.com/API/CreditCard/Cancel';
		$Tradedata = $this->create_mpg_aes_encrypt($post_data_array,$transfer->MerchantHashKey,$transfer->MerchantIvKey);
		$data_str = $Tradedata['TradeInfo'];
		$post_data = array(//post_data 欄位資料            
			 "MerchantID_" => $transfer->MerchantID,             
			 "PostData_" => $data_str
		);
		$post_data_str = http_build_query($post_data); 
		
		$result = $this->curl_work($url, $post_data_str); //背景送出
		$data = json_decode($result['web_info']);
		if($data->Status=='SUCCESS')
		{
			$data_array = array(//post_data 欄位資料            
				 'MerchantOrderNo' => $transfer->MerchantOrderNo,
				 'Amt' =>  $transfer->Amt,
				 'TradeNo' => $data->Result->TradeNo,
			);
			
			$check_code = $this->get_check_code($data_array,$transfer->MerchantID,$transfer->MerchantHashKey,$transfer->MerchantIvKey);
			if($data->Result->CheckCode==$check_code)
			{	
				$input['TradeStatus'] = 3;
				Newebpay_mpg::where('MerchantOrderNo',$transfer->MerchantOrderNo)->update($input);
				$transfer->TradeStatus = 3;
				
				//----------------------------------------------------發票自動作廢
				if(isset($invoice))
				{
					$post_data_array = array(//post_data 欄位資料            
						 "RespondType" => "JSON",             
						 "Version" => "1.0",             
						 "TimeStamp" => time(), //請以 time() 格式            
						 "InvoiceNumber" => $invoice->InvoiceNumber,             
						 "InvalidReason" => '取消信用卡', 
					); 
					$ezpay = new Ezpay();
					$url = 'https://cinv.pay2go.com/API/invoice_invalid';
					$result = $ezpay->set_invalid($url,$post_data_array);
				}
			}
		}
		return array('Status'=>$data->Status,'Message'=>$data->Message);		
		
	}
	
	public function get_tradeSha($data_array=array(),$merchant_id='',$key='',$iv='')
	{
		$TradeInfo = 'IV='.$iv.'&Amt='.$data_array['Amt'].'&MerchantID='.$merchant_id.'&MerchantOrderNo='.$data_array['MerchantOrderNo'].'&Key='.$key;
		return strtoupper(hash("sha256", $TradeInfo)); 
	}
	
	public function get_check_code($data_array=array(),$merchant_id='',$key='',$iv='')
	{
		$check_code_str = 'HashIV='.$iv.'&Amt='.$data_array['Amt'].'&MerchantID='.$merchant_id.'&MerchantOrderNo='.$data_array['MerchantOrderNo'].'&TradeNo='.$data_array['TradeNo'].'&HashKey='.$key;
		return strtoupper(hash("sha256", $check_code_str));
	}
	
	public function return_get_tradeSha($parameter="",$key="",$iv="")
	{
		//ksort($parameter);
		return  strtoupper(hash("sha256", 'HashKey='.$key.'&'.$parameter.'&HashIV='.$iv));
	}
	
	public function send_credit_close($request=array()) //信用卡請退款作業
	{
		
		$merchant = Merchant::where('MerchantID',$request->MerchantID)->select('MerchantHashKey','MerchantIvKey')->first();
		if(!$merchant)
			return array('Status'=>'ERROR-0','Message'=>'無合作商店資料!');
			
		$TimeStamp = time();
		$trade_info_arr = array(  
			'RespondType' => 'JSON',
			'MerchantOrderNo' => $request->MerchantOrderNo,  
			'TradeNo' => $request->TradeNo, 
			'TimeStamp' => $TimeStamp,  
			'Version' => 1.1, 
			'Amt' => $request->Amt,  
			'IndexType' => $request->IndexType,
			'CloseType' => $request->CloseType,
			'Cancel' =>  ((isset($request->Cancel))?$request->Cancel:0)  
		); 
		$url = 'https://ccore.newebpay.com/API/CreditCard/Close';
		$TradeData = $this->create_mpg_aes_encrypt($trade_info_arr,$merchant->MerchantHashKey,$merchant->MerchantIvKey);
		$send_data = array(  
			'MerchantID_' => $request->MerchantID,  
			'PostData_' => $TradeData['TradeInfo'] 
		);
		$result = $this->curl_work($url, $send_data);
		$data = json_decode($result['web_info']);
		if($data->Status=='SUCCESS')
		{
			$transfer = Newebpay_mpg::join('merchants','newebpay_mpgs.MerchantID','=','merchants.MerchantID')->where('newebpay_mpgs.TradeNo',$data->Result->TradeNo)->first();
			$this->query_tradeInfo($transfer);
		}
		return array('Status'=>$data->Status,'Message'=>$data->Message);
	}
	
	public function newebPay_return($request=array())
	{
		$merchant = Merchant::where('MerchantID',$request->MerchantID)->select('MerchantHashKey','MerchantIvKey')->first();
		if(!$merchant)
			return 'error';
		$decode = $this->create_aes_decrypt($request->TradeInfo,$merchant->MerchantHashKey,$merchant->MerchantIvKey);
		if(!$decode)
			return 'error';
		
		$TradeSha = $this->return_get_tradeSha($request->TradeInfo,$merchant->MerchantHashKey,$merchant->MerchantIvKey);
		if($request->TradeSha==$TradeSha)
		{
			$transfer = Newebpay_mpg::where('MerchantOrderNo',$decode->Result->MerchantOrderNo)->select('MerchantOrderNo','Amt')->first();
			if(!$transfer)
				return 'error';
			$data_array = array(//post_data 欄位資料            
				 'MerchantOrderNo' => $transfer->MerchantOrderNo,
				 'Amt' =>  $transfer->Amt,
			);  
			$TradeSha = $this->get_tradeSha($data_array,$request->MerchantID,$merchant->MerchantHashKey,$merchant->MerchantIvKey);
			
			$post_data_array = array(//post_data 欄位資料            
				 'MerchantID' =>  $request->MerchantID, 
				 "RespondType" => "JSON",             
				 "Version" => "1.1",             
				 "TimeStamp" => time(), //請以 time() 格式            
				 'MerchantOrderNo' => $transfer->MerchantOrderNo,
				 'Amt' =>  $transfer->Amt,
				 "CheckValue" => $TradeSha,
			); 
					
			$url = 'https://ccore.newebpay.com/API/QueryTradeInfo';
			$post_data_str = http_build_query($post_data_array); 
			$result = $this->curl_work($url, $post_data_str); //背景送出
			$data = json_decode($result['web_info']);
			
			if(!$data)
				return 'error';
				
			$input['TradeStatus'] = $data->Result->TradeStatus;
			$input['TradeNo'] = $decode->Result->TradeNo;
			$input['PaymentType'] = $decode->Result->PaymentType;
			if(isset($decode->Result->PayTime))
				$input['PayTime'] = $decode->Result->PayTime;
			$input['IP'] = $decode->Result->IP;
			$input['EscrowBank'] = $decode->Result->EscrowBank;
			if($decode->Result->PaymentType=='CREDIT')
			{
				$input['RespondCode'] = $decode->Result->RespondCode;
				$input['Auth'] = $decode->Result->Auth;
				$input['Card6No'] = $decode->Result->Card6No;
				$input['Card4No'] = $decode->Result->Card4No;
				if(isset($decode->Result->TokenUseStatus))
					$input['TokenUseStatus'] = $decode->Result->TokenUseStatus;
				$input['Inst'] = $decode->Result->Inst;
				$input['InstFirst'] = $decode->Result->InstFirst;
				$input['InstEach'] = $decode->Result->InstEach;
				$input['ECI'] = $decode->Result->ECI;
				if(isset($decode->Result->PaymentMethod))
					$input['PaymentMethod'] = $decode->Result->PaymentMethod;
			}elseif($decode->Result->PaymentType=='WEBATM' || $decode->Result->PaymentType=='VACC')
			{
				$input['PayBankCode'] = $decode->Result->PayBankCode;
				$input['PayerAccount5Code'] = $decode->Result->PayerAccount5Code;
			}elseif($decode->Result->PaymentType=='CVS')
			{
				$input['CodeNo'] = $decode->Result->CodeNo;
				$input['StoreType'] = (int)$decode->Result->StoreType;
				$input['StoreID'] = $decode->Result->StoreID;
			}elseif($decode->Result->PaymentType=='BARCODE') //超商條碼
			{
				$input['Barcode_1'] = $decode->Result->Barcode_1;
				$input['Barcode_2'] = $decode->Result->Barcode_2;
				$input['Barcode_3'] = $decode->Result->Barcode_3;
				$input['PayStore'] = ((isset($decode->Result->PayStore))?$decode->Result->PayStore:'');
			}elseif($decode->Result->PaymentType=='CVSCOM') //超商物流
			{
				$input['StoreCode'] = $decode->Result->StoreCode;
				$input['StoreName'] = $decode->Result->StoreName;
				$input['StoreType'] = $decode->Result->StoreType;
				$input['StoreAddr'] = $decode->Result->StoreAddr;
				$input['TradeType'] = $decode->Result->TradeType;
				$input['CVSCOMName'] = $decode->Result->CVSCOMName;
				$input['CVSCOMPhone'] = $decode->Result->CVSCOMPhone;
			}
			Newebpay_mpg::where('MerchantOrderNo',$decode->Result->MerchantOrderNo)->update($input);
			
			if($data->Result->TradeStatus==1)
			{
				Notify::via('notify','訂單編號 : '.$decode->Result->MerchantOrderNo.' 支付方式 : '.$decode->Result->PaymentType.' 前往查看 : ! <'.url('/').'/admin/transfer_records?item=newebPay&action=manage'.'|Click here> for details!');
				/*$transfer = Newebpay_mpg::join('users','newebpay_mpgs.u_id','=','users.id')
					->where('newebpay_mpgs.MerchantOrderNo',$decode->Result->MerchantOrderNo)
					->select('newebpay_mpgs.u_id','newebpay_mpgs.ItemDesc','newebpay_mpgs.Email','users.first_name','users.last_name','users.usr_id')->first();
				
				$invoices = Invoice::where('TransNum',$decode->Result->TradeNo)->count();
				if(isset($transfer) && !$invoices)
				{
					$ezpay = new Ezpay;
					$ezpay->set_invoice($transfer,$decode); //自動開立發票	
				}*/
			}
		}else
		{
			Log::alert("藍新驗證碼失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
		}
		return $decode;	
	}
	
	public function newebPay_customer($request=array()){
		
		$merchant = Merchant::where('MerchantID',$request->MerchantID)->select('MerchantHashKey','MerchantIvKey')->first();
		if(!$merchant)
			return 'error';
		$decode = $this->create_aes_decrypt($request->TradeInfo,$merchant->MerchantHashKey,$merchant->MerchantIvKey);
		if(!$decode)
			return 'error';
		
		$TradeSha = $this->return_get_tradeSha($request->TradeInfo,$merchant->MerchantHashKey,$merchant->MerchantIvKey);
		if($request->TradeSha==$TradeSha)
		{
			
			$input['TradeNo'] = $decode->Result->TradeNo;
			$input['PaymentType'] = $decode->Result->PaymentType;
			$input['ExpireDate'] = $decode->Result->ExpireDate;
			if($decode->Result->PaymentType=='VACC')
			{
				$input['BankCode'] = $decode->Result->BankCode;
				$input['CodeNo'] = $decode->Result->CodeNo;
			}elseif($decode->Result->PaymentType=='CVS')
			{
				$input['CodeNo'] = $decode->Result->CodeNo;
			}elseif($decode->Result->PaymentType=='BARCODE')
			{
				$input['Barcode_1'] = $decode->Result->Barcode_1;
				$input['Barcode_2'] = $decode->Result->Barcode_2;
				$input['Barcode_3'] = $decode->Result->Barcode_3;
			}elseif($decode->Result->PaymentType=='CVSCOM') //超商物流
			{
				$input['StoreCode'] = $decode->Result->StoreCode;
				$input['StoreName'] = $decode->Result->StoreName;
				$input['StoreType'] = $decode->Result->StoreType;
				$input['StoreAddr'] = $decode->Result->StoreAddr;
				$input['TradeType'] = $decode->Result->TradeType;
				$input['CVSCOMName'] = $decode->Result->CVSCOMName;
				$input['CVSCOMPhone'] = $decode->Result->CVSCOMPhone;
			}
			Newebpay_mpg::where('MerchantOrderNo',$decode->Result->MerchantOrderNo)->update($input);
			Notify::via('notify','訂單付款返回 : '.$decode->Result->MerchantOrderNo.' 支付方式 : '.$decode->Result->PaymentType.' 前往查看 : ! <'.url('/').'/admin/transfer_records?item=newebPay&action=manage'.'|Click here> for details!');
		}
		return $decode;
	}
	
	public function create_mpg_form($data_arr=array()){  //交易後串藍新金流
		if(!count($data_arr))
			return array('Status'=>'ERROR-0','Message'=>'無參數值!');
		if(!isset($data_arr['usr_id']) || !isset($data_arr['MerchantID']) || !isset($data_arr['MerchantOrderNo']) || !isset($data_arr['Amt']) || !isset($data_arr['ItemDesc']) || !isset($data_arr['Email']))
			return array('Status'=>'ERROR-1','Message'=>'傳入的參數錯誤!');
		$user = User::where('usr_id',$data_arr['usr_id'])->select('id')->first();
		if(!$user)
			return array('Status'=>'ERROR-2','Message'=>'無會員資料!');
		$merchant = Merchant::where('MerchantID',$data_arr['MerchantID'])->select('MerchantHashKey','MerchantIvKey')->first();
		if(!$merchant)
			return array('Status'=>'ERROR-3','Message'=>'無合作商店資料!');	
					
		$TimeStamp = time();
		$trade_info_arr = array(  
			'MerchantID' =>  $data_arr['MerchantID'],  
			'RespondType' => 'JSON', 
			'TimeStamp' => $TimeStamp,  
			'Version' => '1.5', 
			'MerchantOrderNo' => $data_arr['MerchantOrderNo'],  
			'Amt' =>  $data_arr['Amt'],  
			'ItemDesc' =>  $data_arr['ItemDesc'],
			'Email' => $data_arr['Email'],
			'LoginType' => '0',
			'TokenTerm' => $data_arr['usr_id'],
			'TokenTermDemand' => '3',
			'ReturnURL' => env('newebPay_return_url'),
			'NotifyURL' => env('newebPay_notify_url'),
			'CustomerURL' => env('newebPay_customer_url'),
			'ClientBackURL' => env('newebPay_back_url')
			 
		);
		
		//交易資料經 AES 加密後取得 TradeInfo
		$TradeData = $this->create_mpg_aes_encrypt($trade_info_arr,$merchant->MerchantHashKey,$merchant->MerchantIvKey);
		
		$input = new Newebpay_mpg;
		$input->TradeStatus = 0;
		$input->u_id = $user->id;
		$input->MerchantID = $data_arr['MerchantID'];
		$input->MerchantOrderNo = $data_arr['MerchantOrderNo'];
		$input->Amt = $data_arr['Amt'];
		$input->ItemDesc = $data_arr['ItemDesc'];
		$input->Email = $data_arr['Email'];
		$input->save();
		
		if(Newebpay_mpg::where('MerchantOrderNo',$data_arr['MerchantOrderNo'])->count())
		{
			$url = 'https://ccore.newebpay.com/MPG/mpg_gateway';
			echo '<form name="sendFrm"  action="'.$url.'" method="post">';
			echo '<input type="hidden" name="MerchantID" value="'.$data_arr['MerchantID'].'" />';
			echo '<input type="hidden" name="Version" value="1.5" />';
			echo '<input type="hidden" name="TradeInfo" value="'.$TradeData['TradeInfo'].'" />';
			echo '<input type="hidden" name="TradeSha" value="'.$TradeData['TradeSha'].'" />';
			echo '</form>';
			echo '<script>';
			echo 'document.sendFrm.submit();';
			echo '</script>';
			
		}else
			return array('Status'=>'ERROR-3','Message'=>'系統無回應，請稍後在試!!');
	}
	
	public function merchantCreate($post_data_arr=array()){  //建立合作商店
		
		$post_data_str = '';
		if (!empty($post_data_arr)) { 
            //將參數經過 URL ENCODED QUERY STRING 
            $post_data_str = http_build_query($post_data_arr);         
		}
		$postData = trim(bin2hex(openssl_encrypt($this->addpadding($post_data_str), 'AES-256-CBC', $this->partner_key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $this->partner_iv))); 
		$sendData = array(
			'PartnerID_' => $this->partner_id,
			'PostData_' => $postData
		);
		$url = 'https://ccore.Newebpay.com/API/AddMerchant';
		$result = $this->curl_work($url, $sendData); //背景送出
		$data = json_decode($result['web_info']);
		return $result['web_info'];	
	}
	
	public function merchantModify($post_data_arr=array()){  //修改合作商店
		
		$post_data_str = '';
		if (!empty($post_data_arr)) { 
            //將參數經過 URL ENCODED QUERY STRING 
            $post_data_str = http_build_query($post_data_arr);         
		}
		$postData = trim(bin2hex(openssl_encrypt($this->addpadding($post_data_str), 'AES-256-CBC', $this->partner_key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $this->partner_iv))); 
		$sendData = array(
			'PartnerID_' => $this->partner_id,
			'PostData_' => $postData
		);
		$url = 'https://ccore.NewebPay.com/API/AddMerchant/modify';
		$result = $this->curl_work($url, $sendData); //背景送出
		$data = json_decode($result['web_info']);
		return $result['web_info'];	
	}
	
	public function ExportInstruct ($post_data_arr=array()){  //撥款給合作商店
		
		$post_data_str = '';
		if (!empty($post_data_arr)) { 
            //將參數經過 URL ENCODED QUERY STRING 
            $post_data_str = http_build_query($post_data_arr);         
		}
		$postData = trim(bin2hex(openssl_encrypt($this->addpadding($post_data_str), 'AES-256-CBC', $this->partner_key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $this->partner_iv))); 
		$sendData = array(
			'PartnerID_' => $this->partner_id,
			'PostData_' => $postData
		);
		$url = 'https://ccore.newebpay.com/API/ExportInstruct';
		$result = $this->curl_work($url, $sendData); //背景送出
		$data = json_decode($result['web_info']);
		return $result['web_info'];	
	}
	
	public function ChargeInstruct ($post_data_arr=array()){  //撥款給合作商店
		
		$post_data_str = '';
		if (!empty($post_data_arr)) { 
            //將參數經過 URL ENCODED QUERY STRING 
            $post_data_str = http_build_query($post_data_arr);         
		}
		$postData = trim(bin2hex(openssl_encrypt($this->addpadding($post_data_str), 'AES-256-CBC', $this->partner_key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $this->partner_iv))); 
		$sendData = array(
			'PartnerID_' => $this->partner_id,
			'PostData_' => $postData
		);
		$url = 'https://ccore.newebpay.com/API/ChargeInstruct';
		$result = $this->curl_work($url, $sendData); //背景送出
		$data = json_decode($result['web_info']);
		return $result['web_info'];	
	}
	
	public function get_Platformfee_perday ($FeeDate=''){  //平台費用扣款單日查詢
		if(!$FeeDate)
			return array('Status'=>'ERROR-0','Message'=>'無參數值!');
		
		$TimeStamp = time();
		$check_code_arr = array(  
			'FeeDate' => $FeeDate, 
			'PartnerID_' => $this->partner_id,  
			'TimeStamp' => $TimeStamp,  
			'Version' => '1.0' 
		);
		ksort($check_code_arr);
		$check_code_str = 'HashKey='.$this->partner_key.'&'.http_build_query($check_code_arr).'&HashIV='.$this->partner_iv;
		$CheckCode = strtoupper(hash("sha256", $check_code_str));
		$post_data_str = array(  
			'FeeDate' => $FeeDate, 
			'PartnerID_' => $this->partner_id,
			'TimeStamp' => $TimeStamp,  
			'Version' => '1.0', 
			'CheckValue' => $CheckCode  
		);
		$url = 'https://ccore.newebpay.com/API/Platformfee/perday';
		$result = $this->curl_work($url, $post_data_str); //背景送出
		$data = json_decode($result['web_info']);
		return $result['web_info'];	
	}
	
	public function get_Platformfee_search($id=''){  //平台費用扣款單筆查詢
		if(!$id)
			return array('Status'=>'ERROR-0','Message'=>'無參數值!');
		
		$transfer = Newebpay_mpg::join('merchants','newebpay_mpgs.MerchantID','=','merchants.MerchantID')->where('MerchantOrderNo',$id)->first();
		$TimeStamp = time();
		$check_code_arr = array(  
			'FeeDate' => $transfer->FundTime, 
			'PartnerID_' => $this->partner_id,
			'MerTrade' => $id,
			'TimeStamp' => $TimeStamp,  
			'Version' => '1.0' 
		);
		ksort($check_code_arr);
		$check_code_str = 'HashKey='.$this->partner_key.'&'.http_build_query($check_code_arr).'&HashIV='.$this->partner_iv;
		$CheckCode = strtoupper(hash("sha256", $check_code_str));
		$post_data_str = array(  
			'FeeDate' => $transfer->FundTime, 
			'PartnerID_' => $this->partner_id,
			'MerTrade' => $id,
			'TimeStamp' => $TimeStamp,  
			'Version' => '1.0', 
			'CheckValue' => $CheckCode  
		);
		$url = 'https://ccore.newebpay.com/API/Platformfee/search';
		$result = $this->curl_work($url, $post_data_str); //背景送出
		$data = json_decode($result['web_info']);
		return $result['web_info'];	
	}
	
}
