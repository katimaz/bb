@extends('web.base')
@section('content')
  <!--內容開始 -->

  <!--內容結束 -->
   <div class="item-title">收付款設定</div>
  <div class="container">
    <div class=" offset-md-2 col-md-8">

      <div class="pay-box">
   <div class="form-group bankpay">
      <input id="account" type="radio" name="group-1" checked="checked" data-toggle-select-hide=".change-bank" data-toggle-select-show="#banks">
      <label for="account">收款設定</label>
      <input id="payment" type="radio" name="group-1" data-toggle-select-hide=".change-bank" data-toggle-select-show="#credit">
      <label for="payment">付款設定</label>
      </div>
      <form id="banks" class="change-bank active">
        <div class="form-group row">
          <label  class="col-sm-2 col-form-label">收款銀行</label>
          <div class="col-sm-10">
          <select id="bank_id" class="form-control">
              <option value="004" data-title="臺灣銀行">004 – 臺灣銀行</option>
              <option value="005" data-title="土地銀行">005 – 土地銀行</option>
              <option value="006" data-title="合作商銀">006 – 合作商銀</option>
              <option value="007" data-title="第一銀行">007 – 第一銀行</option>
              <option value="008" data-title="華南銀行">008 – 華南銀行</option>
              <option value="009" data-title="彰化銀行">009 – 彰化銀行</option>
              <option value="011" data-title="上海商業儲蓄銀行">011 – 上海商業儲蓄銀行</option>
              <option value="012" data-title="台北富邦銀行">012 – 台北富邦銀行</option>
              <option value="013" data-title="國泰世華銀行">013 – 國泰世華銀行</option>
              <option value="016" data-title="高雄銀行">016 – 高雄銀行</option>
              <option value="017" data-title="兆豐國際商業銀行">017 – 兆豐國際商業銀行</option>
              <option value="018" data-title="農業金庫">018 – 農業金庫</option>
              <option value="021" data-title="花旗(台灣)商業銀行">021 – 花旗(台灣)商業銀行</option>
              <option value="025" data-title="首都銀行">025 – 首都銀行</option>
              <option value="039" data-title="澳商澳盛銀行">039 – 澳商澳盛銀行</option>
              <option value="040" data-title="中華開發工業銀行">040 – 中華開發工業銀行</option>
              <option value="050" data-title="臺灣企銀">050 – 臺灣企銀</option>
              <option value="052" data-title="渣打國際商業銀行">052 – 渣打國際商業銀行</option>
              <option value="053" data-title="台中商業銀行">053 – 台中商業銀行</option>
              <option value="054" data-title="京城商業銀行">054 – 京城商業銀行</option>
              <option value="072" data-title="德意志銀行">072 – 德意志銀行</option>
              <option value="075" data-title="東亞銀行">075 – 東亞銀行</option>
              <option value="081" data-title="匯豐(台灣)商業銀行">081 – 匯豐(台灣)商業銀行</option>
              <option value="085" data-title="新加坡商新加坡華僑銀行">085 – 新加坡商新加坡華僑銀行</option>
              <option value="101" data-title="大台北銀行">101 – 大台北銀行</option>
              <option value="102" data-title="華泰銀行">102 – 華泰銀行</option>
              <option value="103" data-title="臺灣新光商銀">103 – 臺灣新光商銀</option>
              <option value="104" data-title="台北五信">104 – 台北五信</option>
              <option value="106" data-title="台北九信">106 – 台北九信</option>
              <option value="108" data-title="陽信商業銀行">108 – 陽信商業銀行</option>
              <option value="114" data-title="基隆一信">114 – 基隆一信</option>
              <option value="115" data-title="基隆二信">115 – 基隆二信</option>
              <option value="118" data-title="板信商業銀行">118 – 板信商業銀行</option>
              <option value="119" data-title="淡水一信">119 – 淡水一信</option>
              <option value="120" data-title="淡水信合社">120 – 淡水信合社</option>
              <option value="124" data-title="宜蘭信合社">124 – 宜蘭信合社</option>
              <option value="127" data-title="桃園信合社">127 – 桃園信合社</option>
              <option value="130" data-title="新竹一信">130 – 新竹一信</option>
              <option value="132" data-title="新竹三信">132 – 新竹三信</option>
              <option value="146" data-title="台中二信">146 – 台中二信</option>
              <option value="147" data-title="三信商業銀行">147 – 三信商業銀行</option>
              <option value="158" data-title="彰化一信">158 – 彰化一信</option>
              <option value="161" data-title="彰化五信">161 – 彰化五信</option>
              <option value="162" data-title="彰化六信">162 – 彰化六信</option>
              <option value="163" data-title="彰化十信">163 – 彰化十信</option>
              <option value="165" data-title="鹿港信合社">165 – 鹿港信合社</option>
              <option value="178" data-title="嘉義三信">178 – 嘉義三信</option>
              <option value="179" data-title="嘉義四信">179 – 嘉義四信</option>
              <option value="188" data-title="台南三信">188 – 台南三信</option>
              <option value="204" data-title="高雄三信">204 – 高雄三信</option>
              <option value="215" data-title="花蓮一信">215 – 花蓮一信</option>
              <option value="216" data-title="花蓮二信">216 – 花蓮二信</option>
              <option value="222" data-title="澎湖一信">222 – 澎湖一信</option>
              <option value="223" data-title="澎湖二信">223 – 澎湖二信</option>
              <option value="224" data-title="金門信合社">224 – 金門信合社</option>
              <option value="512" data-title="雲林區漁會">512 – 雲林區漁會</option>
              <option value="515" data-title="嘉義區漁會">515 – 嘉義區漁會</option>
              <option value="517" data-title="南市區漁會">517 – 南市區漁會</option>
              <option value="518" data-title="南縣區漁會">518 – 南縣區漁會</option>
              <option value="520" data-title="小港區漁會；高雄區漁會">520 – 小港區漁會；高雄區漁會</option>
              <option value="521" data-title="彌陀區漁會；永安區漁會；興達港區漁會；林園區漁會">521 – 彌陀區漁會；永安區漁會；興達港區漁會；林園區漁會</option>
              <option value="523" data-title="東港漁會；琉球區漁會；林邊區漁會">523 – 東港漁會；琉球區漁會；林邊區漁會</option>
              <option value="524" data-title="新港區漁會">524 – 新港區漁會</option>
              <option value="525" data-title="澎湖區漁會">525 – 澎湖區漁會</option>
              <option value="605" data-title="高雄市農會">605 – 高雄市農會</option>
              <option value="612" data-title="豐原市農會；神岡鄉農會">612 – 豐原市農會；神岡鄉農會</option>
              <option value="613" data-title="名間農會">613 – 名間農會</option>
              <option value="614" data-title="彰化地區農會">614 – 彰化地區農會</option>
              <option value="616" data-title="雲林地區農會">616 – 雲林地區農會</option>
              <option value="617" data-title="嘉義地區農會">617 – 嘉義地區農會</option>
              <option value="618" data-title="台南地區農會">618 – 台南地區農會</option>
              <option value="619" data-title="高雄地區農會">619 – 高雄地區農會</option>
              <option value="620" data-title="屏東地區農會">620 – 屏東地區農會</option>
              <option value="621" data-title="花蓮地區農會">621 – 花蓮地區農會</option>
              <option value="622" data-title="台東地區農會">622 – 台東地區農會</option>
              <option value="624" data-title="澎湖農會">624 – 澎湖農會</option>
              <option value="625" data-title="台中市農會">625 – 台中市農會</option>
              <option value="627" data-title="連江縣農會">627 – 連江縣農會</option>
              <option value="700" data-title="中華郵政">700 – 中華郵政</option>
              <option value="803" data-title="聯邦商業銀行">803 – 聯邦商業銀行</option>
              <option value="805" data-title="遠東銀行">805 – 遠東銀行</option>
              <option value="806" data-title="元大銀行">806 – 元大銀行</option>
              <option value="807" data-title="永豐銀行">807 – 永豐銀行</option>
              <option value="808" data-title="玉山銀行">808 – 玉山銀行</option>
              <option value="809" data-title="萬泰銀行">809 – 萬泰銀行</option>
              <option value="810" data-title="星展銀行">810 – 星展銀行</option>
              <option value="812" data-title="台新銀行">812 – 台新銀行</option>
              <option value="814" data-title="大眾銀行">814 – 大眾銀行</option>
              <option value="815" data-title="日盛銀行">815 – 日盛銀行</option>
              <option value="816" data-title="安泰銀行">816 – 安泰銀行</option>
              <option value="822" data-title="中國信託">822 – 中國信託</option>
              <option value="901" data-title="大里市農會">901 – 大里市農會</option>
              <option value="903" data-title="汐止農會">903 – 汐止農會</option>
              <option value="904" data-title="新莊農會">904 – 新莊農會</option>
              <option value="910" data-title="財團法人農漁會聯合資訊中心">910 – 財團法人農漁會聯合資訊中心</option>
              <option value="912" data-title="冬山農會">912 – 冬山農會</option>
              <option value="916" data-title="草屯農會">916 – 草屯農會</option>
              <option value="922" data-title="台南市農會">922 – 台南市農會</option>
              <option value="928" data-title="板橋農會">928 – 板橋農會</option>
              <option value="951" data-title="北農中心">951 – 北農中心</option>
              <option value="954" data-title="中南部地區農漁會">954 – 中南部地區農漁會</option>
            </select>
          </div>

        </div>
        <div class="form-group row">
          <label  class="col-sm-2 col-form-label">帳號</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="account_no" value="" placeholder="請勿輸入連結號或空格" required>
          </div>
        </div>
        <div class="form-group row">
          <label  class="col-sm-2 col-form-label">戶名</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="account_name" value="" maxlength="10" required>
          </div>
        </div>
        <div class="form-group row">
          <div class="offset-sm-2 col-sm-10">
            <button type="submit" class="btn btn-lg btn-success" id="btn-add-bank">新增銀行</button>
          </div>
        </div>
        <div class="form-group row">
          <label  class="col-sm-2 col-form-label">己儲存銀行</label>
          @foreach($collection as $value)
          <div class="col-sm-10">
            <div class="card-list">
              <div class="numbers">{{$value->bank_title}}　{{$value->account_no}}</div>
              <div class="dates">己驗證</div>
              <div class="c-delete" data-id="{{$value->id}}"><i class="fa fa-times-circle-o" aria-hidden="true"></i></div></div>
          </div>
          @endforeach
        </div>
      </form>

<div id="credit" class="change-bank" >

  <div class="form-group row">
    <label  class="col-sm-2 col-form-label">卡號</label>
    <div class="col-sm-10 card-number">
  <input class="form-control" maxlength="19" name="credit-number" pattern="\d*" placeholder="Card Number" type="tel" />
    </div>
  </div>
  <div class="form-group row">
    <label  class="col-sm-2 col-form-label">使用期限</label>
    <div class="col-sm-10">
     <input class="form-control" maxlength="7" name="credit-expires" pattern="\d*" placeholder="MM / YY" type="tel" />
    </div>
  </div>
  <div class="form-group row">
    <label  class="col-sm-2 col-form-label">末三碼</label>
    <div class="col-sm-10">
     <input class="form-control" maxlength="4" name="credit-cvc" pattern="\d*" placeholder="CVC" type="tel" />
    </div>
  </div>
  <div class="form-group row">
    <div class="offset-sm-2 col-sm-10">
       <button type="submit" class="btn btn-lg btn-success">新增信用卡</button>
    </div>
  </div>
   <div class="form-group row">
    <label  class="col-sm-2 col-form-label">己儲存卡號</label>
    <div class="col-sm-10"><div class="card-list"><div class="numbers">47051123****0076</div>     <div class="dates">08/2022</div><div class="c-delete"><i class="fa fa-times-circle-o" aria-hidden="true"></i></div></div>
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

    $('#btn-add-bank').on('click', function (e) {
      e.preventDefault();
      $.ajax({
        type: "post",
        url: "{{url('/api/set_bank')}}",
        data: {
          bank_id: $('#bank_id').val(),
          bank_title: $('#bank_id option:selected').data('title'),
          account_name: $("#account_name").val(),
          account_no: $('#account_no').val(),
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
        url: "{{url('/api/del_bank')}}",
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