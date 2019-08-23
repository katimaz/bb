<!DOCTYPE html>
<html>
<head>
	<title>{{$subject}}</title>
    <style>
	body{font-family:"微軟正黑體","新細明體";font-size:1.1em}
	a{color:#09F;text-decoration:none;}
	a.alink{color:#fff;text-decoration:none;margin:0px 10px;}
	a.btn{display:block;font-size:1.2em;text-align:center;text-decoration:none;padding:10px;background-color:#8ec555;color:#ffffff;max-width:200px;}
	a.line{display:inline;font-size:1.2em;text-decoration:none;padding:0px 5px;color:#8ec555;}
	a:hover{text-decoration:underline;}
	div{padding:8px 5px;}
	b{display:block;padding-top:6px;color:#8ec555;}
	</style>
</head>
<body>
	<div style="width:100%;padding:5px 8px; margin-bottom:8px;background-color:#8ec555;text-align:left;"><img src="{{url('/')}}/images/logo_s.jpg" /></div>
    <div style="line-height:130%">{!! $body !!}</div>
    @if(strpos($subject,"會員註冊驗證信"))
    <div style="margin-top:30px;">此連結將從您收到此封電子郵件起24小時內有效。</div>
    @endif
    <div style="margin-top:20px;">此電子郵件係由系統自動發出，請勿直接回覆，謝謝您。</div>
	<div>BounBang幫棒, 您的好幫手。</div>
    <div style="margin-top:20px;padding:8px 0px;text-align:center;background-color:#8fc555;color:#fff;max-height:40px;">
    	<a class="alink" href="https://facebook.com/bounbang.comTW">BounBang幫棒粉絲頁</a> | <a class="alink" href="{{url('/')}}">BounBang幫棒網站</a> | <a class="alink" href="mailto:support@bounbang.com">聯絡我們</a>
    </div>
</body>
</html>