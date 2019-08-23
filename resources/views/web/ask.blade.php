@extends('web.base')
@section('content')
  <!--內容開始 -->

  <!--內容結束 -->
  <div class="item-title"><a href="javascript:history.back();"><i class="fa fa-chevron-left" aria-hidden="true"></i>
      回上頁</a>詢問中</div>
  <div class="container">
    <div class="row">
      <div class=" offset-md-1 col-md-10">
        <div class="ask-fixed">
          <a href="hire.html" class="ask-hire">雇用</a>
          <!-- A區 -->
          <a href="helper-detail.html" class="list-group-item list-group-item-action flex-column align-items-start">
            <div class="mana-box">
              <div class="list-left">
                <span class="b-face fix"><img src="images/5b8cb18be69ac514330441.jpg"></span>
              </div>
              <div class="list-right">
                <div>
                  林美麗
                  <span class="list-start pl-2"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i></span><span class="avg">4.9</span>
                </div>
                <span class="mana">距離：800 公尺</span>
                <span class="mana">水電工程：500元  /小時</span>
                <span class="mana">受雇次數：22 次</span>
                <span class="mana">工作時數：125 / 小時</span >
              </div>
            </div>
          </a>
          <!-- A區 - END -->
          <!-- B區 -->
          <a href="#" class="list-group-item list-group-item-action flex-column align-items-start" data-toggle="modal" data-target="#exampleModalLong">
            <span class="edit"><i class="fa fa-pencil" aria-hidden="true"></i></span>
            <p class="mb-1 pr-3">服務地點：<span class="text-mana">台中市南區工學路55號2-12樓</span></p><p class="mb-1 pr-3">關鍵字：<span class="text-mana">收納大師</span></p><p class="mb-1 pr-3">服務項目：<span class="text-mana">居家清絜</span></p>
            <p class="mb-1">內容：<span class="text-mana">環境清潔等...</span></p>
            <p class="mb-1"><span class="mana">預算：<span class="text-danger">500 </span>/ 小時</span> <span class="mana"> 總價：<span class="text-danger">8000 </span>元</span></p>
            <div class="ask-pic">
            <img src="images/20161005_R030.jpg">
            <img src="images/20161005_R031.jpg">
            </div>
            <p class="mb-1">時間週期：<span class="text-mana">2019/05/01 至 20/05/10 每週三、五，下午1-5點</span></p>
            <div class="pay-list">2019/05/01 下午1-5點4小時  共：<span class="text-danger">2000</span>元</div>
            <div class="pay-list">2019/05/01 下午1-5點4小時  共：<span class="text-danger">2000</span>元</div>
            <div class="pay-list">2019/05/01 下午1-5點4小時  共：<span class="text-danger">2000</span>元</div>
            <div class="pay-list">2019/05/01 下午1-5點4小時  共：<span class="text-danger">2000</span>元</div>
          </a>
          <!-- B區 - END -->
          <!-- C區 -->
          <div class="chat-box">
            <div class="chat-bayer">
              <div class="text-sub">PM:0233</div>好的我會參考看看
            </div>
            <div class="chat-helper">
              <div class="text-sub">PM:0231</div>上面有很多跟你一樣的案例
            </div>
            <div class="chat-helper">
              <div class="text-sub">PM:0231</div>我FB叫做小胖居家清理
            </div>
            <div class="chat-helper">
              <div class="text-sub">PM:0230</div>好的我給你我FB網址上面可以參考
            </div>
            <div class="chat-bayer">
              <div class="text-sub">PM:0210</div>請問可以提供案例參考嗎?
            </div>
          </div>
          <div class="chat-input">
            <input class="form-control" type="text"><button class="upload"><i class="fa fa-upload" aria-hidden="true"></i></button><button class="send-chat"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
          </div>
          <!-- C區 - 結束 -->
        </div>


      </div>
    </div>
  </div>


  <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">修改工作內容及時間</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="">
           <div class="form-group" id="toggle-select">
            <label><i class="fa fa-map-marker" aria-hidden="true"></i> 服務地點：</label>
            <select class="form-control">
              <option  selected>台中市大里區大智路567號12樓</option>
             </select>
             </div>
            <div class="form-group">
              <input id='day' type="radio" name='group-1' />
              <label for="day">每件</label>
              <input id='hour' type="radio" name='group-1' checked='checked' />
              <label for="hour">每小時</label>
            </div>
            <div class="form-group">
              <label for="location"><i class="fa fa-calendar" aria-hidden="true"></i> 週期</label>
              <div class="cy-arr">
                <input id='once' type="radio" name='group-2' data-toggle-select-hide=".cycle"
                  data-toggle-select-show="#cy-a" />
                <label for="once">一次</label>
                <input id='daily' type="radio" name='group-2' data-toggle-select-hide=".cycle"
                  data-toggle-select-show="#cy-b" />
                <label for="daily">每日</label>
                <input id='weekly' type="radio" name='group-2' data-toggle-select-hide=".cycle"
                  data-toggle-select-show="#cy-c" checked />
                <label for="weekly">每週</label>
                <input id='month' type="radio" name='group-2' data-toggle-select-hide=".cycle"
                  data-toggle-select-show="#cy-d" />
                <label for="month">每月</label>
              </div>
              <div class="cycle" id="cy-a">
                <label>預約日期:</label><input type="date" class="form-control" value="2019-05-30">
                <label>預約時間:</label><input type="time" class="form-control" value="09:30">
                <label id="number">工作時間/小時:</label>
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
                <label>預約日期:</label><input type="date" id="start" class="form-control">
                <label>結束日期:</label><input type="date" id="end" class="form-control">
                <label>預約時間:</label><input type="time" class="form-control">
                <label id="number">工作時間/小時:</label>
                <div class="input-group" id="number-picker">
                  <div class="input-group-button">
                    <button class="button minus" value="-" for="break-1">-</button>
                  </div>
                  <input class="input-group-field form-control" id="break-1" type="number" value="4" min="1" max="24">
                  <div class="input-group-button">
                    <button class="button plus" value="+" for="break-1">+</button>
                  </div>
                </div>
              </div>
              <div class="cycle  active" id="cy-c">
                <label>預約日期:</label><input type="date" id="start" class="form-control" value="2019-05-01">
                <label>結束日期:</label><input type="date" id="end" class="form-control" value="2019-05-10">
                <label>星期:</label>
                <div>
                  <input id='week-1' type="checkbox" name='week' />
                  <label for="week-1">星期一</label>
                  <input id='week-2' type="checkbox" name='week' />
                  <label for="week-2">星期二</label>
                  <input id='week-3' type="checkbox" name='week' checked />
                  <label for="week-3">星期三</label>
                  <input id='week-4' type="checkbox" name='week' />
                  <label for="week-4">星期四</label>
                  <input id='week-5' type="checkbox" name='week' checked />
                  <label for="week-5">星期五</label>
                  <input id='week-6' type="checkbox" name='week' />
                  <label for="week-6">星期六</label>
                  <input id='week-7' type="checkbox" name='week' />
                  <label for="week-7">星期日</label>
                </div>
                <label>預約時間:</label><input type="time" class="form-control" value="13:00">
                <label id="number">工作時間/小時:</label>
                <div class="input-group" id="number-picker">
                  <div class="input-group-button">
                    <button class="button minus" value="-" for="break-2">-</button>
                  </div>
                  <input class="input-group-field form-control" id="break-2" type="number" value="4" min="1" max="24">
                  <div class="input-group-button">
                    <button class="button plus" value="+" for="break-2">+</button>
                  </div>
                </div>

              </div>
              <div class="cycle" id="cy-d">
                <label>預約日期:</label><input type="date" id="start" class="form-control">
                <label>結束日期:</label><input type="date" id="end" class="form-control">
                <label>每月幾號:</label><input type="text" class="form-control" placeholder="多個日期以＇，＇分隔">
                <label>預約時間:</label><input type="time" class="form-control">
                <label id="number">工作時間/小時:</label>
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
          </div>

          <textarea name="" 　class="form-control" placeholder="需求描述"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn  btn-success" data-dismiss="modal">送出修改</button>

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
  <script src="js/main.js"></script> -->
  @section('myScript')
  <script>
    $('.input-group').click(function (e) {

      e.preventDefault();

      var button = e.target;
      var target = $(button).attr('for');
      var currValue = $('#' + target).val();
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

      $('#' + target).val(newValue);

    });
    $(function () {
      $('.header,.search-frame').addClass('fixed');
      if ($(window).width() < 991) { $('.header,.search-frame').removeClass('fixed') }
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
    $("a.close-side").on('click', function () {
      $('.map-left').removeClass('active');
    });

    $('#hour').on('change', function () {
      $('#number-picker,#number').show();
    });
    $('#day').on('change', function () {
      $('#number-picker,#number').hide();
    });

  </script>
  @endsection
  @stop