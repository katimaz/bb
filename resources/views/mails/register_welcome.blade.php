<!DOCTYPE html>
<html>
<head>
	<title>{{$subject}}</title>
    <style>
	body{font-family:"微軟正黑體", "新細明體";font-size:1.1em}
	a{color:#09F;text-decoration:none;}
	a.alink{color:#fff;text-decoration:none; margin:0px 10px;}
	a:hover{text-decoration:underline;}
	div{padding:8px 5px;}
	b{display:block;padding-top:6px;}
    </style>
</head>
<body>
	<div style="padding:10px;font-size:1.2em;"><b>{{$subject}}</b></div>
    <div>{{$name}} 您好:</div> 
    <div>{!! nl2br($body) !!}</div>
    <div style="margin-top:20px;padding:8px 0px;text-align:center;background-color:#8fc555;color:#fff; max-height:40px;">
    	<a class="alink" href="#">BounBang幫棒粉絲頁</a> | <a class="alink" href="{{$url}}">BounBang幫棒網站</a> | <a class="alink" href="{{$url}}/contact">聯絡我們</a>
    </div>
</body>
</html>
