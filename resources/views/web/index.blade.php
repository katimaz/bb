@extends('web.base')
@section('content')
<div class="row">
  <div class="swiper-container">
    <div class="swiper-wrapper">
        @if($top_pics && count($top_pics))
            @foreach($top_pics as $top_pic)
            <div class="swiper-slide"><img class="d-none d-sm-block" src="/home/big/{{$top_pic}}" width="100%"></div>
            @endforeach
        @else
            <div class="swiper-slide"><img src="{{asset("/images/banner.jpg")}}" width="100%" class="d-none d-sm-block"></div>
            <div class="swiper-slide"><img src="{{asset("/images/banner.jpg")}}" width="100%" class="d-none d-sm-block"></div>
        @endif
    </div>
    <div class="swiper-scrollbar"></div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
  </div>

  <div class="search-arr">
    <div class="search-box">
    <input type="text" id="keyword" placeholder="熱門關鍵字">
    <div class="searct-bt">搜尋好幫手</div></div> <div class="type-notics">左右滑動</div>
  </div>
  <div class="search-type">
    <div class="type-list">
    <div class="types"  data="#type1"><img src="{{asset("/images/icon1.png")}}"><br>居家服務</div>
      <div class="types" data="#type2"><img src="{{asset("/images/icon2.png")}}"><br>健康美容</div>
        <div data="#type3"  class="types"><img src="{{asset("/images/icon3.png")}}"><br>學習</div>
          <div data="#type4"  class="types"><img src="{{asset("/images/icon4.png")}}"><br>代辦代購</div>
            <div data="#type5"  class="types"><img src="{{asset("/images/icon5.png")}}"><br>活動設計</div>
              <div data="#type6"  class="types"><img src="{{asset("/images/icon6.png")}}"><br>專業設計</div>
     <div data="#type7"  class="types"><img src="{{asset("/images/icon7.png")}}"><br>文字工作</div>
      <div data="#type8" class="types"><img src="{{asset("/images/icon8.png")}}"><br>專業顧問</div>
        <div data="#type9" class="types"><img src="{{asset("/images/icon9.png")}}"><br>旅遊</div>
         <div data="#type10"  class="types"><img src="{{asset("/images/icon10.png")}}"><br>創意市集</div>
            <div data="#type11"  class="types"><img src="{{asset("/images/icon11.png")}}"><br>二手平台</div>
              <div data="#type12"  class="types"><img src="{{asset("/images/icon12.png")}}"><br>其他</div>
    </div>
  </div>
  <div class="w-100">
    <div class="sub-type" id="type1">
      <a href="<?=URL::to('/')?>/web/map?keyword=美味家常菜">美味家常菜</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=居家清掃">居家清掃</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=水電工程">水電工程</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=小孩讀伴玩">小孩讀伴玩</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=銀髮族照護">銀髮族照護</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=寵物美容">寵物美容</a>/
      <a href="<?=URL::to('/')?>/web/map?keyword=寵物照顧">照顧</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=洗車">洗車</a>/
      <a href="<?=URL::to('/')?>/web/map?keyword=汽車美容">汽車美容</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=居家布置">居家布置</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=花藝">花藝</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=衣物送洗">衣物送洗</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=管家服務">管家服務</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=月子媽媽">月子媽媽</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=其他">其他</a></div>
    <div  class="sub-type"  id="type2">
      <a href="<?=URL::to('/')?>/web/map?keyword=美容">美容</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=按摩">按摩</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=美髮">美髮</a>
      <a href="<?=URL::to('/')?>/web/map?keyword=美甲美睫">美甲美睫</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=運動">運動</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=瑜珈">瑜珈</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=舞蹈">舞蹈</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=游泳">游泳</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=其他">其他</a>
     </div>
    <div  class="sub-type"  id="type3">
      <a href="<?=URL::to('/')?>/web/map?keyword=課業伴讀">課業伴讀</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=語言學習">語言學習</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=音樂教學">音樂教學</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=攝影教學">攝影教學</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=廚藝指導">廚藝指導</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=繪畫教學">繪畫教學</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=才藝培養">才藝培養</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=電腦教學">電腦教學</a>、
      <a href="<?=URL::to('/')?>/web/map?keyword=其他">其他</a>
     </div>
  </div>
  <div class="session">
      <div class="line"></div>
      <h3>最新活動</h3>
  </div>
  <div class="ad-arr">
  <div class="ad-container">
        <div class="swiper-wrapper">
        	@if($center_pics && count($center_pics))
                @foreach($center_pics as $center_pic)
                	<div class="swiper-slide"><img src="/home/big/{{$center_pic}}" width="100%"></div>
                @endforeach
            @else
                <div class="swiper-slide"><img src="{{asset("/images/ad1.jpg")}}" width="100%"></div>
            	<div class="swiper-slide"><img src="{{asset("/images/ad.jpg")}}" width="100%"></div>
            @endif


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
      @if($videotype==1)
      <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{$videoid}}" allowfullscreen></iframe>
      @elseif($videotype==2)
      <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{$videoid}}" allowfullscreen></iframe>
      @else
      <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/Pm0_G8Zl0ek" allowfullscreen></iframe>
      @endif
      </div>
      <a href="login.html" class="join">加入幫棒</a>
  </div>
  <div class="session mbl">
      <div class="line"></div>
      <h3>發案者使用教學</h3>
  </div>
  <div class="step-arr">
      <div class="step-box">
      <div class="step-item">
      <img src="{{asset("/images/stepA_01.jpg")}}">

      <div class="step-info"><p>1.想好所需服務並上BounBang幫棒鍵入妳的需求。</p></div>
      </div>
      <div class="step-item">
      <img src="{{asset("/images/stepA_03.jpg")}}">
      <div class="step-info"><p>2.BounBang幫棒依照妳的需求推薦幫手。</p><p>3.選出妳的好幫手並協議所需服務內容。</p></div>
      </div>
      <div class="step-item">
      <img src="{{asset("/images/stepA_04.jpg")}}">
      <div class="step-info"><p>4.提出訂單並確認且完成代收付款。</p></div>
      </div>
      <div class="step-item">
      <img src="{{asset("/images/stepA_05.jpg")}}">
      <div class="step-info"><p>5.完成服務並依照使用條款給予回覆。</p></div>
      </div>
      </div>
  </div>
  <div class="session mbl">
      <div class="line"></div>
      <h3>接案者使用教學</h3>
  </div>
  <div class="step-arr">
    <div class="step-box">
    <div class="step-item">
    <img src="{{asset("/images/stepB_01.jpg")}}">
    <div class="step-info"><p>1.登入BounBang幫棒。</p><p>2.輸入或更新個人專長。</p></div>
    </div>
    <div class="step-item">
    <img src="{{asset("/images/stepB_02.jpg")}}">
    <div class="step-info"><p>3.收到客戶服務需求。</p><p>4.與客戶協議服務內容。</p></div>
    </div>
    <div class="step-item">
    <img src="{{asset("/images/stepB_03.jpg")}}">
    <div class="step-info"><p>5.確認服務需求與客戶訂單。</p></div>
    </div>
    <div class="step-item">
    <img src="{{asset("/images/stepB_04.jpg")}}">
    <div class="step-info"><p>6.完成服務。</p><p>7.BounBang幫棒依照使用條款代付服務費。</p></div>
    </div>
    </div>
  </div>
  <div class="session">
      <div class="line"></div>
      <h3>幫棒！您的好幫手</h3>
  </div>
  <div class="helper-arr">
      <div class="helper-item">
      <div class="helper-tit"> 即時便利性</div>
      <h3>高手藏在民間, 好幫手在您身邊</h3>
      <p>讓您周遭的好幫手們透過 即時預約 即時接單配對 的方式,最快1小時內, 為您提供最即時且便利的服務! </p></div>
      <div class="helper-item"><img src="{{asset("/images/picme.jpg")}}"></div>
       <div class="helper-item d-none d-sm-block"><img src="{{asset("/images/picme1.jpg")}}"></div>
      <div class="helper-item">
      <div class="helper-tit"> 利潤共享</div>
      <h3>經由您介紹進來的朋友都會是您的＂夥伴＂
      也是您的夥伴團隊的一員</h3>
      <p>當他們成為好幫手並完成交易, 您就可以收到交易金額的5%做為您的回饋金,感謝您組建夥伴團隊的努力!</p></div>
      <div class="helper-item d-block d-sm-none"><img src="{{asset("/images/picme1.jpg")}}"></div>
       <div class="helper-item">
      <div class="helper-tit">安心信任</div>
      <h3>我們有提供保險讓好幫手選擇使用</h3>
      <p>讓服務過程中突發的意外也有保障, 而且,好幫手們提供的證照及身分認證讓您可以對好幫手多一層信賴, 最重要的是您可以推薦您合作過的傑出好幫手給您的朋友, 或接受朋友推薦的好幫手, 讓好幫手透過您們彼此信任的口碑提供最優質的服務給您。 </p></div>
      <div class="helper-item fix-t"><img src="{{asset("/images/picme2.jpg")}}"></div>
       <a href="login.html" class="join">成為好幫手</a>
  </div>
  <div class="session">
      <div class="line"></div>
      <h3>用戶迴響</h3>
  </div>
  <div class="user-feedback">
      <div class="swiper-wrapper">
          <div class="swiper-slide">
          <a href="" class="user_up">
          <div class="helper-face"><img src="{{asset("/images/5b8cb18be69ac514330441.jpg")}}"></div><div class="helper-score"><span class="user-name">好幫手：王大明</span><br class="d-block d-sm-none"><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div><div class="feedback">受僱次數 <span class="text-success">65</span>次</div>
          <div class="income">服務類別：居家清掃、水電工程、木工裝潢...</div>
          </div>
          </a>
          <div class="user_border">
          <span class="arrow_t_int"></span>
          <span class="arrow_t_out"></span>
          <span class="user-name">客戶：陳小姐</span>
          <div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div>
          <div class="income">完全是我想要的風格!有什麼需求直接跟攝影師討論,他都會盡量達到我們的完全是我想要要...</div>

          </div>
      </div>
      <div class="swiper-slide">
          <a href="" class="user_up">
          <div class="helper-face"><img src="{{asset("/images/5b8cb18be69ac514330441.jpg")}}"></div><div class="helper-score"><span class="user-name">好幫手：王大明</span><br class="d-block d-sm-none"><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div><div class="feedback">受僱次數 <span class="text-success">65</span>次</div>
          <div class="income">服務類別：居家清掃、水電工程、木工裝潢...</div>
          </div>
          </a>
          <div class="user_border">
          <span class="arrow_t_int"></span>
          <span class="arrow_t_out"></span>
          <span class="user-name">客戶：陳小姐</span>
          <div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div>
          <div class="income">完全是我想要的風格!有什麼需求直接跟攝影師討論,他都會盡量達到我們的完全是我想要要...</div>

          </div>
      </div>
      <div class="swiper-slide">
          <a href="" class="user_up">
          <div class="helper-face"><img src="{{asset("/images/5b8cb18be69ac514330441.jpg")}}"></div><div class="helper-score"><span class="user-name">好幫手：王大明</span><br class="d-block d-sm-none"><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div><div class="feedback">受僱次數 <span class="text-success">65</span>次</div>
          <div class="income">服務類別：居家清掃、水電工程、木工裝潢...</div>
          </div>
          </a>
          <div class="user_border">
              <span class="arrow_t_int"></span>
              <span class="arrow_t_out"></span>
              <span class="user-name">客戶：陳小姐</span>
              <div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div>
              <div class="income">完全是我想要的風格!有什麼需求直接跟攝影師討論,他都會盡量達到我們的完全是我想要要...</div>
          </div>
      </div>
      <div class="swiper-slide">
          <a href="" class="user_up">
          <div class="helper-face"><img src="{{asset("/images/5b8cb18be69ac514330441.jpg")}}"></div><div class="helper-score"><span class="user-name">好幫手：王大明</span><br class="d-block d-sm-none"><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div><div class="feedback">受僱次數 <span class="text-success">65</span>次</div>
          <div class="income">服務類別：居家清掃、水電工程、木工裝潢...</div>
          </div>
          </a>
          <div class="user_border">
          <span class="arrow_t_int"></span>
          <span class="arrow_t_out"></span>
          <span class="user-name">客戶：陳小姐</span>
          <div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div>
          <div class="income">完全是我想要的風格!有什麼需求直接跟攝影師討論,他都會盡量達到我們的完全是我想要要...</div>
          </div>
      </div>
      <div class="swiper-slide">
          <a href="" class="user_up">
          <div class="helper-face"><img src="{{asset("/images/5b8cb18be69ac514330441.jpg")}}"></div><div class="helper-score"><span class="user-name">好幫手：王大明</span><br class="d-block d-sm-none"><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div><div class="feedback">受僱次數 <span class="text-success">65</span>次</div>
          <div class="income">服務類別：居家清掃、水電工程、木工裝潢...</div>
          </div>
          </a>
          <div class="user_border">
          <span class="arrow_t_int"></span>
          <span class="arrow_t_out"></span>
          <span class="user-name">客戶：陳小姐</span>
          <div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div>
          <div class="income">完全是我想要的風格!有什麼需求直接跟攝影師討論,他都會盡量達到我們的完全是我想要要...</div>
          </div>
      </div>
      <div class="swiper-slide">
          <a href="" class="user_up">
          <div class="helper-face"><img src="{{asset("/images/5b8cb18be69ac514330441.jpg")}}"></div><div class="helper-score"><span class="user-name">好幫手：王大明</span><br class="d-block d-sm-none"><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div><div class="feedback">受僱次數 <span class="text-success">65</span>次</div>
          <div class="income">服務類別：居家清掃、水電工程、木工裝潢...</div>
          </div>
          </a>
          <div class="user_border">
          <span class="arrow_t_int"></span>
          <span class="arrow_t_out"></span>
          <span class="user-name">客戶：陳小姐</span>
          <div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div>
          <div class="income">完全是我想要的風格!有什麼需求直接跟攝影師討論,他都會盡量達到我們的完全是我想要要...</div>
          </div>
      </div>
      <div class="swiper-slide">
          <a href="" class="user_up">
          <div class="helper-face"><img src="{{asset("/images/5b8cb18be69ac514330441.jpg")}}"></div><div class="helper-score"><span class="user-name">好幫手：王大明</span><br class="d-block d-sm-none"><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div><div class="feedback">受僱次數 <span class="text-success">65</span>次</div>
          <div class="income">服務類別：居家清掃、水電工程、木工裝潢...</div>
          </div>
          </a>
          <div class="user_border">
          <span class="arrow_t_int"></span>
          <span class="arrow_t_out"></span>
          <span class="user-name">客戶：陳小姐</span>
          <div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div>
          <div class="income">完全是我想要的風格!有什麼需求直接跟攝影師討論,他都會盡量達到我們的完全是我想要要...</div>
          </div>
      </div>
      <div class="swiper-slide">
          <a href="" class="user_up">
          <div class="helper-face"><img src="{{asset("/images/5b8cb18be69ac514330441.jpg")}}"></div><div class="helper-score"><span class="user-name">好幫手：王大明</span><br class="d-block d-sm-none"><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div><div class="feedback">受僱次數 <span class="text-success">65</span>次</div>
          <div class="income">服務類別：居家清掃、水電工程、木工裝潢...</div>
          </div>
          </a>
          <div class="user_border">
          <span class="arrow_t_int"></span>
          <span class="arrow_t_out"></span>
          <span class="user-name">客戶：陳小姐</span>
          <div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i> 地點：台中市南區</div>
          <div class="income">完全是我想要的風格!有什麼需求直接跟攝影師討論,他都會盡量達到我們的完全是我想要要</div>
          </div>
      </div>
   </div>
  <div class="swiper-pagination"></div>
  </div>
  <div class="session">
       <div class="line"></div>
      <h3>本日最佳幫手</h3>
  </div>
</div>
<script src="{{asset("/js/swiper.jquery.min.js")}}"></script>
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

    $('.searct-bt').on('click', function () {
      location.href="<?=URL::to('/')?>/web/map?keyword=" + $('#keyword').val();
    })

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var pos = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        $.ajax({
          type: "post",
          url: "<?=URL::to('/')?>/set_latlng",
          data: {
            lat: pos.lat,
            lng: pos.lng,
            _token: "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function(response){

          }
        });
        // $('#lat').val(pos.lat);
        // $('#lng').val(pos.lng);

      }, function() {
        handleLocationError(true, infoWindow, map.getCenter());
      });
    } else {
      // Browser doesn't support Geolocation
      handleLocationError(false, infoWindow, map.getCenter());
    }
  })

</script>
@stop