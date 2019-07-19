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
   	public $merchant_id;
	public $partner_id;
	public $mer_key;
	public $mer_iv;
	
	public function __construct()
    {
        $this->merchant_id = env('newebPay_merchant_id');
		$this->mer_key = env('newebPay_HashKey');
		$this->mer_iv = env('newebPay_HashIV');
		$this->partner_id = env('newebPay_partner_id');
		
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
	
	public function create_mpg_aes_encrypt($post_data_array=array()) {         
		$data_str = '';
		if (!empty($post_data_array)) { 
            //將參數經過 URL ENCODED QUERY STRING 
            $data_str = http_build_query($post_data_array);         
		}
		$TradeInfo =  trim(bin2hex(openssl_encrypt($this->addpadding($data_str), 'AES-256-CBC', $this->mer_key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $this->mer_iv)));
		$TradeSha = strtoupper(hash("sha256", 'HashKey='.$this->mer_key.'&'.$TradeInfo.'&HashIV='.$this->mer_iv));
		 
		return array('TradeInfo'=>$TradeInfo,'TradeSha'=>$TradeSha);
	} 
	
	public function addpadding($string = '', $blocksize = 32) {         
		$len = strlen($string);         
		$pad = $blocksize - ($len % $blocksize);         
		$string .= str_repeat(chr($pad), $pad);         
		return $string;      
	} 
	
	public function create_aes_decrypt($parameter="") {         
		 return json_decode($this->strippadding(openssl_decrypt(hex2bin($parameter),'AES-256-CBC', $this->mer_key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $this->mer_iv))); 
		     
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
		$TradeSha = $this->get_tradeSha($data_array);
		
		$post_data_array = array(//post_data 欄位資料            
			 'MerchantID' =>  $this->merchant_id, 
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
			
			$check_code = $this->get_check_code($data_array);
			if($data->Result->CheckCode==$check_code)
			{
				$input['TradeStatus'] = $data->Result->TradeStatus;
				$input['TradeNo'] = $data->Result->TradeNo;
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
		$url = 'https://ccore.newebpay.com/API/CreditCard/Cancel';
		$Tradedata = $this->create_mpg_aes_encrypt($post_data_array);
		$data_str = $Tradedata['TradeInfo'];
				
		$post_data = array(//post_data 欄位資料            
			 "MerchantID_" => $this->merchant_id,             
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
			
			$check_code = $this->get_check_code($data_array);
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
	
	public function get_tradeSha($data_array=array())
	{
		$TradeInfo = 'IV='.$this->mer_iv.'&Amt='.$data_array['Amt'].'&MerchantID='.$this->merchant_id.'&MerchantOrderNo='.$data_array['MerchantOrderNo'].'&Key='.$this->mer_key;
		return strtoupper(hash("sha256", $TradeInfo)); 
	}
	
	public function get_check_code($data_array=array())
	{
		$check_code_str = 'HashIV='.$this->mer_iv.'&Amt='.$data_array['Amt'].'&MerchantID='.$this->merchant_id.'&MerchantOrderNo='.$data_array['MerchantOrderNo'].'&TradeNo='.$data_array['TradeNo'].'&HashKey='.$this->mer_key;
		return strtoupper(hash("sha256", $check_code_str));
	}
	
	public function return_get_tradeSha($parameter="")
	{
		return  strtoupper(hash("sha256", 'HashKey='.$this->mer_key.'&'.$parameter.'&HashIV='.$this->mer_iv));
	}
	
	public function send_credit_close($request=array()) //信用卡請退款作業
	{
		$TimeStamp = time();
		$trade_info_arr = array(  
			'RespondType' => 'JSON',
			'MerchantOrderNo' => $request->MerchantOrderNo,  
			'TradeNo' => $request->TradeNo, 
			'TimeStamp' => $TimeStamp,  
			'Version' => 1.1, 
			'Amt' =>  $request->Amt,  
			'IndexType' =>  $request->IndexType,
			'CloseType' =>  $request->CloseType,
			'Cancel' =>  ((isset($request->Cancel))?$request->Cancel:0)  
		); 
		$url = 'https://ccore.newebpay.com/API/CreditCard/Close';
		$TradeData = $this->create_mpg_aes_encrypt($trade_info_arr);
		$send_data = array(  
			'MerchantID_' => $this->merchant_id,  
			'PostData_' => $TradeData['TradeInfo'] 
		);
		$result = $this->curl_work($url, $send_data);
		$data = json_decode($result['web_info']);
		if($data->Status=='SUCCESS')
		{
			$transfer = Newebpay_mpg::where('TradeNo',$data->Result->TradeNo)->first();
			$this->query_tradeInfo($transfer);
		}
		return array('Status'=>$data->Status,'Message'=>$data->Message);
	}
	
	public function newebPay_return($request=array())
	{
		$decode = $this->create_aes_decrypt($request->TradeInfo);
		if(!$decode)
			return 'error';
		$TradeSha = $this->return_get_tradeSha($request->TradeInfo);
		if($request->MerchantID==$this->merchant_id && $request->TradeSha==$TradeSha)
		{
			$transfer = Newebpay_mpg::where('MerchantOrderNo',$decode->Result->MerchantOrderNo)->select('MerchantOrderNo','Amt')->first();
			if(!$transfer)
				return 'error';
			$data_array = array(//post_data 欄位資料            
				 'MerchantOrderNo' => $transfer->MerchantOrderNo,
				 'Amt' =>  $transfer->Amt,
			);  
			$TradeSha = $this->get_tradeSha($data_array);
			
			$post_data_array = array(//post_data 欄位資料            
				 'MerchantID' =>  $this->merchant_id, 
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
				$input['TokenUseStatus'] = $decode->Result->TokenUseStatus;
				$input['Inst'] = $decode->Result->Inst;
				$input['InstFirst'] = $decode->Result->InstFirst;
				$input['InstEach'] = $decode->Result->InstEach;
				$input['ECI'] = $decode->Result->ECI;
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
				Notify::via('notify','訂單編號 : '.$decode->Result->MerchantOrderNo.' 支付方式 : '.$decode->Result->PaymentType.' 前往查看 : ! <'.env('APP_URL').'/admin/transfer_records?item=newebPay&action=manage'.'|Click here> for details!');
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
		
		$decode = $this->create_aes_decrypt($request->TradeInfo);
		if(!$decode)
			return View('web/error_message', array('message' => '回傳值有誤，請稍後在試', 'goUrl'=>'/'));
		$TradeSha = $newebPay->return_get_tradeSha($request->TradeInfo);
		
		if($request->MerchantID==$this->merchant_id && $request->TradeSha==$TradeSha)
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
			Notify::via('notify','訂單付款返回 : '.$decode->Result->MerchantOrderNo.' 支付方式 : '.$decode->Result->PaymentType.' 前往查看 : ! <'.env('APP_URL').'/admin/transfer_records?item=newebPay&action=manage'.'|Click here> for details!');
		}
		return $decode;
	}
	
	public function create_mpg_form($data_arr=array()){  //交易後串藍新金流
		if(!count($data_arr))
			return array('Status'=>'ERROR-0','Message'=>'無參數值!');
		if(!isset($data_arr['usr_id']) || !isset($data_arr['MerchantOrderNo'])|| !isset($data_arr['Amt']) || !isset($data_arr['ItemDesc']) || !isset($data_arr['Email']))
			return array('Status'=>'ERROR-1','Message'=>'傳入的參數錯誤!');
		$user = User::where('usr_id',$data_arr['usr_id'])->select('id')->first();
		if(!$user)
			return array('Status'=>'ERROR-2','Message'=>'無會員資料!');
					
		$TimeStamp = time();
		$trade_info_arr = array(  
			'MerchantID' =>  $this->merchant_id,  
			'RespondType' => 'JSON', 
			'TimeStamp' => $TimeStamp,  
			'Version' => 1.5, 
			'MerchantOrderNo' => $data_arr['MerchantOrderNo'],  
			'Amt' =>  $data_arr['Amt'],  
			'ItemDesc' =>  $data_arr['ItemDesc']  
		); 
		//交易資料經 AES 加密後取得 TradeInfo 
		$TradeData = $this->create_mpg_aes_encrypt($trade_info_arr);
		
		$input = new Newebpay_mpg;
		$input->TradeStatus = 0;
		$input->u_id = $user->id;
		$input->MerchantOrderNo = $data_arr['MerchantOrderNo'];
		$input->Amt = $data_arr['Amt'];
		$input->ItemDesc = $data_arr['ItemDesc'];
		$input->Email = $data_arr['Email'];
		$input->save();
		
		if(Newebpay_mpg::where('MerchantOrderNo',$data_arr['MerchantOrderNo'])->count())
		{
			$url = 'https://ccore.newebpay.com/MPG/mpg_gateway';
			echo '<form name="sendFrm"  action="'.$url.'" method="post">';
			echo '<input type="hidden" name="MerchantID" value="'.$this->merchant_id.'" />';
			echo '<input type="hidden" name="Version" value="1.5" />';
			echo '<input type="hidden" name="RespondType" value="JSON" />';
			echo '<input type="hidden" name="TimeStamp" value="'.$TimeStamp.'" />';
			echo '<input type="hidden" name="MerchantOrderNo" value="'.$data_arr['MerchantOrderNo'].'" />';
			echo '<input type="hidden" name="Amt" value="'.$data_arr['Amt'].'" />';
			echo '<input type="hidden" name="ItemDesc" value="'.$data_arr['ItemDesc'].'" />';
			echo '<input type="hidden" name="LoginType" value="0" />';
			echo '<input type="hidden" name="TokenTerm" value="'.$data_arr['usr_id'].'" />';
			echo '<input type="hidden" name="TokenTermDemand" value="3" />';
			echo '<input type="hidden" name="Email" value="'.$data_arr['Email'].'" />';
			echo '<input type="hidden" name="TradeLimit" value="600" />';
			echo '<input type="hidden" name="ReturnURL" value="'.env('newebPay_return_url').'" />';
			echo '<input type="hidden" name="NotifyURL" value="'.env('newebPay_notify_url').'" />';
			echo '<input type="hidden" name="CustomerURL" value="'.env('newebPay_customer_url').'" />';
			echo '<input type="hidden" name="ClientBackURL" value="'.env('newebPay_back_url').'" />';
			echo '<input type="hidden" name="TradeInfo" value="'.$TradeData['TradeInfo'].'" />';
			echo '<input type="hidden" name="TradeSha" value="'.$TradeData['TradeSha'].'" />';
			echo '</form>';
			echo '<script>';
			echo 'document.sendFrm.submit();';
			echo '</script>';
		}else
			return array('Status'=>'ERROR-3','Message'=>'系統無回應，請稍後在試!!');
	}
	
	public function get_sysAccountings($FeeDate='',$usr_id=''){  //平台費用扣款單日查詢
		if(!$FeeDate)
			return array('Status'=>'ERROR-0','Message'=>'無參數值!');
		
		if($usr_id)
		{
			$user = User::where('usr_id',$data_arr['usr_id'])->select('id')->first();
			if(!$user)
				return array('Status'=>'ERROR-2','Message'=>'無會員資料!');
		}
		$TimeStamp = time();
		$check_code_arr = array(  
			'FeeDate' => $FeeDate, 
			'PartnerID_' => $this->partner_id,  
			'TimeStamp' => $TimeStamp,  
			'Version' => '1.0' 
		);
		ksort($check_code_arr);  
		$check_code_str = http_build_query($check_code_arr);
		$CheckCode = $this->return_get_tradeSha($check_code_str);
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
	
}
