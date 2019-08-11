<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
	
    <title>幫棒 管理後台</title>

    <link href="/css/app.css" rel="stylesheet">
    
    <script src="{{url('/js/app.js')}}"></script>
    <style>
		li{list-style:none;}
		.hidezone{display:none;}
		.aHover{background-color:#fff; color:#555; border-bottom:1px solid #eee;}
		.aHover:hover{ background-color:#f5f5f5;color:#555;}
		.action{background-color:#eee;}
		a:hover{text-decoration:none;}
		a:focus,textarea:focus, input:focus{outline: none;}
		
		.bg_loding{position:fixed;width:100%;height:100%;left:0px; top:0px; background:url(/images/Ring.gif) no-repeat center center / 60px auto rgba(255,255,255,0.5); z-index:9999;}
	</style>
</head>
<body>
  	<header id="master_top">
      <div class="w-100 bg-dark d-table fixed-top p-2">
        <a class="d-block float-left h5 text-light text-left mt-2" href="/admin/owner">{{Session::get('groupName')}} : {{Session::get('ownerName')}}</a>
        <a class="btn btn-outline-info d-block float-right" href="/admin/logout">登出</a>
      </div>	
    </header>
    <div class="w-100" style="margin-top:70px; min-width:1024px;">
      <div class="w-100 row m-0">
        <div id="master_menu" class="col-2 mb-4" style="min-width:150px;">
          <div class="w-100 btn-group-vertical">
            <a :class="'btn btn-secondary w-100 p-2 '+((weburi.indexOf('owner')!=-1)?'active':'')" style="font-size:1.2em;" href="/admin/owner" v-text="'基本資料'"></a>
            
            <div :class="'btn btn-secondary w-100 p-0 '+((menu_item.indexOf('admins')!=-1)?'active':'')" v-if="is_manager==9 || $.inArray('admins',system)!=-1" style="font-size:1.2em;">
            	<div class="p-2" @click="goto_item('admins')" v-text="'系統管理者'"></div>
            	<div id="admins" class="w-100 mb-0 bg-white h6" v-if="menu_item.indexOf('admins')!=-1 && !menu_close">
                	<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('managers')!=-1)?'action':'')" href="/admin/managers" v-text="'帳號管理'"></a>
            		<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('groups')!=-1)?'action':'')" href="/admin/groups" v-text="'群組管理'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('serviceFee')!=-1)?'action':'')" href="/admin/serviceFee" v-text="'手續費設定'"></a>
                </div>
            </div>    
            
            <a :class="'btn btn-secondary w-100 p-2 '+((weburi.indexOf('users')!=-1)?'active':'')" v-if="is_manager==9 || $.inArray('users',system)!=-1" style="font-size:1.2em;" href="/admin/users" v-text="'會員管理'"></a>
            
            <a :class="'btn btn-secondary w-100 p-2 '+((weburi.indexOf('helper')!=-1)?'active':'')" v-if="is_manager==9 || $.inArray('helper',system)!=-1" style="font-size:1.2em;" href="#" v-text="'好幫手管理'"></a>
            
            <div :class="'btn btn-secondary w-100 p-0 '+((menu_item.indexOf('publishs')!=-1)?'active':'')" v-if="is_manager==9 || $.inArray('publishs',system)!=-1" style="font-size:1.2em;">
            	<div class="p-2" @click="goto_item('publishs')" v-text="'服務刊登管理'"></div>
            	<div id="publishs" class="w-100 mb-0 bg-white h6" v-if="menu_item.indexOf('publishs')!=-1 && !menu_close">
                	<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('service_class')!=-1)?'action':'')" href="#" v-text="'服務類別'"></a>
            		<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('service_class')!=-1)?'action':'')" href="#" v-text="'服務刊登'"></a>
                </div>
            </div>  
            
            <div :class="'btn btn-secondary w-100 p-0 '+((menu_item.indexOf('transfer_records')!=-1)?'active':'')" v-if="is_manager==9 || $.inArray('transfer_records',system)!=-1" style="font-size:1.2em;">
            	<div class="p-2" @click="goto_item('transfer_records')" v-text="'交易管理'"></div>
            	<div id="transfer_records" class="w-100 mb-0 bg-white h6" v-if="menu_item.indexOf('transfer_records')!=-1 && !menu_close">
                	<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('action=manage')!=-1)?'action':'')" href="/admin/transfer_records?item=newebPay&action=manage" v-text="'交易查詢作業'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('action=credit_close')!=-1)?'action':'')" href="/admin/transfer_records?item=newebPay&action=credit_close" v-text="'信用卡請退款作業'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('action=mpg_gateway')!=-1)?'action':'')" href="/admin/transfer_records?item=newebPay&action=mpg_gateway" v-text="'手動新增交易'"></a>
                    <!--<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('action=test')!=-1)?'action':'')" href="/admin/transfer_records?item=newebPay&action=test" v-text="'模擬藍新回傳'"></a>-->
                </div>
            </div>
            
            <div :class="'btn btn-secondary w-100 p-0 '+((menu_item.indexOf('accountings')!=-1)?'active':'')" v-if="is_manager==9 || $.inArray('accountings',system)!=-1" style="font-size:1.2em;">
            	<div class="p-2" @click="goto_item('accountings')" v-text="'帳務管理系統'"></div>
            	<div id="accountings" class="w-100 mb-0 bg-white h6" v-if="menu_item.indexOf('accountings')!=-1 && !menu_close">
                	<div @mouseover="mouseOver('invoice_track')" @mouseout="mouseOut('invoice_track')" :class="'w-100 py-2 position-relative aHover '+((weburi.indexOf('invoice')!=-1)?'action':'')">
                    	<span v-text="'發票管理'"></span>
                        <div id="invoice_track" class="w-100 position-absolute border border-info" style="top:0px; right:-100%; display:none;">
                        	<a class="w-100 d-block py-2 aHover" href="/admin/accountings?item=invoices&action=manage" v-text="'發票資料管理'"></a>
                            <a class="w-100 d-block py-2 aHover" href="/admin/accountings?item=invoices&action=create" v-text="'手動開立發票'"></a>
                            <a class="w-100 d-block py-2 aHover" href="/admin/accountings?item=invoice_tracks&action=create" v-text="'新增發票字軌'"></a>
                            <a class="w-100 d-block py-2 aHover" href="/admin/accountings?item=invoice_tracks&action=manage" v-text="'字軌資料管理'"></a>
                        </div>
                    </div>
                    
                    <div @mouseover="mouseOver('systemAccount')" @mouseout="mouseOut('systemAccount')" :class="'w-100 py-2 position-relative aHover '+((weburi.indexOf('systemAccount')!=-1)?'action':'')">
                    	<span v-text="'系統帳務'"></span>
                        <div id="systemAccount" class="w-100 position-absolute border border-info" style="top:0px; right:-100%; display:none;">
                        	<a class="w-100 d-block py-2 aHover" href="/admin/accountings?item=systemAccount&action=Platformfee_perday" v-text="'費用單日查詢'"></a>
                            <a class="w-100 d-block py-2 aHover" href="/admin/accountings?item=systemAccount&action=Platformfee_search" v-text="'費用單筆查詢'"></a>
                            <a class="w-100 d-block py-2 aHover" href="/admin/accountings?item=systemAccount&action=FeeInstruct" v-text="'商店扣撥款作業'"></a>
                            <a class="w-100 d-block py-2 aHover" href="/admin/accountings?item=systemAccount&action=merchant_manager" v-text="'管理合作商店'"></a>
                            <a class="w-100 d-block py-2 aHover" href="/admin/accountings?item=systemAccount&action=merchant_create" v-text="'建立合作商店'"></a>
                        </div>
                    </div>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('accountings_2')!=-1)?'action':'')" href="#" v-text="'會員帳務'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('accountings_3')!=-1)?'action':'')" href="#" v-text="'好幫手帳務'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('accountings_4')!=-1)?'action':'')" href="#" v-text="'年度獎金'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('accountings_5')!=-1)?'action':'')" href="#" v-text="'回饋金'"></a>
                </div>
            </div>
            <div :class="'btn btn-secondary w-100 p-0 '+((menu_item.indexOf('marketings')!=-1)?'active':'')" v-if="is_manager==9 || $.inArray('marketings',system)!=-1" style="font-size:1.2em;">
            	<div class="p-2" @click="goto_item('marketings')" v-text="'行銷內容管理'"></div>
            	<div id="marketings" class="w-100 mb-0 bg-white h6" v-if="menu_item.indexOf('marketings')!=-1 && !menu_close">
                	<div @mouseover="mouseOver('marketing1')" @mouseout="mouseOut('marketing1')" :class="'w-100 py-2 position-relative aHover '+((weburi.indexOf('pic_tables')!=-1)?'action':'')">
                    	<span v-text="'首頁'"></span>
                        <div id="marketing1" class="w-100 position-absolute border border-info" style="top:0px; right:-100%; display:none;">
                        	<a class="w-100 d-block py-2 aHover" href="/admin/marketings?item=pic_tables&type=1" v-text="'首頁頂部圖片'"></a>
                            <a class="w-100 d-block py-2 aHover" href="/admin/marketings?item=pic_tables&type=2" v-text="'大標題圖片多組'"></a>
                            <a class="w-100 d-block py-2 aHover" href="#" v-text="'背景配色設定'"></a>
                        </div>
                    </div>
            		<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('marketings_2')!=-1)?'action':'')" href="#" v-text="'登入頁面'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('marketings_3')!=-1)?'action':'')" href="#" v-text="'行銷管理'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('marketings_4')!=-1)?'action':'')" href="#" v-text="'Email名單管理'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('marketings_5')!=-1)?'action':'')" href="#" v-text="'視訊管理'"></a>
                </div>
            </div>
            
            <div :class="'btn btn-secondary w-100 p-0 '+((menu_item.indexOf('settings')!=-1)?'active':'')" v-if="is_manager==9 || $.inArray('settings',system)!=-1" style="font-size:1.2em;">
            	<div class="p-2" @click="goto_item('settings')" v-text="'資訊頁面管理'"></div>
            	<div id="settings" class="w-100 mb-0 bg-white h6" v-if="menu_item.indexOf('settings')!=-1 && !menu_close">
                	<a :class="'w-100 d-block py-2 aHover '+((id=='privacy')?'action':'')" href="/admin/settings?id=privacy" v-text="'隱私權政策'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((id=='term_of_use')?'action':'')" href="/admin/settings?id=term_of_use" v-text="'使用者條款'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((id=='how2_help_post_list')?'action':'')" href="/admin/settings?id=how2_help_post_list" v-text="'好幫手條款'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((id=='profit_share_post_list')?'action':'')" href="/admin/settings?id=profit_share_post_list" v-text="'利潤共享'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((id=='tutorial_post_list')?'action':'')" href="/admin/settings?id=tutorial_post_list" v-text="'好幫手教學'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((id=='aboutus_post')?'action':'')" href="/admin/settings?id=aboutus_post" v-text="'關於我門'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((id=='GA_code')?'action':'')" href="/admin/settings?id=GA_code" v-text="'GA code'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((id=='Mixpanel_Code')?'action':'')" href="/admin/settings?id=Mixpanel_Code" v-text="'Mixpanel Code'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((id=='welcome_email')?'action':'')" href="/admin/settings?id=welcome_email" v-text="'歡迎加入'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((id=='referral_email')?'action':'')" href="/admin/settings?id=referral_email" v-text="'推薦他人'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((id=='referral_FB_msg')?'action':'')" href="/admin/settings?id=referral_FB_msg" v-text="'分享到FB'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((id=='email_veri')?'action':'')" href="/admin/settings?id=email_veri" v-text="'Email認證'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((id=='email_veri_comp')?'action':'')" href="/admin/settings?id=email_veri_comp" v-text="'Email認證完成'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((id=='email_account_del')?'action':'')" href="/admin/settings?id=email_account_del" v-text="'刪除帳戶'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((id=='email_reward')?'action':'')" href="/admin/settings?id=email_reward" v-text="'獲得紅利'"></a>
                </div>
            </div>
            
            <div :class="'btn btn-secondary w-100 p-0 '+((menu_item.indexOf('promotions')!=-1)?'active':'')" v-if="is_manager==9 || $.inArray('promotions',system)!=-1" style="font-size:1.2em;">
            	<div class="p-2" @click="goto_item('promotions')" v-text="'折扣促銷管理'"></div>
            	<div id="promotions" class="w-100 mb-0 bg-white h6" v-if="menu_item.indexOf('promotions')!=-1 && !menu_close">
                	<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('promotions_1')!=-1)?'action':'')" href="#" v-text="'Coupon 管理'"></a>
            		<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('promotions_2')!=-1)?'action':'')" href="#" v-text="'獎金與回饋管理'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('promotions_3')!=-1)?'action':'')" href="#" v-text="'調整抽成比率'"></a>
                </div>
            </div>
            
            <a :class="'btn btn-secondary w-100 p-2 '+((weburi.indexOf('videos')!=-1)?'active':'')" v-if="is_manager==9 || $.inArray('videos',system)!=-1" style="font-size:1.2em;" href="/admin/videos" v-text="'影片管理'"></a>
            
            <div :class="'btn btn-secondary w-100 p-0 '+((menu_item.indexOf('datas')!=-1)?'active':'')" v-if="is_manager==9 || $.inArray('datas',system)!=-1" style="font-size:1.2em;">
            	<div class="p-2" @click="goto_item('datas')" v-text="'數據統計與分析'"></div>
            	<div id="datas" class="w-100 mb-0 bg-white h6" v-if="menu_item.indexOf('datas')!=-1 && !menu_close">
                	<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('datas_1')!=-1)?'action':'')" href="#" v-text="'會員統計數據'"></a>
            		<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('datas_2')!=-1)?'action':'')" href="#" v-text="'好幫手統計數據'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('datas_3')!=-1)?'action':'')" href="#" v-text="'服務刊登數據統計'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('datas_4')!=-1)?'action':'')" href="#" v-text="'交易統計數據'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('datas_5')!=-1)?'action':'')" href="#" v-text="'配對LOG統計'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('datas_6')!=-1)?'action':'')" href="#" v-text="'資料分析與AI探索'"></a>
                </div>
            </div>
            
            <div :class="'btn btn-secondary w-100 p-0 '+((menu_item.indexOf('flows')!=-1)?'active':'')" v-if="is_manager==9 || $.inArray('flows',system)!=-1" style="font-size:1.2em;">
            	<div class="p-2" @click="goto_item('flows')" v-text="'外部流量、廣告設定'"></div>
            	<div id="flows" class="w-100 mb-0 bg-white h6" v-if="menu_item.indexOf('flows')!=-1 && !menu_close">
                	<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('flows_1')!=-1)?'action':'')" href="#" v-text="'流量'"></a>
            		<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('flows_2')!=-1)?'action':'')" href="#" v-text="'廣告'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('flows_3')!=-1)?'action':'')" href="#" v-text="'SEO'"></a>
                </div>
            </div>
            
            <div :class="'btn btn-secondary w-100 p-0 '+((menu_item.indexOf('logs')!=-1)?'active':'')" v-if="is_manager==9 || $.inArray('logs',system)!=-1" style="font-size:1.2em;">
            	<div class="p-2" @click="goto_item('logs')" v-text="'日誌(Text file)'"></div>
            	<div id="logs" class="w-100 mb-0 bg-white h6" v-if="menu_item.indexOf('logs')!=-1 && !menu_close">
                	<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('logs_1')!=-1)?'action':'')" href="#" v-text="'系統日誌'"></a>
            		<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('logs_2')!=-1)?'action':'')" href="#" v-text="'Hacking Log'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('logs_3')!=-1)?'action':'')" href="#" v-text="'交易日誌'"></a>
                </div>
            </div>
            
            <div :class="'btn btn-secondary w-100 p-0 '+((menu_item.indexOf('customers')!=-1)?'active':'')" v-if="is_manager==9 || $.inArray('customers',system)!=-1" style="font-size:1.2em;">
            	<div class="p-2" @click="goto_item('customers')" v-text="'客服 Portal'"></div>
            	<div id="customers" class="w-100 mb-0 bg-white h6" v-if="menu_item.indexOf('customers')!=-1 && !menu_close">
                	<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('customers_1')!=-1)?'action':'')" href="#" v-text="'會員資料'"></a>
            		<a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('customers_2')!=-1)?'action':'')" href="#" v-text="'訂單資料'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('customers_3')!=-1)?'action':'')" href="#" v-text="'交易資料'"></a>
                    <a :class="'w-100 d-block py-2 aHover '+((weburi.indexOf('customers_4')!=-1)?'action':'')" href="#" v-text="'補賞金發放'"></a>
                </div>
            </div>
          </div>
        </div>
        <div class="col-10 px-4" style="min-height:500px;">
          @yield('content')
        </div>
      </div>
    </div>
</body>
<script>
new Vue({
  el: "#master_menu",
  data: {
	id: '{{((isset($id))?$id:'')}}',
	weburi: '<?php echo $_SERVER['REQUEST_URI'];?>',
	is_manager: <?php echo Session::get('ownerLevel')?>,
	system: '',
	menu_close: 0,
	menu_item: '',
	
  },
  mounted: function () {
  	var self = this;
	axios.get('/admin/get_system').then(function (response){
		console.log(response.data)
		if(response.data=='error')
			window.location = '/error';
		
		self.system = response.data;
	});
	
	if(self.weburi.indexOf('managers')!=-1 || self.weburi.indexOf('groups')!=-1)
		self.menu_item = 'admins';
	else if(self.weburi.indexOf('settings')!=-1)
		self.menu_item = 'settings';
	else if(self.weburi.indexOf('publishs')!=-1)
		self.menu_item = 'publishs';
	else if(self.weburi.indexOf('transfer_records')!=-1)
		self.menu_item = 'transfer_records';	
	else if(self.weburi.indexOf('accountings')!=-1)
		self.menu_item = 'accountings';
	else if(self.weburi.indexOf('marketings')!=-1 || self.weburi.indexOf('pic_tables')!=-1)
		self.menu_item = 'marketings';		
		
  },
  methods: {
  	goto_item: function(x){
		var self = this;
		self.menu_item = x;
		self.menu_close = (($("#"+self.menu_item).length)?1:0);
	},
	mouseOver: function(x){
		$("#"+x).show();
	},
	mouseOut: function(x){
		$("#"+x).hide();
	}
	
  }
  
})
</script>
</html>
