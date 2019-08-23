@extends('web.base')
@section('content')
  <div class="item-title">註冊登入</div>
  <div id="app" class="login-box">
    <a href="{{ url('auth/facebook') }}" class="fb-login"> 使用FaceBook登入</a>
    <a href="{{ url('auth/line') }}"  class="line-login">使用Line登入</a>
    <a href="{{ url('auth/google') }}"  class="w-100 google-login btn btn-danger disabled" style="padding:0px 10px;"> 使用Google登入</a>
    <form id="mainFrm" action="/web/login_pt" method="post">
      @csrf   
      <div class="form-group">
          <input type="text" class="form-control" name="email" id="email" v-model="email" placeholder="請輸入您註冊的Eamil帳號">
      </div>
      <div class="form-group">
          <input type="password" class="form-control" name="password" id="password" v-model="passwd" placeholder="輸入密碼">
      </div>
      <button type="button" class="login-bt" @click="sendform">登入</button>
    </form>    
     <a href="/forgot" class="txtbtn"><i class="fa fa-question-circle-o" aria-hidden="true"></i> 忘記密碼 </a>
     <a href="/signup" class="text-right txtbtn"><i class="fa fa-envelope-o" aria-hidden="true"></i> 使用E-mail註冊 </a>
   </div>
<script>
new Vue({
  el: "#app",
  data: {
	email: '',
	passwd:'',
	message: '<?php echo ((isset($message))?$message:'')?>'
  },
  mounted: function () {
	  var self = this;
	  if(self.message)
	  {
	  	var msg = jQuery.parseJSON(self.message);
		setTimeout(function(){
			$("#alert_title").text(msg.title);
			$("#alert_body").text(msg.body);
			$("#alertBtn").trigger("click");
		},500);
		
	  }
  },
  methods: {
  	sendform: function(){
		var self = this;
		var chk = 1;
		if(!self.email || !self.chk_mail(self.email))
		{
			$("#email").css({"border":"1px solid #a02"});
			chk = 0;	
		}else
			$("#email").css({"border":"1px solid #ccc"});
		
		if(!self.passwd)
		{
			$("#password").css({"border":"1px solid #a02"});
			chk = 0;
		}else
			$("#password").css({"border":"1px solid #ccc"});
		
		
		if(chk)
			$("#mainFrm").submit();
			
	},
	chk_mail: function(value){
		var mail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return mail.test(value);
	}
  }
  
})
</script>   
@stop