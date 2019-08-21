<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Invoice;
use App\Models\Invoice_allowance;

class Newebpay_mpg extends Model
{
    protected $table = 'newebpay_mpgs';
		
	public function mpgs_search($date=array(), $text='',$status='') 
	{     
		$transfers = static::join('users','newebpay_mpgs.u_id','=','users.id')
			  ->join('merchants','newebpay_mpgs.MerchantID','=','merchants.MerchantID')
			  ->where(function($query) use($date,$status){
				  if(isset($date) && count($date) && $date['start']!='' && $date['end']!='')
				  {
					  $query->where('newebpay_mpgs.PayTime','>=',$date['start'].' 00:00:00');
					  $query->where('newebpay_mpgs.PayTime','<=',$date['end'].' 23:59:59');
				  }
				  if($status!='')
					$query->where('newebpay_mpgs.TradeStatus',$status);
			  })
			  ->where(function($query) use($text){
				  if($text!='')
				  {
					  $query->orWhere('newebpay_mpgs.MerchantOrderNo',$text);
					  $query->orWhere('newebpay_mpgs.MerchantID',$text);
					  $query->orWhere('newebpay_mpgs.TradeNo',$text);
					  $query->orWhere('newebpay_mpgs.Email',$text);
					  $query->orWhere('newebpay_mpgs.PayTime','like','%'.$text.'%');
					  $query->orWhere('newebpay_mpgs.ItemDesc','like','%'.$text.'%');
					  $query->orWhere('newebpay_mpgs.IP',$text);
					  $query->orWhere('newebpay_mpgs.PaymentType',$text);
					  $query->orWhere('newebpay_mpgs.Amt',$text);
					  $query->orWhere('newebpay_mpgs.EscrowBank',$text);
					  $query->orWhere('newebpay_mpgs.Auth',$text);
					  $query->orWhere('newebpay_mpgs.Card6No',$text);
					  $query->orWhere('newebpay_mpgs.Card4No',$text);
					  $query->orWhere('newebpay_mpgs.ExpireDate','like','%'.$text.'%');
					  $query->orWhere('newebpay_mpgs.PayStore',$text);
					  $query->orWhere('newebpay_mpgs.StoreCode',$text);
					  $query->orWhere('newebpay_mpgs.CVSCOMName',$text);
					  $query->orWhere('newebpay_mpgs.CVSCOMPhone',$text);
					  $query->orWhere('merchants.MemberName',$text);
					  $query->orWhere('merchants.MemberUnified',$text);
					  $query->orWhere('merchants.ManagerID',$text);
					  $query->orWhere('merchants.MemberPhone',$text);
					  $query->orWhere('merchants.ManagerName','like','%'.$text.'%');
					  $query->orWhere('merchants.MerchantName','like','%'.$text.'%');
					  $query->orWhere('users.last_name',$text);
					  $query->orWhere('users.first_name',$text); 
				  }	
			  })
			  ->select('newebpay_mpgs.MerchantOrderNo','newebpay_mpgs.TradeStatus','newebpay_mpgs.TradeNo','newebpay_mpgs.PaymentType','newebpay_mpgs.Amt','newebpay_mpgs.ItemDesc','newebpay_mpgs.Email','newebpay_mpgs.EscrowBank','newebpay_mpgs.PayTime','newebpay_mpgs.MerchantID','merchants.MemberName','merchants.MerchantName','newebpay_mpgs.FundTime','users.usr_id','users.last_name','users.first_name')
			  ->orderBy('newebpay_mpgs.created_at','desc')
			  ->paginate(30);
			  
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
		return $transfers; 
	} 
}

