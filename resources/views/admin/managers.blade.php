@extends('admin.master')
@section('content')
<div id="app">
	<div class="w-100 p-2 d-table">
        <a class="btn btn-primary pull-right" href="javascript:void(0)" @click="((!mode)?add_manager():list_manager())" v-text="((!mode)?'新增帳號':'返回上一頁')"></a>  
        <h3 class="float-right" v-text="'帳號'+((mode=='add')?'新增':((mode=='edit'))?'修改':'列表')"></h3>
    </div>
    <div class="w-100" v-if="mode">
    	<form id="mainFrm" action="/admin/managers_pt" method="post">
          @csrf
          <input type="hidden" name="mode" v-model="mode" />
          <input type="hidden" v-if="mode=='edit'" name="modifyPassword" v-model="change_psw" />
          <input type="hidden" v-if="mode=='edit'" name="old_account" id="old_account" v-model="old_account" />
              <table class="table table-light table-bordered" >
                  <tr v-if="mode=='edit' && is_admin">
                    <th class="w-25 text-center">狀態</th>
                    <td class="w-75">
                      <div class="d-table" style="min-width:120px;">
                          <div class="w-50 float-left">
                            <input type="checkbox" name="sdm_status" id="status" class="d-none" v-model="manager.adm_status" />
                            <label :class="'d-block position-relative mx-auto '+((manager.adm_status)?'bg-success':'bg-secondary')" for="status" style="width:60px;height:30px; border-radius:99px; cursor:pointer;">
                                <div class="rounded-circle position-absolute" :style="'top:0px;width:30px;height:30px;background-color:#f5f5f5;'+((manager.adm_status)?'right:0px':'left:0px')"></div>
                            </label>
                          </div>
                          <div :class="'w-50 float-right h5 pt-2 pl-2 '+((!manager.adm_status)?'text-secondary':'text-dark')" v-text="((!manager.adm_status)?'停權':'開通')"></div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">群組(部門)</th>
                    <td class="w-75">
                      <select class="form-control" name="group_id" id="group_id" v-model="manager.group_id" style="max-width:300px;">
                      	<option value="" v-text="'請選擇'"></option>
                        <option v-for="group in groups" :value="group.group_id" v-text="group.group_name" selected="selected"></option> 
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">帳號</th>
                    <td class="w-75">
                      <input type="text" class="form-control float-left" name="adm_account" id="adm_account" v-model="manager.adm_account" @blur="chk_account" placeholder="填寫帳號" required="required" maxlength="32" style="max-width:300px;" />
                      <span class="d-block float-left ml-2 h5 text-danger pt-2" v-if="error_message" v-text="error_message"></span>
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">名稱</th>
                    <td class="w-75">
                      <input type="text" class="form-control" name="adm_name" id="adm_name" v-model="manager.adm_name" placeholder="填寫姓名" required="required" style="max-width:300px;" />
                    </td>
                  </tr>
                  <tr v-if="mode=='edit' && !change_psw">
                    <th class="w-25 text-center">密碼</th>
                    <td class="w-75">
                      <a class="btn btn-primary" href="javascript:void(0)" @click="change_psw=1" v-text="'更換密碼'"></a>
                    </td>
                  </tr>
                  <tr v-if="mode=='add' || change_psw">
                    <th class="w-25 text-center">取個密碼</th>
                    <td class="w-75">
                      <input type="password" class="form-control float-left" name="password" id="password" value="" placeholder="填寫Password" required="required" style="max-width:300px;" />
                      <a class="btn btn-primary btn-sm float-left ml-2 mt-1" v-if="mode=='edit'" href="javascript:void(0)" @click="change_psw=''" v-text="'關閉'"></a>
                    </td>
                  </tr>
                  <tr v-if="mode=='add' || change_psw">
                    <th class="w-25 text-center">確認密碼</th>
                    <td class="w-75">
                      <input type="password" class="form-control" name="chk_password" id="chk_password" value="" placeholder="再一次確認密碼" required="required" style="max-width:300px;" />
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">Email</th>
                    <td class="w-75">
                      <input type="text" class="form-control" name="adm_email" v-model="manager.adm_email" placeholder="填寫Email" style="max-width:300px;" />
                    </td>
                  </tr>
                  <tr>
                      <td colspan="2" class="text-center">
                      	<a href="javascript:void(0)" v-if="mode=='edit'" @click="delmanager" class="btn btn-danger float-left" v-text="'刪除'"></a>
                        <a href="javascript:void(0)" @click="sendform" :class="'btn btn-'+((mode=='edit')?'success':'primary')" v-text="'送出'+((mode=='edit')?'修改':'新增')"></a></td>
                  </tr>
              </table>
        </form>
    </div>
    <div class="w-100" v-if="!mode">
    	<table class="table table-hover" >
            <thead>
                <tr>
                    <th>帳號</th>
                    <th>姓名</th>
                    <th>主管</th>
                    <th>Email</th>
                    <th>部門</th>
                    <th>建立日期</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="manager in managers" @click="get_manager(manager.adm_account)" style="cursor:pointer">
                    <td v-text="manager.adm_account"></td>
                    <td v-text="manager.adm_name"></td>
                    <td v-text="manager.adm_manager"></td>
                    <td v-text="manager.adm_email"></td>
                    <td v-text="manager.group_name"></td>
                    <td v-text="manager.created_at"></td>
                </tr>	
            </tbody>
        </table>
    </div>
</div>
<script>
new Vue({
  el: "#app",
  data: {
	weburl: '<?php echo $_SERVER['HTTP_HOST'];?>',
	managers: '',
	mode: '',
	is_admin: {{((Session::get('ownerLevel')>7)?1:0)}},
	old_account: '',
	change_psw: '',
	manager: {adm_account:'',adm_email:'',adm_email:'',adm_name:'',adm_status:'',created_at:'',group_id:''},
	groups: '',
	error_message: ''
	
  },
  mounted: function () {
  	var self = this;
	@if($mode=='edit')
		self.get_manager('{{$id}}');
	@else
		self.list_manager();
	@endif
  },
  methods: {
  	topWindowResize(event) { 
		this.topWidth = (($(window).width()>1200)?1200:$(window).width());
		this.menu_width = ((parseInt(this.topWidth)>1000)?150*$("#top_menu_son a").length:100*$("#top_menu_son a").length)
		this.topHeight=$("#top_nav").height()
	},
	get_manager: function(x){
		var self = this;
		self.mode = 'edit'
		axios.get('/admin/get_managers?mode='+self.mode+'&id='+x).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.groups = response.data.groups;
			self.manager = response.data.manager;
			self.old_account = response.data.manager.adm_account;
			
		});
	},
	sendform: function(){
		var self = this;
		var chk = 1;
		if(!self.manager.group_id)
		{
			$("#group_id").css({"border":"1px solid #a02"});
			chk = 0;	
		}else
			$("#group_id").css({"border":"1px solid #ccc"});
		
		if(!self.manager.adm_account)
		{
			$("#adm_account").css({"border":"1px solid #a02"});
			chk = 0;	
		}else
			$("#adm_account").css({"border":"1px solid #ccc"});
		
		if(!self.manager.adm_name)
		{
			$("#adm_name").css({"border":"1px solid #a02"});
			chk = 0;	
		}else
			$("#adm_name").css({"border":"1px solid #ccc"});
		
		if(self.mode=='add' || self.change_psw )
		{
			if(!$("#password").val() || $("#password").val()!=$("#chk_password").val())
			{
				$("#password").css({"border":"1px solid #a02"});
				$("#chk_password").css({"border":"1px solid #a02"});
				chk = 0;
			}else
			{
				$("#password").css({"border":"1px solid #ccc"});
				$("#chk_password").css({"border":"1px solid #ccc"});				
			}
		}
		if(chk)
			$('#mainFrm').submit();	
	},
	delmanager: function(){
		var self = this;
		if(confirm('確定要刪除此筆資料?'))
			window.location = '/admin/managers?mode=del&id='+self.manager.adm_account;
	},
	chk_account: function(){
		var self = this;
		if(self.mode=='edit' && $("#old_account").val()!=self.manager.adm_account)
		{
			axios.get('/admin/chk_account_repeat?id='+self.manager.adm_account).then(function (response){
				console.log(response.data)
				if(response.data=='error')
					window.location = '/error';
				
				if(response.data>0)
				{
					self.error_message = '此帳號重複，請重填寫';
					self.manager.adm_account = '';
				}else
					self.error_message = '';	
				
			});
		}else
			self.error_message = '';
	},
	add_manager: function(){
		this.mode = 'add';
		this.manager = {adm_account:'',adm_email:'',adm_email:'',adm_name:'',adm_status:'',created_at:'',group_id:''};
	},
	list_manager: function(){
		var self = this;
		self.mode = '';
		axios.get('/admin/get_managers?mode='+self.mode).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.groups = response.data.groups;
			self.managers = response.data.managers;
			
		});
	}
	
  },
  created: function() {
	window.addEventListener('resize', this.topWindowResize);
  },
  beforeDestroy: function () {
	window.removeEventListener('resize', this.topWindowResize)
  }
  
})
</script>   
@stop