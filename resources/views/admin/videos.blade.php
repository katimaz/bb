@extends('admin.master')
@section('content')
<div id="app">
	<div class="w-100 p-2 d-table">
        <a class="btn btn-primary pull-right" href="javascript:void(0)" @click="((!mode)?add_video():list_video())" v-text="((!mode)?'新增影片':'返回')"></a>  
        <h3 class="float-right" v-text="'影片'+((mode=='add')?'新增':((mode=='edit'))?'修改':'列表')"></h3>
    </div>
    <div class="w-100" v-if="mode">
    	<form id="mainFrm" action="/admin/videos_pt" method="post">
          @csrf
          <input type="hidden" name="mode" v-model="mode" />
          <input type="hidden" v-if="mode=='edit'" name="video_id" v-model="video.video_id" />
              <table class="table table-light table-bordered" >
                  <tr>
                    <th class="w-25 text-center">狀態</th>
                    <td class="w-75">
                      <div class="d-table" style="min-width:120px;">
                          <div class="w-50 float-left">
                            <input type="checkbox" name="status" id="status" class="d-none" v-model="video.status" />
                            <label :class="'d-block position-relative mx-auto '+((video.status)?'bg-success':'bg-secondary')" for="status" style="width:60px;height:30px; border-radius:99px; cursor:pointer;">
                                <div class="rounded-circle position-absolute" :style="'top:0px;width:30px;height:30px;background-color:#f5f5f5;'+((video.status)?'right:0px':'left:0px')"></div>
                            </label>
                          </div>
                          <div :class="'w-50 float-right h5 pt-2 pl-2 '+((!video.status)?'text-secondary':'text-dark')" v-text="((!video.status)?'停止':'開通')"></div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">影片名稱</th>
                    <td class="w-75">
                      <input type="text" class="form-control float-left w-75" name="title" id="title" v-model="video.title" placeholder="填寫影片名稱" required="required" maxlength="128" />
                    </td>
                  </tr>
                  <tr>
                    <th class="w-25 text-center">Youtube 網址</th>
                    <td class="w-75">
                      <div class="w-75 float-left mb-5 pr-2">
                          <div class="w-100 d-table">
                          	<a :class="'btn float-left btn-'+((item==1)?'primary':'secondary')" href="javascript:void(0)" v-text="'Youtube'" @click="item=1;video.vimeo_id=''"></a>
                            <a :class="'btn float-left ml-2 disabled btn-'+((item==2)?'primary':'secondary')" href="javascript:void(0)" @click="item=2; video.youtube_id=''" v-text="'Vimeo'" aria-disabled="true"></a>
                          </div>
                          <input type="text" class="w-100 form-control d-inline mt-2" v-if="item==1" name="youtube_id" id="youtube_id" v-model="video.youtube_id" @change="youtubeBtn" placeholder="Youtube影片網址" />
                          <input type="text" class="w-100 form-control d-inline mt-2" v-else="item==1" name="vimeo_id" id="vimeo_id" v-model="video.vimeo_id" @change="vimeoBtn" placeholder="Vimeo影片網址" />
                      </div>
                      <div class="w-25 float-left" v-if="video.youtube_id" style="height:100%">
                          <img :src="'https://img.youtube.com/vi/'+playVideo(video.youtube_id)+'/0.jpg'" width="100%" />
                      </div>
                        
                    </td>
                  </tr>
                  <tr>
                      <td colspan="2" class="text-center">
                      	<a href="javascript:void(0)" v-if="mode=='edit'" @click="delvideo" class="btn btn-danger float-left" v-text="'刪除'"></a>
                        <a href="javascript:void(0)" @click="sendform" :class="'btn btn-'+((mode=='edit')?'success':'primary')" v-text="'送出'+((mode=='edit')?'修改':'新增')"></a></td>
                  </tr>
              </table>
        </form>
    </div>
    <div class="w-100" v-if="!mode">
    	<table class="table table-hover" >
            <thead>
                <tr>
                    <th>影片名稱</th>
                    <th>狀態</th>
                    <th>Youtube ID</th>
                    <th>Vimeo ID</th>
                    <th>建立日期</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-secondary" v-for="video in videos" @click="get_video(video.video_id)" style="cursor:pointer">
                    <td v-text="video.title"></td>
                    <td v-html="((video.status)?'<b>運作中</b>':'停止中')"></td>
                    <td v-text="video.youtube_id"></td>
                    <td v-text="video.vimeo_id"></td>
                    <td v-text="video.created_at"></td>
                </tr>	
            </tbody>
        </table>
    </div>
</div>
<script>
new Vue({
  el: "#app",
  data: {
	videos: '',
	mode: '',
	video: '',
	item: 1,
	error_message: '',
	
  },
  mounted: function () {
  	var self = this;
	@if($mode=='edit')
		self.get_video('{{$id}}');
	@else
		self.list_video();
	@endif
  },
  methods: {
  	get_video: function(x){
		var self = this;
		self.mode = 'edit'
		axios.get('/admin/get_videos?mode='+self.mode+'&id='+x).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.video = response.data.video;
			
		});
	},
	sendform: function(){
		var self = this;
		var chk = 1;
		if(self.item==1)
		{
			if(!self.video.youtube_id)
			{
				$("#youtube_id").css({"border":"1px solid #a02"});
				chk = 0;	
			}else
				$("#youtube_id").css({"border":"1px solid #ccc"});
		}else
		{
			if(!self.video.vimeo_id)
			{
				$("#vimeo_id").css({"border":"1px solid #a02"});
				chk = 0;	
			}else
				$("#vimeo_id").css({"border":"1px solid #ccc"});	
		}
		if(chk)
			$('#mainFrm').submit();	
	},
	delvideo: function(){
		var self = this;
		if(confirm('確定要刪除此筆資料?'))
			window.location = '/admin/videos?mode=del&id='+self.video.video_id;
	},
	add_video: function(){
		this.mode = 'add';
		this.video = {video_id:'',status:0,title:'',youtube_id:'',vimeo_id:''};
	},
	list_video: function(){
		var self = this;
		self.mode = '';
		axios.get('/admin/get_videos?mode='+self.mode).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.videos = response.data.videos;
		});
	},
	youtubeBtn: function(){
		var self = this;
		self.set_video(self.video.youtube_id);
	},
	vimeoBtn: function(){
		var self = this;
		self.set_video(self.video.vimeo_id);
	},
	set_video: function(video_url){
		
		var videoArr = video_url.split('?');;
		
		if(videoArr.length>1)
		{
		  var arr = videoArr[1].split('&');
		  
		  var videoArr1 = arr[arr.length-1].split('=');
		  var video = videoArr1[1];	
		}else
		  var video = video_url;
		
		this.play_video = video;
	},
	playVideo: function(video_url){
		
		var videoArr = video_url.split('?');;
		
		if(videoArr.length>1)
		{
		  var arr = videoArr[1].split('&');
		  
		  var videoArr1 = arr[arr.length-1].split('=');
		  var video = videoArr1[1];	
		}else
		  var video = video_url;
		
		return video;  
	}
	
  }
  
})
</script>   
@stop