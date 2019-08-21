<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
	
	public function invoice_search($date=array(), $text='',$status='') 
	{   
		$invoices = Invoice::
			where(function($query) use($date,$status){
				if(isset($date) && count($date) && $date['start']!='' && $date['end']!='')
				{
					$query->where('CreateTime','>=',$date['start'].' 00:00:00');
					$query->where('CreateTime','<=',$date['end'].' 23:59:59');
				}
				if($status!='')
				  $query->where('InvoiceStatus',$status);
			})
			->where(function($query) use($text){
				if($text!='')
				{
					$query->orWhere('InvoiceTransNo',$text);
					$query->orWhere('TransNum',$text);
					$query->orWhere('MerchantOrderNo',$text);
					$query->orWhere('Category',$text);
					$query->orWhere('BuyerName',$text);
					$query->orWhere('BuyerUBN',$text);
					$query->orWhere('BuyerEmail',$text);
					$query->orWhere('BuyerAddress','like','%'.$text.'%');
					$query->orWhere('CarrierNum',$text);
					$query->orWhere('LoveCode',$text);
					$query->orWhere('PrintFlag',$text);
					$query->orWhere('InvoiceNumber',$text);
				}	
			})
			->orderBy('CreateTime','desc')
			->paginate(30);
						
		foreach($invoices as $invoice)
		{
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
			
		}
		return $invoices; 
	}
	
}