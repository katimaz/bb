@extends('web.base')
@section('content')
<div class="item-title">重新置換密碼</div>
<div id="app" class="pro-box mx-auto py-5" style="max-width:600px;">
   <form id="mainFrm" action="/web/set_forgot_passwd_pt" method="post" enctype="multipart/form-data">
   @csrf
   		<input type="hidden" name="id" value="{{$user->usr_id}}" /> 
        <div class="form-group row">
            <label  class="col-sm-2 col-form-label">更新密碼</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="password" name="password" v-model="passwd"  maxlength="20" placeholder="填寫新密碼">
            </div>
        </div>
        <div class="form-group row mt-4">
            <label  class="col-sm-2 col-form-label">確認密碼  </label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="chk_password" name="chk_password" v-model="chk_passwd"  maxlength="20" placeholder="確認新密碼">
            </div>
        </div>
        <div class="form-group row mt-4">
            <div class="offset-sm-2 col-sm-10">
                <a class="btn btn-lg btn-success" href="javascript:void(0)" @click="sendform">下一步</a> 
            </div>
        </div>
    </form>     
</div>        
<script>
new Vue({
  el: "#app",
  data: {
	passwd: '',
	chk_passwd: '',
  },
  methods: {
  	sendform: function(){
		var self = this;
		var chk=1;
		if(!self.passwd || self.passwd.length<6 || self.passwd!=self.chk_passwd)
		{
			$("#password").css({"border":"1px solid #a02"});
			$("#chk_password").css({"border":"1px solid #a02"});
			chk = 0;	
		}else
		{
			$("#password").css({"border":"1px solid #8fc555"});
			$("#chk_password").css({"border":"1px solid #8fc555"});	
		}
		
		if(chk)
		{
			if(confirm('確定要修改這個密碼?'))
				$("#mainFrm").submit();	
		}
	}
  }
  
})
</script>
@stop