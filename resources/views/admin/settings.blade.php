@extends('admin.master')
@section('content')
<div id="app">
	<div class="w-100 p-2 d-table">
        <h3 class="float-right" v-text="((item_name)?item_name:'文件設定')"></h3>
    </div>
    <div class="w-100" v-if="id">
    	<form id="mainFrm"  action="/admin/settings_pt" method="post">
          @csrf
          <input type="hidden" name="id" v-model="id" />
          <input type="hidden" name="item" v-model="item" />
          <textarea class="form-control mb-2" v-if="item" name="data_subject" id="data_subject" v-model="data_subject" :placeholder="'填寫「'+item_name+'」主旨'"></textarea> 
          <textarea class="form-control" name="data_body" id="data_body" onclick="thisOn(this)" oninput="textareaChange(this)" v-model="data_body" style="min-height:300px;" :placeholder="'填寫「'+item_name+'」內文'"></textarea>    
          <div class="w-100 py-3 text-center"><input type="submit" class="btn btn-primary" value="送出存檔"  /></div>
        </form>
    </div>
</div>
<script>
new Vue({
  el: "#app",
  data: {
	id: '{{$id}}',
	item: '',
	data_subject: '',
	data_body: '',
	item_name: '',
	item: ''
	
	
  },
  mounted: function () {
  	var self = this;
	self.get_settings();
  },
  methods: {
  	get_settings: function(x){
		var self = this;
		axios.get('/admin/get_settings?id='+self.id).then(function (response){
			console.log(response.data)
			if(response.data=='error')
				window.location = '/error';
			
			self.data_subject = response.data.data_subject;
			self.data_body = response.data.data_body;
			self.item_name = response.data.item_name;
			self.item = response.data.item;
		});
	}
	
  }
  
})
function thisOn(x){
	x.style.height=x.scrollHeight + 'px';
	$("#"+x.id).css({"height":x.style.height});
}
function textareaChange(textarea){
	var adjustedHeight=textarea.clientHeight;
    adjustedHeight=Math.max(textarea.scrollHeight,adjustedHeight);
    if (adjustedHeight>textarea.clientHeight){
        textarea.style.height=adjustedHeight+'px';
    }
}
</script>   
@stop