<!----系統帳務------------------------------------------->
<div class="w-100" v-if="item=='systemAccount'">
    <div class="w-100 p-2 d-table">
        <div class="w-25 float-left text-left">
            <a class="btn btn-primary pull-right" v-if="action=='merchant_create'" href="/admin/accountings?item=systemAccount&action=merchant_manager" v-text="'返回'"></a>
        </div>    
        <div class="w-50 float-left mx-auto" v-if="action=='merchant_manager' || action=='FeeInstruct' || action=='Platformfee_search'" >
            <div class="w-100 mb-2" v-if="action=='FeeInstruct' || action=='Platformfee_search'">
                <input type="date" id="startdate" class="form-control d-inline px-1" v-model="start_date" style="width:150px;" />
                <b class="d-inline px-1">~</b>
                <input type="date" id="enddate" class="form-control d-inline px-1" v-model="end_date" style="width:150px;" />
            </div>
            <input type="text" class="form-control w-75 d-inline" id="search" v-model="search_text" @keyup.enter="searchBtn" placeholder="搜尋發票任一字串" />
            <a href="javascript:void(0)" @click="searchBtn" class="btn btn-primary ml-1 d-inline">搜尋</a>
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
          <input type="hidden" name="MerchantID" v-if="is_edit" v-model="merchant.MerchantID" />
          <input type="hidden" name="MerchantID" v-if="!is_edit" value="<?php echo 'BOB'.date('ymdHis')?>" />
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
              <tr>
                <th class="w-25 text-center align-middle">商店營運狀態</th>
                <td class="w-75">
                    <div class="float-left mr-3">
                        <input type="radio" name="MerchantStatus" value="1" id="m1" v-model="merchant.MerchantStatus" />
                        <label for="m1" v-text="'營運中'"></label>
                    </div>
                    <div class="float-left mr-3">
                        <input type="radio" name="MerchantStatus" value="2" id="m2" v-model="merchant.MerchantStatus" />
                        <label for="m2" v-text="'暫停'"></label>
                    </div>
                 </td>
              </tr>
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
                <th>狀態</th>
                <th>商店代號</th>
                <th>名稱</th>
                <th>聯絡電話</th>
                <th>商店名稱</th>
                <th>建立日期</th>
                
            </tr>
            <tr class="text-center" v-for="(merchant,index) in merchants.data" @click="getThisMerchant(index)" style="cursor:pointer">
                <td v-text="((merchant.MerchantClass==1)?'個人':'企業')"></td>
                <td :class="((parseInt(merchant.MerchantStatus)==2)?'text-danger':'')" v-text="((parseInt(merchant.MerchantStatus)==2)?'暫停':'營運中')"></td>
                <td v-text="merchant.MerchantID"></td>
                <td v-text="merchant.MemberName"></td>
                <td v-text="merchant.MemberPhone.head+'-'+merchant.MemberPhone.value"></td>
                <td v-text="merchant.MerchantName"></td>
                <td v-text="merchant.created_at"></td>
                
            </tr>
        </table>
    </div>
    <!--------平台費用扣款單筆查詢------------------------------------------------->
    <div class="w-100" v-if="action=='Platformfee_search'">
        <table class="table table-light table-bordered table-hover" >
            <tr class="text-center bg-secondary text-white">
                <th>交易單號碼</th>
                <th>交易金額</th>
                <th>商家 ID</th>
                <th>商家名稱</th>
                <th>買家姓名</th>
                <th>支付方式</th>
                <th>交易日期</th>
            </tr>
            <tr class="text-center" v-for="(transfer,index) in transfers.data" @click="getThisData(transfer.MerchantOrderNo)" style="cursor:pointer">
                <td v-text="transfer.MerchantOrderNo"></td>
                <td v-text="transfer.Amt"></td>
                <td v-text="transfer.MerchantID"></td>
                <td v-text="transfer.MerchantName"></td>
                <td v-text="transfer.last_name+transfer.first_name"></td>
                <td v-text="transfer.PaymentType"></td>
                <td v-text="transfer.PayTime"></td>
            </tr>
        </table>
        <div class="w-100 d-table py-2 text-center border-top" v-if="transfers.last_page>1">
            <a class="btn btn-light btn-sm float-left" v-if="parseInt(transfers.current_page) > 1" href="javascript:void(0)" @click="go_content_page(parseInt(transfers.current_page-1))">上一頁</a>	
            <span class="h5" v-if="parseInt(transfers.current_page) > 1" v-text="transfers.current_page"></span>
            <a class="btn btn-light btn-sm float-right" v-if="transfers.last_page>transfers.current_page" href="javascript:void(0)" @click="go_content_page(parseInt(transfers.current_page)+1)">下一頁</a>
        </div>
    </div>    
    <!--------商店扣撥款作業------------------------------------------------->
    <div class="w-100" v-if="action=='FeeInstruct'">
        <table class="table table-light table-bordered table-hover" >
            <tr class="text-center bg-secondary text-white">
                <th>交易單號碼</th>
                <th>交易金額</th>
                <th>已扣款</th>
                <th>已撥款</th>
                <th>商家名稱</th>
                <th>買家姓名</th>
                <th>支付方式</th>
                <th>處理</th>
            </tr>
            <tr class="text-center" v-for="(transfer,index) in transfers.data">
                <td v-text="transfer.MerchantOrderNo"></td>
                <td v-text="transfer.Amt"></td>
                <td v-text="((transfer.fee_instruct && transfer.fee_instruct.charge)?transfer.fee_instruct.charge:0)"></td>
                <td v-text="((transfer.fee_instruct && transfer.fee_instruct.export)?transfer.fee_instruct.export:0)"></td>
                <td v-text="transfer.MerchantName"></td>
                <td v-text="transfer.last_name+transfer.first_name"></td>
                <td v-text="transfer.PaymentType"></td>
                <td>
                	<a href="javascript:void(0)" class="btn btn-primary d-inline"  @click="getThisData(transfer.MerchantOrderNo,1)" v-text="'扣退款'"></a>
                    <a href="javascript:void(0)" :class="'btn btn-danger d-inline '+((transfer.fee_instruct && transfer.fee_instruct.charge && !transfer.fee_instruct.export)?'':'disabled')" @click="getThisData(transfer.MerchantOrderNo,2)" v-text="'撥款'"></a>
                    <a href="javascript:void(0)" class="btn btn-success d-inline" @click="getThisData(transfer.MerchantOrderNo,3)" v-text="'明細'"></a> 
                </td>
            </tr>
        </table>
        <div class="w-100 d-table py-2 text-center border-top" v-if="transfers.last_page>1">
            <a class="btn btn-light btn-sm float-left" v-if="parseInt(transfers.current_page) > 1" href="javascript:void(0)" @click="go_content_page(parseInt(transfers.current_page-1))">上一頁</a>	
            <span class="h5" v-if="parseInt(transfers.current_page) > 1" v-text="transfers.current_page"></span>
            <a class="btn btn-light btn-sm float-right" v-if="transfers.last_page>transfers.current_page" href="javascript:void(0)" @click="go_content_page(parseInt(transfers.current_page)+1)">下一頁</a>
        </div>
    </div>
    <!--------平台費用扣款單日查詢------------------------------------------------->
    <div class="w-100" v-if="action=='Platformfee_perday'">
        <div class="w-100 py-2 text-center h4">
        	<label class="d-inline">單日查詢 : </label>
            <input type="date" class="d-inline form-control ml-2 px-2" @change="search_date_go" v-model="search_date" style="width:200px;" />
        </div>
    </div>
    
    <!-----------扣撥款提示框---------------------------------->
    <div id="mark" class="position-fixed fixed-top w-100 h-100" @click="showitem=''" v-if="showitem" style="background-color:rgba(0,0,0,0.6); z-index:1030; display:none;"></div>
	<div id="showitem" class="position-fixed fixed-top w-75 mx-auto bg-white py-4 mt-5 text-center border rounded" v-if="showitem" style="z-index:1050;display:none;">
        <div class="w-100 px-3" v-if="transfer && action=='FeeInstruct' && (parseInt(feeMode)==1 || parseInt(feeMode)==2)">
            <label class="h4 w-100 text-left font-weight-bold" v-text="((parseInt(feeMode)==2)?'合作商店撥款作業':'合作商店扣退款作業')"></label>
            <table class="table table-light table-bordered" >
                <tr>
                    <th>交易單號</th>
                    <td v-text="transfer.MerchantOrderNo"></td>
                    <th>交易金額</th>
                    <td v-text="transfer.Amt"></td>
                </tr>
                <tr>
                    <th>商店 ID</th>
                    <td v-text="transfer.MerchantID"></td>
                    <th>商店名稱</th>
                    <td v-text="transfer.MerchantName"></td>
                </tr>
                <tr>
                    <th>手續費%數</th>
                    <td v-text="serviceFee+'%'"></td>
                    <th v-text="'最大'+((parseInt(feeMode)==1)?'扣款':'撥款')+'金額'"></th>
                    <td v-text="transfer.fee_total"></td>
                </tr>
            </table>
            <div class="w-100 py-4 border border-primary" v-if="parseInt(feeMode)==1">
                <form id="mainFrm"  action="/admin/accountings_pt" method="post">
                    @csrf
                    <input type="hidden" name="item" v-model="item" />
                    <input type="hidden" name="action" v-model="action" />
                    <input type="hidden" name="feeMode" value="ChargeInstruct" />
                    <input type="hidden" name="MerchantID" :value="transfer.MerchantID" />
                    <input type="hidden" name="MerchantOrderNo" :value="transfer.MerchantOrderNo" />
                    <input type="hidden" name="FeeType" value="0" />
                    
                    <div class="w-100 mb-3">
                        <span class="d-inline h4">
                            <input type="radio" name="BalanceType" id="b0" value="0" v-model="balanceType" />
                            <label for="b0" class="text-primary">扣款</label>
                        </span>
                        <span class="d-inline h4 ml-2">
                            <input type="radio" name="BalanceType" id="b1" value="1" v-model="balanceType" />
                            <label for="b1" class="text-danger">退款</label>
                        </span>
                        <select name="FeeType" class="w-25 form-control d-inline ml-3">
                        	<option v-for="(feeType,key,index) in feeTypes" :value="key" v-text="feeType"></option>
                        </select>
                    </div>
                    <label class="d-inline h4 ">欲扣款金額 : </label>
                    <input type="tel" class="w-25 d-inline form-control ml-2" name="Amount" id="Amount" v-model="transfer.fee" />
                    <a href="javascript:void(0)" @click="sendFeeInstruct" class="btn btn-primary ml-1" v-text="'確定'"></a>
                </form>     
            </div>
            <div class="w-100 py-4 border border-danger" v-if="parseInt(feeMode)==2">
                <form id="mainFrm"  action="/admin/accountings_pt" method="post">
                    @csrf
                    <input type="hidden" name="item" v-model="item" />
                    <input type="hidden" name="action" v-model="action" />
                    <input type="hidden" name="feeMode" value="ExportInstruct" />
                    <input type="hidden" name="MerchantID" :value="transfer.MerchantID" />
                    <input type="hidden" name="MerchantOrderNo" :value="transfer.MerchantOrderNo" />
                    
                    <label class="d-inline h4">欲撥款金額 : </label>
                    <input type="tel" class="w-25 d-inline form-control ml-2" name="Amount" id="Amount" v-model="transfer.fee" />
                    <a href="javascript:void(0)" @click="sendFeeInstruct" class="btn btn-primary ml-1" v-text="'確定'"></a>
                </form>     
            </div>
        </div>
        <div class="w-100 px-3" v-if="transfer && action=='FeeInstruct' && parseInt(feeMode)==3">
        	<table class="table table-light table-bordered table-hover" >
                <tr class="text-center bg-secondary text-white">
                    <th>類別</th>
                    <th>金額</th>
                    <th>預計撥款日</th>
                    <th>流水號</th>
                    <th>費用類別</th>
                </tr>
                <tr class="text-center" v-for="(fee_charge,index) in fee_charges">
                    <td v-text="((parseInt(fee_charge.BalanceType))?'退款':'扣款')"></td>
                    <td v-text="fee_charge.Amount"></td>
                    <td v-text="fee_charge.FundTime"></td>
                    <td v-text="fee_charge.ExeNo"></td>
                    <td v-text="feeTypes[fee_charge.FeeType]"></td>
                </tr>
           	</table>         
        </div>
        <div class="w-100 px-3" v-if="platformfee" style="min-height:200px;">
            <div class="w-100 h5 pb-2 border-bottom" v-text="platformfee.Message"></div>
        </div>
    </div>
</div>
<div :class="{ bg_loding: isBg }"></div>
<script>
new Vue({
  el: "#app",
  data: {
	isBg: true,
	item: '<?php echo ((isset($item))?$item:'')?>',
	action: '<?php echo ((isset($action))?$action:'')?>',
	message: '<?php echo ((isset($message))?$message:'')?>',
	title: '',
	today: new Date(),
	Year: '<?php echo date("Y")-1911?>',
	search_text: '',
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
	MaxLimit: '',
	search_date: '',
	perdays: '',
	transfers: '',
	transfer: '',
	trades: {0:'未付款',1:'付款成功',2:'付款失敗',3:'取消付款'},
	start_date: '<?php echo ((date("d")>='01' && date("d")<='05')?date("Y-m-01",strtotime("-1 month")):date("Y-m-01"))?>',
	end_date: '{{date("Y-m-d")}}',
	showitem: '',
	fee_instruct: '',
	serviceFee: '',
	total: '',
	platformfee: '',
	feeMode: '',
	balanceType: 0,
	feeTypes: {0:'平台交易手續費',1:'佣金費用',2:'退款費用',3:'物流費用',4:'其他費用'},
	fee_charges: '',
	fee_exports: ''
  },
  beforeCreate: function(){
  	$("#showitem").show();
	$("#mark").show();
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
		axios.get('/admin/get_accountings?item='+self.item+'&action='+self.action+'&text='+self.search_text).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
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
			}else if(self.action=='merchant_manager')
			{
				self.merchants = response.data.merchants;
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
			}else if(self.action=='FeeInstruct'  || self.action=='Platformfee_search')
			{
				self.transfers = response.data.transfers;
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
	chk_mail: function(value){
		var mail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return mail.test(value);
	},
	searchBtn: function(){
		var self = this;
		if(self.search_text || (self.start_date&&self.end_date))
		{
			self.isBg = true;
			$("#search").css({"border":"1px solid #ccc"});
			axios.get('/admin/get_accountings?item='+self.item+'&action='+self.action+'&start_date='+self.start_date+'&end_date='+self.end_date+'&text='+self.search_text).then(function (response){
				console.log(response.data)
				if(response.data=='error')
					window.location = '/error';
				
				if(self.action=='merchant_manager')
					self.merchants = response.data.merchants;
				else
					self.transfers = response.data.transfers;	
				self.title = response.data.title;
				self.isBg = false;
				
			});		
		}else
		{
			self.get_accountings();
		}
	},
	email_get_account: function(){
		var self = this;
		
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
	},
	search_date_go: function(){
		var self = this;
		if(self.search_date)
		{
			self.isBg = true;
			axios.get('/admin/get_accountings?item='+self.item+'&action='+self.action+'&date='+self.search_date).then(function (response){
				console.log(response.data)
				if(response.data=='error')
					window.location = '/error';
				
				self.perdays = response.data.perdays;
				self.title = response.data.title;
				self.isBg = false;
				
				if(response.data.message)
					alert(response.data.message);
				
			});		
		}
	},
	go_content_page: function(page){
	  
		var self = this;
		self.isBg = true;
		axios.get('/admin/get_accountings?item='+self.item+'&action='+self.action+'&start_date='+self.start_date+'&end_date='+self.end_date+'&text='+self.search_text+'&page='+page).then(function (response){
			console.log(response.data)		
			
			if(self.action=='merchant_manager')
				self.merchants = response.data.merchants;
			else		
				self.transfers = response.data.transfers;
			
			self.isBg = false;
			
	   })
	},
	getThisData: function(id,feeMode){
		var self = this;
		self.isBg = true;
		axios.get('/admin/get_accountings?item='+self.item+'&action='+self.action+'&id='+id+'&feeMode='+feeMode).then(function (response){
			console.log(response.data)		
			if(self.action=='Platformfee_search')
			{
				$("#mark").show();
				self.showitem = true;
				self.platformfee = response.data.platformfee;
			}else if(self.action=='FeeInstruct')
			{
				self.feeMode = feeMode;
				if(response.data.transfer)
				{
					self.transfer = response.data.transfer;
					self.total = response.data.transfer.fee_total;
					self.fee_charges = response.data.fee_charges;
					self.fee_exports = response.data.fee_exports;
					self.serviceFee = response.data.serviceFee;
					$("#mark").show();
					self.showitem = true;
				}
			}
			self.isBg = false;
			
	   })
	},
	sendFeeInstruct: function(){
		var self = this;
		var chk = 1;
		
		if(!self.transfer.fee || self.transfer.fee>self.transfer.fee_total)
		{
			$("#Amount").css({"border":"1px solid #a02"});
			chk = 0;
			alert(((parseInt(self.feeMode)==2)?'請填寫欲撥款金額且不得大於可撥款金額!':'請填寫預扣款金額且不得大於可扣款金額!'));
		}else
			$("#Amount").css({"border":"1px solid #ccc"});
		
		if(chk && confirm('確定要送出此筆'+((parseInt(self.feeMode)==2)?'撥款':((parseInt(self.balanceType)==1)?'退款':'扣款'))+'請求?'))
			$("#mainFrm").submit();	
	}
	
  }
  
})
</script> 