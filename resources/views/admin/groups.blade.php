@extends('admin.master')
@section('content')
<div id="app">
	<div class="w-100 p-2 d-table">
        <a class="btn btn-primary pull-right" href="javascript:void(0)" @click="((!mode)?add_group():list_group())" v-text="((!mode)?'新增群組':'返回上一頁')"></a>  
        <h3 class="float-right" v-text="'群組'+((mode=='add')?'新增':((mode=='edit'))?'修改':'列表')"></h3>
    </div>
    <div class="w-100" v-if="mode">
    	<form id="mainFrm" action="/admin/groups_pt" method="post">
          @csrf
          <input type="hidden" name="mode" v-model="mode" />	      
          <input type="hidden" name="group_setting" v-model="group.group_setting" />
          <input type="hidden" v-if="mode=='edit'" name="group_id" v-model="group.group_id" />
              <table class="table table-light table-bordered" >
                  <tr v-if="mode=='edit' && is_admin">
                    <th class="w-25 text-center">狀態</th>
                    <td class="w-75">
                      <div class="d-table" style="min-width:120px;">
                          <div class="w-50 float-left">
                            <input type="checkbox" name="group_status" id="status" class="d-none" v-model="group.group_status" />
                            <label :class="'d-block position-relative mx-auto '+((group.group_status)?'bg-success':'bg-secondary')" for="status" style="width:60px;height:30px; border-radius:99px; cursor:pointer;">
                                <div class="rounded-circle position-absolute" :style="'top:0px;width:30px;height:30px;background-color:#f5f5f5;'+((group.group_status)?'right:0px':'left:0px')"></div>
                            </label>
                          </div>
                          <div :class="'w-50 float-right h5 pt-2 pl-2 '+((!group.group_status)?'text-secondary':'text-dark')" v-text="((!group.group_status)?'停止':'開通')"></div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">群組名稱</th>
                    <td class="w-75">
                      <input type="text" class="form-control float-left" name="group_name" id="group_name" v-model="group.group_name" placeholder="填寫群組" required="required" maxlength="64" style="max-width:400px;" />
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">群組主管</th>
                    <td class="w-75">
                      <select class="form-control" name="group_manager" v-model="group.group_manager" style="max-width:300px;">
                      	<option value=""></option>
                        <option v-for="admin in admins" :value="admin.id" v-text="admin.adm_name"></option> 
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">設定權限</th>
                    <td class="w-75">
                    	<div class="w-100 d-table" id="settingDiv">
                          <div class="float-left my-1 mr-3 h5 text-primary" v-for="(setting,index) in settings">
                            <input type="checkbox" @change="change_setting(setting.id,index)" :id="'setting_'+index" :checked="$.inArray(setting.id,group.group_setting)!=-1" style="width:18px;" />
                            <label :for="'setting_'+index" v-text="setting.val"></label>
                          </div>
                        </div>
                    </td>
                  </tr>
                  <tr v-if="mode=='edit' && members.length">
                    <th class="w-25 text-center">組員</th>
                    <td class="w-75">
                    	<div class="w-100 d-table">
                          <a class="float-left my-1 mr-3 h5 btn btn-light" :href="'/admin/managers?mode=edit&id='+member.adm_account" title="修改組員" v-for="member in members" v-text="member.adm_name"></a>
                        </div>
                    </td>
                  </tr>
                  <tr>
                      <td colspan="2" class="text-center">
                      	<a href="javascript:void(0)" v-if="mode=='edit'" @click="delgroup" :class="'btn btn-danger float-left '+((members.length)?'disabled':'')" v-text="'刪除'"></a>
                        <a href="javascript:void(0)" @click="sendform" :class="'btn btn-'+((mode=='edit')?'success':'primary')" v-text="'送出'+((mode=='edit')?'修改':'新增')"></a></td>
                  </tr>
              </table>
        </form>
    </div>
    <div class="w-100" v-if="!mode">
    	<table class="table table-hover" >
            <thead>
                <tr>
                    <th>群組名稱</th>
                    <th>狀態</th>
                    <th>主管</th>
                    <th>權限設定</th>
                    <th>建立日期</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-secondary" v-for="group in groups" @click="get_group(group.group_id)" style="cursor:pointer">
                    <td v-text="group.group_name"></td>
                    <td v-html="((group.group_status)?'<b>運作中</b>':'停止中')"></td>
                    <td v-text="group.group_manager"></td>
                    <td>
                    	<span class="d-block float-left m-1" v-if="group.group_setting" v-for="(setting,index) in group.group_setting" v-text="setting"></span>
                    </td>
                    <td v-text="group.created_at"></td>
                </tr>	
            </tbody>
        </table>
    </div>
</div>
<script>
new Vue({
  el: "#app",
  data: {
	admins: '',  
	groups: '',
	settings: '',
	mode: '',
	is_admin: <?php echo ((Session::get('ownerLevel')>7)?1:0);?>,
	group: {group_status:'',group_name:'',group_setting:[],group_manager:'',group_id:''},
	error_message: '',
	members: ''
	
  },
  mounted: function () {
  	var self = this;
	@if($mode=='edit')
		self.get_group('{{$id}}');
	@else
		self.list_group();
	@endif
  },
  methods: {
  	topWindowResize(event) { 
		this.topWidth = (($(window).width()>1200)?1200:$(window).width());
		this.menu_width = ((parseInt(this.topWidth)>1000)?150*$("#top_menu_son a").length:100*$("#top_menu_son a").length)
		this.topHeight=$("#top_nav").height();
	},
	get_group: function(x){
		var self = this;
		self.mode = 'edit';
		axios.get('/admin/get_groups?mode='+self.mode+'&id='+x).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.admins = response.data.admins;
			self.group = response.data.group;
			self.settings = response.data.settings;
			self.members = response.data.members;
			
		});
	},
	sendform: function(){
		var self = this;
		var chk = 1;
		if(!self.group.group_name)
		{
			$("#group_name").css({"border":"1px solid #a02"});
			chk = 0;	
		}else
			$("#group_name").css({"border":"1px solid #ccc"});
		
		
		if(!self.group.group_setting.length)
		{
			$("#settingDiv").css({"border":"1px solid #a02"});
			chk = 0;	
		}else
			$("#settingDiv").css({"border":"none"});
		
		if(chk)
			$('#mainFrm').submit();	
	},
	delgroup: function(){
		var self = this;
		if(confirm('確定要刪除此筆資料?'))
			window.location = '/admin/groups?mode=del&id='+self.group.group_id;
	},
	change_setting: function(id,index){
		var self = this;
		var checked = $("#setting_"+index).prop('checked');
		if(checked)
			self.group.group_setting.push(id);
		else
		{
			//console.log(self.group.group_setting)
			var num = $.inArray(id,self.group.group_setting);
			if(num!=-1)
				self.group.group_setting.splice(num,1);
				
		}		
		
	},
	add_group: function(){
		var self = this;
		self.mode = 'add';
		self.group = {group_status:'',group_name:'',group_setting:[],group_manager:'',group_id:''};
		console.log(self.group.group_setting)
	},
	list_group: function(){
		var self = this;
		self.mode = '';
		axios.get('/admin/get_groups?mode='+self.mode).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.groups = response.data.groups;
			self.admins = response.data.admins;
			self.settings = response.data.settings;
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