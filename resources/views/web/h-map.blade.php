@extends('web.base')
@section('content')
<!--內容開始 -->
<div class="map-box">
    <div class="map-left">
      <div> <a class="close-side"><img src="{{asset('/images/sclose.png')}}"></a>
        <form>
          <div class="form-group">
            <label for="location"><i class="fa fa-map-marker" aria-hidden="true"></i> 服務地點</label>
            <input type="text" class="form-control" id="location" placeholder="目前地點 ( 或輸入地址 )">
          </div>
          <div class="slidecontainer">
            <label for="myRange"><i class="fa fa-adjust" aria-hidden="true"></i> 搜尋半徑</label>
            <input type="range" min="500" max="50000" value="10000" class="slider" id="myRange">
            <div class="c-range">目前範圍：<span id="demo"></span>公里</div>
          </div>
          <div class="form-group" id="toggle-select">
            <label for="exampleInputPassword1"><i class="fa fa-check-square-o" aria-hidden="true"></i> 服務類別</label>
            <select class="form-control">
              <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type1" selected>居家服務</option>
              <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type2">健康美容</option>
              <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type3">學習</option>
              <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type4">代辦代購</option>
              <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type5">活動設計</option>
              <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type6">專業設計</option>
              <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type7">文字工作</option>
              <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type8">專業顧問</option>
              <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type9">旅遊</option>
              <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type10">創意市集</option>
              <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type11">二手平台</option>
              <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type12">其他</option>
            </select>
          </div>
          <div class="type-arr active" id="type1">
           <input id='all-type1' type="checkbox" />
           <label for="all-type1"  style="width:100%">全選</label>
            <input id='check-1' type="checkbox" name='check-1' />
            <label for="check-1">美味家常菜</label>
            <input id='check-2' type="checkbox" name='check-1' />
            <label for="check-2">居家清掃</label>
            <input id='check-3' type="checkbox" name='check-1' />
            <label for="check-3">水電工程</label>
            <input id='check-4' type="checkbox" name='check-1' />
            <label for="check-4">小孩讀伴玩</label>
            <input id='check-5' type="checkbox" name='check-1' />
            <label for="check-5">銀髮族照護</label>
            <input id='check-6' type="checkbox" name='check-1' />
            <label for="check-6">寵物美容/照顧</label>
            <input id='check-7' type="checkbox" name='check-1' />
            <label for="check-7">洗車/汽車美容</label>
            <input id='check-8' type="checkbox" name='check-1' />
            <label for="check-8">居家布置</label>
            <input id='check-9' type="checkbox" name='check-1' />
            <label for="check-9">花藝</label>
            <input id='check-10' type="checkbox" name='check-1' />
            <label for="check-10">衣物送洗</label>
            <input id='check-11' type="checkbox" name='check-1' />
            <label for="check-11">管家服務</label>
            <input id='check-12' type="checkbox" name='check-1' />
            <label for="check-12">月子媽媽</label>
            <input id='check-13' type="checkbox" name='check-1' />
            <label for="check-13">其他</label>

          </div>
          <div class="type-arr" id="type2">
           <input id='all-type2' type="checkbox" />
           <label for="all-type2"  style="width:100%">全選</label>
            <input id='check-14' type="checkbox" name='check-1' />
            <label for="check-14">美容</label>
            <input id='check-15' type="checkbox" name='check-1' />
            <label for="check-15">按摩</label>
            <input id='check-16' type="checkbox" name='check-1' />
            <label for="check-16">美髮美甲美睫</label>
            <input id='check-17' type="checkbox" name='check-1' />
            <label for="check-17">運動</label>
            <input id='check-18' type="checkbox" name='check-1' />
            <label for="check-18">瑜珈</label>
            <input id='check-19' type="checkbox" name='check-1' />
            <label for="check-19">舞蹈</label>
            <input id='check-20' type="checkbox" name='check-1' />
            <label for="check-20">游泳</label>
            <input id='check-21' type="checkbox" name='check-1' />
            <label for="check-21">其他</label>
          </div>
          <div class="type-arr" id="type3">
           <input id='all-type3' type="checkbox" />
           <label for="all-type3"  style="width:100%">全選</label>
            <input id='check-22' type="checkbox" name='check-1' />
            <label for="check-22">課業伴讀</label>
            <input id='check-23' type="checkbox" name='check-1' />
            <label for="check-23">語言學習</label>
            <input id='check-24' type="checkbox" name='check-1' />
            <label for="check-24">音樂教學</label>
            <input id='check-25' type="checkbox" name='check-1' />
            <label for="check-25">攝影教學</label>
            <input id='check-26' type="checkbox" name='check-1' />
            <label for="check-26">廚藝指導</label>
            <input id='check-27' type="checkbox" name='check-1' />
            <label for="check-27">繪畫教學</label>
            <input id='check-28' type="checkbox" name='check-1' />
            <label for="check-28">才藝培養</label>
            <input id='check-29' type="checkbox" name='check-1' />
            <label for="check-29">電腦教學</label>
            <input id='check-30' type="checkbox" name='check-1' />
            <label for="check-30">其他</label>
          </div>     
          <div class="mach-bt"><a href="#" class="lmbt">搜尋工作</a><a href="#" class="rmbt" data-toggle="modal" data-target="#exampleModal"><img src="{{asset('/images/wbell.svg')}}"> 配對工作</a></div>
        </form>
      </div>
    </div>
    <div class="map-right">
      <a href="javascript:void(0)" class="open-side"><i class="fa fa-filter" aria-hidden="true"></i> 進階搜尋</a> <a
        href="h-list.html" class="open-list reset-bt"><i class="fa fa-list-ul" aria-hidden="true"></i> 切換成列表</a><a
        href="javascript:void(0)" class="reset" title="回到定位點"><i class="fa fa-bandcamp" aria-hidden="true"></i></a>
      <div id="map-marker"></div>
    </div>
  </div>
  <!--內容結束 -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">配對確認</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <h4>您確定使用以下設定接收系統配對?</h4>
     <p> 您將會在我的工作的配對項目中收到符合您設定的工作推薦。</p>
      <p><span class="text-success"><i class="fa fa-map-marker" aria-hidden="true"></i> 所需服務地點：</span>目前地點</p>
          <p><span class="text-success"><i class="fa fa-adjust" aria-hidden="true"></i> 搜尋半徑：</span>10 公里</p>
          <p><span class="text-success"><i class="fa fa-check-square-o" aria-hidden="true"></i> 服務項目：</span>水電工程、電腦教學</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-success"  data-dismiss="modal">確定送出</button>
      </div>
    </div>
  </div>
</div> 

<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-bell" aria-hidden="true"></i> 我有興趣訊息已送出</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <h5>如果您符合需求將會收到詢問或雇用通知。</h5></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉視窗</button>
      </div>
    </div>
  </div>
</div> 
<!--內容結束 -->
<script> $(window).on('load', function () { $(".se-pre-con").fadeOut("slow"); });</script>
<script src="{{asset('/js/jquery.tinyMap.min.js')}}"></script>
<script src="{{asset('/js/bootstrap-input-spinner.js')}}"></script>

<script>
$(function () {
  $('.header,.search-frame').addClass('fixed');
  if ($(window).width() < 991) { $('.header,.search-frame').removeClass('fixed') }
});
(function () {
  $.fn.tinyMapConfigure({
	// Google Maps API URL
	'api': '//maps.googleapis.com/maps/api/js',
	// Google Maps API Version
	'v': '3.37',
	// Google Maps API Key，預設 null
	'key': 'AIzaSyCbykV-WUp54j1NuzeYP6rnS6zLN4JC91U',
	// 使用的地圖語言
	// 'libraries': 'geometry'

  });


  var map = $('#map-marker');
  var current = ['23.4678812', '120.43860749999999'];
  var loc = [
	{
	  addr: ['24.9936693', '121.5098049']
	  ,
	  text: '<div  class="user_map"><div class="map-up"><div class="up-face"><img src="{{asset("/images/5b8cb18be69ac514330441.jpg")}}"></div><div class="up-left"><span class="user-name">林天生</span><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i><span class="text-success"> 距離：</span><span class="text-danger">500</span>公尺</div></div></div><div class="map-score"><div class="income"><span class="text-success"> 服務地點：</span>台中市南區</div><div class="income"><span class="text-success"> 服務項目：</span>水電工程</div><div class="income"><span class="text-success">預算：</span><span class="text-danger">500元</span>/小時<div class="income"><span class="text-success">需求說明：</span>居家屋頂加壓馬逹修理...<a href="job-detail.html">詳細內容</a><div class="map-bt"><a href="#" class="lmbt" data-toggle="modal"              data-target="#exampleModalLong">我有興趣</a><a href="re-parner.html" class="rmbt">推薦夥伴</a></div></div></div>',
	  icon: '{{asset("/images/mark.png")}}',
	  newLabel: '<img src="{{asset("/images/5b8cb18be69ac514330441.jpg")}}" style="border-radius:50%;width:30px;height:30px;margin-top: -95px;border: 2px solid #b30b06;">'
	}
	,
	{
	  addr: ['24.9907604', '121.5088074'],
	  text: '<div  class="user_map"><div class="map-up"><div class="up-face"><img src="{{asset("/images/5b8cb18be69ac514330441.jpg")}}"></div><div class="up-left"><span class="user-name">林天生</span><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i><span class="text-success"> 距離：</span><span class="text-danger">500</span>公尺</div></div></div><div class="map-score"><div class="income"><span class="text-success"> 服務地點：</span>台中市南區</div><div class="income"><span class="text-success"> 服務項目：</span>水電工程</div><div class="income"><span class="text-success">預算：</span><span class="text-danger">500元</span>/小時<div class="income"><span class="text-success">需求說明：</span>居家屋頂加壓馬逹修理...<a href="job-detail.html">詳細內容</a><div class="map-bt"><a href="#" class="lmbt" data-toggle="modal"              data-target="#exampleModalLong">我有興趣</a><a href="re-parner.html" class="rmbt">推薦夥伴</a></div></div></div>',
	  icon: '{{asset("/images/mark.png")}}',
	  newLabel: '<img src="http://shop.ray-lee.name/map-job/images/face1.jpg" style="border-radius:50%;width:30px;height:30px;margin-top: -95px;border:2px solid #b30b06;	">'
	},
	{
	  addr: ['24.9908682', '121.5095038'],
	  text: '<div  class="user_map"><div class="map-up"><div class="up-face"><img src="{{asset("/images/5b8cb18be69ac514330441.jpg")}}"></div><div class="up-left"><span class="user-name">林天生</span><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i><span class="text-success"> 距離：</span><span class="text-danger">500</span>公尺</div></div></div><div class="map-score"><div class="income"><span class="text-success"> 服務地點：</span>台中市南區</div><div class="income"><span class="text-success"> 服務項目：</span>水電工程</div><div class="income"><span class="text-success">預算：</span><span class="text-danger">500元</span>/小時<div class="income"><span class="text-success">需求說明：</span>居家屋頂加壓馬逹修理...<a href="job-detail.html">詳細內容</a><div class="map-bt"><a href="#" class="lmbt" data-toggle="modal"  data-target="#exampleModalLong">我有興趣</a><a href="re-parner.html" class="rmbt">推薦夥伴</a></div></div></div>',
	  icon: '{{asset("/images/mark.png")}}',
	  newLabel: '<img src="/images/face.jpg" style="border-radius:50%;width:30px;height:30px;margin-top: -95px;border: 2px solid #b30b06;	">'
	}
	,
	{
	  addr: ['24.9907156', '121.5070689'],
	  text: '<div  class="user_map"><div class="map-up"><div class="up-face"><img src="{{asset("/images/5b8cb18be69ac514330441.jpg")}}"></div><div class="up-left"><span class="user-name">林天生</span><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i><span class="text-success"> 距離：</span><span class="text-danger">500</span>公尺</div></div></div><div class="map-score"><div class="income"><span class="text-success"> 服務地點：</span>台中市南區</div><div class="income"><span class="text-success"> 服務項目：</span>水電工程</div><div class="income"><span class="text-success">預算：</span><span class="text-danger">500元</span>/小時<div class="income"><span class="text-success">需求說明：</span>居家屋頂加壓馬逹修理...<a href="job-detail.html">詳細內容</a><div class="map-bt"><a href="#" class="lmbt" data-toggle="modal"              data-target="#exampleModalLong">我有興趣</a><a href="re-parner.html" class="rmbt">推薦夥伴</a></div></div></div>',
	  icon: '{{asset("/images/mark.png")}}',
	  newLabel: '<img src="http://shop.ray-lee.name/map-job/images/face.jpg" style="border-radius:50%;width:30px;height:30px;margin-top: -95px;border: 2px solid #b30b06;	">'
	}
	,
	{
	  addr: ['24.989985', '121.510150'],
	  text: '<div  class="user_map"><div class="map-up"><div class="up-face"><img src="{{asset("/images/5b8cb18be69ac514330441.jpg")}}"></div><div class="up-left"><span class="user-name">林天生</span><span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span><span class="avg">4.9</span><div class="income"><i class="fa fa-map-marker" aria-hidden="true"></i><span class="text-success"> 距離：</span><span class="text-danger">500</span>公尺</div></div></div><div class="map-score"><div class="income"><span class="text-success"> 受雇次數：</span><span class="text-danger">21</span>次</div><div class="income"><span class="text-success"> 當地導遊：</span><span class="text-danger">500元</span>/小時</div><div class="income"><span class="text-success">其他服務：</span>居家清掃、水電工程、木工...<a href="job-detail.html">詳細說明</a><div class="map-bt"><a href="#" class="lmbt" data-toggle="modal"              data-target="#exampleModalLong">線上諮詢</a><a href="#" class="rmbt" data-toggle="modal"              data-target="#exampleModalLong">雇用</a></div></div></div>',
	  icon: '{{asset("/images/mark.png")}}',
	  newLabel: '<img src="{{asset("/images/5b7d4694c0ab9850731325.jpg")}}" style="border-radius:50%;width:30px;height:30px;margin-top: -95px;border: 2px solid #b30b06;	">'
	}
  ];
  var screen = true;
  if ($(window).width() > 768) { screen = true } else { screen = false };
  // 執行 tinyMap
  map.tinyMap({
	center: ['24.9911433', '121.5097802'],
	markerCluster: true,
	marker: loc,
	zoom: 17,
	zoomControl: screen,
	scaleControl: false,
	mapTypeControl: false,

  });


}());
var slider = document.getElementById("myRange");
var output = document.getElementById("demo");
output.innerHTML = slider.value/1000;

slider.oninput = function () {
  output.innerHTML = this.value/1000;
}

$(function () {
  $("#toggle-select").change(function () {
	var $this = $(this).find(':selected');
	var $hideElements = $($this.attr("data-toggle-select-hide"));
	var $showElements = $($this.attr("data-toggle-select-show"));

	$hideElements.slideUp();
	$showElements.slideDown();

  });
});
$(function () {
  $('.cy-arr input').on('change', function () {
	var $this = $('.cy-arr input:checked')
	var $hideElements = $($this.attr("data-toggle-select-hide"));
	var $showElements = $($this.attr("data-toggle-select-show"));

	$hideElements.slideUp();
	$showElements.slideDown();

  });
});
$(".map-detail a").on('click', function () {
  $(this).find('i').toggleClass('fa fa-plus fa fa-minus')
  $('.detail-option').slideToggle();
});
$("a.open-side").on('click', function () {
  $('.map-left').addClass('active');
});
$("a.close-side,a.hmbt,a.lmbt").on('click', function () {
  $('.map-left').removeClass('active');
});
$('#number-picker,#number').hide();
$('#hour').on('change', function () {
  $('#number-picker,#number').show();
});
$('#day').on('change', function () {
  $('#number-picker,#number').hide();
});
$('.third').hide();
$('#third').on('change', function () {
  $('.third').show();
  $('.twice').hide();
});
$('#twice').on('change', function () {
  $('.twice').show();
  $('.third').hide();
});
$(function(){ 	
	$("#all-type1,#all-type2,#all-type3").click(function () {
		$(this).siblings('input:checkbox').not(this).prop('checked', this.checked);
	}); 
});

</script>
@stop