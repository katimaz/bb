@extends('admin.master')
@section('content')
<div id="app">
	<div class="w-100 p-2 d-table">
        <h3 class="float-right" v-text="'基本資料'"></h3>
    </div>
    <div class="w-100">
    	<form id="mainFrm" action="/admin/owner_pt" method="post">
          @csrf
          <input type="hidden" name="modifyPassword" v-model="change_psw" />
          <input type="hidden" name="old_account" id="old_account" v-model="old_account" />
              <table class="table table-light table-bordered" >
                  <tr>
                    <th class="w-25 text-center">群組(部門)</th>
                    <td class="w-75">
                      <select class="form-control" name="group_id" id="group_id" v-model="owner.group_id" style="max-width:300px;">
                      	<option v-for="group in groups" :value="group.group_id" v-text="group.group_name" selected="selected"></option> 
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">帳號</th>
                    <td class="w-75">
                      <input type="text" class="form-control float-left" name="adm_account" id="adm_account" v-model="owner.adm_account" @blur="chk_account" placeholder="填寫帳號" required="required" maxlength="32" style="max-width:300px;" />
                      <span class="d-block float-left ml-2 h5 text-danger pt-2" v-if="error_message" v-text="error_message"></span>
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">名稱</th>
                    <td class="w-75">
                      <input type="text" class="form-control" name="adm_name" id="adm_name" v-model="owner.adm_name" placeholder="填寫姓名" required="required" style="max-width:300px;" />
                    </td>
                  </tr>
                  <tr v-if="!change_psw">
                    <th class="w-25 text-center">密碼</th>
                    <td class="w-75">
                      <a class="btn btn-primary" href="javascript:void(0)" @click="change_psw=1" v-text="'更換密碼'"></a>
                    </td>
                  </tr>
                  <tr v-if="change_psw">
                    <th class="w-25 text-center">取個密碼</th>
                    <td class="w-75">
                      <input type="password" class="form-control float-left" name="password" id="password" value="" placeholder="填寫Password" required="required" style="max-width:300px;" />
                      <a class="btn btn-primary btn-sm float-left ml-2 mt-1" href="javascript:void(0)" @click="change_psw=''" v-text="'關閉'"></a>
                    </td>
                  </tr>
                  <tr v-if="change_psw">
                    <th class="w-25 text-center">確認密碼</th>
                    <td class="w-75">
                      <input type="password" class="form-control" name="chk_password" id="chk_password" value="" placeholder="再一次確認密碼" required="required" style="max-width:300px;" />
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">Email</th>
                    <td class="w-75">
                      <input type="text" class="form-control w-100" name="adm_email" v-model="owner.adm_email" placeholder="填寫Email" style="max-width:500px;" />
                    </td>
                  </tr>
                  <tr>
                      <td colspan="2" class="text-center">
                      	<a href="javascript:void(0)" @click="sendform" class="btn btn-success" v-text="'送出修改'"></a></td>
                  </tr>
              </table>
        </form>
    </div>
</div>
<script>
new Vue({
  el: "#app",
  data: {
	old_account: '',
	change_psw: '',
	owner: {adm_account:'',adm_email:'',adm_email:'',adm_name:'',adm_status:'',created_at:'',group_id:''},
	groups: '',
	error_message: ''
	
  },
  mounted: function () {
  	var self = this;
	self.get_owner();
  },
  methods: {
  	get_owner: function(x){
		var self = this;
		axios.get('/admin/get_owner').then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.groups = response.data.groups;
			self.owner = response.data.owner;
			self.old_account = response.data.owner.adm_account;
			
		});
	},
	sendform: function(){
		var self = this;
		var chk = 1;
		if(!self.owner.group_id)
		{
			$("#group_id").css({"border":"1px solid #a02"});
			chk = 0;	
		}else
			$("#group_id").css({"border":"1px solid #ccc"});
		
		if(!self.owner.adm_account)
		{
			$("#adm_account").css({"border":"1px solid #a02"});
			chk = 0;	
		}else
			$("#adm_account").css({"border":"1px solid #ccc"});
		
		if(!self.owner.adm_name)
		{
			$("#adm_name").css({"border":"1px solid #a02"});
			chk = 0;	
		}else
			$("#adm_name").css({"border":"1px solid #ccc"});
		
		if(self.change_psw )
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
	chk_account: function(){
		var self = this;
		if($("#old_account").val()!=self.owner.adm_account)
		{
			axios.get('/admin/chk_account_repeat?id='+self.owner.adm_account).then(function (response){
				console.log(response.data)
				if(response.data=='error')
					window.location = '/error';
				
				if(response.data>0)
				{
					self.error_message = '此帳號重複，請重填寫';
					self.owner.adm_account = '';
				}else
					self.error_message = '';	
				
			});
		}else
			self.error_message = '';
	}
	
  }
  
})
</script>   
@stop