<!DOCTYPE html>
<html>
<head>
	<title>{{$subject}}</title>
    <style>
	body{font-family:"微軟正黑體", "新細明體";font-size:1.1em}
	a{color:#09F;text-decoration:none;}
	a.alink{color:#fff;text-decoration:none;margin:0px 10px;}
	a.btn{display:block;border-radius:5px; font-size:1.2em; text-align:center; text-decoration:none; padding:10px 15px; background-color:#026cc5; color:#ffffff; max-width:200px;}
	a:hover{text-decoration:underline;}
	div{padding:8px 5px;}
	b{display:block;padding-top:6px;}
    </style>
</head>
<?php
if($btn)
	$btnArr = json_decode($btn)
?>
<body>
	<div style="padding:10px;font-size:1.2em;"><b>{{$subject}}</b></div>
    <div>{{$name}} 您好:</div>
    <div style="line-height:200%">{!! nl2br($body) !!}</div>
    @if($btnArr)
    	<div style="width:100%;margin-top:30px;">
			<a class="btn" href="{{$btnArr->url}}" style="color:#ffffff;">{{$btnArr->name}}</a>
        </div>
    @endif
    @if(!strpos($subject,"完成註冊"))
    <div style="margin-top:30px;">此連結將從您收到此封電子郵件起24小時內有效。</div>
    @endif
    <div style="margin-top:20px;">此電子郵件係由系統自動發出，請勿直接回覆，謝謝您。</div>
	<div>BounBang幫棒, 您的好幫手。</div>
    <div style="margin-top:20px;padding:8px 0px;text-align:center;background-color:#8fc555;color:#fff;max-height:40px;">
    	<a class="alink" href="#">BounBang幫棒粉絲頁</a> | <a class="alink" href="{{$url}}">BounBang幫棒網站</a> | <a class="alink" href="{{$url}}/contact">聯絡我們</a>
    </div>
</body>
</html>
