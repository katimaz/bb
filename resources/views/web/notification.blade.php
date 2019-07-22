@extends('web.base')
@section('content')
  <!--內容開始 -->

  <!--內容結束 -->
   <div class="item-title">通知設定</div>
  <div class="container">
    <div class=" offset-md-2 col-md-8 pb-5">

     <!--     <div class="pay-box">
 <div class="form-group bankpay">
      <input id="account" type="radio" name="group-1" checked="checked" data-toggle-select-hide=".change-bank" data-toggle-select-show="#banks">
      <label for="account">E-mail通知</label>
    <input id="payment" type="radio" name="group-1" data-toggle-select-hide=".change-bank" data-toggle-select-show="#credit">
      <label for="payment">Line通知</label>
      </div>    -->
      @if($datalist->isEmpty())
      <div id="banks" class="change-bank active">
        <div class="noti-list">未讀訊息通知 <input class='toggle notify' type="checkbox" name='check-3' id="setting_email_notify1" value="true" /></div>
        <div class="noti-list">服務鈴(配對)通知<input class='toggle notify' type="checkbox" name='check-3' id="setting_email_notify2" value="true" /></div>
        <div class="noti-list">24小時訂單提醒<input class='toggle notify' type="checkbox" name='check-3' id="setting_email_notify3" value="true" /></div>
        <div class="noti-list">回饋金通知<input class='toggle notify' type="checkbox" name='check-3' id="setting_email_notify4" value="true" /></div>
      </div>
      @else
      <div id="banks" class="change-bank active">
        <div class="noti-list">未讀訊息通知 <input class='toggle notify' type="checkbox" name='check-3' id="setting_email_notify1" {{($datalist[0]->setting_email_notify1) ? 'checked=checked' : ''}}  /></div>
        <div class="noti-list">服務鈴(配對)通知<input class='toggle notify' type="checkbox" name='check-3' id="setting_email_notify2" {{($datalist[0]->setting_email_notify2) ? 'checked=checked' : ''}}  /></div>
        <div class="noti-list">24小時訂單提醒<input class='toggle notify' type="checkbox" name='check-3' id="setting_email_notify3" {{($datalist[0]->setting_email_notify3) ? 'checked=checked' : ''}} /></div>
        <div class="noti-list">回饋金通知<input class='toggle notify' type="checkbox" name='check-3' id="setting_email_notify4" {{($datalist[0]->setting_email_notify4) ? 'checked=checked' : ''}}  /></div>
      </div>
      @endif
<!--<div id="credit" class="change-bank" >
   <div class="noti-list">未讀訊息通知 <input class='toggle' type="checkbox" name='check-3' checked='checked' /></div>
  <div class="noti-list">服務鈴(配對)通知<input class='toggle' type="checkbox" name='check-3' checked='checked'  /></div>
  <div class="noti-list">24小時訂單提醒<input class='toggle' type="checkbox" name='check-3'  checked='checked' /></div>
  <div class="noti-list">回饋金通知<input class='toggle' type="checkbox" name='check-3' checked='checked'  /></div>

</div> -->
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

  $('.notify').on('click', function () {
    var setting_email_notify1 = 1;
    var setting_email_notify2 = 1;
    var setting_email_notify3 = 1;
    var setting_email_notify4 = 1;
    if($('#setting_email_notify1').prop('checked') == false) {
      setting_email_notify1 = 0;
    }
    if($('#setting_email_notify2').prop('checked') == false) {
      setting_email_notify2 = 0;
    }
    if($('#setting_email_notify3').prop('checked') == false) {
      setting_email_notify3 = 0;
    }
    if($('#setting_email_notify4').prop('checked') == false) {
      setting_email_notify4 = 0;
    }
    $.ajax({
      type: "post",
      url: "{{url('/api/set_notify')}}",
      data: {
        setting_email_notify1: setting_email_notify1,
        setting_email_notify2: setting_email_notify2,
        setting_email_notify3: setting_email_notify3,
        setting_email_notify4: setting_email_notify4,
        _token: '{{ csrf_token() }}'
      },
      dataType: "json",
      success: function (response) {

      }
    });
  });
});


  </script>
@endsection
@stop