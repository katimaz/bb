<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Notifications\Notify;
use App\Models\Invoice;
use App\Models\Invoice_allowance;

class Ezpay extends Model
{
    
	public $company_id;
	public $merchant_id;
	public $track_key;
	public $track_iv;
	public $invoice_key;
	public $invoice_iv;
	
	public function __construct()
    {
        $this->company_id = env('ezPay_invoice_track_company_id');
		$this->merchant_id = env('ezPay_invoice_merchant_id');
		$this->track_key = env('ezPay_invoice_track_HashKey');
		$this->track_iv = env('ezPay_invoice_track_HashIV');
		$this->invoice_key = env('ezPay_invoice_merchant_HashKey');
		$this->invoice_iv = env('ezPay_invoice_merchant_HashIV');
    }
	
	//====以下為副程式==== 
	public function addpadding($string = '', $blocksize = 32) {     
		$len = strlen($string);     
		$pad = $blocksize - ($len % $blocksize);     
		$string .= str_repeat(chr($pad), $pad);     
		return $string; 
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
		
	//====以上為副程式====
	
	public function inv_track_post($url="",$post_data_array=array()) 
	{
		$post_data_str = http_build_query($post_data_array);
		$key = $this->track_key;
		$iv = $this->track_iv;
		$post_data = trim(bin2hex(openssl_encrypt($this->addpadding($post_data_str), 'AES-256-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv)));
		
		//$company_id = env('ezPay_invoice_track_company_id'); //會員編號 
		$post_data_array = array(//送出欄位     
			'CompanyID_' => $this->company_id,     
			'PostData_' => $post_data 
		); 
		$post_data_str = http_build_query($post_data_array); 
		return $this->curl_work($url, $post_data_str); //背景送出
	}
	
	public function invoice_post($url='',$post_data_array=array(),$MerchantID='') 
	{
		$post_data_str = http_build_query($post_data_array);
		$key = $this->invoice_key;
		$iv = $this->invoice_iv;
		$post_data = trim(bin2hex(openssl_encrypt($this->addpadding($post_data_str), 'AES-256-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv)));
		
		$post_data_array = array(//送出欄位     
			'MerchantID_' => $this->merchant_id,     
			'PostData_' => $post_data 
		); 
		$post_data_str = http_build_query($post_data_array); 
		return $this->curl_work($url, $post_data_str); //背景送出
	}
	
	public function check_track_code($check_code_array=array()) 
	{
		$key = $this->track_key;
		$iv = $this->track_iv;
		ksort($check_code_array); 
		$check_code = strtoupper(hash('sha256', 'HashIv='.$iv.'&' . http_build_query($check_code_array) . '&HashKey='.$key));  
		return $check_code; //背景送出<strong></strong>
	}
	
	public function check_invoice_code($check_code_array=array()) 
	{
		$key = $this->invoice_key;
		$iv = $this->invoice_iv;
		
		ksort($check_code_array); 
		$check_str = http_build_query($check_code_array); 
		$CheckCode = 'HashIV='.$iv.'&'.$check_str.'&HashKey='.$key; 
		$CheckCode = strtoupper(hash("sha256", $CheckCode));
		return $CheckCode; //背景送
	}
	
	public function set_invalid($url='',$post_data_array=array()) 
	{
		$merchant_id = $this->merchant_id;
		$result = $this->invoice_post($url,$post_data_array);
		$result_data = json_decode($result['web_info']);
		if($result_data->Status=='SUCCESS')
		{
			$deArr = json_decode($result_data->Result);
			$invoice = Invoice::where('InvoiceNumber',$deArr->InvoiceNumber)->select('MerchantOrderNo','InvoiceTransNo','TotalAmt','RandomNum')->first();
			if(isset($invoice))
			{
				$check_code_array = array(     
					"MerchantID" => $merchant_id,//商店代號     
					"MerchantOrderNo" => $invoice->MerchantOrderNo,  //商店自訂單號(訂單編號)     
					"InvoiceTransNo" => $invoice->InvoiceTransNo,  //智付寶電子發票開立序號     
					"TotalAmt" => $invoice->TotalAmt,  //發票金額    
					"RandomNum" => $invoice->RandomNum  //發票防偽隨機碼 
				);
				$check_code = $this->check_invoice_code($check_code_array);
				if($check_code==$deArr->CheckCode)
				{
					$input['InvoiceStatus'] = 2;
					$input['InvalidReason'] = $post_data_array['InvalidReason'];
					$input['InvalidTime'] = $deArr->CreateTime;
					
					Invoice::where('InvoiceNumber', $deArr->InvoiceNumber)->update($input);
					
				}else
					Log::alert("發票驗證碼失敗(".Session::get('ownerID')." >> ".basename(url()->current(),".php")." >> ".$_SERVER["REMOTE_ADDR"].")");
			}
		}
	}
	
	public function query_term(){
		$m = ((substr(date("m"),0,1)=='0')?substr(date("m"),1):date("m"))/2;
		$mArr = explode(".",$m);
		$Term = ((count($mArr)>1)?$mArr[0]+1:$mArr[0]);
		$invoice_tracks = Invoice_track::where('Year',date("Y")-1911)->where('Term',$Term)->orderBy('Year','desc')->orderBy('Term','desc')->orderBy('StartNumber','desc')->get();
		$url = 'https://cinv.ezpay.com.tw/Api_number_management/searchNumber';
		foreach($invoice_tracks as $invoice_track)
		{
			$post_data_array = array(//post_data 欄位資料     
				'RespondType' => 'JSON', //回傳格式     
				'Version' => '1.0', //串接程式版本     
				'TimeStamp' => time(), //時間戳記     
				'ManagementNo' => $invoice_track->ManagementNo, //平台序號
				'Year' => $invoice_track->Year, //發票年度     
				'Term' => $invoice_track->Term, //發票期別     
				'Flag' => $invoice_track->Flag //字軌狀態 
			);
			$result = $this->inv_track_post($url,$post_data_array);
			$result_data = json_decode($result['web_info']);
			if($result_data->Status=='INM10002' && $invoice_track->Flag!=2)
			{
				$Flag = (($invoice_track->Flag==1)?0:1);
				$post_data_array = array(//post_data 欄位資料     
					'RespondType' => 'JSON', //回傳格式     
					'Version' => '1.0', //串接程式版本     
					'TimeStamp' => time(), //時間戳記     
					'ManagementNo' => $invoice_track->ManagementNo, //平台序號
					'Year' => $invoice_track->Year, //發票年度     
					'Term' => $invoice_track->Term, //發票期別     
					'Flag' => $Flag //字軌狀態 
				);
				
				$result = $this->inv_track_post($url,$post_data_array);
				$result_data = json_decode($result['web_info']);	
			}
			if($result_data->Status=='SUCCESS')
			{
				$check_code_array = array(     
					'AphabeticLetter' => $result_data->Result[0]->AphabeticLetter, //發票字軌     
					'CompanyId' => $this->company_id, //會員編號     
					'EndNumber' => $result_data->Result[0]->EndNumber, //發票結束號碼     
					'ManagementNo' => $result_data->Result[0]->ManagementNo, //字軌流水號     
					'StartNumber' => $result_data->Result[0]->StartNumber //發票起始號碼 
				);
				
				$check_code = $this->check_track_code($check_code_array);
				if($check_code==$result_data->Result[0]->CheckCode && ($invoice_track->Flag!=$result_data->Result[0]->Flag || $result_data->Result[0]->LastNumber!=$invoice_track->LastNumber))
				{
					$input['Flag'] = $result_data->Result[0]->Flag;
					$input['LastNumber'] = (int)$result_data->Result[0]->LastNumber;
					Invoice_track::where('ManagementNo',$result_data->Result[0]->ManagementNo)->update($input);
				}
			}
		}
	}
	
	public function set_invoice($transfer=array(),$decode=array()) { //交易後自動開發票作業
      
	  $ItemName = '手續費';
	  $ItemCount = 1;
	  $ItemUnit = '筆';
	  $ItemPrice = round($decode->Result->Amt*0.2);
	  $ItemAmt = round($decode->Result->Amt*0.2);
	  
	  $Amt = round($ItemAmt/1.05);
	  $TaxAmt = $ItemAmt-$Amt;
		  
	  $post_data_array = array(//post_data 欄位資料            
		   "RespondType" => "JSON",             
		   "Version" => "1.4",             
		   "TimeStamp" => time(), //請以 time() 格式            
		   "TransNum" => $decode->Result->TradeNo,             
		   "MerchantOrderNo" => time().rand(100,999), 
		   "BuyerName" => $transfer->last_name.$transfer->first_name,             
		   "BuyerUBN" => NULL, 
		   "BuyerAddress" => '',             
		   "BuyerEmail" => $transfer->Email,             
		   "Category" => 'B2C',           
		   "TaxType" => 1,             
		   "TaxRate" => 5,             
		   "Amt" => $Amt,             
		   "TaxAmt" => $TaxAmt,             
		   "TotalAmt" => $Amt+$TaxAmt,             
		   "CarrierType" => 2, 
		   "CarrierNum" => rawurlencode($transfer->u_id),
		   "PrintFlag" => 'N', 
		   "ItemName" => $ItemName, //多項商品時，以「|」分開 
		   "ItemCount" => $ItemCount, //多項商品時，以「|」分開 
		   "ItemUnit" => $ItemUnit, //多項商品時，以「|」分開 
		   "ItemPrice" => $ItemPrice, //多項商品時，以「|」分開 
		   "ItemAmt" => $ItemAmt, //多項商品時，以「|」分開 
		   "Comment" => $transfer->ItemDesc.'手續費發票', 
		   "Status" => 1, //1=立即開立，0=待開立，3=延遲開立             
		   "NotifyEmail" => "1", //1=通知，0=不通知 
	  ); 
	  
	  //dd($post_data_array);
	  $url = 'https://cinv.pay2go.com/API/invoice_issue';
	  $result = $this->invoice_post($url,$post_data_array);
	  
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
		  $check_code = $this->check_invoice_code($check_code_array);
		  if($check_code==$deArr->CheckCode)
		  {
			  $input = new Invoice;
			  $input->u_id = $transfer->u_id;
			  $input->InvoiceStatus = 1;
			  $input->InvoiceTransNo = $deArr->InvoiceTransNo;
			  $input->TransNum = $decode->Result->TradeNo;
			  $input->MerchantOrderNo = $deArr->MerchantOrderNo;
			  $input->Status = 1;
			  $input->Category = 'B2C';
			  $input->BuyerName = $transfer->last_name.$transfer->first_name;
			  //$input->BuyerUBN = NULL;
			  $input->BuyerEmail = $transfer->Email;
			  $input->BuyerAddress = '';
			  $input->CarrierType = 2;
			  $input->CarrierNum = rawurlencode($transfer->usr_id);
			  $input->PrintFlag = 'N';
			  $input->TaxType = 1;
			  $input->TaxRate = 5;
			  $input->TotalAmt = (int)$deArr->TotalAmt;
			  $input->InvoiceNumber = $deArr->InvoiceNumber;
			  $input->RandomNum = $deArr->RandomNum;
			  $input->CreateTime = $deArr->CreateTime;
			  $input->BarCode = ((isset($deArr->BarCode))?$deArr->BarCode:'');
			  $input->QRcodeL = ((isset($deArr->QRcodeL))?$deArr->QRcodeL:'');
			  $input->QRcodeR = ((isset($deArr->QRcodeR))?$deArr->QRcodeR:'');
			  $input->RemainAmt = (int)$deArr->TotalAmt;
			  $input->Comment = $transfer->ItemDesc.'手續費發票';
			  $input->save();
			  
		  }
		  
	  }
	  //================發票作業 end=====================
  }
	
}
