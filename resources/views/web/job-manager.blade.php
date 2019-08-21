@extends('web.base')
@section('content')
  <!--內容開始 -->
   <div class="item-title">我的工作</div>
  <div class="container">
    <div class="row">
      <div class=" offset-md-1 col-md-10">
        <div class="pay-box">
          <div class="item-link"><a href="job-manager.html" class="active"><img src="images/sbell.svg"> 配對</a><a href="job-reservation.html">預約(2)</a><a href="job-history.html">歷史記錄</a></div>
          <!-- 開始 -->
          @foreach($nlos as $nlo)
            @foreach($nlo as $key => $value)
            <div class="incold fix">
              <!-- 刪除鈕 -->
              <span class="b-close fix"><i class="fa fa-times-circle" aria-hidden="true"></i></span>
              <!-- 本文 -->
              <div class="mana-link"><a href="#" class="a-add-fav" data-toggle="modal" data-target="#exampleModalLong" data-id="{{$value->id}}" data-olo="{{$value->offer_id}}">我有興趣</a><a href="re-parner.html" class="hire">推薦夥伴</a></div>
              <a href="/web/job_detail/{{$value->usr_id}}/{{$value->distance}}" class="list-group-item list-group-item-action flex-column align-items-start jobs">
                <div class="mana-box">
                  <div class="list-left">
                    <span class="b-face fix"><img src="{{URL::to('/')}}/avatar/small/{{$value->usr_photo}}"></span>
                  </div>
                  <div class="list-right">
                    <small>{{$value->datetime_from}}</small>
                    <div>{{$value->last_name}}{{$value->first_name}}
                      <span class="list-start pl-2"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i></span>
                      <span class="avg">4.9</span>
                    </div>
                    <span class="mana">距離：{{round($value->distance * 1000)}}公尺</span>
                    <span class="mana">地點：{{$value->mem_addr}}</span>
                    <br class="hidexs">
                    <span class="mana">關鍵字：{{$value->keyword}}</span>
                    <span class="mana">服務項目：{{$value->service_type}}</span>
                    <br class="hidexs">
                    <span class="mana">預算：
                      <span class="text-danger">{{$value->budget}}</span> / {{$value->budget_type}} * {{$value->available_daytime_enum}}小時 共
                      <span class="text-danger">{{$value->total}}</span>元
                    </span>
                  </div>
                </div>
              </a>
            </div>
            @endforeach
          @endforeach
          <!-- 結束 -->

          <div class="d-flex mt-5">
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
              <li class="page-item"><a class="page-link" href="#">4</a></li>
              <li class="page-item">
                <a class="page-link" href="#" aria-label="Next">
                  <span aria-hidden="true">»</span>
                  <span class="sr-only">Next</span>
                </a>
              </li>
            </ul>
          </div>
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
        <h5>如果您符合需求將會收到詢問或雇用通知。</h5>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉視窗</button>
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
    $('.a-add-fav').on('click', function () {
      $.ajax({
        type: "post",
        url: "{{url('/api/add_fav')}}",
        data: {
          need_id: $(this).data('id'),
          offer_id: $(this).data('olo'),
          _token: '{{csrf_token()}}'
        },
        dataType: "json",
        success: function (response) {

        }
      });
    })
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