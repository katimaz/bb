<!--頁腳開始 -->
@if(strpos(basename(url()->current(),".php"),'map')===false)
<footer>
    <a href="javascript:void(0)" class="top-bt"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>
    <div class="footer">
        <div class="f-logo"><img src="{{asset("/images/logo-b.svg")}}" class="logob"><br><a href="#"
                target="_blank"><img src="{{asset("/images/fb.png")}}"></a><a href="#" target="_blank"><img
                    src="{{asset("/images/line.png")}}"></a><a href="#" target="_blank"><img
                    src="{{asset("/images/yotube.png")}}"></a></div>
        <div class="f-info">
            <div class="f-link"><a href="map.html">開始預約</a> | <a href="service.html">客服中心</a> | <a
                    href="FAQ.html">常見問題</a> | <a href="/term_of_use">使用條款</a> | <a href="about.html"> 關於我們</a>
            </div>
            <div class="f-copy">服務專線：02-22741167　Ｅ-mail：service.bb.com.tw<br>
                地址：新竹市北區四維路130號4F-2 (0511)<br>
                Copyright (c) BounBang.com, Inc. All Rights Reserved. </div>
        </div>
    </div>
</footer>
<button type="button" id="alertBtn" data-toggle="modal" data-target="#errorAlertModal" style="display:none;" /></button>
<div class="modal fade" id="errorAlertModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h5 id="alert_title" class="modal-title"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div id="alert_body" class="modal-body"></div>
        <div class="modal-footer">
           <button type="button" class="btn btn-success" data-dismiss="modal">關閉視窗</button>
        </div>
    </div>
  </div>
</div>
@endif
<!--頁腳結束 -->>
