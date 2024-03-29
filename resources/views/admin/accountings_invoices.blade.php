<div class="w-100" v-if="item=='invoices'"> 
    <div class="w-100 p-2 d-table">
        <div class="w-25 float-left text-left">
            <a class="btn btn-primary pull-right" v-if="action!='create' && (invoice_detail || invoice_detail || allowances)" href="javascript:void(0)" @click="invoice_detail='';action='manage'" v-text="'返回'"></a>
        </div>    
        <div class="w-50 float-left mx-auto" v-if="action!='create' && action!='credit_close'">
            <div class="w-100 mb-2">
                <input type="date" id="startdate" class="form-control d-inline px-1" v-model="start_date" style="width:150px;" />
                <b class="d-inline px-1">~</b>
                <input type="date" id="enddate" class="form-control d-inline px-1" v-model="end_date" style="width:150px;" />
            </div>
            <input type="text" class="form-control float-left w-50" id="search" v-model="search_text" @keyup.enter="searchBtn" placeholder="搜尋交易單任一字串" />
            <select class="form-control float-left w-25 ml-1" v-model="tradeStatus">
                <option value="" v-text="'全部'"></option>
                <option :value="index" v-for="(trade,index) in trades" v-text="trade"></option>
            </select>
            <a href="javascript:void(0)" @click="searchBtn" class="btn btn-primary ml-1">搜尋</a>
        </div>
        <h3 class="float-right" v-text="title"></h3>
    </div>
    <!---訂單開立發票----------------------------------------------------------->    
    <div class="w-100" v-if="action=='transfer' && transfers">
        <table class="table table-light table-bordered table-hover" >
            <tr class="text-center bg-secondary text-white">
                <th>狀態</th>
                <th>商店ID</th>
                <th>商家名稱</th>
                <th>金額</th>
                <th>發票</th>
                <th>交易時間</th>
                <th>處理</th>
            </tr>
            <tr class="text-center" v-for="(transfer,index) in transfers.data">
                <td v-text="trades[transfer.TradeStatus]"></td>
                <td v-text="transfer.MerchantID"></td>
                <td v-text="transfer.MerchantName"></td>
                <td v-text="transfer.Amt"></td>
                <td v-text="((parseInt(transfer.InvoiceStatus)==1)?'開立':((parseInt(transfer.InvoiceStatus)==2))?'作廢':'未開')"></td>
                <td v-text="transfer.PayTime"></td>
                <td>
                	<a href="javascript:void(0)" class="btn btn-sm btn-primary d-inline" @click="invoiceBtn(index)" v-text="'開立發票'"></a>
                </td>   
            </tr>
        </table>
        <div class="w-100 d-table py-2 text-center border-top" v-if="transfers.last_page>1">
            <a class="btn btn-light btn-sm float-left" v-if="parseInt(transfers.current_page) > 1" href="javascript:void(0)" @click="go_content_page(parseInt(transfers.current_page-1))">上一頁</a>	
            <span class="h5" v-if="parseInt(transfers.current_page) > 1" v-text="transfers.current_page"></span>
            <a class="btn btn-light btn-sm float-right" v-if="transfers.last_page>transfers.current_page" href="javascript:void(0)" @click="go_content_page(parseInt(transfers.current_page)+1)">下一頁</a>
        </div>
    </div>
    <!--------手動開立發票--------------------------------->    
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
    <div :class="{ bg_loding: isBg }"></div>
    
    <!-----------提示框---------------------------------->
    <div id="mark" class="position-fixed fixed-top w-100 h-100 border" @click="showitem=''" v-if="showitem" style="background-color:rgba(0,0,0,0.6); z-index:1030;display:none;"></div>
    <div id="showitem" class="position-fixed fixed-top w-75 mx-auto bg-white p-4 border rounded" v-if="showitem" style="z-index:1050;height:92%; margin-top:2%; overflow:auto;display:none; ">
       <form id="mainFrm"  action="/admin/accountings_pt" method="post">
          @csrf
          <input type="hidden" name="item" v-model="item" />
          <input type="hidden" name="action" value="create" />
          <input type="hidden" name="u_id" v-model="create_invoice.usr_id" />
          <input type="hidden" name="MerchantID" v-model="create_invoice.MerchantID" />
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
                    <input type="text" class="w-50 form-control d-inline" name="MerchantOrderNo" id="MerchantOrderNo" v-model="create_invoice.MerchantOrderNo" placeholder="填寫訂單編號" readonly="readonly" />
                 </td>
              </tr>
              <tr>
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
</div>
<script>
new Vue({
  el: "#app",
  data: {
	isBg: true,
	item: '<?php echo ((isset($item))?$item:'')?>',
	action: '<?php echo ((isset($action))?$action:'')?>',
	mode: '',
	id: '<?php echo ((isset($id))?$id:'')?>',
	status: '<?php echo ((isset($status))?$status:'')?>',
	message: '<?php echo ((isset($message))?$message:'')?>',
	title: '',
	today: new Date(),
	Year: '<?php echo date("Y")-1911?>',
	count: 1,
	create_invoice: '',
	invoices: '',
	invoice_detail: '',
	other_lovecode: '',
	start_date: '<?php echo ((date("d")>='01' && date("d")<='05')?date("Y-m-01",strtotime("-1 month")):date("Y-m-01"))?>',
	end_date: '{{date("Y-m-d")}}',
	search_text: '',
	units: ['筆','個','支','隻','其他'],
	uploadStatus: {0:'未上傳',1:'已上傳',2:'上傳中',3:'上傳失敗'},
	InvalidReason: '',
	invalidZone: 0,
	back_message: '',
	allowances: '',
	transfers: '',
	service_fee: '',
	trades: {0:'未付款',1:'付款成功',2:'付款失敗',3:'取消付款'},
	search_text: '',
	tradeStatus: 1,
	showitem: '',
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
		axios.get('/admin/get_accountings?item='+self.item+'&Year='+self.Year+'&action='+self.action+'&id='+self.id+'&mode='+self.mode+'&text='+self.search_text+'&tradeStatus='+self.tradeStatus).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.create_invoice = response.data.create_invoice;
			self.invoices = response.data.invoices;
			self.transfers = response.data.transfers;	
			
			self.title = response.data.title;
			self.service_fee = ((response.data.service_fee)?parseInt(response.data.service_fee):20);
			
			if(self.message)
			{
				alert(self.message);
				self.message = '';
			}
			self.isBg = false;			
		});
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
	chk_mail: function(value){
		var mail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return mail.test(value);
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
		self.isBg = true;
		self.mode = 'search';
		$("#search").css({"border":"1px solid #ccc"});
		axios.get('/admin/get_accountings?item='+self.item+'&action='+self.action+'&mode='+self.mode+'&start_date='+self.start_date+'&end_date='+self.end_date+'&text='+self.search_text+'&tradeStatus='+self.tradeStatus).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.invoices = response.data.invoices;
			self.transfers = response.data.transfers;
			self.invoice_detail = '';
			self.title = response.data.title;
			self.isBg = false;
			
		});	
	},
	email_get_account: function(){
		var self = this;
		
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
	go_content_page: function(page){
	  
		var self = this;
		axios.get('/admin/get_accountings?item='+self.item+'&action='+self.action+'&mode='+self.mode+'&start_date='+self.start_date+'&end_date='+self.end_date+'&text='+self.search_text+'&tradeStatus='+self.tradeStatus+'&page='+page).then(function (response){
			console.log(response.data)		
			if(self.action=='transfer')
				self.transfers = response.data.transfers;
			
			if(self.action=='manage')	
				self.invoices = response.data.invoices;
				
			self.title = response.data.title;	
			
	   })
	},
	invoiceBtn: function(x){
		var self = this;
		var transfer =  self.transfers.data[x];
		var create_invoice_arr = [];
		
		$("#mark").show();
		this.showitem = true;
		
		var tax_price = parseInt(transfer.Amt)*self.service_fee/100;
		var tax = 0.05;
		var Amt = Math.round((tax_price/(1+tax)));
		var TaxAmt = tax_price-Amt;
				
		self.create_invoice = {usr_id:transfer.usr_id, TransNum:transfer.TradeNo, MerchantID:transfer.MerchantID, MerchantOrderNo:transfer.MerchantOrderNo, Status:1, CarrierType:2, Category:'B2C', PrintFlag:'N', BuyerName:transfer.MerchantName, BuyerEmail:transfer.Email, BuyerUBN:'', BuyerAddress:'', Amt:Amt, TaxAmt:TaxAmt, TotalAmt:tax_price, details:[{ItemName:'手續費', ItemCount:1, ItemUnit:'筆', ItemPrice:tax_price, ItemAmt:tax_price}]};
		
		
	}
	
  }
  
})
</script> 