@extends('admin.master')
@section('content')
<div id="app">
	<div class="w-100 p-2 d-table text-center mb-4">
        <div class="w-25 float-left text-left">
        	<a class="btn btn-primary" v-if="mode" href="/admin/users" v-text="((!mode)?'':'返回')"></a>
        </div>    
        <div class="w-50 float-left mx-auto">
        	<input type="text" class="form-control float-left w-75" id="search" v-model="search_text" @keyup.enter="searchBtn" placeholder="搜尋會員任一字串" />
            <a href="javascript:void(0)" @click="searchBtn" class="btn btn-primary">搜尋</a>
        </div>  
        <div class="w-25 float-right text-right">
        	<h3 v-text="'會員'+((mode=='edit')?'修改':'列表')"></h3>
        </div>
    </div>
    <div class="w-100" v-if="mode=='edit'">
    	<form id="mainFrm" action="/admin/users_pt" method="post">
          @csrf
          <input type="hidden" name="mode" v-model="mode" />
          <input type="hidden" v-if="mode=='edit'" name="id" v-model="user.usr_id" />
              <table class="table table-light table-bordered" >
                  <tr>
                    <th class="w-25 text-center" style="vertical-align:middle">狀態</th>
                    <td class="w-75 px-2 py-1">
                    	<div class="form-check form-check-inline pt-3">
                          <input class="form-check-input" type="radio" name="usr_status" value="-1" v-model="user.usr_status" id="a" >
                          <label class="form-check-label" for="a">停權</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="usr_status" value="0" v-model="user.usr_status" id="b">
                          <label class="form-check-label" for="b">待驗證</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="usr_status" value="1" v-model="user.usr_status" id="c">
                          <label class="form-check-label" for="c">正常</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="usr_status" value="4" v-model="user.usr_status" id="d">
                          <label class="form-check-label" for="d">VIP</label>
                        </div>
                    	<div class="rounded-circle border float-right" :style="'width:60px;height:60px;background:url('+((user.usr_photo)?'/avatar/small/'+user.usr_photo:'/images/person-icon.jpg')+') no-repeat center center / auto 100% #eee;'"></div>  
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">登入身份</th>
                    <td class="w-75">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="open_offer_setting" value="0" v-model="user.open_offer_setting" id="c" >
                        <label class="form-check-label" for="c">客戶</label>
                        </div>
                        <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="open_offer_setting" value="1" v-model="user.open_offer_setting" id="h">
                        <label class="form-check-label" for="h">好幫手</label>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">真實姓名</th>
                    <td class="w-75">
                        <input type="text" class="form-control w-25 d-inline" placeholder="姓" name="last_name" id="last_name" v-model="user.last_name">
                        <input type="text" class="form-control w-25 ml-2 d-inline" placeholder="名" name="first_name" id="first_name" v-model="user.first_name">
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">手機號碼</th>
                    <td class="w-75">
                      <input type="text" class="form-control d-inline" name="phone_nat_code" v-model="user.phone_nat_code" style="width:100px;" />
                      <input type="text" class="form-control w-50 d-inline ml-2" name="phone_number" v-model="user.phone_number" placeholder="例:901234567">
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">Email</th>
                    <td class="w-75">
                      <input type="email" class="form-control w-75 d-inline" name="email" id="email" v-model="user.email" required  readonly="readonly" placeholder="請填寫郵件信箱">
                      <span class="w-25 d-inline text-center pl-2">
                      	<span v-if="!user.email_validated" class="text-danger" v-text="'尚未驗證'"></span>
                        <span v-if="user.email_validated" class="text-success" v-text="'已驗證'"></span>
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">性別</th>
                    <td class="w-75">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sex" id="w" value="2" v-model="user.sex">
                        <label class="form-check-label" for="w">女</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sex" id="m" value="1" v-model="user.sex">
                        <label class="form-check-label" for="m">男</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sex" id="x" value="0" v-model="user.sex">
                        <label class="form-check-label" for="x">不揭露</label>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">常用地址</th>
                    <td class="w-75">
                      <div class="w-100" v-for="(address,index) in user.addr" v-text="(index+1)+'. '+address.zipcode+' '+address.county+address.nat+address.addr"></div>
                    </td>
                  </tr>
                  <tr>
                      <td colspan="2" class="text-center">
                      	<a href="javascript:void(0)" @click="sendform" :class="'btn btn-'+((mode=='edit')?'success':'primary')" v-text="'送出'+((mode=='edit')?'修改':'新增')"></a></td>
                  </tr>
              </table>
        </form>
    </div>
    <div class="w-100" v-if="!mode || mode=='search'">
    	<table class="table table-hover" >
            <thead>
                <tr>
                    <th>會員名稱</th>
                    <th>狀態</th>
                    <th>Email</th>
                    <th>電話號碼</th>
                    <th style="width:200px;">建立日期</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-secondary" v-for="user in users.data" @click="get_user(user.usr_id)" style="cursor:pointer">
                    <td v-text="((user.last_name || user.first_name)?user.last_name+' '+user.first_name:'')"></td>
                    <td v-html="((user.usr_status)?'正常':'未驗證')"></td>
                    <td v-text="user.email"></td>
                    <td v-text="user.phone_number"></td>
                    <td v-text="user.created_at"></td>
                </tr>	
            </tbody>
        </table>
        <div class="w-100 d-table py-2 text-center border-top" v-if="users.last_page>1">
            <a class="btn btn-light btn-sm float-left" v-if="parseInt(users.current_page) > 1" href="javascript:void(0)" @click="go_content_page(parseInt(users.current_page-1))">上一頁</a>	
            <span class="h5" v-if="parseInt(users.current_page) > 1" v-text="users.current_page"></span>
            <a class="btn btn-light btn-sm float-right" v-if="users.last_page>users.current_page" href="javascript:void(0)" @click="go_content_page(parseInt(users.current_page)+1)">下一頁</a>
        </div>
    </div>
</div>
<script>
new Vue({
  el: "#app",
  data: {
	users: '',
	mode: '',
	user: '',
	search_text: '',
	error_message: '',
	thisPage: ''
  },
  mounted: function () {
  	var self = this;
	@if($mode=='edit')
		self.get_user('{{$id}}');
	@else
		self.list_user();
	@endif
  },
  methods: {
  	get_user: function(x){
		var self = this;
		self.mode = 'edit'
		axios.get('/admin/get_users?mode='+self.mode+'&id='+x).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.user = response.data.user;
			
		});
	},
	sendform: function(){
		var self = this;
		var chk = 1;
		if(!self.user.first_name)
		{
			$("#first_name").css({"border":"1px solid #a02"});
			chk = 0;	
		}else
			$("#first_name").css({"border":"1px solid #ccc"});
		
		if(!self.user.last_name)
		{
			$("#last_name").css({"border":"1px solid #a02"});
			chk = 0;	
		}else
			$("#last_name").css({"border":"1px solid #ccc"});
				
		if(chk)
			$('#mainFrm').submit();	
	},
	searchBtn: function(){
		var self = this;
		if(self.search_text)
		{
			$("#search").css({"border":"1px solid #ccc"});
			self.mode = 'search';
			axios.get('/admin/get_users?mode=search&text='+self.search_text).then(function (response){
				console.log(response.data)
				if(response.data=='error')
					window.location = '/error';
				
				self.users = response.data.users;
				
			});		
		}else
			$("#search").css({"border":"1px solid #a02"});
	},
	go_content_page: function(page){
	  
	  var self = this;
	  self.thisPage = page;
	  axios.get('/admin/get_users?mode=turn&text='+self.search_text+'&page='+page).then(function (response){
			console.log(response.data)		
		  self.users = response.data.users;
	 })
	  
	},
	list_user: function(){
		var self = this;
		self.mode = '';
		axios.get('/admin/get_users?mode=').then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.users = response.data.users;
		});
	}
	
  }
  
})
</script>   
@stop