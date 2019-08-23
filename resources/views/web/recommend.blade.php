@extends('web.base')
@section('content')
<div class="item-title"> 推薦連結</div>
<div id="app" class="container">
    <div class="row">
    	<div class=" offset-md-2 col-md-8">
            <div class="recomm-box"> 
            	<p>一個人走得快，一群人走得遠。朋友們，平常忙於工作與家庭外，透過BounBang幫棒平台利潤回饋的機制，讓您介紹進來的朋友成為您的夥伴團隊；您可以由夥伴們的每次服務中獲得幫棒平台的利潤回饋金!!您當然就有更多收入與時間可以聊天、逛街 、喝下午茶囉!</p>
            	<a href="javascript:void(0)" data-toggle="modal" data-target="#exampleModalLong"> ※了解邀請詳情 </a>
                <div class="row mt-3 mb-4">
                    <div class="col-md-6">  
                        <div class="recomm-link">  
                            <h5>掃描您的QR code邀請加入</h5>
                            <img src="{{asset('/web/set_qrcode?data='.$data)}}" width="100%">   
                        </div>  
                    </div>
                    <div class="col-md-6">
                        <a href="javascript:void(0)" onclick="copytext()" class="copy-link">複製推薦訊息</a>
                        <a href="javascript:void(0)" onclick="copyline()" class="copy-link" rel="nofollow" style="margin-top:15px;">複製我的推薦連結</a>
                        <a href="javascript: void(window.open('https://social-plugins.line.me/lineit/share?url='.concat(encodeURIComponent('{{$data}}')) ));" rel="nofollow"  class="line-login">使用Line推薦</a>
                        <a href="javascript: void(window.open('http://www.facebook.com/share.php?u='.concat(encodeURIComponent('{{$data}}')) ));" class="fb-login">使用FaceBook推薦</a>
                        <a href="javascript:void(0)" @click="mailto"  class="google-login"> 使用E-mail推薦</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="text" id="url" v-model="recommend_url" style="opacity:0; height:0px;" />
    <input type="text" id="linetext" style="opacity:0; height:0px;" />
</div>
<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">了解邀請詳情</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>不論您是消費者或是好幫手, 只要透過您幫棒個人帳戶的邀請連結介紹進來的親朋好友們，都會是您的"夥伴" !  </p>
          <p>您的夥伴團隊中若成為好幫手並完成服務需求，幫棒平台將提供給您此次交易金額的5%作為利潤分享!!</p>
          <strong>回饋無上限。夥伴愈多， 回饋愈多!!</strong>
          <h5 class="text-success">超棒的事在"幫棒"!</h5>
      </div>
      <div class="modal-footer">
       <button type="button" class="btn btn-success" data-dismiss="modal">關閉視窗</button>
    </div>
  </div>
  </div>
</div>

<script>
new Vue({
	el: "#app",
	data: {
	  recommend_url: '<?php echo ((isset($data))?$data:'')?>',
	},
	methods: {
		mailto: function(){
			var email = '';
			var subject = '<?php echo ((isset($user) && $user->nickname!='')?$user->nickname:$user->last_name.''.$user->first_name).' '?>邀請您加入BounBang幫棒家族, 期待您的加入😊';
			var mailbody = '';
			mailbody += '幫您服務還能幫您賺現金%3F%20這麼棒的事就在%20"幫棒"%20!!%0D無論您是消費者或者是好幫手，只要是您介紹進來的朋友，都會是您拓展業務的夥伴們，享有團隊收益%205%%20的現金回饋! 回饋無上限!!%0D%0D';
			
			mailbody += this.recommend_url+'%0D%0D';
			
			mailbody += 'BounBang幫棒,您的好幫手。%0D%0D';
			
			window.location = 'mailto:'+email+'?subject='+subject+'&body='+mailbody;
		}
	
	}
  
})
</script>
<script>
window.Clipboard = (function(window, document, navigator) {
	var textArea,
	copy;
	
	function isOS() {
	return navigator.userAgent.match(/ipad|iphone/i);
	}
	
	function createTextArea(text) {
	textArea = document.createElement('textArea');
	textArea.value = text;
	document.body.appendChild(textArea);
	}
	
	function selectText() {
	var range,
	selection;
	
	if (isOS()) {
	range = document.createRange();
	range.selectNodeContents(textArea);
	selection = window.getSelection();
	selection.removeAllRanges();
	selection.addRange(range);
	textArea.setSelectionRange(0, 999999);
	} else {
	textArea.select();
	}
	}
	
	function copyToClipboard() {
	document.execCommand("Copy");
	document.body.removeChild(textArea);
	}
	
	copy = function(text) {
	createTextArea(text);
	selectText();
	copyToClipboard();
	};
	
	return {
	copy: copy
	};
})(window, document, navigator);

function copyline(){
	value = decodeURIComponent($("#url").val());
	window.Clipboard.copy(value);
	alert("複製成功!");
}

function copytext(){
	$("#linetext").val('幫您服務還能幫您賺現金?這麼棒的事就在 "幫棒" !! \n無論您是消費者或者是好幫手，只要是您介紹進來的朋友，都會是您拓展業務的夥伴們，享有團隊收益 5% 的現金回饋! 回饋無上限!!');
	value = $("#linetext").val();
	window.Clipboard.copy(value);
	alert("複製推薦訊息成功!");
}
</script>
@stop
