@extends('web.base')
@section('content')
  <!--內容開始 -->
    <div class="map-box">
      <div class="map-left fix">
        <div> <a class="close-side"><img src="<?=URL::to('/')?>/images/sclose.png"></a>
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
              <select class="form-control" id="main-servicetype">
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
            <div class="type-arr active" id="type1" name="sub_servicetype[]">
              <input id='check-1' type="checkbox" class="sub-servicetype" name='check-1' value="美味家常菜" />
              <label for="check-1">美味家常菜</label>
              <input id='check-2' type="checkbox" class="sub-servicetype" name='check-1' value="居家清掃" />
              <label for="check-2">居家清掃</label>
              <input id='check-3' type="checkbox" class="sub-servicetype" name='check-1' value="水電工程" />
              <label for="check-3">水電工程</label>
              <input id='check-4' type="checkbox" class="sub-servicetype" name='check-1' value="小孩讀伴玩" />
              <label for="check-4">小孩讀伴玩</label>
              <input id='check-5' type="checkbox" class="sub-servicetype" name='check-1' value="銀髮族照護" />
              <label for="check-5">銀髮族照護</label>
              <input id='check-6' type="checkbox" class="sub-servicetype" name='check-1' value="寵物美容" />
              <label for="check-6">寵物美容/照顧</label>
              <input id='check-7' type="checkbox" class="sub-servicetype" name='check-1' value="洗車" />
              <label for="check-7">洗車/汽車美容</label>
              <input id='check-8' type="checkbox" class="sub-servicetype" name='check-1' value="居家布置" />
              <label for="check-8">居家布置</label>
              <input id='check-9' type="checkbox" class="sub-servicetype" name='check-1' value="花藝" />
              <label for="check-9">花藝</label>
              <input id='check-10' type="checkbox" class="sub-servicetype" name='check-1' value="衣物送洗" />
              <label for="check-10">衣物送洗</label>
              <input id='check-11' type="checkbox" class="sub-servicetype" name='check-1' value="管家服務" />
              <label for="check-11">管家服務</label>
              <input id='check-12' type="checkbox" class="sub-servicetype" name='check-1' value="月子媽媽" />
              <label for="check-12">月子媽媽</label>
              <input id='check-13' type="checkbox" class="sub-servicetype" name='check-1' value="其他" />
              <label for="check-13">其他</label>

            </div>
            <div class="type-arr" id="type2">
              <input id='check-14' type="checkbox" class="sub-servicetype" name='check-1' value="美容" />
              <label for="check-14">美容</label>
              <input id='check-15' type="checkbox" class="sub-servicetype" name='check-1' value="按摩" />
              <label for="check-15">按摩</label>
              <input id='check-16' type="checkbox" class="sub-servicetype" name='check-1' value="美髮美甲美睫" />
              <label for="check-16">美髮美甲美睫</label>
              <input id='check-17' type="checkbox" class="sub-servicetype" name='check-1' value="運動" />
              <label for="check-17">運動</label>
              <input id='check-18' type="checkbox" class="sub-servicetype" name='check-1' value="瑜珈" />
              <label for="check-18">瑜珈</label>
              <input id='check-19' type="checkbox" class="sub-servicetype" name='check-1' value="舞蹈" />
              <label for="check-19">舞蹈</label>
              <input id='check-20' type="checkbox" class="sub-servicetype" name='check-1' value="游泳" />
              <label for="check-20">游泳</label>
              <input id='check-21' type="checkbox" class="sub-servicetype" name='check-1' value="其他" />
              <label for="check-21">其他</label>
            </div>
            <div class="type-arr" id="type3">
              <input id='check-22' type="checkbox" class="sub-servicetype" name='check-1' value="課業伴讀" />
              <label for="check-22">課業伴讀</label>
              <input id='check-23' type="checkbox" class="sub-servicetype" name='check-1' value="語言學習" />
              <label for="check-23">語言學習</label>
              <input id='check-24' type="checkbox" class="sub-servicetype" name='check-1' value="音樂教學" />
              <label for="check-24">音樂教學</label>
              <input id='check-25' type="checkbox" class="sub-servicetype" name='check-1' value="攝影教學" />
              <label for="check-25">攝影教學</label>
              <input id='check-26' type="checkbox" class="sub-servicetype" name='check-1' value="廚藝指導" />
              <label for="check-26">廚藝指導</label>
              <input id='check-27' type="checkbox" class="sub-servicetype" name='check-1' value="繪畫教學" />
              <label for="check-27">繪畫教學</label>
              <input id='check-28' type="checkbox" class="sub-servicetype" name='check-1' value="才藝培養" />
              <label for="check-28">才藝培養</label>
              <input id='check-29' type="checkbox" class="sub-servicetype" name='check-1' value="電腦教學" />
              <label for="check-29">電腦教學</label>
              <input id='check-30' type="checkbox" class="sub-servicetype" name='check-1' value="其他" />
              <label for="check-30">其他</label>
            </div>
            <div class="mach-bt"><a href="#" class="hmbt">搜尋幫手</a></div>
          </form>
        <a href="#" class="bell"　　　　 data-toggle="modal"              data-target="#exampleModalLong"><img src="<?=URL::to('/')?>/images/bell.svg"></a>
        <div class="bell-text">需要客製化與即時服務嗎?<br>使用<a href="#"　　　　 data-toggle="modal"              data-target="#exampleModalLong">服務鈴(配對)</a>．</div>
        </div>
      </div>

      <div class="map-right fix">
        <a href="javascript:void(0)" class="open-side"><i class="fa fa-filter" aria-hidden="true"></i> 進階搜尋</a>
        <a href="{{url('/web/map')}}" class="open-list reset-bt"><i class="fa fa-list-ul" aria-hidden="true"></i> 切換成地圖</a>
        <div class="list">
          <div class="list-box best-bg">
            <div class="list-left hide-m">
            </div>
            <div class="list-right">
              <div class="list-name hide-m"></div>
              <div class="list-comm"><a href="#" class="sbtn">評價 <i class="fa fa-long-arrow-down" aria-hidden="true"></i></a></div>
              <div class="list-ds"><a href="#" class="sbtn">距離 <i class="fa fa-long-arrow-down" aria-hidden="true"></i></a></div>
              <div class="list-dl"><a href="#" class="sbtn">受雇次數 <i class="fa fa-long-arrow-down" aria-hidden="true"></i></a></div>
              <div class="list-dl"><a href="#" class="sbtn">工作時數 <i class="fa fa-long-arrow-down" aria-hidden="true"></i></a></div>
              <div class="list-dl">
                <div class="dropdown show">
                  <a class="sbtn  dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="hide-xs">服務</span>項目</a>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#">水電工程 <i class="fa fa-long-arrow-down" aria-hidden="true"></i></a>
                    <a class="dropdown-item" href="#">室內設計 <i class="fa fa-long-arrow-down" aria-hidden="true"></i></a>
                  </div>
                </div>
              </div>
              <div class="list-dl">
                <div class="dropdown show">
                  <a class="sbtn  dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">價格</a>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#">/件 <i class="fa fa-long-arrow-down" aria-hidden="true"></i></a>
                    <a class="dropdown-item" href="#">/小時 <i class="fa fa-long-arrow-down" aria-hidden="true"></i></a>
                  </div>
                </div>
              </div>
              <div class="list-types  hide-m"></div>
              <div class="list-hire  hide-m"></div>
            </div>
          </div>
          <div id="list-content">
            @foreach($loc as $value)
            <div class="list-box"> <div class="list-left"> <span class="b-face"><img src="<?=URL::to('/')?>/images/{{$value->usr_photo}}"></span> </div> <a href="<?=URL::to('/')?>/web/helper_detail/{{$value->usr_id}}/{{(int)($value->distance * 1000)}}" class="list-right"> <div class="list-name">{{$value->last_name}}{{$value->first_name}}</div> <div  class="list-comm"> <span class="list-start"> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> </span> <span class="avg">4.9</span> </div> <div class="list-ds"><span class="show-m">距離：</span>{{(int)($value->distance * 1000)}}公尺</div> <div class="list-dl"><span class="show-m">受雇次數：</span>256次</div > <div class="list-dl"><span class="show-m">工作時數：</span>125/小時</div > <div class="list-dl"><span class="show-m">服務項目：</span>水電工程</div> <div class="list-dl"><span class="show-m">價格：</span>{{$value->price}}/{{$value->price_type}}</div> <div class="list-types"><img src="images/work1.jpg"><img src="images/works.jpg"></div> </a> <div class="list-bt"> <a href="#" class="lask" data-toggle="modal" data-target="#exampleModalLong">詢問</a> <a href="#" class="lhire"  data-toggle="modal" data-target="#exampleModalLong">雇用</a> </div> </div>
            @endforeach
          </div>
          <div class="more"><button class="btn ">更多</button></div>
        </div>
      </div>
    </div>
 <!--配對確認 -->
<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><img src="{{asset('images/wbell.svg')}}"> 服務鈴(配對)設定</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        <p><span class="text-success">關鍵字：</span>加壓馬逹安裝</p>

          <p><span class="text-success"><i class="fa fa-check-square-o" aria-hidden="true"></i> 服務類別：</span>水電工程</p><p><span class="text-success"><i class="fa fa-adjust" aria-hidden="true"></i> 搜尋半徑：</span>10 公里</p>
            <div class="form-group" id="toggle-select">
            <label><i class="fa fa-map-marker" aria-hidden="true"></i> 服務地點：</label>
            <select class="form-control">
              <option  selected>台中市大里區大智路567號12樓</option>
             </select>
             </div>
            <div class="form-group">
              <label for="location"><i class="fa fa-usd" aria-hidden="true"></i> 預算</label>
              <input type="text" class="form-control" id="location" placeholder="請輸入金額">
              <span>元</span>
            </div>
            <div class="form-group">
              <input id='day' type="radio" name='group-1' checked='checked' />
              <label for="day">每件</label>
              <input id='hour' type="radio" name='group-1' />
              <label for="hour">每小時</label>
            </div>
            <div class="form-group">
              <label for="location"><i class="fa fa-calendar" aria-hidden="true"></i> 週期</label>
              <div class="cy-arr">
                <input id='once' type="radio" name='group-2' data-toggle-select-hide=".cycle"
                  data-toggle-select-show="#cy-a" checked />
                <label for="once">一次</label>
                <input id='daily' type="radio" name='group-2' data-toggle-select-hide=".cycle"
                  data-toggle-select-show="#cy-b" />
                <label for="daily">每日</label>
                <input id='weekly' type="radio" name='group-2' data-toggle-select-hide=".cycle"
                  data-toggle-select-show="#cy-c" />
                <label for="weekly">每週</label>
                <input id='month' type="radio" name='group-2' data-toggle-select-hide=".cycle"
                  data-toggle-select-show="#cy-d" />
                <label for="month">每月</label>
              </div>
              <div class="cycle active" id="cy-a">
                <label>預約日期：</label><input type="date" class="form-control">
                <label>預約時間：</label><input type="time" class="form-control">
                <label id="number">工作時間/小時：</label>
                <div class="input-group" id="number-picker">
                  <input type="number" value="0" min="0" max="10">
                </div>
              </div>
              <div class="cycle" id="cy-b">
                <label>預約日期：</label><input type="date" id="start" class="form-control">
                <label>結束日期：</label><input type="date" id="end" class="form-control">
                <label>預約時間：</label><input type="time" class="form-control">
                <label id="number">工作時間/小時：</label>
                <div class="input-group" id="number-picker">
                 <input type="number" value="0" min="0" max="10" />
              </div>
               </div>
              <div class="cycle" id="cy-c">
                <label>預約日期：</label><input type="date" id="start" class="form-control">
                <label>結束日期：</label><input type="date" id="end" class="form-control">
                <label>星期：</label>
                <div>
                  <input id='week-1' type="checkbox" name='week' />
                  <label for="week-1">星期一</label>
                  <input id='week-2' type="checkbox" name='week' />
                  <label for="week-2">星期二</label>
                  <input id='week-3' type="checkbox" name='week' />
                  <label for="week-3">星期三</label>
                  <input id='week-4' type="checkbox" name='week' />
                  <label for="week-4">星期四</label>
                  <input id='week-5' type="checkbox" name='week' />
                  <label for="week-5">星期五</label>
                  <input id='week-6' type="checkbox" name='week' />
                  <label for="week-6">星期六</label>
                  <input id='week-7' type="checkbox" name='week' />
                  <label for="week-7">星期日</label>
                </div>
                <label>預約時間：</label><input type="time" class="form-control">
                <label id="number">工作時間/小時：</label>
                <div class="input-group" id="number-picker">
                   <input type="number" value="0" min="0" max="10" />
                </div>

              </div>
              <div class="cycle" id="cy-d">
                <label>預約日期：</label><input type="date" id="start" class="form-control">
                <label>結束日期：</label><input type="date" id="end" class="form-control">
                <label>每月幾號：</label><input type="text" class="form-control" placeholder="多個日期以＇，＇分隔">
                <label>預約時間：</label><input type="time" class="form-control">
                <label id="number">工作時間/小時：</label>
                <div class="input-group" id="number-picker">
                   <input type="number" value="0" min="0" max="10" />
                </div>
              </div>
            </div>
         <label for="file-1">照片上傳：</label><input  id="file-1" type="file" multiple>
          <textarea name="" 　class="form-control" placeholder="需求描述"></textarea>
          <div class="summary">
            <p> 預算：500元/小時 </p>
            <p>時間週期：每週三、五，2019/05/01 至 20/05/10，下午1-5點</p>
            <p>2019/05/01 下午1-5點4小時、2019/05/01 下午1-5點4小時、2019/05/01 下午1-5點4小時、2019/05/01 下午1-5點4小時</p>
            <p> 總金額：8000元 </p>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
          <button type="button" class="btn btn-success">確認配對</button>
        </div>
      </div>
    </div>
  </div>
  <!--內容結束 -->
    <!-- <script src="js/jquery.min.js"></script>
    <script> $(window).on('load', function () { $(".se-pre-con").fadeOut("slow"); });</script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/swiper.jquery.min.js"></script>
    <script src="js/jquery.tinyMap.min.js"></script>
    <script src="js/position.js"></script>
    <script src="js/main.js"></script>
     <script src="js/bootstrap-input-spinner.js"></script> -->
@section('myScript')
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyABW4BgnHQyCb11qpo3kx6t97BwxgG1k18" type="text/javascript"></script>
<script src="{{asset('js/bootstrap-input-spinner.js')}}"></script>
<script>
  $("input[type='number']").inputSpinner();
  $('.input-group').click(function(e) {

    e.preventDefault();

    var button = e.target;
    var target = $(button).attr('for');
    var currValue = $('#'+ target).val();
    var sign = $(button).val();
    var newValue = 0;

    if (sign === '+') {
      newValue = +currValue + 1;
    }
    if (sign === '-') {
      if (+currValue <= 1) {
        newValue = 1;
      } else {
        newValue = +currValue - 1;
      }
    }

    $('#'+ target).val(newValue);

  });
  $(function(){
    $('.header,.search-frame').addClass('fixed') ;
    if ($(window).width() < 991) {$('.header,.search-frame').removeClass('fixed')  }
  });

  var slider = document.getElementById("myRange");
  var output = document.getElementById("demo");
  output.innerHTML = slider.value;

  slider.oninput = function() {
    output.innerHTML = this.value;
  }

  $(function(){
    $("#toggle-select").change(function(){
      var $this = $(this).find(':selected');
    var $hideElements = $($this.attr("data-toggle-select-hide"));
      var $showElements = $($this.attr("data-toggle-select-show"));

      $hideElements.slideUp();
      $showElements.slideDown();

    });
  });
  $(function(){
    $('.cy-arr input').on('change', function()  {
        var $this = $('.cy-arr input:checked')
      var $hideElements = $($this.attr("data-toggle-select-hide"));
        var $showElements = $($this.attr("data-toggle-select-show"));
        $hideElements.slideUp();
        $showElements.slideDown();
      });
    });
    $( ".map-detail a" ).on('click', function() {
      $(this).find('i').toggleClass('fa fa-plus fa fa-minus')
      $('.detail-option').slideToggle();
    });
    $( "a.open-side" ).on('click', function() {
      $('.map-left').addClass('active');
    });
    $( "a.close-side,a.hmbt" ).on('click', function() {
      $('.map-left').removeClass('active');
    });
    $('#number-picker,#number').hide();
    $('#hour').on('change', function()  {
      $('#number-picker,#number').show();
      });
    $('#day').on('change', function()  {
      $('#number-picker,#number').hide();
      });
    $(function(){
    $("#all-type1,#all-type2,#all-type3").click(function () {
      $(this).siblings('input:checkbox').not(this).prop('checked', this.checked);
    });

    // 搜尋
    $('.mach-bt a.hmbt').on('click', function(){
    var lat = '<?=session()->get('lat')?>';
    var lng = '<?=session()->get('lng')?>';

    if($('#location').val() != '') {
      // sub_servicetype_str = sub_servicetype.join(',');
      geocoder = new google.maps.Geocoder();
      geocoder.geocode({
        "address": $('#location').val()
      },
      function (results, status) {

        if (status == google.maps.GeocoderStatus.OK) {

          lat = results[0].geometry.location.lat();
          lng = results[0].geometry.location.lng();

          search_offer(lat, lng);
        }else {
          //$("#target").val(content + addr + "查無經緯度" + "\n");
        }
      });
    } else {
      search_offer(lat, lng);
    }


  })

  function search_offer(lat, lng) {
    var sub_servicetype = [];

    // var sub_servicetype_str = "";
    $('.sub-servicetype').each( function(){
      if($(this).prop('checked') == true) {
        sub_servicetype.push($(this).val());
      }
    })

    $.ajax({
      type: "post",
      url: "<?=URL::to('/')?>/api/search_offer_list",
      data: {
        lat: lat,
        lng: lng,
        distance: $('#myRange').val(),
        main_servicetype: $('#main-servicetype').val(),
        sub_servicetype: sub_servicetype,
        _token: '{{ csrf_token() }}'
      },
      dataType: "json",
      success: function (response) {
        $('#list-content').empty();
        $('#list-content').append(response.loc);
      }
    });
  }

  });
 </script>
@endsection
@stop