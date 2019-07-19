<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>幫棒_後台登入</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
    <script src="/js/app.js"></script>
    <!-- Scripts -->
</head>
<body>
<div id="app" class="w-100 mx-auto text-left bg-white m-5 border rounded" style="max-width:380px;">
	<div class="w-100 p-2" style="background:url(/images/logo.svg) no-repeat 5% center / auto 70% #9acf4d; height:40px;"></div> 
	<form id="login_form" class="py-3 px-4" action="/admin/adm_login_pt" novalidate method="post">
    	@csrf
    	<div class="h5"><img class="mr-2 rounded-circle" src="/images/person-icon.jpg" width="30px">管理員登入</div>
        <div class="form-group mt-4">
          <label class="h5">帳號</label>
          <input type="text" name="account" class="form-control"  placeholder="Enter account" v-model="account" >
        </div>
        <div class="form-group">
          <label class="h5">密碼</label>
          <input type="password" name="password" class="form-control" @keyup.enter="manager_login"  placeholder="Enter password" v-model="password" >
            </ul>
        </div>
        <div class="w-100 d-table" >
          <button id="loginBtn" type="button" class="btn btn-primary float-right" @click="manager_login">Login</button>
        </div>
      </form> 
    </div>	         
</body>
</html>

<script>
new Vue({
  el: "#app",
  data: {
	account: '',
	password: ''
	
  },
  methods: {
  	 manager_login: function(){
		var chk = 1;
		if(!this.account)
		{
			$("input[name='account']").css({"border":"1px solid #a02"})
			chk = 0;
		}else
			$("input[name='account']").css({"border":"1px solid #ccc"})
		
		if(!this.password)
		{
			$("input[name='password']").css({"border":"1px solid #a02"})
			chk = 0;
		}else
			$("input[name='password']").css({"border":"1px solid #ccc"})
		if(chk)
			$("#login_form").submit()	
	}
  }
  
})
</script>
