@extends('admin.master')
@section('content')
<style>
.photoZone{width:200px; height:120px; overflow:hidden;padding:2px; box-sizing:border-box;background:#f5f5f5 url('/images/add_photo.png')no-repeat; background-position:center; background-size:48px 48px; position:relative; border:2px dashed rgb(3,110,184); text-align:center;}
.photoZone a{width:20px; height:20px; position:absolute; top:-2px; right:0px;}
.photoZone span{width:100%; height:100%; display:block; }
.photoZone input[type='file']{width:100%;min-height:100%;position:absolute;top:0px;left:0px;opacity:0; cursor:pointer;}
.photoZone img{width:100%; height:auto;}
.photoZone div{width:100%; height:100%; overflow:hidden;}
</style>
<div id="app">
	<div class="w-100 p-2 d-table">
        <a class="btn btn-primary pull-right" href="javascript:void(0)" @click="((!mode)?add_pic_table():list_pic_table())" v-text="((!mode)?'新增照片':'返回')"></a>  
        <h3 class="float-right" v-text="((type==1)?'首頁頂部大圖':'首頁多照片')+((mode=='add')?'新增':((mode=='edit'))?'修改':'列表')"></h3>
    </div>
    <div class="w-100" v-if="mode">
    	<form id="mainFrm" action="/admin/marketings_pt" method="post" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="mode" v-model="mode" />
          <input type="hidden" name="type" v-model="type" />
          <input type="hidden" name="item" value="pic_tables" />
          <input type="hidden" v-if="mode=='edit'" name="pic_id" v-model="pic_table.pic_id" />
          <input type="hidden" v-if="mode=='edit'" name="old_photo" v-model="old_photo" />
              <table class="table table-light table-bordered" >
                  <tr>
                    <th class="w-25 text-center">狀態/類型</th>
                    <td class="w-75">
                      <div class="d-table" style="min-width:120px;">
                          <div class="w-50 float-left">
                            <input type="checkbox" name="pic_status" id="pic_status" class="d-none" v-model="pic_table.pic_status" />
                            <label :class="'d-block position-relative mx-auto '+((pic_table.pic_status)?'bg-success':'bg-secondary')" for="pic_status" style="width:60px;height:30px; border-radius:99px; cursor:pointer;">
                                <div class="rounded-circle position-absolute" :style="'top:0px;width:30px;height:30px;background-color:#f5f5f5;'+((pic_table.pic_status)?'right:0px':'left:0px')"></div>
                            </label>
                          </div>
                          <div :class="'w-50 float-right h5 pt-2 pl-2 '+((!pic_table.pic_status)?'text-secondary':'text-dark')" v-text="((!pic_table.pic_status)?'停止':'開通')"></div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">上傳照片</th>
                    <td class="w-75">
                     	<div class="w-100 d-table" id="uploadDiv">
                          <div :class="'w-'+((type==1)?'100':'25')+' float-left photoZone border-primary'">
                              <input type="file" name="photo" id="photo" @change="upload_image" />
                              <div :style="((pic_table.home_frontpage_pic)?'background:url('+pic_table.home_frontpage_pic+') center center / 100% auto no-repeat #eee;':'')"></div>
                              <input type="hidden" name="up_photo" v-model="pic_table.home_frontpage_pic" />
                          </div>
                        </div>
                    </td>
                  </tr>
                  
                  <tr>
                      <td colspan="2" class="text-center">
                      	<a href="javascript:void(0)" v-if="mode=='edit'" @click="del_pic_table" class="btn btn-danger float-left" v-text="'刪除'"></a>
                        <a href="javascript:void(0)" @click="sendform" :class="'btn btn-'+((mode=='edit')?'success':'primary')" v-text="'送出'+((mode=='edit')?'修改':'新增')"></a></td>
                  </tr>
              </table>
        </form>
    </div>
    <div class="w-100" v-if="!mode">
    	<table class="table table-hover" >
            <thead>
                <tr>
                    <th>照片</th>
                    <th>狀態</th>
                    <th class="w-25">建立日期</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-secondary" v-for="pic_table in pic_tables" @click="get_pic_table(pic_table.pic_id)" style="cursor:pointer">
                    <td>
                    	<div :style="'width:100px; height:60px; background:url('+((pic_table.home_frontpage_pic)?'/home/small/'+pic_table.home_frontpage_pic:'')+') no-repeat center center/100% auto #eee;'"></div>
                    </td>
                    <td v-html="pic_table.pic_status"></td>
                    <td v-text="pic_table.created_at"></td>
                </tr>	
            </tbody>
        </table>
    </div>
</div>
<script>
new Vue({
  el: "#app",
  data: {
	type: '<?php echo ((isset($type))?$type:'1')?>',
	pic_tables: '',
	mode: '',
	pic_table: '',
	old_photo: ''
	
  },
  mounted: function () {
  	var self = this;
	@if($mode=='edit')
		self.get_pic_table('{{$id}}');
	@else
		self.list_pic_table();
	@endif
  },
  methods: {
  	get_pic_table: function(x){
		var self = this;
		self.mode = 'edit'
		axios.get('/admin/get_marketings?item=pic_tables&mode='+self.mode+'&type='+self.type+'&id='+x).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.pic_table = response.data.pic_table;
			self.old_photo = response.data.old_photo;
		});
	},
	sendform: function(){
		var self = this;
		var chk = 1;
		if(!self.pic_table.home_frontpage_pic)
		{
			alert('請上傳一張照片!');
			chk = 0;
		}else
			$("#uploadDiv").css({"border":"none"});	
		
		if(chk)
			$('#mainFrm').submit();	
	},
	del_pic_table: function(){
		var self = this;
		if(confirm('確定要刪除此筆資料?'))
			window.location = '/admin/marketings?item=pic_tables&mode=del&type='+self.type+'&id='+self.pic_table.pic_id;
	},
	add_pic_table: function(){
		this.mode = 'add';
		this.pic_table = {pic_id:'',pic_status:0,home_frontpage_pic:''};
	},
	list_pic_table: function(){
		var self = this;
		self.mode = '';
		axios.get('/admin/get_marketings?item=pic_tables&mode='+self.mode+'&type='+self.type).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			self.pic_tables = response.data.pic_tables;
		});
	},
	upload_image: function(e)
	{
		
		var files = e.target.files || e.dataTransfer.files;
	   	if(!files.length)
		  return; 
		
		var self = this;
		if(files[0].type.indexOf('image')!=-1 && files[0].type.indexOf('jpeg')!=-1)
		{
			var image = URL.createObjectURL(files[0])
			var num = e.target.id.substr(5);
			self.pic_table.home_frontpage_pic = image
		}else
		{
			alert('上傳檔案只接受JPG檔!');
			self.pic_table.home_frontpage_pic = '';
		}
		//console.log(self.article_add)
		
	}
  }
  
})
</script>   
@stop