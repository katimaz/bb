@extends('web.base')
@section('content')
<div class="item-title">E-mail註冊</div>
<div id="app" class="login-box">
    <form id="mainFrm" action="<?=URL::to('/')?>/web/signup_pt" method="post">
    @csrf
      <div class="form-group">
          <input type="text" class="form-control" name="email" id="email" v-model="email" @blur="is_email" placeholder="請輸入您的Eamil帳號">
      </div>
      <div class="form-group">
          <input type="password" class="form-control" name="password" id="password" v-model="passwd" placeholder="輸入密碼">
      </div>
      <div class="form-group">
          <input type="password" class="form-control" name="chk_password" id="chk_password" v-model="chk_passwd" placeholder="確認密碼">
      </div>
      <button type="button" class="login-bt" @click="sendform"><i class="fa fa-spinner fa-pulse" v-if="sending" aria-hidden="true"></i> 註冊</button>
      <a href="/login" class="txtbtn"><i class="fa fa-chevron-left" aria-hidden="true"></i> 己有帳號回上頁登入 </a>
   </form>
</div>
<script>
new Vue({
  el: "#app",
  data: {
	email: '',
	passwd:'',
	chk_passwd:'',
	checkmail: '',
	socials: '',
	sending: ''
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

		if(!self.passwd || self.passwd.length<6 || self.passwd.length>20 || !self.chk_passwd || self.passwd!=self.chk_passwd || self.passwd==self.email)
		{
			$("#password").css({"border":"1px solid #a02"});
			$("#chk_password").css({"border":"1px solid #a02"});
			chk = 0;
			if(self.passwd.length<6 || self.passwd.length>20)
				alert('密碼字數需大於6小於20');
			else if(self.passwd!=self.chk_passwd)
				alert('兩組密碼不一致!');
			else if(self.passwd==self.email)
				alert('密碼請勿跟Email帳號相同!');

		}else
		{
			$("#password").css({"border":"1px solid #ccc"});
			$("#chk_password").css({"border":"1px solid #ccc"});
		}

		if(chk)
		{
			self.sending = 1;
			$("#mainFrm").submit();
		}
	},
	chk_mail: function(value){
		var mail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return mail.test(value);
	},
	is_email: function(){
		console.log(this.chk_mail(this.email))
		var self = this;
		if(self.email && self.chk_mail(self.email))
		{
		  axios.get('/api/chk_mail?id='+self.email).then(function (response){
			  self.checkmail = response.data.checkmail;
			  if(!response.data.checkmail)
			  {
				  self.email = '';
				  $("#email").attr('placeholder','您填寫的信箱已有人使用喔!').css({"border":"1px solid #a02"});
				  if(response.data.socials.length)
				  	alert('此帳號已存在，請使用'+response.data.socials.join(',')+'社群進入本系統');
			  }else
			  	$("#email").css({"border":"1px solid #ccc"});
		  })
		}else
			$("#email").val('').css({"border":"1px solid #a02"}).attr('placeholder','郵件格式有誤，請重新填寫!');
	}
  }

})
</script>
@stop