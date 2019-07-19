@extends('admin.master')
@section('content')
<div id="app">
	<div class="w-100" v-if="item=='newebPay'">
    	<div class="w-100 p-2 d-table">
            <div class="w-25 float-left text-left">
                <a class="btn btn-primary pull-right" v-if="action=='detail'" href="javascript:void(0)" @click="action='manage';title='交易查詢管理'" v-text="'返回'"></a>
            </div>    
            <div class="w-50 float-left mx-auto" v-if="action!='create' && action!='credit_close'">
                <input type="text" class="form-control float-left w-50" id="search" v-model="search_text" @keyup.enter="searchBtn" placeholder="搜尋交易單任一字串" />
                <select class="form-control float-left w-25 ml-1" v-model="tradeStatus">
                	<option value="" v-text="'全部'"></option>
                    <option :value="index" v-for="(trade,index) in trades" v-text="trade"></option>
                </select>
                <a href="javascript:void(0)" @click="searchBtn" class="btn btn-primary ml-1">搜尋</a>
            </div>
        	<h3 class="float-right" v-text="title"></h3>
        </div>
        <div class="w-100" v-if="action=='mpg_gateway'">
            <form id="mainFrm"  action="/admin/transfer_records_pt" method="post">
              @csrf
              <input type="hidden" name="item" v-model="item" />
              <input type="hidden" name="action" v-model="action" />
              <input type="hidden" name="usr_id" v-model="create_transfer.usr_id" />
              <table class="table table-light table-bordered" >
                  <tr>
                    <th class="w-25 text-center align-middle">Email</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control d-inline" @blur="email_get_account" @keyup.enter="email_get_account"  name="Email" id="Email" v-model="create_transfer.Email" placeholder="填寫好幫手Email" />
                        <span class="ml-2 d-inline text-danger" id="back_message" v-text="back_message"></span>
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center align-middle">好幫手名稱</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control" name="BuyerName" id="BuyerName" v-model="create_transfer.BuyerName" placeholder="好幫手名稱" />
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center align-middle">訂單編號</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control d-inline" name="MerchantOrderNo" id="MerchantOrderNo" v-model="create_transfer.MerchantOrderNo" placeholder="填寫訂單編號" />
                        <a href="javascript:void(0)" @click="today=new Date();create_transfer.MerchantOrderNo='BB_'+today.getTime()" class="btn btn-sm btn-primary d-inlie" v-text="'自動產生'"></a>
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center align-middle">訂單內容</th>
                    <td class="w-75">
                        <div class="w-100 py-2">
                        	<label class="d-inline">訂單金額 : </label>
                            <input type="text" class="w-25 form-control d-inline" name="Amt" id="Amt" v-model="create_transfer.Amt" placeholder="填寫銷貨金額" />
                        </div>
                        <div class="w-100 py-2">
                        	<label class="d-inline">商品資訊 : </label>
                            <input type="text" class="w-75 form-control d-inline" name="ItemDesc" id="ItemDesc" v-model="create_transfer.ItemDesc" placeholder="填寫商品資訊，限50字內" />
                        </div>
                     </td>
                  </tr>
              </table>    
              <div class="w-100 py-3 text-center">
                <a href="javascript:void(0)" class="btn btn-primary" @click="sendTransferAdd" v-text="'送出交易新增'"></a>
              </div>
            </form>
        </div>
        <div class="w-100" v-if="action=='detail'">
        	<table class="table table-light table-bordered" >
                <tr>
                  <th class="w-25 text-center bg-light">狀態</th>
                  <td v-text="trades[transfer.TradeStatus]"></td>
                  <th class="text-center bg-light">支付方式 </th>
                  <td v-text="paymentType[transfer.PaymentType]"></td>
                </tr>
                <tr>
                  <th class="text-center bg-light">訂單編號</th>
                  <td v-text="transfer.MerchantOrderNo"></td>
                  <th class="text-center bg-light">金流交易編號</th>
                  <td v-text="transfer.TradeNo"></td>
                </tr>
                <tr>
                  <th class="text-center bg-light">交易金額</th>
                  <td v-text="transfer.Amt"></td>
                  <th class="text-center bg-light">商品資訊</th>
                  <td v-text="transfer.ItemDesc"></td>
                </tr>
                <tr>
                  <th class="text-center bg-light">款項保管銀行</th>
                  <td v-text="transfer.EscrowBank"></td>
                  <th class="text-center bg-light" v-text="((transfer.PayTime)?'交易時間':'繳費有效期限')"></th>
                  <td v-text="((transfer.PayTime)?transfer.PayTime:transfer.ExpireDate)"></td>
                </tr>
                <tr>
                  <th class="w-25 text-center bg-light">Email</th>
                  <td v-text="transfer.Email"></td>
                  <th class="text-center bg-light">IP</th>
                  <td v-text="transfer.IP"></td>
                </tr>
                <tr>
                  <th class="w-25 text-center bg-light">支付回傳內容</th>
                  <td colspan="3" class="w-75">
                  	<div class="w-100" v-if="transfer.PaymentType=='CREDIT'">
                    	<ul class="w-100 row m-0 py-1">
                            <li class="col-sm-2 text-primary" v-text="'交易類別'"></li>
                            <li class="col-sm-2" style="background-color:#eee;" v-text="transfer.PaymentMethod"></li>
                            <li class="col-sm-2 text-primary" v-text="'分期-期別'"></li>
                            <li class="col-sm-2" v-text="transfer.Inst" style="background-color:#eee;"></li>
                            <li class="col-sm-2 text-primary" v-text="'金融回應碼'"></li>
                            <li class="col-sm-2" v-text="transfer.RespondCode" style="background-color:#eee;"></li>
                        </ul>
                        <ul class="w-100 row m-0 py-1">
                        	<li class="col-sm-2 text-primary" v-text="'卡號前六碼'"></li>
                            <li class="col-sm-2" v-text="transfer.Card6No" style="background-color:#eee;"></li>
                            <li class="col-sm-2 text-primary" v-text="'卡號末四碼'"></li>
                            <li class="col-sm-2" v-text="transfer.Card4No" style="background-color:#eee;"></li>
                            <li class="col-sm-2 text-primary" v-text="'授權碼'"></li>
                            <li class="col-sm-2" v-text="transfer.Auth" style="background-color:#eee;"></li>
                        </ul>
                        <ul class="w-100 row m-0 py-1">
                        	<li class="col-sm-2 text-primary" v-text="'授權結果'"></li>
                            <li class="col-sm-2" v-text="transfer.RespondMsg" style="background-color:#eee;"></li>
                            <li class="col-sm-2 text-primary" v-text="'請款狀態'"></li>
                            <li class="col-sm-2" v-text="closeStatus[transfer.CloseStatus]" style="background-color:#eee;"></li>
                            <li class="col-sm-2 text-primary" v-text="'退款狀態'"></li>
                            <li class="col-sm-2" v-text="backStatus[transfer.BackStatus]" style="background-color:#eee;"></li>
                        </ul>
                    </div>
                    
                    <div class="w-100" v-if="transfer.PaymentType=='WEBATM' || transfer.PaymentType=='VACC'">
                    	<ul class="w-100 m-0">
                            <li class="d-inline text-primary" v-text="'付款人金融機構代碼'"></li>
                            <li class="d-inline px-2 ml-2" v-text="transfer.PayBankCode" style="background-color:#eee;"></li>
                            <li class="d-inline text-primary ml-4" v-text="'付款人金融帳號末五碼'"></li>
                            <li class="d-inline px-2 ml-2" v-text="transfer.PayerAccount5Code" style="background-color:#eee;"></li>
                            <li class="d-inline text-primary ml-4" v-text="'預計撥款日'"></li>
                            <li class="d-inline px-2 ml-2" v-text="transfer.FundTime" style="background-color:#eee;"></li>
                        </ul>
                    </div>
                    
                    <div class="w-100" v-if="transfer.PaymentType=='CVS'">
                    	<ul class="w-100 m-0">
                        	<li class="d-inline text-primary" v-text="'付款資訊'" v-if="transfer.PayInfo"></li>
                            <li class="d-inline px-2 ml-2" v-text="transfer.PayInfo" v-if="transfer.PayInfo" style="background-color:#eee;"></li>
                            <li class="d-inline text-primary ml-4" v-text="'繳費有效期限'" v-if="transfer.ExpireDate"></li>
                            <li class="d-inline px-2 ml-2" v-text="transfer.ExpireDate" v-if="transfer.ExpireDate" style="background-color:#eee;"></li>
                        </ul>
                        <ul class="w-100 m-0">
                        	<li class="d-inline text-primary" v-text="'繳費代碼'" v-if="transfer.CodeNo"></li>
                            <li class="d-inline px-2 ml-2" v-text="transfer.CodeNo" v-if="transfer.CodeNo" style="background-color:#eee;"></li>
                            <li class="d-inline text-primary ml-4" v-text="'繳費門市類別'" v-if="storeType[transfer.StoreType]"></li>
                            <li class="d-inline px-2 ml-2" v-text="storeType[transfer.StoreType]" v-if="storeType[transfer.StoreType]" style="background-color:#eee;"></li>
                            <li class="d-inline text-primary ml-4" v-text="'繳費門市代號'" v-if="transfer.StoreID"></li>
                            <li class="d-inline px-2 ml-2" v-text="transfer.StoreID" v-if="transfer.StoreID" style="background-color:#eee;"></li>
                        </ul>
                    </div>
                    
                    <div class="w-100" v-if="transfer.PaymentType=='BARCODE'">
                    	<ul class="w-100 m-0">
                        	<li class="d-inline text-primary" v-text="'付款資訊'" v-if="transfer.PayInfo"></li>
                            <li class="d-inline px-2 ml-2" v-text="transfer.PayInfo" v-if="transfer.PayInfo" style="background-color:#eee;"></li>
                            <li class="d-inline text-primary ml-4" v-text="'繳費有效期限'" v-if="transfer.ExpireDate"></li>
                            <li class="d-inline px-2 ml-2" v-text="transfer.ExpireDate" v-if="transfer.ExpireDate" style="background-color:#eee;"></li>
                            <li class="d-inline text-primary" v-text="'繳費超商'" v-if="payStore[transfer.PayStore]"></li>
                            <li class="d-inline px-2 ml-2" v-text="payStore[transfer.PayStore]" v-if="payStore[transfer.PayStore]" style="background-color:#eee;"></li>
                        </ul>
                        <ul class="w-100 m-0">
                        	<li class="d-inline text-primary ml-4" v-text="'第一段條碼'" v-if="transfer.Barcode_1"></li>
                            <li class="d-inline px-2 ml-2" v-text="transfer.Barcode_1" v-if="transfer.Barcode_1" style="background-color:#eee;"></li>
                            <li class="d-inline text-primary ml-4" v-text="'第二段條碼'" v-if="transfer.Barcode_2"></li>
                            <li class="d-inline px-2 ml-2" v-text="transfer.Barcode_2" v-if="transfer.Barcode_2" style="background-color:#eee;"></li>
                            <li class="d-inline text-primary ml-4" v-text="'第三段條碼'" v-if="transfer.Barcode_3"></li>
                            <li class="d-inline px-2 ml-2" v-text="transfer.Barcode_3" v-if="transfer.Barcode_3" style="background-color:#eee;"></li>
                        </ul>
                    </div>
                  </td>
                </tr>
            </table>
            <form id="mainFrm" action="/admin/transfer_records_pt" method="post">
              @csrf
              <input type="hidden" name="item" v-model="item" />
              <input type="hidden" name="action" value="cancel" />
              <input type="hidden" name="id" v-model="transfer.MerchantOrderNo" />
              <div class="w-100 py-2 text-center d-table">
                  <a href="javascript:void(0)" class="btn btn-danger" @click="delthis(transfer.MerchantOrderNo,tradeStatus)" v-if="!transfer.TradeNo || transfer.RespondMsg=='未授權' || transfer.RespondMsg=='授權失敗'" v-text="'刪除此筆'"></a>
                  <a :href="'/admin/accountings?item=invoices&action=create&id='+transfer.TradeNo" class="btn btn-success" v-if="!invoice && transfer.TradeNo && parseInt(transfer.TradeStatus)==1" v-text="'開立發票'"></a>
                  <a href="javascript:void(0)" class="btn btn-dark float-right" @click="cancelthis(transfer.MerchantOrderNo)" v-if="transfer.TradeStatus==1 && transfer.PaymentType=='CREDIT' && transfer.Auth && !parseInt(transfer.CloseStatus)" v-text="'取消授權'"></a>
              </div> 
            </form>
        </div>
        <div class="w-100" v-if="action=='detail' && invoice">
        	<div class="w-100" v-text="'※發票資料'"></div>
            <table class="table table-light table-bordered" >
                <tr>
                  <th class="w-25 text-center bg-light">狀態</th>
                  <td v-text="((parseInt(invoice.InvoiceStatus)==1)?'開立':((parseInt(invoice.InvoiceStatus)==2))?'作廢':'')"></td>
                  <th class="w-25 text-center bg-light" v-text="((parseInt(invoice.InvoiceStatus)==2)?'發票作廢時間':'開立發票時間')"></th>
                  <td v-text="((parseInt(invoice.InvoiceStatus)==2)?invoice.InvalidTime:invoice.CreateTime)"></td>
                </tr>
                <tr>
                  <th class="w-25 text-center bg-light">發票號碼</th>
                  <td v-text="invoice.InvoiceNumber"></td>
                  <th class="w-25 text-center bg-light">平台發票編號</th>
                  <td v-text="invoice.InvoiceTransNo"></td>
                </tr>
               	<tr>
                  <th class="w-25 text-center bg-light">買受人名稱</th>
                  <td v-text="invoice.BuyerName"></td>
                  <th class="w-25 text-center bg-light">買受人統一編號</th>
                  <td v-text="invoice.BuyerUBN"></td>
                </tr>
                <tr>
                  <th class="w-25 text-center bg-light">發票金額</th>
                  <td v-text="invoice.TotalAmt"></td>
                  <th class="w-25 text-center bg-light">發票類別</th>
                  <td v-text="invoice.Category"></td>
                </tr>
                <tr>
                  <th class="w-25 text-center bg-light">可折讓金額</th>
                  <td v-text="invoice.RemainAmt"></td>
                  <th class="w-25 text-center bg-light">防偽隨機碼</th>
                  <td v-text="invoice.RandomNum"></td>
                </tr> 
                <tr>
                  <th class="w-25 text-center bg-light">發票載具</th>
                  <td v-text="carrierType[invoice.CarrierType]"></td>
                  <th class="w-25 text-center bg-light">載具編號</th>
                  <td v-text="invoice.CarrierNum"></td>
                </tr>
                <tr>
                  <th class="w-25 text-center bg-light">愛心捐贈碼</th>
                  <td v-text="invoice.LoveCode"></td>
                  <th class="w-25 text-center bg-light">索取紙本發票</th>
                  <td v-text="invoice.PrintFlag"></td>
                </tr> 
                <tr>
                  <th class="w-25 text-center bg-light">課稅別</th>
                  <td v-text="taxType[invoice.TaxType]"></td>
                  <th class="w-25 text-center bg-light">稅率%</th>
                  <td v-text="invoice.TaxRate"></td>
                </tr>
                <tr v-if="parseInt(invoice.InvoiceStatus)==2">
                  <th class="w-25 text-center bg-light" >作廢原因</th>
                  <td colspan="3" v-text="invoice.InvalidReason"></td>
                </tr> 
                <tr v-if="invoice.BuyerAddress">
                  <th class="w-25 text-center bg-light" >買受人地址</th>
                  <td colspan="3" v-text="invoice.BuyerAddress"></td>
                </tr> 
            </table>      
        </div>
        
        <div class="w-100" v-if="(action=='manage'||!action || action=='search') && action!='detail' && transfers">
        	<table class="table table-light table-bordered table-hover" >
                <tr class="text-center bg-secondary text-white">
                    <th>狀態</th>
                    <th>交易序號</th>
                    <th>支付方式</th>
                    <th>金額</th>
                    <th>商品資訊</th>
                    <th>Email</th>
                    <th>保管銀行</th>
                    <th>發票</th>
                    <th>交易時間</th>
                </tr>
                <tr class="text-center" v-for="(transfer,index) in transfers.data" @click="getThisData(transfer.MerchantOrderNo)" style="cursor:pointer">
                    <td v-text="trades[transfer.TradeStatus]"></td>
                    <td v-text="transfer.TradeNo"></td>
                    <td v-text="transfer.PaymentType"></td>
                    <td v-text="transfer.Amt"></td>
                    <td v-text="transfer.ItemDesc"></td>
                    <td v-text="transfer.Email"></td>
                    <td v-text="transfer.EscrowBank"></td>
                    <td v-text="((parseInt(transfer.InvoiceStatus)==1)?'開立':((parseInt(transfer.InvoiceStatus)==2))?'作廢':'未開')"></td>
                    <td v-html="transfer.PayTime"></td>  
                </tr>
            </table>
            <div class="w-100 d-table py-2 text-center border-top" v-if="transfers.last_page>1">
                <a class="btn btn-light btn-sm float-left" v-if="parseInt(transfers.current_page) > 1" href="javascript:void(0)" @click="go_content_page(parseInt(transfers.current_page-1))">上一頁</a>	
                <span class="h5" v-if="parseInt(transfers.current_page) > 1" v-text="transfers.current_page"></span>
                <a class="btn btn-light btn-sm float-right" v-if="transfers.last_page>transfers.current_page" href="javascript:void(0)" @click="go_content_page(parseInt(transfers.current_page)+1)">下一頁</a>
            </div>
        </div>
        
        <div class="w-100" v-if="action=='credit_close'">
        	<form id="mainFrm" action="/admin/transfer_records_pt" method="post">
                @csrf
                <input type="hidden" name="item" v-model="item" />
                <input type="hidden" name="action" value="credit_close" />
                <input type="hidden" name="MerchantOrderNo" id="MerchantOrderNo" />
                <input type="hidden" name="Amt" id="Amt" />
                <input type="hidden" name="TradeNo" id="TradeNo" />
                <input type="hidden" name="IndexType" value="1" />
                <input type="hidden" name="CloseType" id="CloseType" />
                <input type="hidden" name="Cancel" id="Cancel" />
            </form>              
            <table class="table table-light table-bordered table-hover" >
                <tr class="text-center bg-secondary text-white">
                    <th>交易單號</th>
                    <th>請款狀態</th>
                    <th>退款狀態</th>
                    <th>金額</th>
                    <th>商品資訊</th>
                    <th>保管銀行</th>
                    <th>支付時間</th>
                    <th>處理</th>
                </tr>
                <tr class="text-center" v-for="(credit,index) in credits.data">
                    <td v-text="credit.TradeNo"></td>
                    <td v-text="closeStatus[credit.CloseStatus]"></td>
                    <td v-text="backStatus[credit.BackStatus]"></td>
                    <td v-text="credit.Amt"></td>
                    <td v-text="credit.ItemDesc"></td>
                    <td v-text="credit.EscrowBank"></td>
                    <td v-text="credit.PayTime"></td>
                    <td class="px-0">
                      <a href="javascript:void(0)" @click="credit_request(1,credit.CloseStatus,index)" :class="'btn btn-sm btn-primary d-inline '+((parseInt(credit.CloseStatus)==3)?'disabled':'')" v-text="((parseInt(credit.CloseStatus)==1 || parseInt(credit.CloseStatus)==2)?'取消請款':((parseInt(credit.CloseStatus)==3)?'請款完成':'請款申請'))"></a>
                      <a href="javascript:void(0)" @click="credit_request(2,credit.BackStatus,index)" :class="'btn btn-sm btn-danger d-inline '+((parseInt(credit.BackStatus)==3)?'disabled':'')" v-text="((parseInt(credit.BackStatus)==1 || parseInt(credit.BackStatus)==2)?'取消退款':((parseInt(credit.BackStatus)==3)?'退款完成':'退款申請'))"></a>
                    </td>  
                </tr>
            </table>
            <div class="w-100 d-table py-2 text-center border-top" v-if="credits.last_page>1">
                <a class="btn btn-light btn-sm float-left" v-if="parseInt(credits.current_page) > 1" href="javascript:void(0)" @click="go_content_page(parseInt(credits.current_page-1))">上一頁</a>	
                <span class="h5" v-if="parseInt(credits.current_page) > 1" v-text="credits.current_page"></span>
                <a class="btn btn-light btn-sm float-right" v-if="credits.last_page>credits.current_page" href="javascript:void(0)" @click="go_content_page(parseInt(credits.current_page)+1)">下一頁</a>
            </div>
        </div>
        <!--<div class="w-100" v-if="action=='back_recode'">
        	<form id="mainFrm"  action="/newebPay_return_url" method="post">
        	<input type="text" class="form-control my-1" name="Status" value=""  placeholder="狀態" />
            <input type="text" class="form-control my-1" name="MerchantID" value="MS36097631"  placeholder="商店代號" />
            <input type="text" class="form-control my-1" name="Version" value="1.5"  placeholder="版本" />
        	<textarea class="form-control my-1" name="TradeInfo" placeholder="交易資料 AES 加密"></textarea>
            <input type="text" class="form-control" name="TradeSha" placeholder="交易資料 SHA256 加密" />
            <input type="submit" value="送出" class="btn btn-primary my-2" />
            </form>
        </div>-->
    </div>
    <div class="pb-4" v-if="(action=='test')">
        <form id="mainFrm" action="/admin/transfer_records_pt" method="post">
          @csrf
          <input type="hidden" name="item" v-model="item" />
          <input type="hidden" name="action" value="test" />
          <div class="w-100 text-center">
              <input type="text" class="form-control w-25 mt-5 mx-auto" name="MerchantOrderNo" value="" placeholder="欲查詢的訂單編號" />
              <input type="submit" class="btn btn-primary my-3" value="送出模擬" />
          </div>
        </form>
    </div>          
    <div :class="{ bg_loding: isBg }"></div>
</div>
<script>
new Vue({
  el: "#app",
  data: {
	isBg: true,
	item: '<?php echo ((isset($item))?$item:'')?>',
	action: '<?php echo ((isset($action))?$action:'')?>',
	message: '<?php echo ((isset($message))?$message:'')?>',
	title: '',
	create_transfer: '',
	transfers: '',
	transfer: '',
	invoice: '',
	credits: '',
	search_text: '',
	payment_item: '',
	back_message: '',
	trades: {0:'未付款',1:'付款成功',2:'付款失敗',3:'取消付款'},
	tradeStatus: '',
	storeType: {1:'7-11 統一超商',2:'全家便利商店',3:'OK 便利商店',4:'萊爾富便利商店'},
	payStore: {SEVEN:'7-11',FAMILY:'全家',OK:'OK 超商',HILIFE:'萊爾富'},
	carrierType: {0:'手機條碼',1:'自然人憑證',2:'ezPay平台'},
	taxType: {1:'應稅',2:'零稅率',3:'免稅',9:'混和應稅'},
	closeStatus: {0:'未請款',1:'等待提送請款至收單機構',2:'請款處理中',3:'請款完成'},
	backStatus: {0:'未退款',1:'等待提送退款至收單機構',2:'退款處理中',3:'退款完成'},
	paymentType: {CREDIT:'信用卡',WEBATM:'WebATM',VACC:'ATM 轉帳',CVS:'超商代碼繳費',BARCODE:'超商條碼繳費',CVSCOM:'超商取貨付款'},
  },
  mounted: function () {
  	var self = this;
	self.get_transfer_records();
	if(self.message)
	{
		alert(self.message);
		self.message = '';
	}
	//console.log(self.storeType);
  },
  methods: {
  	get_transfer_records: function(x){
		var self = this;
		self.isBg = true;
		axios.get('/admin/get_transfer_records?item='+self.item+'&action='+self.action).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.create_transfer = response.data.create_transfer;
			self.transfers = response.data.transfers;
			self.title = response.data.title;
			self.credits = response.data.credits;
			
			if(self.message)
				alert(self.message);
			
			self.isBg = false;			
		});
	},
	sendTransferAdd: function(){
		var self = this;
		var chk = 1;
		if(!self.create_transfer.Email || self.back_message || !self.chk_mail(self.create_transfer.Email))
		{
			$("#Email").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(0);
			chk = 0;	
		}else
			$("#Email").css({"border":"1px solid #ccc"});
			
		if(!self.create_transfer.MerchantOrderNo)
		{
			$("#MerchantOrderNo").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(0);
			chk = 0;	
		}else
			$("#MerchantOrderNo").css({"border":"1px solid #ccc"});	
			
		if(!self.create_transfer.Amt)
		{
			$("#Amt").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(0);
			chk = 0;	
		}else
			$("#Amt").css({"border":"1px solid #ccc"});	
			
		if(!self.create_transfer.ItemDesc)
		{
			$("#ItemDesc").css({"border":"1px solid #a02"});
			chk = 0;	
		}else
			$("#ItemDesc").css({"border":"1px solid #ccc"});		
			
		if(chk && confirm('確定要送出此筆交易新增?'))
			$("#mainFrm").submit();
	},
	getThisData: function(x){
		var self = this;
		self.isBg = true;
		axios.get('/admin/get_transfer_records?item='+self.item+'&action=detail&id='+x).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.transfer = response.data.transfer;
			self.invoice = response.data.invoice;
			self.title = '交易單詳細資料';
			self.action = 'detail';
			self.isBg = false;
			
			if(response.data.message)
				alert(response.data.message);
			
		});
	},
	searchBtn: function(){
		var self = this;
		self.isBg = true;
		self.action = 'search';
		axios.get('/admin/get_transfer_records?item='+self.item+'&action='+self.action+'&text='+self.search_text+'&tradeStatus='+self.tradeStatus).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.transfers = response.data.transfers;
			self.invoice_detail = '';
			self.title = response.data.title;
			self.isBg = false;
			
		});
	},
	chk_mail: function(value){
		var mail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return mail.test(value);
	},
	email_get_account: function(){
		var self = this;
		if(!self.create_transfer.Email || !self.chk_mail(self.create_transfer.Email))
			$("#Email").css({"border":"1px solid #a02"});
		else
		{
			axios.get('/admin/email_get_account?id='+self.create_transfer.Email).then(function (response){
				console.log(response.data)
				if(response.data=='error')
					window.location = '/error';
				
				if(response.data.buyer)
				{
					self.create_transfer.Email = response.data.buyer.email;
					self.create_transfer.BuyerName = response.data.buyer.last_name+response.data.buyer.first_name;
					self.create_transfer.usr_id = response.data.buyer.usr_id;
					$("#Email").css({"border":"1px solid #ccc"})
					self.back_message = '';
				}else
				{
					self.back_message = '查無此會員資料!!';
					self.create_transfer.BuyerName = '';
					$("#Email").css({"border":"1px solid #a02"});
				}
			});
				
		}
	},
	go_content_page: function(page){
	  
		var self = this;
		axios.get('/admin/get_transfer_records?item='+self.item+'&action='+self.action+'&text='+self.search_text+'&tradeStatus='+self.tradeStatus+'&page='+page).then(function (response){
			console.log(response.data)		
			self.transfers = response.data.transfers;
			
	   })
	},
	delthis: function(x,y){
		if(confirm('確定要刪除此筆資料?'))
		{
			var self = this;
			axios.get('/admin/get_transfer_records?item='+self.item+'&action=del&id='+x+'&TradeStatus='+y).then(function (response){
				console.log(response.data)		
				
				self.action = 'manage';
				self.transfers = response.data.transfers;
		  })
		}
	},
	cancelthis: function(x){
		if(confirm('確定要取消此筆授權資料?'))
		{
			this.isBg = true;
			$("#mainFrm").submit();
		}
	},
	credit_request: function(indexType,status,index){
		var self = this;
		var txt = '';
		$("#MerchantOrderNo").val(self.credits.data[index].MerchantOrderNo) ;
		$("#TradeNo").val(self.credits.data[index].TradeNo);
		$("#Amt").val(self.credits.data[index].Amt) ;
		
		if(indexType==1)
		{
			if(parseInt(status)==0)
				txt = '請款申請?';
			else
			{
				txt = '取消請款?';
				$("#Cancel").val(1);
			}
			$("#CloseType").val(1);
					
		}
		if(indexType==2)
		{
			if(parseInt(status)==0)
				txt = '退款申請?';
			else
			{
				txt = '取消退款?';
				$("#Cancel").val(1)
			}
			$("#CloseType").val(2)
		}
		if(confirm('請定要送出此'+txt))
		{
			self.isBg = true;
			$("#mainFrm").submit();	
		}
	}
	
  }
  
})
</script>   
@stop