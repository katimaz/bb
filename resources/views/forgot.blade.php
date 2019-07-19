@extends('web.base')
@section('content')
<div class="item-title">忘記密碼</div>
<div id="app" class="login-box">
    <div class="form-group">
    	<input type="text" class="form-control" id="email" v-model="email" placeholder="請輸入您註冊的Eamil帳號" />
    </div>
    <button type="button" class="login-bt" v-if="!is_tomail" @click="sendmail"><i class="fa fa-spinner fa-pulse" v-if="sending" aria-hidden="true"></i> 寄出密碼更新通知</button>
    <div class="alert alert-warning alert-dismissible fade show px-2" v-if="is_tomail" role="alert">
      <strong>信件已送出!</strong>接下來請您前往收取您的信件並更換新密碼。
    </div>
    <a href="/signup" class="txtbtn"><i class="fa fa-chevron-left" aria-hidden="true"></i> 註冊新的帳號</a></div>
<script>
new Vue({
  el: "#app",
  data: {
	email: '',
	is_tomail: '',
	sending: ''
  },
  methods: {
  	chk_mail: function(value){
		var mail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return mail.test(value);
	},
	sendmail: function(){
		var self = this;
		if(self.email && self.chk_mail(self.email))
		{
		  $("#email").css({"border":"1px solid #ccc"});
		  if(confirm('確定要寄發更換密碼通知到您的信箱?'))
		  {
			self.sending = 1;
			axios.get('/api/get_forgot_passwd?id='+self.email).then(function (response){
				console.log(response.data);
				self.is_tomail = response.data.is_tomail;
				
			})
		  }
		}else
			$("#email").val('').css({"border":"1px solid #a02"}).attr('placeholder','郵件格式有誤，請重新填寫!');
	}
  }
  
})
</script>
@stop