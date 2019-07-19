@extends('web.base')
@section('content')
  <!--內容開始 -->
  <div class="map-box">
    <div class="map-left">
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
    <div class="map-right">
      <a href="javascript:void(0)" class="open-side"><i class="fa fa-filter" aria-hidden="true"></i> 進階搜尋</a> <a
        href="{{url('/web/list')}}" class="open-list reset-bt"><i class="fa fa-list-ul" aria-hidden="true"></i> 切換成列表</a><a
        href="javascript:void(0)" class="reset" title="回到定位點"><i class="fa fa-bandcamp" aria-hidden="true"></i></a>
      <div id="map-marker"></div>
    </div>
  </div>
  <!--配對確認 -->
  <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">服務鈴(配對)設定</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p><span class="text-success">關鍵字：</span>加壓馬逹安裝</p>
          <p><span class="text-success"><i class="fa fa-map-marker" aria-hidden="true"></i> 所需服務地點：</span>目前地點</p>
          <p><span class="text-success"><i class="fa fa-adjust" aria-hidden="true"></i> 搜尋半徑：</span>10 公里</p>
          <p><span class="text-success"><i class="fa fa-check-square-o" aria-hidden="true"></i> 服務類別：</span>水電工程</p>


            <!-- 設計選項
            <div class="form-group">
              <label for="location"><i class="fa fa-usd" aria-hidden="true"></i> 預算</label>
              <input type="text" class="form-control" id="location" placeholder="請輸入金額">
              <span>元</span>
              </div>
            <div class="form-group">
              <input id='twice' type="radio" name='group-1' checked='checked' />
              <label for="twice">二階段付款</label>
              <input id='third' type="radio" name='group-1' />
              <label for="third">三階段付款</label>
              </div>
              <div class="twice">
                <label>第一次付款日期:</label><input type="date" id="start" class="form-control">
                <label>付款比例(輸入百分比)%:</label><input type="text" id="start" class="form-control" placeholder="50">
                <label>結案付款日期:</label><input type="date" id="end" class="form-control">
                <label>付款比例(輸入百分比)%:</label><input type="text" id="start" class="form-control"  placeholder="50">
                </div>
                <div class="third">
                <label>第一次付款日期:</label><input type="date" id="start" class="form-control">
                <label>付款比例(輸入百分比)%:</label><input type="text" id="start" class="form-control" placeholder="40">
                <label>第二次付款日期:</label><input type="date" id="start" class="form-control">
                <label>付款比例(輸入百分比)%:</label><input type="text" id="start" class="form-control" placeholder="30">
                <label>結案付款日期:</label><input type="date" id="end" class="form-control">
                <label>付款比例(輸入百分比)%:</label><input type="text" id="start" class="form-control"  placeholder="30">
                </div>  -->
            <!--
            <div class="form-group">
              <label for="location"><i class="fa fa-usd" aria-hidden="true"></i>每件：</label>
              <input type="text" class="form-control" id="location" placeholder="請輸入金額">
              <span>元</span>
              </div>
              <label>件數：</label>
                        <div class="input-group">
                          <div class="input-group-button">
                            <button class="button minus" value="-" for="break-3">-</button>
                          </div>
                          <input class="input-group-field form-control" id="break-3" type="number" value="" min="1" max="24">
                          <div class="input-group-button">
                            <button class="button plus" value="+" for="break-3">+</button>
                          </div>
                        </div>
              <label>預約日期：</label><input type="date" class="form-control">
              <label>預約時間：</label><input type="time" class="form-control">
                -->

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
                  <div class="input-group-button">
                    <button class="button minus" value="-" for="break">-</button>
                  </div>
                  <input class="input-group-field form-control" id="break" type="number" value="" min="1" max="24">
                  <div class="input-group-button">
                    <button class="button plus" value="+" for="break">+</button>
                  </div>
                </div>
              </div>
              <div class="cycle" id="cy-b">
                <label>預約日期：</label><input type="date" id="start" class="form-control">
                <label>結束日期：</label><input type="date" id="end" class="form-control">
                <label>預約時間：</label><input type="time" class="form-control">
                <label id="number">工作時間/小時：</label>
                <div class="input-group" id="number-picker">
                  <div class="input-group-button">
                    <button class="button minus" value="-" for="break-1">-</button>
                  </div>
                  <input class="input-group-field form-control" id="break-1" type="number" value="" min="1" max="24">
                  <div class="input-group-button">
                    <button class="button plus" value="+" for="break-1">+</button>
                  </div>
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
                  <div class="input-group-button">
                    <button class="button minus" value="-" for="break-2">-</button>
                  </div>
                  <input class="input-group-field form-control" id="break-2" type="number" value="" min="1" max="24">
                  <div class="input-group-button">
                    <button class="button plus" value="+" for="break-2">+</button>
                  </div>
                </div>

              </div>
              <div class="cycle" id="cy-d">
                <label>預約日期：</label><input type="date" id="start" class="form-control">
                <label>結束日期：</label><input type="date" id="end" class="form-control">
                <label>每月幾號：</label><input type="text" class="form-control" placeholder="多個日期以＇，＇分隔">
                <label>預約時間：</label><input type="time" class="form-control">
                <label id="number">工作時間/小時：</label>
                <div class="input-group" id="number-picker">
                  <div class="input-group-button">
                    <button class="button minus" value="-" for="break-3">-</button>
                  </div>
                  <input class="input-group-field form-control" id="break-3" type="number" value="" min="1" max="24">
                  <div class="input-group-button">
                    <button class="button plus" value="+" for="break-3">+</button>
                  </div>
                </div>
              </div>
            </div>
            <input id="file-1" type="file" name="file-1" class="file-upload" data-file-upload="">
            <label for="file-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17">
                <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path>
              </svg>
              <span class="input-file-label">照片上傳：</span>
            </label>
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
  <!-- <script src="{{asset('/js/jquery.min.js')}}"></script>
  <script> $(window).on('load', function () { $(".se-pre-con").fadeOut("slow"); });</script> -->
  <!-- <script src="{{asset('/js/popper.min.js')}}"></script>
  <script src="{{asset('js/bootstrap.min.js')}}"></script> -->
  <!-- <script src="{{asset('/js/swiper.jquery.min.js')}}"></script>
  <script src="{{asset('/js/jquery.tinyMap.min.js')}}"></script> -->
  <!-- <script src="{{asset('/js/position.js')}}"></script>
  <script src="{{asset('/js/main.js')}}"></script> -->
  <!-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyABW4BgnHQyCb11qpo3kx6t97BwxgG1k18&callback=initMap" type="text/javascript"></script> -->

@section('myScript')
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
        // 'key': 'AIzaSyCbykV-WUp54j1NuzeYP6rnS6zLN4JC91U',
        'key': 'AIzaSyABW4BgnHQyCb11qpo3kx6t97BwxgG1k18',
        // 使用的地圖語言
        // 'libraries': 'geometry'

      });


      var map = $('#map-marker');
      var current = ['<?=Session::get('lat')?>', '<?=Session::get('lng')?>'];
      var loc = <?=json_encode($loc)?>;

      var screen = true;
      if ($(window).width() > 768) { screen = true } else { screen = false };
      // 執行 tinyMap
      map.tinyMap({
        center: ['<?=Session::get('lat')?>', '<?=Session::get('lng')?>'],
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
    $("a.close-side,a.hmbt").on('click', function () {
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
        url: "<?=URL::to('/')?>/api/search_offer",
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
          var map = $('#map-marker');
          $(map).tinyMap('clear', 'marker,label');
          map.tinyMap('panTo', [lat, lng]);

          map.tinyMap('modify', {
            marker: response.loc,

          });
        }
      });
    }

  </script>
  @endsection
@stop