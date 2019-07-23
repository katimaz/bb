<!--頭部開始 -->
<div class="se-pre-con"></div>
<div class="btbg"></div>
<div class="userbg"></div>
<div class="search-frame">
    <input name="" type="search">
    <div class="search-bt">
        <img src="{{asset("/images/bsearch-bt.png")}}" class="d-none d-sm-block">
        <img src="{{asset("/images/search-bt.png")}}" class="show-xs"></div>
</div>
<div class="s-close">
    <img src="{{asset("/images/close.png")}}" width="19" height="20">
</div>
</div>
<div class="header {{((isset($home) && $home==1)?'':'fixed')}}">
    <a class="brand" href="/"><img src="{{asset("/images/logo.svg")}}"></a>
    <!--  登入後隱藏 START-->
    @if(!session()->has('usrID'))
    <div class="login-arr">
        <a href="<?=URL::to('/')?>/login">登入 / 註冊</a>
    </div>
    @else
    <!--登入後顥示選單-->
    <div class="button_container">
        <span class="top"></span>
        <span class="middle"></span>
        <span class="bottom"></span>
    </div>
    <div class="main-menu">
        <ul class="menu-ul">
            <li>
                <a href="reservation.html">我的需求</a>
            </li>
            <li>
                <a href="order.html">訂單狀態</a>
            </li>
            <li class="submenu" id="subA">
                <a href="javascript:void(0)">
                    我的帳務<i class="fa fa-angle-down" aria-hidden="true"></i>
                </a>
                <div class="sub-list" id="boxA">
                    <a class="sub-item" href="feedback.html">回饋金</a>
                    <a class="sub-item" href="income.html">服務收入</a>
                    <a class="sub-item" href="pay.html">支出</a>
                </div>
            </li>
            <li class="submenu" id="subB">
                <a href="javascript:void(0)">
                    推薦賺回饋<i class="fa fa-angle-down" aria-hidden="true"></i>
                </a>
                <div class="sub-list" id="boxB">
                    <a class="sub-item" href="recommend.html">推薦連結</a>
                    <a class="sub-item" href="promote.html">介紹幫棒給新夥伴</a>
                    <a class="sub-item" href="partners.html">我的夥伴團隊</a>
                    <a class="sub-item" href="total.html">我的夥伴團隊總營收</a>
                </div>
            </li>
            <li>
                <a href="best-choice.html">我的首選</a>
            </li>
            <li>
                <a href="calendar.html">我的行事曆</a>
            </li>
        </ul>
    </div>
    <div class="user">
        <a href="javascript:void(0)" class="user-icon">
            <img src="<?=URL::to('/')?>/images/user.png">
        </a>
        <div class="user-box">
            <div class="user-info">
                <div class="user-face"><img src="{{asset("/images/5b7d4694c0ab9850731325.jpg")}}"></div>
                <div class="user-score"><span class="user-name">吳大偉</span><span class="start"><i class="fa fa-star"
                            aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star"
                            aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star"
                            aria-hidden="true"></i> </span><span class="avg">4.9</span>
                    <!--<div class="income">年度收入 <span class="text-danger">$50,100</span></div> -->
                    <div class="feedback">年度回饋金 <span class="text-danger">$2,300</span></div>
                </div>
            </div>
            <div class="user-item" id="base">
                <a href="javascript:void(0)">基本設定<i class="fa fa-angle-down" aria-hidden="true"></i></a>
                <div class="user-sub">
                    <a class="sub-item" href="{{url('/web/profile')}}">個人資訊</a>
                    <a class="sub-item" href="{{url('/web/certification')}}">身份認證</a>
                    <a class="sub-item" href="{{url('/web/collection_info')}}">收付款設定</a>
                    <a class="sub-item" href="{{url('/web/einvoice_info')}}">發票設定</a>
                    <a class="sub-item" href="{{url('/web/set_notify')}}">通知設定</a>
                </div>
            </div>
            <a class="user-item" href="coupon.html">我的優惠劵</a>
            <a class="user-item" href="javascript:void(0)" id="head-change">切換成幫手</a>
            <a class="user-item" href="contact.html">聯絡幫棒</a>
            <a class="user-item" href="{{url('/web/logout')}}">登出</a>
        </div>
    </div>
    @endif
</div>
<!--頭部結束 -->
