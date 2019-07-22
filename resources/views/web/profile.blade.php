@extends('web.base')
@section('content')
<script src="https://maps.google.com/maps/api/js?key={{ config('services.google_api.key') }}&libraries=places"></script>
<script src="{{asset("/js/jquery.min.js")}}"></script>
<script src="{{asset("/js/jquery.twzipcode.js")}}"></script>
<div class="item-title">個人資訊</div>
<div id="app" class="container">
    <div class=" offset-md-2 col-md-8">
    <div class="alert alert-warning alert-dismissible fade show" v-if="user.usr_status<1" role="alert">
      <span v-if="!user.email_validated"><strong>恭喜您註冊成功!</strong> 接下來您需要填完各項基本設定，並完成郵件驗證程序才能完整使用本站功能。</span>
      <span v-else="!user.email_validated"><strong>恭喜您信箱驗證成功!</strong> 接下來請填完各項基本設定，並按下一步就能完整使用本站功能。</span>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="pro-box">
      <form id="mainFrm" action="<?=URL::to('/')?>/web/register_pt" method="post" enctype="multipart/form-data">
      @csrf
      	<input type="hidden" name="old_email" v-model="old_email" />
        <input type="hidden" name="all_address" id="all_address" value="" />
        <input type="hidden" name="count" id="count" value="1" />
        <div class="containers">
            <div class="imageWrapper">
                <img class="image" :src="((user.usr_photo)? '/avatar/small/' + user.usr_photo : '{{asset("/images/person-icon.jpg")}}')">
                <div class="file-upload">
                    <input type="file" name="avatar" id="avatar" class="file-input">
                    <i class="fa fa-camera" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label  class="col-sm-2 col-form-label">登入身份</label>
            <div class="col-sm-10">
            <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="open_offer_setting" value="0" v-model="user.open_offer_setting" id="c" >
            <label class="form-check-label" for="c">客戶</label>
            </div>
            <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="open_offer_setting" value="1" v-model="user.open_offer_setting" id="h">
            <label class="form-check-label" for="h">好幫手</label>
            </div>
            </div>
        </div>
        <div class="form-group row">
        <label for="inputPassword3" class="col-sm-2 col-form-label">真實姓名 <b class="text-danger">*</b></label>
        <div class="form-row col-sm-10">
        <div class="col">
        <input type="text" class="form-control" placeholder="姓" name="last_name" id="last_name" v-model="user.last_name">
        </div>
        <div class="col">
        <input type="text" class="form-control add5" placeholder="名" name="first_name" id="first_name" v-model="user.first_name">
        </div>
        </div>
        </div>
        <div class="form-group row">
        <label  class="col-sm-2 col-form-label">手機號碼 <b class="text-danger">*</b></label>
        <div class="form-row col-sm-10">
        <div class="col-4">
        <input type="text" class="form-control" name="phone_nat_code" v-model="user.phone_nat_code">
        </div>
        <div class="col-8">
        <input type="text" class="form-control add5" name="phone_number" id="phone_number" v-model="user.phone_number" placeholder="例:901234567">
        </div>
        </div>

        </div>
        <div class="form-group row">
          <label  class="col-sm-2 col-form-label">Email <b class="text-danger">*</b></label>
          <div class="col-sm-8">
          <input type="email" class="form-control" name="email" id="email" v-model="user.email" @change="is_existed" required placeholder="請填寫郵件信箱">
          </div>
          <div class="col-sm-2">
          	<a href="javascript:void(0)" v-if="!user.usr_status && !is_tomail && !user.email_validated" @click="veri_mail" class="text-danger" ><i :class="((sending)?'fa fa-spinner fa-pulse':'fa fa-paper-plane')" aria-hidden="true"></i> 送出驗證</a>
            <a href="javascript:void(0)" v-if="is_tomail" class="text-primary"><i class="fa fa-paper-plane" aria-hidden="true"></i> 已送出驗證Email。請至Email 信箱完成驗證。</a>
            <a href="javascript:void(0)" v-if="user.email_validated && !existed" class="text-success"><i class="fa fa-paper-plane" aria-hidden="true"></i> 已驗證</a>
            <a href="javascript:void(0)" v-if="existed" class="text-danger">郵件重複</a>
          </div>
        </div>
        <div class="form-group row">
        <label  class="col-sm-2 col-form-label">性別 <b class="text-danger">*</b></label>
        <div class="col-sm-10">
        <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="sex" id="w" value="2" v-model="user.sex">
        <label class="form-check-label" for="w">女</label>
        </div>
        <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="sex" id="m" value="1" v-model="user.sex">
        <label class="form-check-label" for="m">男</label>
        </div>
        <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="sex" id="x" value="0" v-model="user.sex">
        <label class="form-check-label" for="x">不揭露</label>
        </div>
        </div>
        </div>
        <div class="form-group row">
        <label  class="col-sm-2 col-form-label">更新密碼</label>
        <div class="col-sm-10">
        <input type="password" class="form-control" id="password" name="password" v-model="user.password"  maxlength="10" placeholder="不更新密碼請勿輸入">
        </div>
        </div>
        <div class="form-group row">
        <label  class="col-sm-2 col-form-label">確認密碼  </label>
        <div class="col-sm-10">
        <input type="password" class="form-control" id="chk_password" name="chk_password" v-model="chk_password"  maxlength="10" placeholder="不更新密碼請勿輸入">
        </div>
        </div>
        <div class="form-group row">
    		<div id="lable_area" class="col-sm-2">
            	<label class="col-form-label">常用地址 <b class="text-danger">*</b>
                	<br class="d-none d-sm-block">
            		<a href="javascript:void(0)" class="text-danger addadd">增加地址<i class="fa fa-plus-circle" aria-hidden="true"></i></a>
               	</label>
                <div id="menuBtn" class="w-100" style="margin-top:48px; display:none;"><a href="javascript:void(0)" onclick="lessBtn();" class="text-dark">減少地址<i class="fa fa-minus-circle" aria-hidden="true"></i></a></div>
            </div>
            <div class="col-sm-10" id="address">
              <div id="twzipcode0"></div>
              <input type="text" class="form-control" id="addr0" onblur="get_latlng(0)" />
              <input type="hidden" id="lat0" />
              <input type="hidden" id="lng0" />
            </div>
    	</div>
        <div class="form-group row">
            <div class="offset-sm-2 col-sm-10">
                <div class="w-100 pb-4 px-0" v-if="user.usr_status<1">
                    <input class="border border-success" type="checkbox" v-model="agree" id="ag">
                    <label class="form-check-label text-dark" for="ag">
                      我同意
                      <a class="txtbtn" href="/term_of_use?back=1">使用者</a>
                      <a class="txtbtn" v-if="user.open_offer_setting==1" href="/how2help_post_list?back=1">好幫手</a>
                      條款相關規定。
                    </label>
                </div>
                <a class="btn btn-lg btn-success" href="javascript:void(0)" @click="sendform"><i class="fa fa-spinner fa-pulse" v-if="sending" aria-hidden="true"></i> 下一步</a>
            </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
new Vue({
  el: "#app",
  data: {
	email: '',
	user: {open_offer_setting:'',first_name:'',last_name:'',email:'',password:'',sex:'',phone_nat_code:'886',phone_number:'',usr_status:''},
	chk_password: '',
	old_email: '',
	is_tomail: '',
	existed: '',
	sending: '',
	latlngs: [],
	agree: false,
	sending: ''
  },
  mounted: function () {
	var self = this;
	axios.get('<?=URL::to('/')?>/api/get_profile').then(function (response){
		console.log(response.data);

		if(response.data=='error')
			alert('喔喔!錯誤了喔')
		else
		{
			self.user = response.data.user;
			self.old_email = response.data.user.email;

			$("#count").val(((response.data.user.addr.length)?response.data.user.addr.length:1));
			if(response.data.user.addr.length)
			{
				for(var i=0;i<response.data.user.addr.length;i++)
				{
					if(i>0)
						addZipcode();
					$("#twzipcode"+i).twzipcode('set', {
						'county': response.data.user.addr[i].city,
						'district': response.data.user.addr[i].nat
					});
					$("#addr"+i).val(response.data.user.addr[i].addr);
					$("#lat"+i).val(response.data.user.addr[i].lat);
					$("#lng"+i).val(response.data.user.addr[i].lng);
				}
			}
		}
	})
  },
  methods: {
  	chk_mail: function(value){
		var mail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return mail.test(value);
	},
	sendform: function(){
		var self = this;
		self.latlngs = [];
		var chk = 1;
		if(!self.agree && self.user.usr_status<1)
		{
			alert('請詳閱使用者'+((self.user.open_offer_setting)?'與好幫手':'')+'條款，並點選同意方框，才能執行下一步驟。')
			chk = 0;
		}
		if(!self.user.last_name)
		{
			$("#last_name").css({"border":"1px solid #a02"})
			chk = 0;
			$("body,html").scrollTop(0);
		}else
			$("#last_name").css({"border":"1px solid #8fc555"})

		if(!self.user.first_name)
		{
			$("#first_name").css({"border":"1px solid #a02"})
			chk = 0;
			$("body,html").scrollTop(0);
		}else
			$("#first_name").css({"border":"1px solid #8fc555"})

		if(!self.user.phone_number)
		{
			$("#phone_number").css({"border":"1px solid #a02"})
			chk = 0;
			$("body,html").scrollTop(100);
		}else
			$("#phone_number").css({"border":"1px solid #8fc555"})

		if(!self.user.email || !self.chk_mail(self.user.email))
		{
			$("#email").css({"border":"1px solid #a02"})
			chk = 0;
		}else
			$("#email").css({"border":"1px solid #8fc555"})

		if(self.user.password && self.chk_password && self.user.password!=self.chk_password)
		{
			$("#password").css({"border":"1px solid #a02"});
			$("#chk_password").css({"border":"1px solid #a02"})
			chk = 0;
		}else
		{
			$("#password").css({"border":"1px solid #8fc555"});
			$("#chk_password").css({"border":"1px solid #8fc555"})
		}

		var addrs = [];
		for(var i=0;i<parseInt($("#count").val());i++)
		{
			if($("#twzipcode"+i).children().eq(0).val())
			{
				addrs.push( {county:$("#twzipcode"+i).children().eq(0).val(),nat:$("#twzipcode"+i).children().eq(1).val(),zipcode:$("#twzipcode"+i).children().eq(2).val(),addr:$("#addr"+i).val(),lat:$("#lat"+i).val(),lng:$("#lng"+i).val()});
			}
		}
		console.log(addrs);
		if(!addrs.length)
		{
			$("#twzipcode0").children().css({"border":"1px solid #a02"});
			chk = 0;
		}else
		{
			$("#twzipcode0").children().css({"border":"1px solid #8fc555"});
			$("#all_address").val(JSON.stringify(addrs));
		}
		if(chk)
		{
			self.sending = 1;
			$("#mainFrm").submit();
		}
	},
	veri_mail: function(){
		var self = this;
		if(confirm('要執行Email認證作業?'))
		{
		  self.sending = 1;
		  axios.get('/api/set_veri_mail?id='+self.user.email).then(function (response){
			  console.log(response.data);
			  if(response.data.is_tomail)
			  	self.is_tomail = response.data.is_tomail;

		  })
		}
	},
	is_existed: function(){
		var self = this;
		if(self.user.email!=self.old_email)
		{
			axios.get('/api/is_existed?id='+self.user.email).then(function (response){
			  console.log(response.data);
			  self.existed = response.data;
			  if(response.data)
			  	self.user.email = '';

		  })
		}
	}

  }

})
</script>
<script>
$('#twzipcode0,#twzipcode1,#twzipcode2,#twzipcode3,#twzipcode4').twzipcode({
    // 依序套用至縣市、鄉鎮市區及郵遞區號框
    'css': ['county', 'district', 'zipcode']
});
var n=1;
$( ".addadd" ).on('click', function(){
	addZipcode();
});

function addZipcode(){
	var cun = $("#address div").length;
	if(cun<5){
		$('#address').append('<div id="twzipcode'+cun+'"></div><input type="text" class="form-control" id="addr'+cun+'" onblur="get_latlng('+cun+')"><input type="hidden" id="lat'+cun+'"><input type="hidden" id="lng'+cun+'">');
		if(cun==1)
			$("#menuBtn").show();
		else if(cun==0)
			$("#menuBtn").hide();
		else
			$("#menuBtn").css({"margin-top":((cun-1)*100+48)+"px"});

		$('#twzipcode1,#twzipcode2,#twzipcode3,#twzipcode4').twzipcode({
			// 依序套用至縣市、鄉鎮市區及郵遞區號框
			'css': ['county', 'district', 'zipcode'],
		  });


		$("#count").val($("#address div").length);


	}

};

$('.file-input').change(function(){
    var curElement = $(this).parent().parent().find('.image');
    console.log(curElement);
    var reader = new FileReader();

    reader.onload = function (e) {
        // get loaded data and render thumbnail.
        curElement.attr('src', e.target.result);
    };

    reader.readAsDataURL(this.files[0]);
});

function lessBtn(){
	var cun = $("#address div").length-1;
	$("#twzipcode"+cun).remove();
	$("#addr"+cun).remove();
	$("#lat"+cun).remove();
	$("#lng"+cun).remove();
	if(cun>1)
		$("#menuBtn").css({"margin-top":((cun-2)*100+48)+"px"});
	else
		$("#menuBtn").hide();
	$("#count").val($("#address div").length);
}

function get_latlng(x){
	var map_address = $("#twzipcode"+x).children().eq(0).val()+$("#twzipcode"+x).children().eq(1).val()+$("#addr"+x).val();
	self.addressToLatLng(map_address,x);
}

function addressToLatLng (addr,x) {
	var self = this;
	geocoder = new google.maps.Geocoder();
	geocoder.geocode({
		"address": addr
	},
	function (results, status) {

	  if (status == google.maps.GeocoderStatus.OK) {

		  $("#lat"+x).val(results[0].geometry.location.lat());
		  $("#lng"+x).val(results[0].geometry.location.lng());
	  }else {
		  //$("#target").val(content + addr + "查無經緯度" + "\n");
	  }
	});
}

var ms_ie = false;
var ua = window.navigator.userAgent;
var old_ie = ua.indexOf('MSIE ');
var new_ie = ua.indexOf('Trident/');

if ((old_ie > -1) || (new_ie > -1)) {
  ms_ie = true;
}

if (ms_ie) {
  document.documentElement.className += " ie";
}
</script>
@stop