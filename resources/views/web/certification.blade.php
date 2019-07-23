@extends('web.base')
@section('content')
<!--內容開始 -->

  <!--內容結束 -->
<div class="item-title">身份認證</div>
  <div class="container">
   <div class=" offset-md-2 col-md-8 mb-5">
    <div class="alert alert-warning" role="alert">
      <strong>注意！</strong> 身分證僅供幫棒做身份認證用，您提供的資料將不會顯示於任何頁面。
    </div>
    <div class="pay-box">
      <form class="change-bank active" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-group row">
          <label  class="col-sm-2 col-form-label">姓名</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="id_name" id="" value="{{$user->id_name}}" placeholder="" required>
          </div>
        </div>
        <div class="form-group row">
          <label  class="col-sm-2 col-form-label">身份證字號</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="id_number" id="" value="{{$user->id_number}}" maxlength="10" required>
          </div>
        </div>
        <div class="form-group row">
          <label  class="col-sm-2 col-form-label">發證日期</label>
          <div class="col-4 col-sm-3">
            <select name="id_year" class="form-control" data-selected="{{$user->id_year}}">
              <option value="94">94年</option>
              <option value="95">95年</option>
              <option value="96">96年</option>
              <option value="97">97年</option>
              <option value="98">98年</option>
              <option value="99">99年</option>
              <option value="100">100年</option>
              <option value="101">101年</option>
              <option value="102">102年</option>
              <option value="103">103年</option>
              <option value="104">104年</option>
              <option value="105">105年</option>
              <option value="106">106年</option>
              <option value="107">107年</option>
              <option value="108">108年</option>
            </select>
          </div>
          <div class="col-4 col-sm-3">
            <select name="id_month"  class="form-control" data-selected="{{$user->id_month}}">
              <option value="1">1月</option>
              <option value="2">2月</option>
              <option value="3">3月</option>
              <option value="4">4月</option>
              <option value="5">5月</option>
              <option value="6">6月</option>
              <option value="7">7月</option>
              <option value="8">8月</option>
              <option value="9">9月</option>
              <option value="10">10月</option>
              <option value="11">11月</option>
              <option value="12">12月</option>
            </select>
          </div>
          <div class="col-4 col-sm-3">
            <select name="id_day"  class="form-control" data-selected="{{$user->id_day}}">
              <option value="1">1日</option>
              <option value="2">2日</option>
              <option value="3">3日</option>
              <option value="4">4日</option>
              <option value="5">5日</option>
              <option value="6">6日</option>
              <option value="7">7日</option>
              <option value="8">8日</option>
              <option value="9">9日</option>
              <option value="10">10日</option>
              <option value="11">11日</option>
              <option value="12">12日</option>
              <option value="13">13日</option>
              <option value="14">14日</option>
              <option value="15">15日</option>
              <option value="16">16日</option>
              <option value="17">17日</option>
              <option value="18">18日</option>
              <option value="19">19日</option>
              <option value="20">20日</option>
              <option value="21">21日</option>
              <option value="22">22日</option>
              <option value="23">23日</option>
              <option value="24">24日</option>
              <option value="25">25日</option>
              <option value="26">26日</option>
              <option value="27">27日</option>
              <option value="28">28日</option>
              <option value="29">29日</option>
              <option value="30">30日</option>
              <option value="31">31日</option>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label  class="col-sm-2 col-form-label">領補換類別</label>
          <div class="col-8 col-sm-8">
            <select name="id_type" class="form-control" data-selected="{{$user->id_type}}">
              <option value="0">請選擇</option>
              <option value="1">初發</option>
              <option value="2">補發</option>
              <option value="3">換發</option>
            </select>
          </div>
          <div class="col-4 col-sm-2 pt-1">
            <a href="#" data-toggle="modal" data-target="#exampleModal">輸入說明</a>
          </div>
        </div>
        <div class=" offset-sm-2 col-sm-10 p-1">
          <input id="file-1" type="file" name="id_photo" class="file-upload" data-file-upload="">
          <label for="file-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17">
              <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path>
            </svg>
            <span class="input-file-label">身份證正面</span>
          </label>
          <input id="file-2" type="file" name="id_photo2" class="file-upload" data-file-upload="">
          <label for="file-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17">
              <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path>
            </svg>
            <span class="input-file-label">身份證背面</span>
          </label>
        </div>
        <div class=" offset-sm-2 col-sm-10 serd">
          <input class="form-check-input fix" type="checkbox"  id="ig" required>
          <label class="form-check-label" for="ig">我已閱讀完畢並同意</label>
          <a href="#" data-toggle="modal" data-target=".bd-example-modal-lg">(好幫手條款)</a>．
        </div>
        <hr>
        <button type="submit" class="btn btn-success float-right">送出驗證</button>
      </form>
    </div>
  </div>
</div>


<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">好幫手條款 </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        條約內容....
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉視窗</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">身份證填寫說明</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
     <img src="{{asset('/images/ID.png')}}" width="100%"> </div>
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
    <script src="js/ekko-lightbox.min.js"></script>
    <script src="js/main.js"></script> -->
  @section('myScript')
  <script>
    $(function(){
      $('.header,.search-frame').addClass('fixed') ;
      if ($(window).width() < 991) {$('.header,.search-frame').removeClass('fixed')  }
    });
    $(function(){
        $('.bankpay input').on('change', function()  {
          var $this = $('.bankpay input:checked')
          var $hideElements = $($this.attr("data-toggle-select-hide"));
          var $showElements = $($this.attr("data-toggle-select-show"));

          $hideElements.slideUp();
          $showElements.slideDown();

      });

      $("select").each(function(){
          if($(this).data('selected')){
              $("option[value='"+$(this).data('selected')+"']",this).attr('selected','selected');
          }
      });
    });
  </script>
  @endsection
  @stop