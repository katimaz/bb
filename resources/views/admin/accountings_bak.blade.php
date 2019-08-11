@extends('admin.master')
@section('content')
<div id="app">
	<!----系統帳務------------------------------------------->
    <div class="w-100" v-if="item=='systemAccount'">
    	<div class="w-100 p-2 d-table">
            <div class="w-25 float-left text-left">
                <a class="btn btn-primary pull-right" v-if="action!='create' && (invoice_detail || invoice_detail || allowances)" href="javascript:void(0)" @click="invoice_detail='';action='manage'" v-text="'返回'"></a>
            </div>    
            <div class="w-50 float-left mx-auto" v-if="action!='create'">
                <input type="text" class="form-control float-left w-75" id="search" v-model="search_text" @keyup.enter="searchBtn" placeholder="搜尋發票任一字串" />
                <a href="javascript:void(0)" @click="searchBtn" class="btn btn-primary ml-1">搜尋</a>
            </div>
        	<h3 class="float-right" v-text="title"></h3>
        </div>
        <!----新增合作商店------------------------------------------->
        <div class="w-100" v-if="action=='merchant_create'">
        	<form id="mainFrm"  action="/admin/accountings_pt" method="post">
              @csrf
              <input type="hidden" name="item" v-model="item" />
              <input type="hidden" name="action" v-model="action" />
              <input type="hidden" name="usr_id" v-model="merchant.usr_id" />
              <input type="hidden" name="MerchantID" value="<?php echo 'BOB'.date('ymdHis')?>" />
              <input type="hidden" name="mode" :value="((is_edit)?'edit':'add')" />
              <input type="hidden" name="PaymentType" id="PaymentType" value="" />
              <table class="table table-light table-bordered" >
                  <tr v-if="!is_edit">
                    <th class="w-25 text-center align-middle">申請類別</th>
                    <td class="w-75">
                        <div class="d-inline">
                        	<input type="radio" name="MerchantClass" id="m1" value="1" v-model="merchant.MerchantClass"  />
                            <label for="m1" class="ml-1">個人</label>
                        </div>
                        <div class="d-inline ml-2">
                        	<input type="radio" name="MerchantClass" id="m2" value="2" v-model="merchant.MerchantClass" />
                            <label for="m2" class="ml-1">企業</label>
                        </div>
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center align-middle">管理者 E-mail</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control d-inline" @blur="email_get_account" @keyup.enter="email_get_account"  name="ManagerEmail" id="ManagerEmail" v-model="merchant.ManagerEmail" placeholder="填寫好幫手Email" maxlength="40" />
                        <span class="ml-2 d-inline text-danger" id="back_message" v-text="back_message"></span>
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center align-middle" v-text="((parseInt(merchant.MerchantClass)==2)?'公司登記之名稱':'會員姓名')"></th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control" name="MemberName" id="MemberName" v-model="merchant.MemberName" placeholder="合作商店名稱" maxlength="60" />
                     </td>
                  </tr>
                  <tr v-if="!is_edit">
                    <th class="w-25 text-center align-middle">商店證號</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control d-inline" name="MemberUnified" id="MemberUnified" v-model="merchant.MemberUnified" :placeholder="((parseInt(merchant.MerchantClass)==1)?'填寫個人身份證字號':'填寫公司統一編號')" />
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center align-middle">聯絡電話</th>
                    <td class="w-75">
                        <input type="tel" class="form-control d-inline" name="telHead" id="telHead" v-model="merchant.MemberPhone.head" style="width:100px;" placeholder="0x 或 09xx" maxlength="4" />
                        <b class="mx-1 d-inline">-</b>
                        <input type="tel" class="w-25 form-control d-inline" name="telValue" id="telValue" v-model="merchant.MemberPhone.value" placeholder="市話或手機" maxlength="8" />
                     </td>
                  </tr>
                  <tr v-if="parseInt(merchant.MerchantClass)==2 && !is_edit">
                    <th class="w-25 text-center align-middle">企業代表人身分</th>
                    <td class="w-75">
                        <div class="float-left" style="width:120px;">
                            <select class="form-control" @change="change_manager_id" name="Manager_id" id="Manager_id" v-model="merchant.ManagerID.ID">
                                <option value="" v-text="'選擇項目'"></option>
                                <option value="1" v-text="'身分證字號'"></option>
                                <option value="2" v-text="'居留證號'"></option>
                                <option value="3" v-text="'稅籍編號'"></option>
                            </select>
                        </div>
                        <div class="w-25 float-left ml-2">
                            <input type="text" class="form-control" name="Manager_id_number" id="Manager_id_number" v-model="merchant.ManagerID.Number" placeholder="請先選擇項目" maxlength="18" />
                        </div>
                     </td>
                  </tr>
                  <tr v-if="parseInt(merchant.MerchantClass)==1 && !is_edit">
                    <th class="w-25 text-center align-middle">身份證發證日期</th>
                    <td class="w-75">
                        <select class="form-control d-inline" name="ID_year" id="ID_year" style="width:100px;" v-model="select_date.year">
                        	<option v-for="y in parseInt(Year)" v-if="y>30" :value="y" v-text="y"></option>
                        </select>
                        <select class="form-control d-inline" name="ID_month" id="ID_month" style="width:80px;" v-model="select_date.month">
                        	<option value="" v-text="'選擇'"></option>
                            <option v-for="m in 12" :value="((m<10)?'0'+m:m)" v-text="((m<10)?'0'+m:m)"></option>
                        </select>
                        <select class="form-control d-inline" name="ID_day" id="ID_day" style="width:80px;" v-model="select_date.day">
                        	<option value="" v-text="'選擇'"></option>
                            <option v-for="d in 31" :value="((d<10)?'0'+d:d)" v-text="((d<10)?'0'+d:d)"></option>
                        </select>
                     </td>
                  </tr>
                  <tr v-if="parseInt(merchant.MerchantClass)==1 && !is_edit">
                    <th class="w-25 text-center align-middle">身份證發證地點</th>
                    <td class="w-75">
                        <select class="w-25 form-control d-inline" name="IDCardPlace" id="IDCardPlace" v-model="merchant.IDCardPlace">
                        	<option value="" v-text="'選擇'"></option>
                            <option v-for="(area,key,index) in min_areas" :value="key" v-text="key+' - '+area"></option>
                        </select>
                     </td>
                  </tr>
                  <tr v-if="parseInt(merchant.MerchantClass)==1 && !is_edit">
                    <th class="w-25 text-center align-middle">身分證有否照片</th>
                    <td class="w-75">
                        <select class="w-25 form-control d-inline" name="IDPic" id="IDPic" v-model="merchant.IDPic">
                        	<option value="" v-text="'選擇'"></option>
                            <option value="0" v-text="'有照片'" :select="parseInt(merchant.IDPic)===0"></option>
                            <option value="1" v-text="'無照片'" :select="parseInt(merchant.IDPic)===1"></option>
                        </select>
                     </td>
                  </tr>
                  <tr v-if="parseInt(merchant.MerchantClass)==1 && !is_edit">
                    <th class="w-25 text-center align-middle">身分證領補換</th>
                    <td class="w-75">
                        <select class="w-25 form-control d-inline" name="IDFrom" id="IDFrom" v-model="merchant.IDFrom">
                        	<option value="" v-text="'選擇'"></option>
                            <option value="1" v-text="'初發'" :select="parseInt(merchant.IDFrom)==1"></option>
                            <option value="2" v-text="'補證'" :select="parseInt(merchant.IDFrom)==2"></option>
                            <option value="3" v-text="'換發'" :select="parseInt(merchant.IDFrom)==3"></option>
                        </select>
                     </td>
                  </tr>
                  <tr v-if="!is_edit">
                    <th class="w-25 text-center align-middle">管理者中文姓名</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control d-inline" name="ManagerName" id="ManagerName" v-model="merchant.ManagerName" placeholder="無中文姓名，請填入英文姓名" maxlength="20" />
                     </td>
                  </tr>
                  <tr v-if="!is_edit">
                    <th class="w-25 text-center align-middle">管理者英文姓名</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control d-inline" name="ManagerNameE" id="ManagerNameE" v-model="merchant.ManagerNameE" placeholder="無中文姓名，請填入英文姓名" maxlength="100" />
                     </td>
                  </tr>
                  <tr v-if="!is_edit">
                    <th class="w-25 text-center align-middle">管理者行動電話號碼</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control d-inline" name="ManagerMobile" id="ManagerMobile" v-model="merchant.ManagerMobile" placeholder="格式為10碼數字，例:0912000111" maxlength="10" />
                     </td>
                  </tr>
                  <tr v-if="!is_edit">
                    <th class="w-25 text-center align-middle">商店中文名稱</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control d-inline" name="MerchantName" id="MerchantName" v-model="merchant.MerchantName" placeholder="合作商店中文名稱" maxlength="20" />
                     </td>
                  </tr>
                  <tr v-if="!is_edit">
                    <th class="w-25 text-center align-middle">商店英文名稱</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control d-inline" name="MerchantNameE" id="MerchantNameE" v-model="merchant.MerchantNameE" placeholder="合作商店英文名稱" maxlength="100" />
                     </td>
                  </tr>
                  <tr v-if="!is_edit">
                    <th class="w-25 text-center align-middle">商店網址</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control d-inline" name="MerchantWebURL" id="MerchantWebURL" v-model="merchant.MerchantWebURL" placeholder="合作商店網址" maxlength="100" />
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center align-middle">聯絡地址</th>
                    <td class="w-75">
                        <select class="form-control d-inline" @change="select_city" name="MerchantAddrCity" id="MerchantAddrCity" style="width:100px;" v-model="merchant.MerchantAddrCity">
                        	<option value="" v-text="'選擇'"></option>
                            <option v-for="(city,index) in citys" :value="city" v-text="city"></option>
                        </select>
                        <select class="form-control d-inline" @change="select_nat" name="MerchantAddrArea" id="MerchantAddrArea" style="width:100px;" v-model="merchant.MerchantAddrArea">
                        	<option value="" v-text="'選擇'"></option>
                            <option v-for="(area,key,index) in areas" :value="key" v-text="key"></option>
                        </select>
                        <input class="form-control d-inline text-center" name="MerchantAddrCode" id="MerchantAddrCode" style="width:80px;" v-model="merchant.MerchantAddrCode" placeholder="郵遞區號" maxlength="3" />
                        <input class="w-50 form-control d-block mt-1" name="MerchantAddr" id="MerchantAddr" v-model="merchant.MerchantAddr" placeholder="路名及門牌號碼" maxlength="60" />
                     </td>
                  </tr>
                  <tr v-if="!is_edit">
                    <th class="w-25 text-center align-middle">英文名稱</th>
                    <td class="w-75">
                        <div class="w-25 float-left">
                        	<label class="d-block">設立登記營業國家</label>
                            <input type="text" class="form-control d-inline" name="NationalE" id="NationalE" v-model="merchant.NationalE" placeholder="營業國家英文名稱" maxlength="20" />
                        </div>
                        <div class="w-25 float-left ml-4">
                        	<label class="d-block">設立登記營業城市 </label>
                            <input type="text" class="form-control d-inline" name="CityE" id="CityE" v-model="merchant.CityE" placeholder="營業城市英文名稱" maxlength="20" />
                        </div>
                     </td>
                  </tr>
                  <tr v-if="!is_edit">
                    <th class="w-25 text-center align-middle">販售商品型態</th>
                    <td class="w-75">
                        <select class="w-25 form-control d-inline" name="MerchantType" id="MerchantType" v-model="merchant.MerchantType">
                        	<option value="1" v-text="'實體商品'"></option>
                            <option value="2" v-text="'服務'"></option>
                            <option value="3" v-text="'虛擬商品'"></option>
                            <option value="4" v-text="'票劵'"></option>
                        </select>
                     </td>
                  </tr>
                  <tr v-if="!is_edit">
                    <th class="w-25 text-center align-middle">商品類別</th>
                    <td class="w-75">
                        <select class="w-25 form-control d-inline" name="BusinessType" id="BusinessType" v-model="merchant.BusinessType">
                        	<option value="" v-text="'選擇'"></option>
                            <option v-for="(type,key) in types" :value="key" v-text="type"></option>
                        </select>
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center align-middle">會員金融帳戶</th>
                    <td class="w-75">
                        <div class="float-left" style="width:200px;">
                        	<label class="d-block">金融機構代碼</label>
                            <select class="form-control" name="BankCode" id="BankCode" v-model="merchant.BankCode">
                                <option value="" v-text="'選擇'"></option>
                                <option v-for="(bank,index) in banks" :value="bank.code" v-text="bank.code+' '+bank.name"></option>
                            </select>
                        </div>
                        <div class="float-left ml-3" style="width:120px;">
                        	<label class="d-block">分行代碼</label>
                            <input type="text" class="form-control" name="SubBankCode" id="SubBankCode" v-model="merchant.SubBankCode" placeholder="分行代碼" maxlength="4" />
                        </div>
                        <div class="float-left ml-3" style="width:280px;">
                        	<label class="d-block">帳號</label>
                            <input type="text" class="form-control" name="BankAccount" id="BankAccount" v-model="merchant.BankAccount" placeholder="會員金融機構帳戶" maxlength="14" />
                        </div>
                     </td>
                  </tr>
                  <tr v-if="merchant.PaymentType">
                    <th class="w-25 text-center align-middle">啟用支付方式</th>
                    <td class="w-75">
                        <div class="float-left mr-3" v-for="(payment,key,index) in paymentType">
                        	<input type="checkbox" name="payType" :value="key" :id="'p'+index" v-model="merchant.PaymentType[key]" />
                            <label :for="'p'+index" v-text="payment"></label>
                        </div>	
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center align-middle">信用卡自動請款</th>
                    <td class="w-75">
                        <div class="float-left mr-3">
                        	<input type="radio" name="CreditAutoType" value="1" id="c1" v-model="merchant.CreditAutoType" />
                            <label for="c1" v-text="'自動請款'"></label>
                        </div>
                        <div class="float-left mr-3">
                        	<input type="radio" name="CreditAutoType" value="0" id="c0" v-model="merchant.CreditAutoType" />
                            <label for="c0" v-text="'手動請款'"></label>
                        </div>
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center align-middle">信用卡30天收款額度</th>
                    <td class="w-75">
                        <select class="w-25 form-control" name="CreditLimit" id="CreditLimit" v-model="merchant.CreditLimit">
                            <option value="50000" v-text="'5萬元'"></option>
                            <option value="100000" v-text="'10萬元'"></option>
                            <option v-if='150000<=MaxLimit' value="150000" v-text="'15萬元'"></option>
                            <option v-if='200000<=MaxLimit' value="200000" v-text="'20萬元'"></option>
                            <option v-if='300000<=MaxLimit' value="300000" v-text="'30萬元'"></option>
                            <option v-if='400000<=MaxLimit' value="400000" v-text="'40萬元'"></option>
                            <option v-if='500000<=MaxLimit' value="500000" v-text="'50萬元'"></option>
                        </select>
                     </td>
                  </tr>
                  <!--<tr v-if="merchant.PaymentType">
                    <th class="w-25 text-center align-middle">交易手續費</th>
                    <td class="w-75">
                        <div class="w-100 pb-1" v-for="(payment,key,index) in paymentType" v-if="merchant.PaymentType[key]">
                        	<label class="w-25 text-center" v-text="payment"></label>
                            <input type="tel" onchange="if(isNaN(value))value=4; if(value>8)value=8; if(value<2.8)value=2.8" class="form-control d-inline text-center" :name="'AgreedFee_'+key" :value="merchant.AgreedFee[key]" style="width:80px;" />
                            <b class="ml-2">%</b>
                        </div>
                     </td>
                  </tr>
                  <tr v-if="merchant.PaymentType">
                    <th class="w-25 text-center align-middle">撥款天數</th>
                    <td class="w-75">
                        <div class="w-100 pb-1" v-for="(payment,key,index) in paymentType" v-if="merchant.PaymentType[key]">
                        	<label class="w-25 text-center" v-text="payment"></label>
                            <input type="tel" onchange="if(isNaN(value))value=7; if(value>30)value=30; if(value<3)value=3" class="form-control d-inline text-center" :name="'AgreedDay_'+key" :value="merchant.AgreedDay[key]" style="width:80px;" />
                            <b class="ml-2">天</b>
                        </div>
                     </td>
                  </tr>-->
                  <tr v-if="!is_edit">
                    <th class="w-25 text-center align-middle">商店簡介</th>
                    <td class="w-75">
                        <textarea class="form-control" name="MerchantDesc" id="MerchantDesc" v-model="merchant.MerchantDesc" placeholder="商店簡介 字數為255字以內" maxlength="255"></textarea>
                        	
                     </td>
                  </tr>
              </table>
              <div class="w-100 py-3 text-center">
                <a href="javascript:void(0)" v-if="is_edit" class="btn btn-success" @click="sendMerchantEdit" v-text="'送出修改'"></a>
                <a href="javascript:void(0)" v-if="!is_edit" class="btn btn-primary" @click="sendMerchantAdd" v-text="'送出新增'"></a>
              </div>
           </form>     
        </div>
        <!--------合作商店管理------------------------------------------------->
        <div class="w-100" v-if="action=='merchant_manager'">
        	<table class="table table-light table-bordered table-hover" >
                <tr class="text-center bg-secondary text-white">
                    <th>類別</th>
                    <th>名稱</th>
                    <th>聯絡電話</th>
                    <th>商店代號</th>
                    <th>商店名稱</th>
                    <th>建立日期</th>
                </tr>
                <tr class="text-center" v-for="(merchant,index) in merchants.data" @click="getThisMerchant(index)" style="cursor:pointer">
                    <td v-text="((merchant.MerchantClass==1)?'個人':'企業')"></td>
                    <td v-text="merchant.MemberName"></td>
                    <td v-text="merchant.MemberPhone.head+'-'+merchant.MemberPhone.value"></td>
                    <td v-text="merchant.MerchantID"></td>
                    <td v-text="merchant.MerchantName"></td>
                    <td v-text="merchant.created_at"></td>
                </tr>
            </table>
        </div>
    </div>
	
    <!----發票字軌-------------------------------------------->
    <div class="w-100" v-if="item=='invoice_tracks'">
        <div class="w-100 p-2 d-table">
            <a class="btn btn-primary pull-right" v-if="action!='create' && invoice_track" href="javascript:void(0)" @click="get_accountings()" v-text="'返回'"></a>
            <div class="w-50 float-left h4" v-if="action=='manage' && !invoice_track">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="Year" @change="get_accountings" :value="today.getFullYear()-1911" v-model="Year" id="y1">
                    <label class="form-check-label" for="y1" v-text="(today.getFullYear()-1911)+'年'"></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="Year" @change="get_accountings" :value="today.getFullYear()-1910" v-model="Year" id="y2">
                    <label class="form-check-label" for="y2" v-text="(today.getFullYear()-1910)+'年'"></label>
                </div>
            </div>
            <h3 class="float-right" v-text="title"></h3>
        </div>
        <div class="w-100" v-if="action=='create'">
            <form id="mainFrm"  action="/admin/accountings_pt" method="post">
              @csrf
              <input type="hidden" name="item" v-model="item" />
              <input type="hidden" name="action" v-model="action" />
              <table class="table table-light table-bordered" >
                  <tr>
                    <th class="w-25 text-center">發票年度</th>
                    <td class="w-75">
                        <div class="w-100 p-1" id="YearDiv">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Year" :value="today.getFullYear()-1911" v-model="invoice_track.Year" id="c" >
                                <label class="form-check-label" for="c" v-text="(today.getFullYear()-1911)+'年'"></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Year" :value="today.getFullYear()-1910" v-model="invoice_track.Year" id="h">
                                <label class="form-check-label" for="h" v-text="(today.getFullYear()-1910)+'年'"></label>
                            </div>
                        </div>
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">發票期別</th>
                    <td class="w-75">
                        <div class="w-100 p-1" id="TermDiv">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Term" value="1" v-model="invoice_track.Term" id="t1" >
                                <label class="form-check-label" for="t1" v-text="'一、二月'"></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Term" value="2" v-model="invoice_track.Term" id="t2" >
                                <label class="form-check-label" for="t2" v-text="'三、四月'"></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Term" value="3" v-model="invoice_track.Term" id="t3" >
                                <label class="form-check-label" for="t3" v-text="'五、六月'"></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Term" value="4" v-model="invoice_track.Term" id="t4" >
                                <label class="form-check-label" for="t4" v-text="'七、八月'"></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Term" value="5" v-model="invoice_track.Term" id="t5" >
                                <label class="form-check-label" for="t5" v-text="'九、十月'"></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Term" value="6" v-model="invoice_track.Term" id="t6" >
                                <label class="form-check-label" for="t6" v-text="'十一、十二月'"></label>
                            </div>
                        </div>
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">字軌英文代碼</th>
                    <td class="w-75">
                        <input type="text" class="form-control text-center" @blur="upper" name="AphabeticLetter" id="AphabeticLetter" v-model="invoice_track.AphabeticLetter" placeholder="兩碼大寫英文" style="max-width:100px;" >
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">發票號碼</th>
                    <td class="w-75">
                        <div class="w-25 float-left">
                            <span class="text-primary">起始號碼</span>
                            <input type="text" class="w-100 form-control" name="StartNumber" id="StartNumber" v-model="invoice_track.StartNumber" placeholder="發票起始號碼 如：00000001" />
                        </div>
                        <div class="w-25 float-left ml-3">
                            <span class="text-primary">結束號碼</span>
                            <input type="text" class="w-100 form-control" name="EndNumber" id="EndNumber" v-model="invoice_track.EndNumber" placeholder="發票結束號碼 如：00009999
    " />
                        </div>
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">發票類別</th>
                    <td class="w-75">
                        <div class="w-100 p-1" id="TypeDiv">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Type" value="07" v-model="invoice_track.Type" id="c1" >
                                <label class="form-check-label" for="c1" v-text="'一般稅額'"></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Type" value="08" v-model="invoice_track.Type" id="h1">
                                <label class="form-check-label" for="h1" v-text="'特種稅額'"></label>
                            </div>
                        </div>
                     </td>
                  </tr>
              </table>    
              <div class="w-100 py-3 text-center">
                <a href="javascript:void(0)" class="btn btn-primary" @click="sendFormAdd" v-text="'新增字軌'"></a>
              </div>
            </form>
        </div>
        <div class="w-100" v-if="action=='manage' && invoice_track.ManagementNo">
            <form id="mainFrm"  action="/admin/accountings_pt" method="post">
              @csrf
              <input type="hidden" name="item" v-model="item" />
              <input type="hidden" name="action" v-model="action" />
              <input type="hidden" name="Year" v-model="invoice_track.Year" />
              <input type="hidden" name="ManagementNo" v-model="invoice_track.ManagementNo" />
              <table class="table table-light table-bordered" >
                  <tr>
                    <th class="w-25 text-center">詳細資料</th>
                    <td class="w-75">
                        <div class="w-100" v-text="'年度 : '+invoice_track.Year"></div>
                        <div class="w-100" v-text="'月份 : '+invoice_track.Term"></div>
                        <div class="w-100" v-text="'字軌英文代碼 : '+invoice_track.AphabeticLetter"></div>
                        <div class="w-100" v-text="'起始號碼 : '+invoice_track.StartNumber"></div>
                        <div class="w-100" v-text="'結束號碼 : '+invoice_track.EndNumber"></div>
                   </td>
                  </tr>
                  
                  <tr>
                    <th class="w-25 text-center">字軌狀態</th>
                    <td class="w-75">
                        <div class="w-100 p-1" id="FlagDiv">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Flag" value="0" v-model="invoice_track.Flag" id="f1" >
                                <label class="form-check-label" for="f1" v-text="'暫停'"></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Flag" value="1" v-model="invoice_track.Flag" id="f2" >
                                <label class="form-check-label" for="f2" v-text="'啟用'"></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Flag" value="2" v-model="invoice_track.Flag" id="f3" >
                                <label class="form-check-label" for="f3" v-text="'停止'"></label>
                            </div>
                        </div>
                     </td>
                  </tr>
              </table>    
              <div class="w-100 py-3 text-center">
                <a href="javascript:void(0)" class="btn btn-primary" @click="sendFormEdit" v-text="'修改字軌'"></a>
              </div>
            </form>
        </div>
        <div class="w-100" v-if="invoice_tracks && !invoice_track.ManagementNo">
            <table class="table table-light table-bordered table-hover" >
                <tr class="text-center bg-secondary text-white">
                    <th>年度</th>
                    <th>月份</th>
                    <th>代碼</th>
                    <th>起始編號</th>
                    <th>結束編號</th>
                    <th>剩餘數量</th>
                    <th>稅別</th>
                    <th>狀態</th>
                </tr>
                <tr class="text-center" v-for="(invoice_track,index) in invoice_tracks" @click="getThisData(index)" style="cursor:pointer">
                    <td v-text="invoice_track.Year"></td>
                    <td v-text="invoice_track.Term"></td>
                    <td v-text="invoice_track.AphabeticLetter"></td>
                    <td v-text="invoice_track.StartNumber"></td>
                    <td v-text="invoice_track.EndNumber"></td>
                    <td v-text="invoice_track.LastNumber"></td>
                    <td v-text="invoice_track.Type"></td>
                    <td v-html="invoice_track.status"></td>  
                </tr>
            </table>      	
        </div>
    </div>
    
    <!--------手動開立發票--------------------------------->
    <div class="w-100" v-if="item=='invoices'"> 
    	<div class="w-100 p-2 d-table">
            <div class="w-25 float-left text-left">
                <a class="btn btn-primary pull-right" v-if="action!='create' && (invoice_detail || invoice_detail || allowances)" href="javascript:void(0)" @click="invoice_detail='';action='manage'" v-text="'返回'"></a>
            </div>    
            <div class="w-50 float-left mx-auto" v-if="action!='create'">
                <input type="text" class="form-control float-left w-75" id="search" v-model="search_text" @keyup.enter="searchBtn" placeholder="搜尋發票任一字串" />
                <a href="javascript:void(0)" @click="searchBtn" class="btn btn-primary ml-1">搜尋</a>
            </div>
        	<h3 class="float-right" v-text="title"></h3>
        </div>
        
        <div class="w-100" v-if="action=='create'">
            <form id="mainFrm"  action="/admin/accountings_pt" method="post">
              @csrf
              <input type="hidden" name="item" v-model="item" />
              <input type="hidden" name="action" v-model="action" />
              <input type="hidden" name="u_id" v-model="create_invoice.usr_id" />
              <input type="hidden" name="CarrierType" v-model="create_invoice.CarrierType" />
              <input type="hidden" name="PrintFlag" v-model="create_invoice.PrintFlag" />
              <input type="hidden" name="Status" v-model="create_invoice.Status" />
              <input type="hidden" name="TaxType" value="1" />
              <input type="hidden" name="TaxRate" value="5" />
              <input type="hidden" name="count" v-model="count" />
              <table class="table table-light table-bordered" >
                  <tr>
                    <th class="w-25 text-center">Email</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control d-inline" @blur="email_get_account" @keyup.enter="email_get_account"  name="BuyerEmail" id="BuyerEmail" v-model="create_invoice.BuyerEmail" placeholder="填寫好幫手Email" />
                        <span class="ml-2 d-inline text-danger" id="back_message" v-text="back_message"></span>
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">好幫手名稱</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control" name="BuyerName" id="BuyerName" v-model="create_invoice.BuyerName" placeholder="好幫手名稱" />
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">訂單編號</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control d-inline" name="MerchantOrderNo" id="MerchantOrderNo" v-model="create_invoice.MerchantOrderNo" placeholder="填寫訂單編號" />
                        <a href="javascript:void(0)" @click="today=new Date();create_invoice.MerchantOrderNo=today.getTime()" class="btn btn-sm btn-primary d-inlie" v-text="'自動產生'"></a>
                     </td>
                  </tr>
                  <tr v-if="id">
                    <th class="w-25 text-center">金流交易編號</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control d-inline" name="TransNum" id="TransNum" v-model="create_invoice.TransNum" readonly="readonly" placeholder="如已有金流交易資料，請填寫交易編號!" />
                        
                     </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">發票種類</th>
                    <td class="w-75">
                        <div class="w-100 p-1" id="YearDiv">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Category" value="B2C" v-model="create_invoice.Category" id="c1" @change="create_invoice.PrintFlag='N';create_invoice.CarrierType='2'">
                                <label class="form-check-label" for="c1" v-text="'買受人為個人'"></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Category" value="B2B" @change="create_invoice.PrintFlag='Y';create_invoice.CarrierType=''" v-model="create_invoice.Category" id="c2" >
                                <label class="form-check-label" for="c2" v-text="'買受人為營業公司'"></label>
                            </div>
                        </div>
                     </td>
                  </tr>
                  <tr v-if="create_invoice.Category=='B2B'">
                    <th class="w-25 text-center">統一發票號碼</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control" name="BuyerUBN" id="BuyerUBN" v-model="create_invoice.BuyerUBN" placeholder="請輸入營業統一編號" />
                     </td>
                  </tr>
                  <tr v-if="create_invoice.Category=='B2B'">
                    <th class="w-25 text-center">發票寄送地址</th>
                    <td class="w-75">
                        <input type="text" class="w-50 form-control" name="BuyerAddress" id="BuyerAddress" v-model="create_invoice.BuyerAddress" placeholder="請輸入紙本寄送地址" />
                     </td>
                  </tr>
                  
                  <tr>
                    <th class="w-25 text-center">銷項內容</th>
                    <td class="w-75">
                        <div class="w-100">
                            <ul class="row py-1 text-center border-bottom">
                                <li class="col-sm-2">品名</li>
                                <li class="col-sm-2">數量</li>
                                <li class="col-sm-2">單位</li>
                                <li class="col-sm-2">單價</li>
                                <li class="col-sm-2">金額</li>
                                <li class="col-sm-2">處理</li>
                            </ul>
                            <ul class="row text-center" v-for="(detail,index) in create_invoice.details">
                                <li class="col-sm-2 py-0 px-1"><input type="text" class="form-control py-1 text-center" :name="'ItemName'+index" :id="'ItemName'+index" v-model="detail.ItemName" placeholder="填寫品名" /></li>
                                <li class="col-sm-2 py-0 px-1"><input type="text" @input="change_price(index)" class="form-control py-1 text-center" :name="'ItemCount'+index" :id="'ItemCount'+index" v-model="detail.ItemCount" placeholder="填寫數量" /></li>
                                <li class="col-sm-2 py-0 px-1">
                                    <select class="form-control text-center" :name="'ItemUnit'+index" :id="'ItemUnit'+index" v-model="detail.ItemUnit">
                                        <option value="" v-text="'選擇'">
                                        <option :value="unit" v-for="unit in units" v-text="unit">
                                    </select>
                                <li class="col-sm-2 py-0 px-1"><input type="text" @input="change_price(index)" class="form-control py-1 text-center" :name="'ItemPrice'+index" :id="'ItemPrice'+index" v-model="detail.ItemPrice" placeholder="填寫單價金額" /></li>
                                <li class="col-sm-2 py-0 px-1"><input type="text" class="form-control py-1 text-center" :name="'ItemAmt'+index" v-model="detail.ItemAmt" placeholder="小計金額" readonly="readonly" /></li>
                                <li class="col-sm-2 py-0 px-1"><a href="javascript:void(0)" @click="clickBtn(index)" :class="'btn btn-sm btn-'+((index==0)?'primary':'danger')" v-text="((index==0)?'增加一列':'減少此列')"></a></li>
                            </ul>
                        </div>
                        <div class="w-100 py-3"><span>※應稅</span><span class="ml-3">※稅率:5%(內含)</span></div>
                        <div class="w-100 py-3">
                            <ul class="row">
                                <li class="col-sm-2 pt-1 text-center" style="background-color:#eee">稅額 :</li>
                                <li class="col-sm-10"><input type="tel" class="w-25 form-control py-1" name="TaxAmt" v-model="create_invoice.TaxAmt" /></li>
                            </ul>
                            <ul class="row">
                                <li class="col-sm-2 pt-1 text-center" style="background-color:#eee">銷售額總計 :</li>
                                <li class="col-sm-10"><input type="tel" class="w-25 form-control py-1" name="Amt" v-model="create_invoice.Amt" /></li>
                            </ul>
                            <ul class="row">
                                <li class="col-sm-2 pt-1 text-center" style="background-color:#eee">總計 :</li>
                                <li class="col-sm-10"><input type="tel" class="form-control py-1" name="TotalAmt" v-model="create_invoice.TotalAmt" readonly="readonly" style="width:300px;" /></li>
                            </ul>
                            <ul class="row">
                                <li class="col-sm-2 pt-3 text-center" style="background-color:#eee">備註 :</li>
                                <li class="col-sm-10"><textarea class="form-control" name="Comment"></textarea></li>
                            </ul>
                        </div>
                     </td>
                  </tr>
              </table>    
              <div class="w-100 py-3 text-center">
                <a href="javascript:void(0)" class="btn btn-primary" @click="sendInvoiceAdd" v-text="'送出新增發票'"></a>
              </div>
            </form>
        </div>
        
        <div class="pb-4" v-if="(!action || action=='manage' || action=='search') && invoice_detail">
            <table class="table table-light table-bordered">
                <tr>
                    <th class="text-center">狀態 :</th>
                    <td v-text="((parseInt(invoice_detail.InvoiceStatus)==1)?'開立':'作廢')"></td>
                    <th class="text-center">上傳財政部 :</th>
                    <td v-text="uploadStatus[invoice_detail.UploadStatus]"></td>
                </tr>
                <tr>
                    <th class="text-center">購買者 :</th>
                    <td v-text="invoice_detail.BuyerName"></td>
                    <th class="text-center">發票號碼 :</th>
                    <td v-text="invoice_detail.InvoiceNumber"></td>
                </tr>
                <tr>
                    <th class="text-center">訂單編號 :</th>
                    <td v-text="invoice_detail.MerchantOrderNo"></td>
                    <th class="text-center">金流平台編號 :</th>
                    <td v-text="invoice_detail.TransNum"></td>
                </tr>
                <tr>
                    <th class="text-center">發票種類 :</th>
                    <td v-text="invoice_detail.Category"></td>
                    <th class="text-center">統一編號 :</th>
                    <td v-text="invoice_detail.BuyerUBN"></td>
                </tr>
                <tr>
                    <th class="text-center">金額 :</th>
                    <td v-text="invoice_detail.TotalAmt"></td>
                    <th class="text-center">課稅別 :</th>
                    <td v-text="invoice_detail.TaxType"></td>
                </tr>

                <tr>
                    <th class="text-center">稅率 :</th>
                    <td v-text="invoice_detail.TaxRate"></td>
                    <th class="text-center">開立發票時間 :</th>
                    <td v-text="invoice_detail.CreateTime"></td>
                </tr>
                <tr>
                    <th class="text-center">銷項內容 :</th>
                    <td colspan="3">
                        <div class="w-100">
                            <ul class="row py-1 text-center border-bottom">
                                <li class="col-sm-2">品名</li>
                                <li class="col-sm-2">數量</li>
                                <li class="col-sm-2">單位</li>
                                <li class="col-sm-2">單價</li>
                                <li class="col-sm-2">金額</li>
                            </ul>
                            <ul class="row text-center" v-for="(detail,index) in invoice_detail.ItemDetails">
                                <li class="col-sm-2 py-0 px-1" v-text="detail.ItemName"></li>
                                <li class="col-sm-2 py-0 px-1" v-text="detail.ItemCount"></li>
                                <li class="col-sm-2 py-0 px-1" v-text="detail.ItemWord"></li>
                                <li class="col-sm-2 py-0 px-1" v-text="detail.ItemPrice"></li>
                                <li class="col-sm-2 py-0 px-1" v-text="detail.ItemAmount"></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-center">Email :</th>
                    <td v-text="invoice_detail.BuyerEmail" colspan="3"></td>
                </tr>
                <tr v-if="invoice_detail.BuyerAddress">
                    <th class="text-center">紙本寄送住址 :</th>
                    <td v-text="invoice_detail.BuyerAddress" colspan="3"></td>
                </tr>
                <tr v-if="invoice_detail.CarrierType">
                    <th class="text-center">載具 :</th>
                    <td v-text="((parseInt(invoice_detail.CarrierType)==2)?'Ezpay電子載具':'')" colspan="3"></td>
                </tr>
                <tr v-if="invoice_detail.CarrierNum">
                    <th class="text-center">載具編號 :</th>
                    <td v-text="invoice_detail.CarrierNum" colspan="3"></td>
                </tr>
                <tr v-if="invoice_detail.LoveCode">
                    <th class="text-center">愛心碼 :</th>
                    <td v-text="invoice_detail.LoveCode" colspan="3"></td>
                </tr>
                <tr>
                    <th class="text-center">折讓</th>
                    <td colspan="3">
                        <a href="javascript:void(0)" v-if="invoice_detail.allowance" @click="confirmAllowance(invoice_detail.InvoiceNumber,invoice_detail.index)" class="btn btn-primary" v-text="'查看折讓'"></a>
                        <span v-if="!invoice_detail.allowance" v-text="'無'"></span>
                    </td>
                </tr>
                <tr v-if="invoice_detail.Comment">
                    <th class="text-center">備註</th>
                    <td v-text="invoice_detail.Comment" colspan="3"></td>
                </tr>
            </table>
            <form id="mainFrm" action="/admin/accountings_pt" method="post">
              @csrf
              <input type="hidden" name="item" v-model="item" />
              <input type="hidden" name="action" value="invalid" />
              <input type="hidden" name="InvoiceNumber" v-model="invoice_detail.InvoiceNumber" />
              <div class="w-100 d-table">
                <a href="javascript:void(0)" v-if="invoice_detail.InvoiceStatus==1" @click="((invalidZone)?invalidZone=0:invalidZone=1)" class="btn btn-secondary text-white float-left" v-text="'發票作廢作業'"></a>
                <div class="w-75 float-left ml-2" v-if="invalidZone && invoice_detail.InvoiceStatus==1">
                    <textarea class="w-75 form-control float-left" name="InvalidReason" id="InvalidReason" v-model="InvalidReason" style="height:40px;" placeholder="填寫作廢原因,限70字內"></textarea>
                    <a href="javascript:void(0)" class="btn btn-primary float-left ml-2" @click="sendInvaild" v-text="'送出作廢'"></a>
                </div>
              </div>
            </form>
        </div>
        
        <!---折讓作業-------------------------------------------------------->
        <div class="pb-4" v-if="(action=='allowance') && invoice_detail">
            <form id="mainFrm" action="/admin/accountings_pt" method="post">
              @csrf
              <input type="hidden" name="item" v-model="item" />
              <input type="hidden" name="action" value="allowance" />
              <input type="hidden" name="TaxType" v-model="invoice_detail.TaxType" />
              <input type="hidden" name="TaxRate" v-model="invoice_detail.TaxRate" />
              <input type="hidden" name="Status" value="0" />
              <input type="hidden" name="BuyerEmail" v-model="invoice_detail.BuyerEmail" />
              <input type="hidden" name="count" v-model="count" />
              <table class="table table-light table-bordered">
                <tr>
                    <th class="w-25 text-center">發票號碼 :</th>
                    <td class="w-75"><input type="text" class="w-50 form-control" name="InvoiceNo" v-model="invoice_detail.InvoiceNumber" readonly="readonly" /></td>
                </tr>
                <tr>
                  <th class="w-25 text-center">訂單編號</th>
                  <td class="w-75">
                      <input type="text" class="w-50 form-control d-inline" name="MerchantOrderNo" id="MerchantOrderNo" v-model="invoice_detail.MerchantOrderNo" placeholder="填寫訂單編號" readonly="readonly" />
                   </td>
                </tr>
                <tr>
                  <th class="w-25 text-center">銷項內容</th>
                  <td class="w-75">
                      <div class="w-100">
                            <ul class="row text-center" v-for="(detail,index) in invoice_detail.ItemDetails">
                                <li class="col-sm-2 py-0 px-1" v-text="detail.ItemName"></li>
                                <li class="col-sm-2 py-0 px-1" v-text="detail.ItemCount"></li>
                                <li class="col-sm-2 py-0 px-1" v-text="detail.ItemWord"></li>
                                <li class="col-sm-2 py-0 px-1" v-text="detail.ItemPrice"></li>
                                <li class="col-sm-2 py-0 px-1" v-text="detail.ItemAmount"></li>
                            </ul>
                        </div>
                   </td>
                </tr>
                <tr>
                    <th class="w-25 text-center">折讓內容</th>
                    <td class="w-75">
                        <div class="w-100">
                            <ul class="row py-1 text-center border-bottom">
                                <li class="col-sm-2">品名</li>
                                <li class="col-sm-1">數量</li>
                                <li class="col-sm-2">單位</li>
                                <li class="col-sm-2">單價</li>
                                <li class="col-sm-2">金額</li>
                                <li class="col-sm-1">稅額</li>
                                <li class="col-sm-2">處理</li>
                            </ul>
                            <ul class="row text-center" v-for="(detail,index) in invoice_detail.details">
                                <li class="col-sm-2 py-0 px-1"><input type="text" class="form-control py-1 text-center" :name="'ItemName'+index" :id="'ItemName'+index" v-model="detail.ItemName" placeholder="填寫品名" /></li>
                                <li class="col-sm-1 py-0 px-1"><input type="text" @input="change_price(index)" class="form-control py-1 text-center" :name="'ItemCount'+index" :id="'ItemCount'+index" v-model="detail.ItemCount" placeholder="填寫數量" /></li>
                                <li class="col-sm-2 py-0 px-1">
                                    <select class="form-control text-center" :name="'ItemUnit'+index" :id="'ItemUnit'+index" v-model="detail.ItemUnit">
                                        <option value="" v-text="'選擇'">
                                        <option :value="unit" v-for="unit in units" v-text="unit">
                                    </select>
                                <li class="col-sm-2 py-0 px-1">
                                    <input type="text" @input="change_price(index)" class="form-control py-1 text-center" :id="'ItemPriceInput'+index" value="0" placeholder="填寫單價金額" />
                                    <input type="hidden" :name="'ItemPrice'+index" :id="'ItemPrice'+index" v-model="detail.ItemPrice" placeholder="填寫單價金額" />
                                </li>
                                <li class="col-sm-2 py-0 px-1"><input type="text" class="form-control py-1 text-center" :name="'ItemAmt'+index" v-model="detail.ItemAmt" placeholder="小計" readonly="readonly" /></li>
                                 <li class="col-sm-1 py-0 px-1"><input type="text" class="form-control py-1 text-center" :name="'ItemTaxAmt'+index" v-model="detail.ItemTaxAmt" placeholder="稅額" readonly="readonly" /></li>
                                <li class="col-sm-2 py-0 px-1"><a href="javascript:void(0)" @click="clickBtn(index)" :class="'btn btn-sm btn-'+((index==0)?'primary':'danger')" v-text="((index==0)?'增加一列':'減少此列')"></a></li>
                            </ul>
                        </div>
                        <div class="w-100 py-3">
                            <ul class="row">
                                <li class="col-sm-2 pt-1 text-center" style="background-color:#eee">折讓總金額 :</li>
                                <li class="col-sm-6"><input type="tel" class="w-25 form-control py-1 d-inline" name="TotalAmt" v-model="invoice_detail.TotalAmt" readonly="readonly" /><span class="d-inline text-danger ml-2" v-text="back_message"></span></li>
                                <li class="col-sm-4 pt-1 text-right" v-text="'可折讓金額 : '+invoice_detail.RemainAmt"> </li>
                            </ul>
                        </div>
                     </td>
                  </tr>
              </table>
              <div class="w-100 py-3 text-center">
                <a href="javascript:void(0)" class="btn btn-primary" @click="sendInvoiceAllowance" v-text="'折讓新增'"></a>
              </div>  
            </form>
        </div>
        
        <!---折讓確認、取消作業----------------------------------------------------------->
        <div class="pb-4" v-if="(action=='confirm') && allowances">
            <form id="mainFrm" action="/admin/accountings_pt" method="post">
              @csrf
              <input type="hidden" name="item" v-model="item" />
              <input type="hidden" name="action" value="confirm" />
              <input type="hidden" name="InvoiceNo" id="InvoiceNo" value />
              <input type="hidden" name="AllowanceStatus" id="AllowanceStatus" value="" />
              <input type="hidden" name="AllowanceNo" id="AllowanceNo" value="" />
              <input type="hidden" name="MerchantOrderNo" id="MerchantOrderNo" value="" />
              <input type="hidden" name="TotalAmt" id="TotalAmt" value="" />
              <table class="table table-light table-bordered table-hover" >
                <tr class="text-center bg-secondary text-white">
                    <th>狀態</th>
                    <th>發票號碼</th>
                    <th>折讓編號</th>
                    <th>折讓內容</th>
                    <th>折讓金額</th>
                    <th>折讓時間</th>
                    <th>處理</th>
                </tr>
                 <tr class="text-center" v-for="(allowance,index) in allowances">
                    <td v-text="((allowance.Status==1)?'已傳出':'未傳出')"></td>
                    <td v-text="allowance.InvoiceNumber"></td>
                    <td v-text="allowance.AllowanceNo"></td>
                    <td v-text="allowance.ItemName+' '+allowance.ItemCount+' '+allowance.ItemUnit+' '+allowance.ItemPrice+' '+allowance.ItemAmt+' '+allowance.ItemTaxAmt"></td>
                    <td v-text="allowance.AllowanceAmt"></td>
                    <td v-text="allowance.created_at"></td>
                    <td>
                        <a href="javascript:void(0)" :class="'btn btn-sm btn-primary d-inline '+((allowance.Status==1)?'disabled':'')" @click="actionBtn('C',index)" v-text="'確認'"></a>
                        <a href="javascript:void(0)" :class="'btn btn-sm btn-danger d-inline '+((allowance.Status==1)?'disabled':'')" @click="actionBtn('D',index)" v-text="'取消'"></a>
                    </td>
                </tr>
              </table>      
           </form>
        </div>         
        
            <div class="w-100" v-if="(!action || action=='manage' || action=='search') && !invoice_detail">
                <table class="table table-light table-bordered table-hover" >
                    <tr class="text-center bg-secondary text-white">
                        <th>狀態</th>
                        <th>發票號碼</th>
                        <th>金流交易序號</th>
                        <th>發票種類</th>
                        <th>買受人</th>
                        <th>紙本</th>
                        <th>交易金額</th>
                        <th>開立時間</th>
                        <th>處理</th>
                    </tr>
                    <tr class="text-center" v-for="(invoice,index) in invoices.data">
                        <td v-text="((invoice.InvoiceStatus==1)?'開立':'作廢')"></td>
                        <td v-text="invoice.InvoiceNumber"></td>
                        <td v-text="invoice.MerchantOrderNo"></td>
                        <td v-text="invoice.Category"></td>
                        <td v-text="invoice.BuyerName"></td>
                        <td v-text="((invoice.PrintFlag)?invoice.PrintFlag:'N')"></td>
                        <td v-text="invoice.TotalAmt"></td>
                        <td v-text="invoice.CreateTime"></td>
                        <td>
                            <a href="javascript:void(0)" class="btn btn-sm btn-success d-inline" @click="getInvoiceData(invoice.InvoiceNumber,index)" v-text="'詳細'"></a>
                            <a href="javascript:void(0)" :class="'btn btn-sm btn-danger d-inline '+((invoice.TotalAmt<invoice.RemainAmt || invoice.InvoiceStatus==2)?'disabled':'')" @click="getAllowance(invoice.InvoiceNumber,index)" v-text="'折讓'"></a>
                            <a href="javascript:void(0)" :class="'btn btn-sm btn-'+((invoice.need_confirm==2)?'dark':'secondary')+' d-inline '+((!invoice.need_confirm)?'disabled':'')" @click="confirmAllowance(invoice.InvoiceNumber,index)" v-text="((invoice.need_confirm==2)?'須確認':((invoice.need_confirm==1)?'有折讓':'無折讓'))"></a>
                        </td>
                    </tr>
                </table>      	
                <div class="w-100 d-table py-2 text-center border-top" v-if="invoices.last_page>1">
                    <a class="btn btn-light btn-sm float-left" v-if="parseInt(invoices.current_page) > 1" href="javascript:void(0)" @click="go_content_page(parseInt(invoices.current_page-1))">上一頁</a>	
                    <span class="h5" v-if="parseInt(invoices.current_page) > 1" v-text="invoices.current_page"></span>
                    <a class="btn btn-light btn-sm float-right" v-if="invoices.last_page>invoices.current_page" href="javascript:void(0)" @click="go_content_page(parseInt(invoices.current_page)+1)">下一頁</a>
                </div>
            </div>
        </div>    
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
	id: '<?php echo ((isset($id))?$id:'')?>',
	status: '<?php echo ((isset($status))?$status:'')?>',
	message: '<?php echo ((isset($message))?$message:'')?>',
	invoice_tracks: '',
	invoice_track: '',
	title: '',
	today: new Date(),
	old_Flag: '',
	Year: '<?php echo date("Y")-1911?>',
	count: 1,

	create_invoice: '',
	invoices: '',
	invoice_detail: '',
	other_lovecode: '',
	sysAccountings: '',
	choose: 1,
	search_text: '',
	units: ['筆','個','支','隻','其他'],
	uploadStatus: {0:'未上傳',1:'已上傳',2:'上傳中',3:'上傳失敗'},
	InvalidReason: '',
	invalidZone: 0,
	back_message: '',
	allowances: '',
	merchants: '',
	merchant: {MemberPhone:{head:'',value:''}},
	select_date: {year:100,month:'',day:''},
	citys: '',
	nats: '',
	areas: '',
	min_areas: '',
	types: '',
	englishs:'',
	banks: '',
	paymentType: {CREDIT:'信用卡',WEBATM:'WebATM',VACC:'ATM 轉帳',CVS:'超商代碼繳費',BARCODE:'超商條碼繳費'},
	is_edit:'',
	MaxLimit: ''
	
  },
  mounted: function () {
  	var self = this;
	if(self.message)
	{
		alert(self.message);
		self.message = '';
	}
	self.get_accountings();
  },
  methods: {
  	get_accountings: function(x){
		var self = this;
		self.isBg = true;
		axios.get('/admin/get_accountings?item='+self.item+'&Year='+self.Year+'&action='+self.action+'&id='+self.id).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			if(self.item=='invoice_tracks')
			{
				self.invoice_tracks = response.data.invoice_tracks;
				self.invoice_track = response.data.single_invoice_track;
			}else if(self.item=='invoices')
			{
				self.create_invoice = response.data.create_invoice;
				self.invoices = response.data.invoices;
			}else if(self.item=='systemAccount')
			{
				if(self.action=='merchant_create')
				{
					self.merchant = response.data.merchant;
					
					if(self.merchant.IDCardDate)
					{
						var length = self.merchant.IDCardDate.length;
						var year,month,day;
						if(length==7)
						{
							year = self.merchant.IDCardDate.substr(0,3);
							month = self.merchant.IDCardDate.substr(3,2);
							day = self.merchant.IDCardDate.substr(5,2)
						}else if(length==6)
						{
							year = self.merchant.IDCardDate.substr(0,2);
							month = self.merchant.IDCardDate.substr(2,2);
							day = self.merchant.IDCardDate.substr(4,2)
						}else if(length==5)
						{
							year = self.merchant.IDCardDate.substr(0,1);
							month = self.merchant.IDCardDate.substr(1,2);
							day = self.merchant.IDCardDate.substr(3,2)
						}
						self.select_date = {year:year,month:month,day:day};
					}
				}else if(self.action=='merchant_manager')
				{
					self.merchants = response.data.merchants; 
				}
				self.citys = response.data.citys;
				self.nats = response.data.nats;
				self.min_areas = response.data.min_areas;
				self.types = response.data.types;
				self.englishs = response.data.englishs;
				self.banks = response.data.banks;
				self.MaxLimit = response.data.MaxLimit;
				if(self.merchant.MerchantAddrCity)
				{
					var index = self.citys.indexOf(self.merchant.MerchantAddrCity);
					self.areas = self.nats[index];
				}
				//console.log(self.nats);
			}
			self.title = response.data.title;
			
			if(self.message)
			{
				alert(self.message);
				self.message = '';
			}
			self.isBg = false;			
		});
	},
	sendFormAdd: function(){
		var self = this;
		var chk = 1;
		if(!self.invoice_track.Year)
		{
			$("#YearDiv").css({"border":"1px solid #a02"});
			chk = 0;
		}else
			$("#YearDiv").css({"border":"1px solid #eee"});
			
		if(!self.invoice_track.Term)
		{
			$("#TermDiv").css({"border":"1px solid #a02"});
			chk = 0;
		}else
			$("#TermDiv").css({"border":"1px solid #eee"});
			
		if(!self.invoice_track.AphabeticLetter || self.invoice_track.AphabeticLetter.length!=2)
		{
			$("#AphabeticLetter").css({"border":"1px solid #a02"});
			chk = 0;
		}else
		{
			$("#AphabeticLetter").css({"border":"1px solid #eee"}).val($("#AphabeticLetter").val().toUpperCase());
		}
		if(!self.invoice_track.StartNumber)
		{
			$("#StartNumber").css({"border":"1px solid #a02"});
			chk = 0;
		}else
			$("#StartNumber").css({"border":"1px solid #eee"});
			
		if(!self.invoice_track.EndNumber)
		{
			$("#EndNumber").css({"border":"1px solid #a02"});
			chk = 0;
		}else
			$("#EndNumber").css({"border":"1px solid #eee"});
			
		if(!self.invoice_track.Type)
		{
			$("#TypeDiv").css({"border":"1px solid #a02"});
			chk = 0;
		}else
			$("#TypeDiv").css({"border":"1px solid #eee"});	
			
		if(chk && confirm('確定要新增此筆資料?'))
		{
			$("#mainFrm").submit();						
		}	
	},
	sendFormEdit: function(){
		var self = this;
		var chk = 1;
		if(!self.invoice_track.Year)
		{
			$("#YearDiv").css({"border":"1px solid #a02"});
			chk = 0;
		}else
			$("#YearDiv").css({"border":"1px solid #eee"});
			
		if(chk && confirm('確定要修改此筆字軌資料?'))
		{
			if(self.invoice_track.Flag==2)
			{
				if(confirm('修改狀態為"停用"，將無法改為啟用，確定?'))
					$("#mainFrm").submit();		
			}else
				$("#mainFrm").submit();						
		}	
	},
	sendInvoiceAdd: function(){
		var self = this;
		var chk = 1;
		if(!self.create_invoice.BuyerEmail || self.back_message)
		{
			$("#BuyerEmail").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(0);
			chk = 0;	
		}else
			$("#BuyerEmail").css({"border":"1px solid #ccc"});
			
		if(!self.create_invoice.MerchantOrderNo)
		{
			$("#MerchantOrderNo").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(0);
			chk = 0;	
		}else
			$("#MerchantOrderNo").css({"border":"1px solid #ccc"});	
				
		if(self.create_invoice.Category=='B2B')
		{
			if(!self.create_invoice.BuyerUBN || isNaN(self.create_invoice.BuyerUBN) || self.create_invoice.BuyerUBN.length!=8)
			{
				$("#BuyerUBN").css({"border":"1px solid #a02"});
				chk = 0;	
			}else
				$("#BuyerUBN").css({"border":"1px solid #ccc"});
		}
		
		for(var i=0;i<self.create_invoice.details.length;i++)
		{
			if(!self.create_invoice.details[i].ItemName)
			{
				$("#ItemName"+i).css({"border":"1px solid #a02"});
				chk = 0;
			}else
				$("#ItemName"+i).css({"border":"1px solid #ccc"});
			
			if(!self.create_invoice.details[i].ItemCount)
			{
				$("#ItemCount"+i).css({"border":"1px solid #a02"});
				chk = 0;
			}else
				$("#ItemCount"+i).css({"border":"1px solid #ccc"});
			
			if(!self.create_invoice.details[i].ItemUnit)
			{
				$("#ItemUnit"+i).css({"border":"1px solid #a02"});
				chk = 0;
			}else
				$("#ItemUnit"+i).css({"border":"1px solid #ccc"});
				
			if(!self.create_invoice.details[i].ItemPrice)
			{
				$("#ItemPrice"+i).css({"border":"1px solid #a02"});
				chk = 0;
			}else
				$("#ItemPrice"+i).css({"border":"1px solid #ccc"});					
		}
		
		if(chk && confirm('確定要送出此筆發票新增?'))
			$("#mainFrm").submit();
	},
	sendInvoiceAllowance: function(){
		var self = this;
		var chk = 1;
		if(!self.invoice_detail.MerchantOrderNo)
		{
			$("#MerchantOrderNo").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(0);
			chk = 0;	
		}else
			$("#MerchantOrderNo").css({"border":"1px solid #ccc"});	
		for(var i=0;i<self.invoice_detail.details.length;i++)
		{
			if(!self.invoice_detail.details[i].ItemName)
			{
				$("#ItemName"+i).css({"border":"1px solid #a02"});
				chk = 0;
			}else
				$("#ItemName"+i).css({"border":"1px solid #ccc"});
			
			if(!self.invoice_detail.details[i].ItemCount)
			{
				$("#ItemCount"+i).css({"border":"1px solid #a02"});
				chk = 0;
			}else
				$("#ItemCount"+i).css({"border":"1px solid #ccc"});
			
			if(!self.invoice_detail.details[i].ItemUnit)
			{
				$("#ItemUnit"+i).css({"border":"1px solid #a02"});
				chk = 0;
			}else
				$("#ItemUnit"+i).css({"border":"1px solid #ccc"});
				
			if(!self.invoice_detail.details[i].ItemPrice)
			{
				$("#ItemPrice"+i).css({"border":"1px solid #a02"});
				chk = 0;
			}else
				$("#ItemPrice"+i).css({"border":"1px solid #ccc"});					
		}
		
		if(chk && confirm('確定要送出此筆發票新增?'))
			$("#mainFrm").submit();
	},
	sendMerchantAdd: function(){
		var self = this;
		var chk = 1;
		if(!self.merchant.ManagerEmail)
		{
			$("#ManagerEmail").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(0);
			chk = 0;	
		}else
			$("#ManagerEmail").css({"border":"1px solid #ccc"});	
		
		if(!self.merchant.MemberName)
		{
			$("#MemberName").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(0);
			chk = 0;	
		}else
			$("#MemberName").css({"border":"1px solid #ccc"});
		
		if(!self.merchant.MemberUnified)
		{
			$("#MemberUnified").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(0);
			chk = 0;	
		}else
			$("#MemberUnified").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.MemberPhone.head)
		{
			$("#telHead").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(100);
			chk = 0;	
		}else
			$("#telHead").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.MemberPhone.value || isNaN(self.merchant.MemberPhone.value))
		{
			$("#telValue").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(100);
			chk = 0;	
		}else
			$("#telValue").css({"border":"1px solid #ccc"});	
			
		if(parseInt(self.merchant.MerchantClass)==2)
		{
			if(!self.merchant.ManagerID.ID)
			{
				$("#Manager_id").css({"border":"1px solid #a02"});
				$("body,html").scrollTop(100);
				chk = 0;	
			}else
				$("#Manager_id").css({"border":"1px solid #ccc"});
			
			if(!self.merchant.ManagerID.Number)
			{
				$("#Manager_id_number").css({"border":"1px solid #a02"});
				$("body,html").scrollTop(100);
				chk = 0;	
			}else
				$("#Manager_id_number").css({"border":"1px solid #ccc"});	
		}
		
		if(parseInt(self.merchant.MerchantClass)==1)
		{
			if(!self.select_date.month)
			{
				$("#ID_month").css({"border":"1px solid #a02"});
				$("body,html").scrollTop(100);
				chk = 0;	
			}else
				$("#ID_month").css({"border":"1px solid #ccc"});
			
			if(!self.select_date.day)
			{
				$("#ID_day").css({"border":"1px solid #a02"});
				$("body,html").scrollTop(100);
				chk = 0;	
			}else
				$("#ID_day").css({"border":"1px solid #ccc"});
				
			if(!self.merchant.IDCardPlace)
			{
				$("#IDCardPlace").css({"border":"1px solid #a02"});
				$("body,html").scrollTop(100);
				chk = 0;	
			}else
				$("#IDCardPlace").css({"border":"1px solid #ccc"});
				
			if(self.merchant.IDPic==='')
			{
				$("#IDPic").css({"border":"1px solid #a02"});
				$("body,html").scrollTop(100);
				chk = 0;	
			}else
				$("#IDPic").css({"border":"1px solid #ccc"});
				
			if(!self.merchant.IDFrom)
			{
				$("#IDFrom").css({"border":"1px solid #a02"});
				$("body,html").scrollTop(100);
				chk = 0;	
			}else
				$("#IDFrom").css({"border":"1px solid #ccc"});		
					
		}
		
		if(!self.merchant.ManagerName)
		{
			$("#ManagerName").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(0);
			chk = 0;	
		}else
			$("#ManagerName").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.ManagerName)
		{
			$("#ManagerName").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(300);
			chk = 0;	
		}else
			$("#ManagerName").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.ManagerNameE)
		{
			$("#ManagerNameE").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(0);
			chk = 300;	
		}else
			$("#ManagerNameE").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.ManagerMobile || !self.chk_tel(self.merchant.ManagerMobile))
		{
			$("#ManagerMobile").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(300);
			chk = 0;	
		}else
			$("#ManagerMobile").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.MerchantName)
		{
			$("#MerchantName").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(300);
			chk = 0;	
		}else
			$("#MerchantName").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.MerchantNameE)
		{
			$("#MerchantNameE").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(400);
			chk = 0;	
		}else
			$("#MerchantNameE").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.MerchantWebURL)
		{
			$("#MerchantWebURL").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(400);
			chk = 0;	
		}else
			$("#MerchantWebURL").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.MerchantAddrCity)
		{
			$("#MerchantAddrCity").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(300);
			chk = 0;	
		}else
			$("#MerchantAddrCity").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.MerchantAddrArea)
		{
			$("#MerchantAddrArea").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(300);
			chk = 0;	
		}else
			$("#MerchantAddrArea").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.MerchantAddrCode)
		{
			$("#MerchantAddrCode").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(400);
			chk = 0;	
		}else
			$("#MerchantAddrCode").css({"border":"1px solid #ccc"});
		
		if(!self.merchant.MerchantAddr)
		{
			$("#MerchantAddr").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(400);
			chk = 0;	
		}else
			$("#MerchantAddr").css({"border":"1px solid #ccc"});	
		
		if(!self.merchant.NationalE)
		{
			$("#NationalE").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(400);
			chk = 0;	
		}else
			$("#NationalE").css({"border":"1px solid #ccc"});
		
		if(!self.merchant.CityE)
		{
			$("#CityE").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(500);
			chk = 0;	
		}else
			$("#CityE").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.BusinessType)
		{
			$("#BusinessType").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(500);
			chk = 0;	
		}else
			$("#BusinessType").css({"border":"1px solid #ccc"});
		
		if(!self.merchant.BankCode)
		{
			$("#BankCode").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(500);
			chk = 0;	
		}else
			$("#BankCode").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.SubBankCode)
		{
			$("#SubBankCode").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(500);
			chk = 0;	
		}else
			$("#SubBankCode").css({"border":"1px solid #ccc"});	
			
		if(!self.merchant.BankAccount)
		{
			$("#BankAccount").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(500);
			chk = 0;	
		}else
			$("#BankAccount").css({"border":"1px solid #ccc"});	
		
		if(!self.merchant.MerchantDesc)
		{
			$("#MerchantDesc").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(500);
			chk = 0;	
		}else
			$("#MerchantDesc").css({"border":"1px solid #ccc"});
			
		var PaymentTypes = [];
		$("input[name='payType']").each(function(){
			if($(this).prop('checked'))
				PaymentTypes.push($(this).val());
		});
		if(PaymentTypes.length>1)
		{
			$("#PaymentType").val(PaymentTypes.join('|'));
			if(chk && confirm('確定要送出此商店新增?'))
				$("#mainFrm").submit();
		}else
			alert('請選擇支付方式!');
	},
	sendMerchantEdit: function(){
		var self = this;
		var chk = 1;
		if(!self.merchant.ManagerEmail)
		{
			$("#ManagerEmail").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(0);
			chk = 0;	
		}else
			$("#ManagerEmail").css({"border":"1px solid #ccc"});	
		
		if(!self.merchant.MemberName)
		{
			$("#MemberName").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(0);
			chk = 0;	
		}else
			$("#MemberName").css({"border":"1px solid #ccc"});
		
		if(!self.merchant.MemberPhone.head || isNaN(self.merchant.MemberPhone.head))
		{
			$("#telHead").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(100);
			chk = 0;	
		}else
			$("#telHead").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.MemberPhone.value || isNaN(self.merchant.MemberPhone.value))
		{
			$("#telValue").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(100);
			chk = 0;	
		}else
			$("#telValue").css({"border":"1px solid #ccc"});	
			
		if(!self.merchant.MerchantAddrCity)
		{
			$("#MerchantAddrCity").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(100);
			chk = 0;	
		}else
			$("#MerchantAddrCity").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.MerchantAddrArea)
		{
			$("#MerchantAddrArea").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(100);
			chk = 0;	
		}else
			$("#MerchantAddrArea").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.MerchantAddrCode)
		{
			$("#MerchantAddrCode").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(200);
			chk = 0;	
		}else
			$("#MerchantAddrCode").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.MerchantAddr)
		{
			$("#MerchantAddr").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(200);
			chk = 0;	
		}else
			$("#MerchantAddr").css({"border":"1px solid #ccc"});		
		
		if(!self.merchant.BankCode)
		{
			$("#BankCode").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(200);
			chk = 0;	
		}else
			$("#BankCode").css({"border":"1px solid #ccc"});
			
		if(!self.merchant.SubBankCode)
		{
			$("#SubBankCode").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(200);
			chk = 0;	
		}else
			$("#SubBankCode").css({"border":"1px solid #ccc"});	
			
		if(!self.merchant.BankAccount)
		{
			$("#BankAccount").css({"border":"1px solid #a02"});
			$("body,html").scrollTop(200);
			chk = 0;	
		}else
			$("#BankAccount").css({"border":"1px solid #ccc"});	
		
		var PaymentTypes = [];
		$("input[name='payType']").each(function(){
			if($(this).prop('checked'))
				PaymentTypes.push($(this).val());
		});
		if(PaymentTypes.length>1)
		{
			$("#PaymentType").val(PaymentTypes.join('|'));
			if(chk && confirm('確定要送出此商店修改?'))
				$("#mainFrm").submit();
		}else
		{
			alert('請選擇啟用支付方式!');
			$("body,html").scrollTop(250);																				
		}	
	},
	upper: function(){
		$("#AphabeticLetter").val($("#AphabeticLetter").val().toUpperCase());
	},
	getThisData: function(x){
		var self = this;
		self.invoice_track = self.invoice_tracks[x];
		self.old_Flag = self.invoice_tracks[x].Flag;
	},
	changelovecode: function(){
		var self = this;
		if(self.create_invoice.LoveCode!='')
		{
			self.other_lovecode = '';
		}	
	},
	chk_num: function(value){
		var chk = /[A-Z0-9\+\-\.]{7}/;
		///[0-9]{10}/;
		return chk.test(value);
	},
	chk_num1: function(value){
		var chk = /[A-Z]{2}[0-9]{14}/;
		///[0-9]{10}/;
		return chk.test(value);
	},
	chk_mail: function(value){
		var mail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return mail.test(value);
	},
	refreshBtn: function(){
		var self = this;
		self.create_invoice.LoveCode = '';
		self.create_invoice.BuyerAddress = '';
		self.create_invoice.CarrierType = '';
		self.create_invoice.CarrierNum = '';
		$("#other_lovecode").css({"border":"1px solid #ccc"});
		$("#BuyerAddress").css({"border":"1px solid #ccc"});
		if(self.choose==3)
			self.create_invoice.PrintFlag = 'Y';
		else if(self.choose==1)
		{
			self.create_invoice.CarrierType = 2;
			self.create_invoice.PrintFlag = 'N';
		}else
			self.create_invoice.PrintFlag = 'N';		
	},
	getInvoiceData: function(id,index){
		var self = this;
		self.isBg = true;
		axios.get('/admin/get_accountings?item='+self.item+'&action=detail&id='+id).then(function (response){
			console.log(response.data);
			if(response.data=='error')
				window.location = '/error';
			
			self.invoice_detail = response.data.invoice;
			self.invoice_detail.allowance = self.invoices.data[index].allowance;
			self.invoice_detail.TotalAmt = 0;
			self.invoice_detail.details = [{ItemName:'',ItemCount:1,ItemUnit:'筆',ItemPrice:0,ItemAmt:0,ItemTaxAmt:0}];
			if(response.data.message)
				alert(response.data.message);
			
			$("body,html").scrollTop(0);	
			self.isBg = false;		
		});
	},
	searchBtn: function(){
		var self = this;
		if(self.search_text)
		{
			self.isBg = true;
			self.action = 'search';
			$("#search").css({"border":"1px solid #ccc"});
			axios.get('/admin/get_accountings?item='+self.item+'&action='+self.action+'&text='+self.search_text).then(function (response){
				console.log(response.data)
				if(response.data=='error')
					window.location = '/error';
				
				self.invoices = response.data.invoices;
				self.invoice_detail = '';
				self.title = response.data.title;
				self.isBg = false;
				
			});		
		}else
		{
			self.action = 'manage';
			self.get_accountings();
		}
	},
	email_get_account: function(){
		var self = this;
		if(self.item=='systemAccount' && self.action=='merchant_create')
		{
			if(!self.merchant.ManagerEmail || !self.chk_mail(self.merchant.ManagerEmail))
			{
				self.merchant.MemberName = '';
				$("#ManagerEmail").css({"border":"1px solid #a02"});
			}else
			{
				axios.get('/admin/email_get_account?id='+self.merchant.ManagerEmail).then(function (response){
					console.log(response.data)
					if(response.data=='error')
						window.location = '/error';
					
					if(response.data.buyer)
					{
						self.is_edit = response.data.is_edit;
						if(response.data.merchant)
						{
							self.merchant = response.data.merchant;
							var index = self.citys.indexOf(response.data.merchant.MerchantAddrCity);
							self.areas = self.nats[index];
							if(response.data.is_edit)
								self.title = '修改合作商店';
							var length = response.data.merchant.IDCardDate.length;
							var year,month,day;
							if(length==7)
							{
								year = response.data.merchant.IDCardDate.substr(0,3);
								month = response.data.merchant.IDCardDate.substr(3,2);
								day = response.data.merchant.IDCardDate.substr(5,2)
							}else if(length==6)
							{
								year = response.data.merchant.IDCardDate.substr(0,2);
								month = response.data.merchant.IDCardDate.substr(2,2);
								day = response.data.merchant.IDCardDate.substr(4,2)
							}else if(length==5)
							{
								year = response.data.merchant.IDCardDate.substr(0,1);
								month = response.data.merchant.IDCardDate.substr(1,2);
								day = response.data.merchant.IDCardDate.substr(3,2)
							}
							self.select_date = {year:year,month:month,day:day};
							self.back_message = '';
							$("#ManagerEmail").css({"border":"1px solid #ccc"});
						}else
						{	
							self.merchant = {usr_id:'',MerchantClass:'1',MemberUnified:'',ManagerID:{ID:'',Number:''},LoginAccount:'',IDCardDate:'',IDCardPlace:'',IDPic:'',IDFrom:'',MemberName:response.data.buyer.last_name+response.data.buyer.first_name,MemberPhone:{head:'',value:''},ManagerName:'',ManagerNameE:'',ManagerMobile:'',ManagerEmail:response.data.buyer.email,MerchantID:'',MerchantName:'',MerchantNameE:'',MerchantWebURL:'',MerchantAddrCity:'',MerchantAddrArea:'',MerchantAddrCode:'',MerchantAddr:'',NationalE:'Taiwan',CityE:'',MerchantType:'2',BusinessType:'',MerchantDesc:'',BankCode:'',SubBankCode:'',BankAccount:'',PaymentType:{CREDIT:1,WEBATM:1,VACC:1,CVS:1,BARCODE:1},CreditAutoType:1,CreditLimit:200000};
							
							self.select_date = {year:100,month:'',day:''};
							self.merchant.usr_id = response.data.buyer.usr_id;
							$("#ManagerEmail").css({"border":"1px solid #ccc"})
							self.back_message = '';
						}
					}else
					{
						self.back_message = '查無此會員資料!!';
						$("#ManagerEmail").css({"border":"1px solid #a02"});
						self.merchant = {usr_id:'',MerchantClass:'1',MemberUnified:'',ManagerID:{ID:'',Number:''},LoginAccount:'',IDCardDate:'',IDCardPlace:'',IDPic:'',IDFrom:'',MemberName:'',MemberPhone:{head:'',value:''},ManagerName:'',ManagerNameE:'',ManagerMobile:'',ManagerEmail:'',MerchantID:'',MerchantName:'',MerchantNameE:'',MerchantWebURL:'',MerchantAddrCity:'',MerchantAddrArea:'',MerchantAddrCode:'',MerchantAddr:'',NationalE:'Taiwan',CityE:'',MerchantType:'2',BusinessType:'',MerchantDesc:'',BankCode:'',SubBankCode:'',BankAccount:'',PaymentType:{CREDIT:1,WEBATM:1,VACC:1,CVS:1,BARCODE:1},CreditAutoType:1,CreditLimit:200000};
						
						self.select_date = {year:100,month:'',day:''};
					}
				});
			}
		}else
		{
			if(!self.create_invoice.BuyerEmail || !self.chk_mail(self.create_invoice.BuyerEmail))
				$("#BuyerEmail").css({"border":"1px solid #a02"});
			else
			{
				axios.get('/admin/email_get_account?id='+self.create_invoice.BuyerEmail).then(function (response){
					console.log(response.data)
					if(response.data=='error')
						window.location = '/error';
					
					if(response.data.buyer)
					{
						self.create_invoice.BuyerEmail = response.data.buyer.email;
						self.create_invoice.BuyerName = response.data.buyer.last_name+response.data.buyer.first_name;
						self.create_invoice.usr_id = response.data.buyer.usr_id;
						$("#BuyerEmail").css({"border":"1px solid #ccc"})
						self.back_message = '';
					}else
					{
						self.back_message = '查無此會員資料!!';
						self.create_invoice.BuyerName = '';
						$("#BuyerEmail").css({"border":"1px solid #a02"});
					}
				});
			}
		}
	},
	clickBtn: function(x){
		var self = this;
		if(x==0)
		{
			if(self.action=='allowance')
				self.invoice_detail.details.push({ItemName:'',ItemCount:1,ItemUnit:'',ItemPrice:0,ItemAmt:0,ItemTaxAmt:0});
			else	
				self.create_invoice.details.push({ItemName:'',ItemCount:1,ItemUnit:'',ItemPrice:0,ItemAmt:0});
		}else
		{
			if(self.action=='allowance')
				self.invoice_detail.details.splice(x,1);
			else	
				self.create_invoice.details.splice(x,1);
		}
		
		if(self.action=='allowance')
			self.count = self.invoice_detail.details.length;
		else
			self.count = self.create_invoice.details.length;		
		self.change_price();	
	},
	change_price: function(x=null){
		var self = this;
		
		if(self.action=='allowance')
		{
			var tax = ((parseInt(self.invoice_detail.TaxType)==1)?parseFloat(self.invoice_detail.TaxRate/100):0);
			if(x!=null)
			{
				var priceInput = $("#ItemPriceInput"+x).val();
				var itemTotal = parseInt(self.invoice_detail.details[x].ItemCount)*parseInt($("#ItemPriceInput"+x).val());
				self.invoice_detail.details[x].ItemPrice = Math.round((parseInt($("#ItemPriceInput"+x).val())/(1+tax)));
				self.invoice_detail.details[x].ItemAmt = Math.round((itemTotal/(1+tax)));
				self.invoice_detail.details[x].ItemTaxAmt = itemTotal-self.invoice_detail.details[x].ItemAmt;
			}
			var total = 0;
			for(var i=0;i<self.invoice_detail.details.length;i++)
			{
				total += self.invoice_detail.details[i].ItemAmt+self.invoice_detail.details[i].ItemTaxAmt;
			}
			if(total>self.invoice_detail.RemainAmt)
				self.back_message = '折讓金額不能大於可折讓金額!';
			else
				self.back_message = '';	
			self.invoice_detail.TotalAmt = total;	
		}else
		{
			if(x!=null)
				self.create_invoice.details[x].ItemAmt = parseInt(self.create_invoice.details[x].ItemCount)*parseInt(self.create_invoice.details[x].ItemPrice);
			var total = 0;
			for(var i=0;i<self.create_invoice.details.length;i++)
			{
				total += self.create_invoice.details[i].ItemAmt;
			}
			
			self.create_invoice.Amt = Math.round(total/1.05);
			self.create_invoice.TaxAmt = total-parseInt(self.create_invoice.Amt);
			self.create_invoice.TotalAmt = parseInt(self.create_invoice.Amt)+parseInt(self.create_invoice.TaxAmt);	
		}
		
	},
	sendInvaild: function(){
		var self = this;
		var chk = 1;
		if(!self.InvalidReason || self.InvalidReason.length>70)
		{
			$("#InvalidReason").css({"border":"1px solid #a02"});
			chk = 0;
		}else
			$("#InvalidReason").css({"border":"1px solid #ccc"});
			
		if(chk && confirm('確定要作廢這張發票?'))
			$("#mainFrm").submit();
				
	},
	go_content_page: function(page){
	  
		var self = this;
		axios.get('/admin/get_accountings?item='+self.item+'&action='+self.action+'&text='+self.search_text+'&page='+page).then(function (response){
			  console.log(response.data)		
			self.invoices = response.data.invoices;
	   })
	},
	getAllowance: function(id,index){
		var self = this;
		self.action = 'allowance'
		self.getInvoiceData(id,index);
		self.title = '發票折讓作業';
	},
	confirmAllowance: function(id,index,con){
		var self = this;
		self.action = 'confirm';
		self.isBg = true;
		axios.get('/admin/get_accountings?item='+self.item+'&action='+self.action+'&id='+id).then(function (response){
			console.log(response.data)		
			
			self.allowances = response.data.allowances;
			self.title = '是否上傳至財政部';
			
			$("body,html").scrollTop(0);
			self.isBg = false;
	   })
	},
	actionBtn: function(Status,index){
		var self = this;
		$("#AllowanceStatus").val(Status);
		$("#InvoiceNo").val(self.allowances[index].InvoiceNumber);
		$("#AllowanceNo").val(self.allowances[index].AllowanceNo);
		$("#MerchantOrderNo").val(self.allowances[index].MerchantOrderNo);
		$("#TotalAmt").val(self.allowances[index].AllowanceAmt);
		
		if(confirm('確認要送出此'+((Status=='C')?'確認':'取消')+'資料?'))
		{
			self.isBg = true;
			$("#mainFrm").submit();
		}
	},
	padLeft: function(str,lenght){
		if(str.length >= lenght)
			return str;
		else
			return padLeft("0" +str,lenght);
	},
	select_city: function(){
		var self = this;
		var index = self.citys.indexOf(self.merchant.MerchantAddrCity);
		self.areas = self.nats[index];
		self.merchant.MerchantAddrArea = '';
		self.merchant.MerchantAddrCode = '';
		self.merchant.CityE = self.englishs[index];
		//console.log(self.areas);
	},
	select_nat: function(){
		var self = this;
		self.merchant.MerchantAddrCode  = self.areas[self.merchant.MerchantAddrArea];
	},
	change_manager_id: function(){
		var self = this;
		if(parseInt(self.merchant.ManagerID.ID)==1)
			$("#Manager_id_number").attr('placeholder','請填寫身份證字號');
		else if(parseInt(self.merchant.ManagerID.ID)==2)
			$("#Manager_id_number").attr('placeholder','請填寫居留證號');	
		else if(parseInt(self.merchant.ManagerID.ID)==3)
			$("#Manager_id_number").attr('placeholder','請填寫稅籍編號');		
	},
	chk_tel: function(value){
		var tel = /[0-9]{10}/;
		return tel.test(value);
	},
	getThisMerchant: function(x){
		var self = this;
		self.merchant = self.merchants.data[x];
		var index = self.citys.indexOf(self.merchants.data[x].MerchantAddrCity);
		self.areas = self.nats[index];
		self.is_edit = true;
		self.title = '修改合作商店';
		var length = self.merchants.data[x].IDCardDate.length;
		var year,month,day;
		if(length==7)
		{
			year = self.merchants.data[x].IDCardDate.substr(0,3);
			month = self.merchants.data[x].IDCardDate.substr(3,2);
			day = self.merchants.data[x].IDCardDate.substr(5,2)
		}else if(length==6)
		{
			year = self.merchants.data[x].IDCardDate.substr(0,2);
			month = self.merchants.data[x].IDCardDate.substr(2,2);
			day = self.merchants.data[x].IDCardDate.substr(4,2)
		}else if(length==5)
		{
			year = self.merchants.data[x].IDCardDate.substr(0,1);
			month = self.merchants.data[x].IDCardDate.substr(1,2);
			day = self.merchants.data[x].IDCardDate.substr(3,2)
		}
		self.select_date = {year:year,month:month,day:day};
							
		self.action = 'merchant_create';
	}
	
  }
  
})
</script>   
@stop