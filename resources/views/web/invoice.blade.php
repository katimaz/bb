@extends('web.base')
@section('content')
  <!--內容開始 -->

  <!--內容結束 -->
  <div class="item-title">發票設定</div>
    <div class="container">
      <div class=" offset-md-2 col-md-8">
        <div class="pay-box">
          <div class="form-group bankpay">
            <input id="account" type="radio" name="group-1" class="einvoice" value="personal" checked="checked" data-toggle-select-hide=".change-bank" data-toggle-select-show="#banks">
            <label for="account">個人發票</label>
            <input id="payment" type="radio" name="group-1" class="einvoice" value="crop" data-toggle-select-hide=".change-bank" data-toggle-select-show="#credit">
            <label for="payment">公司發票</label>
          </div>
          <div id="banks" class="change-bank active">
            <div class="text-dark"> 發票中獎將會主動通知寄出</div>
          </div>
          <div id="credit" class="change-bank" >
            <div class="form-group row">
              <label  class="col-sm-2 col-form-label">公司名稱</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="title" value="">
              </div>
            </div>
            <div class="form-group row">
              <label  class="col-sm-2 col-form-label">統一編號</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="number" value="">
              </div>
            </div>
            <div class="form-group row">
              <div class="offset-sm-2 col-sm-10">
                <button type="submit" class="btn btn-lg btn-success" id="btn-add-invoice">新增公司資訊</button>
              </div>
            </div>
            <div class="form-group row">
              <label  class="col-sm-2 col-form-label">己儲存公司</label>
              <div class="col-sm-10">
                @foreach($datalist as $value)
                <div class="card-list">
                  <div class="numbers">{{$value->title}}</div>
                  <div class="dates">{{$value->number}}</div>
                  <div class="c-delete" data-id="{{$value->id}}"><i class="fa fa-times-circle-o" aria-hidden="true"></i></div>
                </div>
                @endforeach
              </div>
            </div>
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
    $(function(){
      $('.header,.search-frame').addClass('fixed') ;
      if ($(window).width() < 991) {$('.header,.search-frame').removeClass('fixed')  }
    });
    $('.file-input').change(function(){
      var curElement = $(this).parent().parent().find('.image');
      console.log(curElement);
      var reader = new FileReader();

      reader.onload = function (e) {
          // get loaded data and render thumbnail.
          curElement.attr('src', e.target.result);
      };

      // read the image file as a data URL.
      reader.readAsDataURL(this.files[0]);
    })
    $(function(){
      $('.bankpay input').on('change', function()  {
        var $this = $('.bankpay input:checked')
        var $hideElements = $($this.attr("data-toggle-select-hide"));
          var $showElements = $($this.attr("data-toggle-select-show"));

        $hideElements.slideUp();
        $showElements.slideDown();

      });

      $('#btn-add-invoice').on('click', function () {
        var crop_or_personal = '';
        $('.einvoice').each(function () {
          if($(this).prop('checked') == true) {
            crop_or_personal = $(this).val();
          }
        })
        $.ajax({
          type: "post",
          url: "{{url('/api/set_invoice')}}",
          data: {
            crop_or_personal: crop_or_personal,
            title: $('#title').val(),
            number: $('#number').val(),
            _token: '{{ csrf_token() }}'
          },
          dataType: "json",
          success: function (response) {
            location.reload();
          }
        });
      })
      $('.c-delete').on('click', function () {
        var id = $(this).data('id');
        $.ajax({
          type: "post",
          url: "{{url('/api/del_invoice')}}",
          data: {
            id: id,
            _token: '{{ csrf_token() }}'
          },
          dataType: "json",
          success: function (response) {
            location.reload();
          }
        });
      })
    });


  </script>
  @endsection
@stop