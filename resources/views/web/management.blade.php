@extends('web.base')
@section('content')
  <!--內容開始 -->

  <!--內容結束 -->
  <div class="item-title">我的需求</div>
  <div class="container">
    <div class="row">
      <div class=" offset-md-1 col-md-10">
        <div class="pay-box">
          <div class="item-link">
            <a href="/web/management" class="active"><img src="images/sbell.svg"> 配對</a><a href="reservation.html">預約(2)</a><a href="history.html">歷史記錄</a>
          </div>
          <!-- 會員的需求 -->
          @foreach($nlo as $key => $value)
          <div class="list-group">
            <a href="#" class="list-group-item list-group-item-action flex-column align-items-start" data-toggle="modal" data-target="#exampleModalLong">
              <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">{{$value->service_type}}</h5>
                <small>{{$value->created_at}}</small>
              </div>
              <p class="mb-1">服務地點：{{$value->mem_addr}}</p>
              <p class="mb-1">範圍：50公里</p>
              <p class="mb-1">預算：<span class="text-danger">{{$value->budget}}</span> / {{$value->budget_type}}</p>

              <span class="edita"><i class="fa fa-pencil" aria-hidden="true"></i></span>
            </a>
            <!-- 有興趣的好幫手 -->
            @foreach($value->mf as $h_key => $h_value)
            <div class="incold">
              <span class="b-close"><i class="fa fa-times-circle" aria-hidden="true"></i></span>
              <div class="mana-link"><a href="/ask/{{$value->id}}/{{$h_value->need_id}}">線上諮詢</a><a href="hire.html" class="hire">雇用</a></div>
              <a href="{{url('/web/helper_detail/' . $h_value->usr_id . '/' . $h_value->distance * 1000)}}" class="list-group-item list-group-item-action flex-column align-items-start">
                <div class="mana-box">
                  <div class="list-left">
                    <span class="b-face fix"><img src="{{url('/avatar/small/' . $h_value->usr_photo)}}"></span>
                  </div>
                  <div class="list-right fix">
                    <div>{{$h_value->last_name . $h_value->first_name}}
                      <span class="list-start pl-2"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i></span><span class="avg">4.9</span>
                    </div>
                    <span class="mana">距離：{{round($h_value->distance * 1000)}}公尺</span>
                    <span class="mana">{{$value->service_type}}：{{$h_value->price}}元 / {{$h_value->price_type}}</span><br class="hidexs">
                    <span class="mana">工作時數：125 / 小時</span>
                    <span class="mana">受雇次數：22 次</span>
                    <div class="ott-service">其他服務：居家清掃、水電工程、木工...</div>
                  </div>
                </div>
              </a>
            </div>
            @endforeach
            <!-- 有興趣的好幫手 - END -->

          </div>
          <!-- 頁碼 -->
          <div class="d-flex mt-3">
            <ul class="pagination mx-auto">
                <li class="page-item disabled">
                    <a class="page-link" href="#" aria-label="Previous">
                        <span aria-hidden="true">«</span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
                <li class="page-item active">
                    <a class="page-link" href="#">1</a>
                </li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                        <span aria-hidden="true">»</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            </ul>
          </div>
          <!-- 頁碼 - END -->
          @endforeach
          <!-- 會員的需求 - END -->

        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">修改(刪除)配對</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
     <div class="form-group" id="toggle-select">
            <label><i class="fa fa-map-marker" aria-hidden="true"></i> 服務地點：</label>
            <select class="form-control">
              <option  selected>台中市大里區大智路567號12樓</option>
             </select>
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
            <div class="form-group">
              <label for="location"><i class="fa fa-usd" aria-hidden="true"></i> 預算</label>
              <input type="text" class="form-control" id="location"  value="500" >
              <span>元</span>
            </div>
            <div class="form-group">
              <input id='day' type="radio" name='group-1'  checked='checked'/>
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
               <input type="number" value="0" min="1" max="10">
                </div>
              </div>
              <div class="cycle" id="cy-b">
                <label>預約日期：</label><input type="date" id="start" class="form-control">
                <label>結束日期：</label><input type="date" id="end" class="form-control">
                <label>預約時間：</label><input type="time" class="form-control">
                <label id="number">工作時間/小時：</label>
                <div class="input-group" id="number-picker">
               <input type="number" value="0" min="1" max="10">
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
               <input type="number" value="0" min="1" max="10">
                </div>

              </div>
              <div class="cycle" id="cy-d">
                <label>預約日期：</label><input type="date" id="start" class="form-control">
                <label>結束日期：</label><input type="date" id="end" class="form-control">
                <label>每月幾號：</label><input type="text" class="form-control" placeholder="多個日期以＇，＇分隔">
                <label>預約時間：</label><input type="time" class="form-control">
                <label id="number">工作時間/小時：</label>
                <div class="input-group" id="number-picker">
               <input type="number" value="0" min="1" max="10">
                </div>
              </div>
            </div>

            <div class="form-group">
             <input id='opic' type="checkbox" name=''/>
              <label for="opic">刪除已上傳照片</label>  </div>
           <label for="file-1">照片重新上傳：</label><input  id="file-1" type="file" multiple>
          <textarea name="" 　class="form-control" placeholder="需求描述"></textarea>
          <div class="summary">
         <p> 預算：500元/小時 </p>
    <p>時間週期：每週三、五，2019/05/01 至 20/05/10，下午1-5點</p>
    <p>2019/05/01 下午1-5點4小時、2019/05/01 下午1-5點4小時、2019/05/01 下午1-5點4小時、2019/05/01 下午1-5點4小時</p>
    <p> 總金額：8000元 </p>
              </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">取消配對</button>
          <button type="button" class="btn btn-success"  data-dismiss="modal">修改配對</button>
        </div>
      </div>
    </div>
  </div>
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
    $( "a.close-side" ).on('click', function() {
      $('.map-left').removeClass('active');
    });
    $('#number-picker,#number').hide();
    $('#hour').on('change', function()  {
      $('#number-picker,#number').show();
    });
    $('#day').on('change', function()  {
      $('#number-picker,#number').hide();
    });
  </script>
  @endsection
@stop