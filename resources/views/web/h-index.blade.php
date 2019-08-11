@extends('web.base')
@section('content')
<div class="container-fluid pb-5">
    <div class="row">
      <div class="swiper-container">
        <div class="swiper-wrapper">
            @if($top_pics && count($top_pics))
                  @foreach($top_pics as $top_pic)
                  <div class="swiper-slide"><img class="d-none d-sm-block" src="/home/big/{{$top_pic}}" width="100%"></div>
                  @endforeach
              @else
                  <div class="swiper-slide"><img src="/images/banner.jpg" width="100%" class="d-none d-sm-block"></div>
                  <div class="swiper-slide"><img src="/images/banner.jpg" width="100%" class="d-none d-sm-block"></div>
              @endif
          </div>
          <div class="swiper-scrollbar"></div>
          <div class="swiper-pagination"></div>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
      </div>
      <div class="search-arr"><div class="search-box"> <input type="text"  placeholder="熱門關鍵字"><div class="searct-bt">搜尋好幫手</div></div> <div class="type-notics">左右滑動</div></div>
      <div class="search-type">     
      <div class="type-list">
      <div class="types"  data="#type1"><img src="{{asset('/images/icon1.png')}}"><br>居家服務</div>
        <div class="types" data="#type2"><img src="{{asset('/images/icon2.png')}}"><br>健康美容</div>
          <div data="#type3"  class="types"><img src="{{asset('/images/icon3.png')}}"><br>學習</div>
            <div data="#type4"  class="types"><img src="{{asset('/images/icon4.png')}}"><br>代辦代購</div>
              <div data="#type5"  class="types"><img src="{{asset('/images/icon5.png')}}"><br>活動設計</div>
                <div data="#type6"  class="types"><img src="{{asset('/images/icon6.png')}}"><br>專業設計</div>
       <div data="#type7"  class="types"><img src="{{asset('/images/icon7.png')}}"><br>文字工作</div>
        <div data="#type8" class="types"><img src="{{asset('/images/icon8.png')}}"><br>專業顧問</div>
          <div data="#type9" class="types"><img src="{{asset('/images/icon9.png')}}"><br>旅遊</div>
           <div data="#type10"  class="types"><img src="{{asset('/images/icon10.png')}}"><br>創意市集</div>
              <div data="#type11"  class="types"><img src="{{asset('/images/icon11.png')}}"><br>二手平台</div>
                <div data="#type12"  class="types"><img src="{{asset('/images/icon12.png')}}"><br>其他</div>
      </div>
      </div>
      <div class="w-100">
        <div class="sub-type" id="type1">
          <a href="h-map.html">美味家常菜</a>、<a href="h-map.html">居家清掃</a>、<a href="h-map.html">水電工程</a>、<a href="#">小孩讀伴玩</a>、<a href="#">銀髮族照護</a>、<a href="#">寵物美容/照顧</a>、<a href="#">洗車/汽車美容</a>、<a href="#">居家布置</a>、<a href="#">花藝</a>、<a href="#">衣物送洗</a>、<a href="#">管家服務</a>、<a href="#">月子媽媽</a>、<a href="#">其他</a></div>
        <div  class="sub-type"  id="type2">
          <a href="#">美容</a>、<a href="#">按摩</a>、<a href="#">美髮</a><a href="#">美甲美睫</a>、<a href="#">運動</a>、<a href="#">瑜珈</a>、<a href="#">舞蹈</a>、<a href="#">游泳</a>、<a href="#">其他</a>
         </div>
        <div  class="sub-type"  id="type3">
          <a href="#"> 課業伴讀</a>、<a href="#">語言學習</a>、<a href="#">音樂教學</a>、<a href="#">攝影教學</a>、<a href="#">廚藝指導</a>、<a href="#">繪畫教學</a>、<a href="#">才藝培養</a>、<a href="#">電腦教學</a>、<a href="#">其他</a>
         </div> 
      </div>
<div class="session">
 <div class="line"></div>
<h3>最新活動</h3>
 </div>
 <div class="ad-arr">
  <div class="ad-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="{{asset('/images/ad.jpg')}}" width="100%"><a href="news.html">news.html</a></div>
            <div class="swiper-slide"><a href="news.html"><img src="{{asset('/images/ad1.jpg')}}" width="100%"></a></div>
        </div>
        <div class="swiper-pagination x"></div>
        <div class="swiper-button-next x"></div>
        <div class="swiper-button-prev x"></div>
    </div>
 </div> 
 <div class="session">
 <div class="line"></div>
<h3>利潤共享</h3>
 </div> 
 <div class="ad-arr">
  <div class="embed-responsive embed-responsive-16by9">
  <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/Pm0_G8Zl0ek" allowfullscreen></iframe>
</div>  
<a href="login.html" class="join">加入幫棒(成為客戶)</a>
 </div> 
 <div class="session">
 <div class="line"></div>
<h3>利潤共享還可以怎麼獲得?</h3>
 </div> 
 <div class="helper-arr">
 <div class="helper-item">
<div class="helper-tit"> 情境一</div>
<h3>我的工作太多，分身乏術</h3>
<p>我的朋友圈有很多跟我有相似的專業背景，我也相信他們的工作能力，因此我推薦朋友們進入BounBang幫棒平台的夥伴團隊!我的團隊中若有好幫手來承接這份工作，當他完成工作後，我也能得到利潤回饋。</p></div>
  <div class="helper-item"><img src="{{asset('/images/picmea.jpg')}}"></div> 
   <div class="helper-item d-none d-sm-block"><img src="{{asset('/images/picmeb.jpg')}}"></div>
 <div class="helper-item">
<div class="helper-tit"> 情境二</div>
<h3>我無法承接的工作</h3>
<p>我認識各種身懷絕技的好朋友，由我推薦進入BounBang幫棒平台，成為我的夥伴團隊!即使有我能力不及而無法承接的工作，我的夥伴透過BounBang幫棒平台承接案件，當他完成工作後，我也可以得到利潤回饋!! 
</p></div>
 <div class="helper-item d-block d-sm-none"><img src="{{asset('/images/picmeb.jpg')}}"></div>
   <div class="helper-item">
<div class="helper-tit">情境三</div>
<h3>我專門替夥伴們推薦工作</h3>
<p>我成為夥伴團隊的領導者，在我推薦好幫手們進入BounBang幫棒平台後，我可以負責為他們搜尋合適的工作，利用各種方式增加夥伴們的工作能見度與行銷服務。團隊幫手們的業務量越高，我可以獲得的利潤回饋就愈高!!!</p></div>
  <div class="helper-item fix-t"><img src="{{asset('/images/picmec.jpg')}}"></div>  
   <a href="login.html" class="join">成為好幫手(接案者)</a>
 </div>
  <div class="session mbl">
 <div class="line"></div>
<h3>接案者使用教學</h3>
 </div>
 <div class="step-arr">
 <div class="step-box">
 <div class="step-item">
 <img src="{{asset('/images/stepB_01.jpg')}}">
<div class="step-info"><p>1.登入BounBang幫棒。</p><p>2.輸入或更新個人專長。</p></div>
 </div>
 <div class="step-item">
 <img src="{{asset('/images/stepB_02.jpg')}}">
<div class="step-info"><p>3.收到客戶服務需求。</p><p>4.與客戶協議服務內容。</p></div>
 </div>
 <div class="step-item">
 <img src="{{asset('/images/stepB_03.jpg')}}">
<div class="step-info"><p>5.確認服務需求與客戶訂單。</p></div>
 </div>
 <div class="step-item">
 <img src="{{asset('/images/stepB_04.jpg')}}">
<div class="step-info"><p>6.完成服務。</p><p>7.BounBang幫棒依照使用條款代付服務費。</p></div>
 </div>
 </div>
 </div> 
</div>
  </div>
<script src="/js/swiper.jquery.min.js"></script>
<script>
  $( document ).ready(function() {
	  if ($(window).width() > 991) {
		  $(window).scroll(function(evt){
			  if ($(window).scrollTop()>560) {
				  $('.header,.search-frame').addClass('fixed')  
			  } else {
				  $('.header,.search-frame').removeClass('fixed') }
		  }
	  )} 
  })
  
  var swiper = new Swiper('.swiper-container', {	
	scrollbar:'.swiper-scrollbar',
	pagination: '.swiper-pagination',
	nextButton: '.swiper-button-next',
	prevButton: '.swiper-button-prev',
	paginationClickable: true,
	autoplay: 8000,
	speed:600,
	loop: true
  });
  
  var swiper = new Swiper('.ad-container', {	
  pagination: '.swiper-pagination',
  nextButton: '.swiper-button-next',
  prevButton: '.swiper-button-prev',
  paginationClickable: true,
  autoplay: 4000,
  speed:600,
  loop: true
  });
  var swiper = new Swiper('.user-feedback,.user-new', {
	  autoplay: 3000,
	  speed:600,
	  pagination: '.swiper-pagination',
	  paginationClickable: true,
	  slidesPerView: 3,
	  spaceBetween: 30,
	  breakpoints: {
		  1024: {
			  slidesPerView: 3,
			  spaceBetween: 30
		  },
		  768: {
			  slidesPerView: 2,
			  spaceBetween: 30
		  },
		  540: {
			  slidesPerView: 1,
			  spaceBetween: 25
		  }
	  }
  });
  $(function(){
	  $(".type-list").onPositionChanged(function(){$(".type-notics").hide();});
  })
</script>   
@stop