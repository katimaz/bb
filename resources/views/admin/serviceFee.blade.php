@extends('admin.master')
@section('content')
<div id="app" class="w-100 py-5 text-center h4">
<form id="mainFrm" action="/admin/serviceFee_pt" method="post">
  	@csrf
    <label class="d-inline">交易手續費 : </label>
    <input type="tel" class="form-control ml-2 d-inline text-center" name="serviceFee" id="serviceFee" v-model="serviceFee" style="width:100px" />
    <b class="px-2">%</b>	  
    <a href="javascript:void(0)" @click="sendform" class="btn btn-primary d-inline ml-2" v-text="'送出存檔'" ></a>  
</form>
</div>
<script>
new Vue({
  el: "#app",
  data: {
	serviceFee: '<?php echo ((isset($fee) && $fee)?$fee:20)?>'
	
  },
  methods: {
	  sendform: function(){
		  var self = this;
		  var chk = 1;
		  if(!self.serviceFee || parseInt(self.serviceFee)<10 || parseInt(self.serviceFee)>50)
		  {
			  $("#serviceFee").css({"border":"1px solid #a02"});
			  chk = 0;
			  alert('手續費需介於10%~50%');	
		  }else
			  $("#serviceFee").css({"border":"1px solid #ccc"});
		  
		  if(chk && confirm('確定要送出存檔?'))
			  $('#mainFrm').submit();	
	  }
  }
  
})
</script>
@stop