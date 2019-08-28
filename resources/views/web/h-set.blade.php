@extends('web.base')
@section('content')
  @section('myStyle')
  <link rel="stylesheet" href="{{asset('/css/ekko-lightbox.css')}}">
  @endsection
  <!--內容開始 -->
  <!--內容結束 -->
  <div class="item-title"><a href="javascript:history.back();"><i class="fa fa-chevron-left" aria-hidden="true"></i> 回上頁</a></div>
  <div class="container">
    <div class=" offset-md-2 col-md-8 mb-5">
      <div class="pay-box fix">
        <div class="helper-box">
          <div class="h-face">
            <img src="{{URL::to('/') . '/avatar/small/' . $user->usr_photo}}">
          </div>
          <form action="" id="form-helper" method="post" style="width:100%">
            <div class="helper-info">
              <span class="user-name">{{$user->last_name . $user->first_name}}</span>
              <span class="start">
                  @if($user->customer_avg_rate == 0 || $user->customer_avg_rate == null)
                      @for($i = 1 ;$i<=5;$i++)
                        <i class="fa fa-star-o" aria-hidden="true"></i>
                      @endfor
                  @elseif($user->customer_avg_rate > 0)
                      @for($i =1 ;$i<=5;$i++)
                          @if(floor($user->customer_avg_rate) >= $i)
                            <i class="fa fa-star" aria-hidden="true"></i>
                          @else
                            <i class="fa fa-star-o" aria-hidden="true"></i>
                          @endif
                      @endfor
                  @endif
              </span>
              <span class="avg">{{is_null($user->customer_avg_rate)?'0':$user->customer_avg_rate}}</span>
              <div class="feedback">受雇次數：
                <span class="text-danger">{{is_null($user->total_served_case)?'0':$user->total_served_case}}</span>次
              </div>
              <div class="feedback">總工作時數：
                <span class="text-danger">{{is_null($user->total_served_hours)?'0':$user->total_served_hours}}</span>小時
              </div>
              <div class="form-group mt-2">
                <label><i class="fa fa-map-marker" aria-hidden="true"></i> 選取服務地址</label>
                <select class="form-control" id="address" name="address">
                  @foreach($member_addr_recode as $key => $value)
                  <option value="{{$value->id}}">{{$value->city . $value->nat . $value->addr}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label><i class="fa fa-user" aria-hidden="true"></i> 個人簡介</label>
                <textarea class="form-control" name="personal_brief" placeholder="請提供簡單的描述，包含自我介紹，學經歷，證照，專業背景，服務範圍，案例分享等等">{{$user->personal_brief}}</textarea>
              </div>
              <div class="showof">
                <label><i class="fa fa-folder-open-o" aria-hidden="true"></i> 公開好幫手檔案 </label>
                <p>公開檔案可以讓消費者搜尋到您的服務</p>
                <input class="toggle" type="checkbox" name="open_offer_setting" value="1" {{$user->open_offer_setting == '1' ?'checked':''}}>
              </div>
              <div class="helper-jobs">
                <div class="set-ser">
                  <i class="fa fa-check-square-o" aria-hidden="true"></i> 服務設定
                  <a href="#" class="ser-add"  data-toggle="modal" data-target="#exampleModal">新增服務項目 <i class="fa fa-plus-circle" aria-hidden="true"></i> </a>
                </div>
                @foreach($olo as $key => $value)
                <div class="ser-list">{{$value->offer_title}}
                  <a href="#" class="ser-editb" data-toggle="modal" @if($value->class_flag == 0) data-target='#editModal' @elseif($value->class_flag == 1) data-target='#editfood' @else data-target='#editdesign' @endif data-id="{{$value->id}}">編輯 <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                  <a href="#" class="ser-edit" data-id="{{$value->id}}" >刪除<i class="fa fa-times-circle" aria-hidden="true"></i></a>
                </div>
                @endforeach
              <div class="jobs-tit">服務評分 <i class="fa fa-star-o" aria-hidden="true"></i> </div>
              <div class="jobs-all"> 總體服務評分<span class="start-all">
                      @if($user->customer_avg_rate == 0 || $user->customer_avg_rate == null)
                          @for($i = 1 ;$i<=5;$i++)
                              <i class="fa fa-star-o" aria-hidden="true"></i>
                          @endfor
                      @elseif($user->customer_avg_rate > 0)
                          @for($i =1 ;$i<=5;$i++)
                              @if(floor($user->customer_avg_rate) >= $i)
                                  <i class="fa fa-star" aria-hidden="true"></i>
                              @else
                                  <i class="fa fa-star-o" aria-hidden="true"></i>
                              @endif
                          @endfor
                      @endif
                <span class="avg">{{is_null($user->customer_avg_rate)?'0':$user->customer_avg_rate}}</span></span>
              </div>
              <div class="start-box">
                <div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>
                </div>
                <div class="box-pros">
                  <div class="progress">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 95%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
                <div class="box-pres">95%</div>
              </div>
              <div class="start-box">
                <div class="box-satrt">
                  <i class="fa fa-star" aria-hidden="true"></i>
                  <i class="fa fa-star" aria-hidden="true"></i>
                  <i class="fa fa-star" aria-hidden="true"></i>
                  <i class="fa fa-star" aria-hidden="true"></i>
                </div>
                <div class="box-pros">
                  <div class="progress">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 5%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
                <div class="box-pres">5%</div>
              </div>
              <div class="start-box">
                <div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i></div>
                <div class="box-pros">
                  <div class="progress">
                <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                </div></div><div class="box-pres">0%</div>
              </div>
              <div class="start-box">
                <div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i></div>
                <div class="box-pros">
                  <div class="progress">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
                <div class="box-pres">0%</div>
                </div>
                <div class="start-box">
                  <div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i></div>
                  <div class="box-pros">
                    <div class="progress">
                      <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="box-pres">0%</div>
                </div>
                <a type="" class="btn btn-lg btn-success mt-4 float-right" id="btn-send">更新設定</a>
                <a id="preview" href="{{URL::to('/')}}/web/helper_detail/{{session()->get('usrID')}}/0/{{$member_addr_recode->first()->id}}" class="btn btn-lg btn-warning mt-4 mr-2 float-right">預覧</a>
              </div>
            </div>
          </form>
        </div>
        </div>
      </div>
  </div>

   <!-- Modal -->
   <!-- 新增服務 類別 -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">選取服務項目</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="" id="form-add-olo">
            <div class="form-group" id="toggle-select">
              <label for="exampleInputPassword1"><i class="fa fa-check-square-o" aria-hidden="true"></i> 服務類別</label>
              <select class="form-control" name="service_type_main">
                <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type1" selected="">居家服務</option>
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
              <input id="check-1" type="checkbox" class="service_type_sub" name="service_type_sub" value="美味家常菜">
              <label for="check-1">美味家常菜</label>
              <input id="check-2" type="checkbox" class="service_type_sub" name="service_type_sub" value="居家清掃">
              <label for="check-2">居家清掃</label>
              <input id="check-3" type="checkbox" class="service_type_sub" name="service_type_sub" value="水電工程">
              <label for="check-3">水電工程</label>
              <input id="check-4" type="checkbox" class="service_type_sub" name="service_type_sub" value="小孩讀伴玩">
              <label for="check-4">小孩讀伴玩</label>
              <input id="check-5" type="checkbox" class="service_type_sub" name="service_type_sub" value="銀髮族照護">
              <label for="check-5">銀髮族照護</label>
              <input id="check-6" type="checkbox" class="service_type_sub" name="service_type_sub" value="照顧">
              <label for="check-6">寵物美容/照顧</label>
              <input id="check-7" type="checkbox" class="service_type_sub" name="service_type_sub" value="汽車美容">
              <label for="check-7">洗車/汽車美容</label>
              <input id="check-8" type="checkbox" class="service_type_sub" name="service_type_sub" value="居家布置">
              <label for="check-8">居家布置</label>
              <input id="check-9" type="checkbox" class="service_type_sub" name="service_type_sub" value="花藝">
              <label for="check-9">花藝</label>
              <input id="check-10" type="checkbox" class="service_type_sub" name="service_type_sub" value="衣物送洗">
              <label for="check-10">衣物送洗</label>
              <input id="check-11" type="checkbox" class="service_type_sub" name="service_type_sub" value="管家服務">
              <label for="check-11">管家服務</label>
              <input id="check-12" type="checkbox" class="service_type_sub" name="service_type_sub" value="月子媽媽">
              <label for="check-12">月子媽媽</label>
              <input id="check-13" type="checkbox" class="service_type_sub" name="service_type_sub" value="其他">
              <label for="check-13">其他</label>

            </div>
            <div class="type-arr" id="type2">
              <input id="check-14" type="checkbox" class="service_type_sub" name="service_type_sub" value="美容">
              <label for="check-14">美容</label>
              <input id="check-15" type="checkbox" class="service_type_sub" name="service_type_sub" value="按摩">
              <label for="check-15">按摩</label>
              <input id="check-16" type="checkbox" class="service_type_sub" name="service_type_sub" value="美髮美甲美睫">
              <label for="check-16">美髮美甲美睫</label>
              <input id="check-17" type="checkbox" class="service_type_sub" name="service_type_sub" value="運動">
              <label for="check-17">運動</label>
              <input id="check-18" type="checkbox" class="service_type_sub" name="service_type_sub" value="瑜珈">
              <label for="check-18">瑜珈</label>
              <input id="check-19" type="checkbox" class="service_type_sub" name="service_type_sub" value="舞蹈">
              <label for="check-19">舞蹈</label>
              <input id="check-20" type="checkbox" class="service_type_sub" name="service_type_sub" value="游泳">
              <label for="check-20">游泳</label>
              <input id="check-21" type="checkbox" class="service_type_sub" name="service_type_sub" value="其他">
              <label for="check-21">其他</label>
            </div>
            <div class="type-arr" id="type3">
              <input id="check-22" type="checkbox" class="service_type_sub" name="service_type_sub" value="課業伴讀">
              <label for="check-22">課業伴讀</label>
              <input id="check-23" type="checkbox" class="service_type_sub" name="service_type_sub" value="語言學習">
              <label for="check-23">語言學習</label>
              <input id="check-24" type="checkbox" class="service_type_sub" name="service_type_sub" value="音樂教學">
              <label for="check-24">音樂教學</label>
              <input id="check-25" type="checkbox" class="service_type_sub" name="service_type_sub" value="攝影教學">
              <label for="check-25">攝影教學</label>
              <input id="check-26" type="checkbox" class="service_type_sub" name="service_type_sub" value="廚藝指導">
              <label for="check-26">廚藝指導</label>
              <input id="check-27" type="checkbox" class="service_type_sub" name="service_type_sub" value="繪畫教學">
              <label for="check-27">繪畫教學</label>
              <input id="check-28" type="checkbox" class="service_type_sub" name="service_type_sub" value="才藝培養">
              <label for="check-28">才藝培養</label>
              <input id="check-29" type="checkbox" class="service_type_sub" name="service_type_sub" value="電腦教學">
              <label for="check-29">電腦教學</label>
              <input id="check-30" type="checkbox" class="service_type_sub" name="service_type_sub" value="其他">
              <label for="check-30">其他</label>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉視窗</button>
          <button type="button" class="btn btn-success" id="btn-add-olo">送出選取</button>
        </div>
      </div>
    </div>
  </div>
  <!-- 一般服務編輯 -->
  <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"><span class="offer_title"></span>設定</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="" id="form-nomal" enctype="multipart/form-data">
            <input type="hidden" name="id" class="id" value="">
            <div class="form-group">
              <label>計價方式</label>
              <input id="case" type="radio" class="price_type case" name="price_type" value="每件">
              <label for="case">每件</label>
              <input id="hour" type="radio" class="price_type hour" name="price_type" value="小時">
              <label for="hour">每小時</label>
            </div>
            <div class="form-group">
              <label>服務價格</label>
              <input type="text" class="form-control price" name="price" placeholder="請輸入金額">
              <span>元</span>
            </div>
            <div class="form-group">
              <label> 服務簡介</label>
              <textarea class="form-control offer_description" name="offer_description"></textarea>
            </div>

            <div class="form-group">
              <label>最高學歷</label>
              <input type="text" class="form-control education" name="education" placeholder="">
            </div>

            <label>證照照片<span class="text-danger"> （上傳前請遮蓋個資）</span></label>
            <div class="row fix mb-2 olo_license_img"></div>

            <div class="mb-2">
              <img id="preview_license_img" src=""/>
              <input id="file-1" onchange="readURL(this,'preview_license_img');" type="file" name="license_img[]" class="form-control-file styled" data-file-upload="" multiple="">
            </div>

            <label for="file-1">作品照片</label>
            <div class="row fix mb-2 olo_img">
            </div>
            <div class="mt-2">
              <img id="preview_img" src=""/>
              <input id="file-1" onchange="readURL(this,'preview_img');" type="file" name="img[]" class="form-control-file styled" data-file-upload="" multiple="">
            </div>

            <div class="form-group yotube olo_video">
              <label class="mt-2">Yotube 影片</label><span class="add-more">新增更多影片 <i class="fa fa-plus-circle" aria-hidden="true"></i></span>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉視窗</button>
          <button type="button" class="btn btn-success" id="btn-send-nomal">儲存設定</button>
        </div>
      </div>
    </div>
  </div>

<!-- 家常菜、文創、二手編輯 -->
<div class="modal fade" id="editfood" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><span class="offer_title"></span>設定</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" id="form-food">
          <input type="hidden" name="id" class="id">
          <a href="#" class="add-menu">新增菜單 <i class="fa fa-plus-circle" aria-hidden="true"></i></a>
          <div id="menu-list">
          </div>
          <div class="form-group">
            <label> 服務簡介</label>
            <textarea class="form-control offer_description" name="offer_description"></textarea>
          </div>
          <label>證照照片<span class="text-danger"> （上傳前請遮蓋個資）</span></label>
          <div class="row fix mb-2 olo_license_img"> </div>
          <div class="mb-2">
            <img id="preview_menu_license_img" src=""/>
            <input id="file-1" onchange="readURL(this,'preview_menu_license_img')" type="file" name="license_img[]" class=" form-control-file" data-file-upload="" multiple>
          </div>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉視窗</button>
        <button type="button" class="btn btn-success" id="btn-send-food">儲存設定</button>
      </div>
    </div>
  </div>
</div>

<!-- 設計類-->
<div class="modal fade" id="editdesign" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><span class="offer_title"></span>設定</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" id="form-design">
          <input type="hidden" name="id" class="id">
          <div class="form-group">
            <label>計價方式</label>
            <input id='case' type="radio" name='price_type' value="每件" />
            <label for="case">每件</label>
            <input id='xhour' type="radio" name='price_type' value="小時" />
            <label for="xhour">每小時</label>
            <input id='quote' type="radio" name='price_type' value="報價" />
            <label for="quote">依報價</label>
          </div>
          <div class="form-group" id="price">
            <label>服務價格</label>
            <input type="text" class="form-control price" name="price" placeholder="請輸入金額">
            <span>元</span>
          </div>
          <div class="form-group">
            <label> 服務簡介</label>
            <textarea class="form-control offer_description" name="offer_description"></textarea>
          </div>
          <label>證照照片<span class="text-danger"> （上傳前請遮蓋個資）</span></label>
          <div class="row fix mb-2 olo_license_img"></div>
          <div class="mb-2">
              <img id="preview_design_license_img" src=""/>
            <input id="file-1" onchange="readURL(this,'preview_design_license_img');" type="file" name="license_img[]" class=" form-control-file" data-file-upload="" multiple>
          </div>
          <label for="file-1">作品照片</label>
          <div class="row fix mb-2 olo_img"> </div>
          <div class="mt-2">
              <img id="preview_design_img" src=""/>
            <input id="file-1" onchange="readURL(this,'preview_design_img');" type="file" name="img[]"  class="form-control-file" data-file-upload="" multiple>
          </div>
          <div class="form-group yotube">
            <label class="mt-2">Yotube 影片</label><span class="add-more">新增更多影片 <i class="fa fa-plus-circle" aria-hidden="true"></i></span>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉視窗</button>
        <button type="button" class="btn btn-success" id="btn-send-design">儲存設定</button>
      </div>
    </div>
  </div>
</div>

@section('myScript')
    <script src="{{asset('/js/ekko-lightbox.min.js')}}"></script>
    <script src="{{asset('/js/common.js')}}"></script>
  <script>
    $(function(){
      var food_index = 0;
      // 送出一般
      $('#btn-send-nomal').on('click', function () {
        var formdata = new FormData($('#form-nomal')[0]);
        formdata.append('_token', '{{csrf_token()}}');
        var old_video = [];
        var olo_video = [];
        $('#form-nomal .yt-video').each(function () {
          if($(this).data('id') != '0') {
            // old_video.push($(this).data('id'));
            formdata.append('old_video[]', $(this).data('id'));
          }
          formdata.append('olo_video_id[]', $(this).data('id'));
          // olo_video.push($(this).data('id'));
        })
        var old_license_img = [];
        var olo_license_img = [];
        $('#form-nomal .old_license_img').each(function () {
          if($(this).data('id') != '0') {
            // old_video.push($(this).data('id'));
            formdata.append('old_license_img[]', $(this).data('id'));
          }
          // formdata.append('olo_video_id[]', $(this).data('id'));
          // olo_video.push($(this).data('id'));
        })
        var old_img = [];
        var olo_img = [];
        $('#form-nomal .old_img').each(function () {
          if($(this).data('id') != '0') {
            // old_video.push($(this).data('id'));
            formdata.append('old_img[]', $(this).data('id'));
          }
          // formdata.append('olo_video_id[]', $(this).data('id'));
          // olo_video.push($(this).data('id'));
        })

        $.ajax({
          type: "post",
          url: "{{url('/api/set_olo')}}",
          data: formdata,
          processData: false,
          contentType: false,
          dataType: "json",
          success: function (response) {
            if(response.success) {
              alert('修改完成');
              location.reload();
            } else {
              alert(response.msg);
            }
          }
        });
      });
      // 送出家常菜
      $('#btn-send-food').on('click', function () {
        var formdata = new FormData($('#form-food')[0]);
        formdata.append('_token', '{{csrf_token()}}');
        var old_video = [];
        var olo_video = [];
        $('#form-food .yt-video').each(function () {
          if($(this).data('id') != '0') {
            // old_video.push($(this).data('id'));
            formdata.append('old_video[]', $(this).data('id'));
          }
          formdata.append('olo_video_id[]', $(this).data('id'));
          // olo_video.push($(this).data('id'));
        })
        var old_license_img = [];
        var olo_license_img = [];
        $('#form-food .old_license_img').each(function () {
          if($(this).data('id') != '0') {
            // old_video.push($(this).data('id'));
            formdata.append('old_license_img[]', $(this).data('id'));
          }
          // formdata.append('olo_video_id[]', $(this).data('id'));
          // olo_video.push($(this).data('id'));
        })
        var old_food = [];
        var olo_img = [];
        $('#form-food .old_food').each(function () {

          if($(this).data('id') != '0') {
            // old_video.push($(this).data('id'));
            formdata.append('old_food['+$(this).data('index')+']', $(this).data('id'));
          }
          formdata.append('olo_food_id['+$(this).data('index')+']', $(this).data('id'));
          if($('.food_title', this).val() == '') {
            alert('標題必填');
            return false;
          }
          if($('.food_price', this).val() == '') {
            alert('價格必填');
            return false;
          }
          if($('.food_img', this).get(0).files.length == 0 && $('.old_img', this).attr('src') == undefined ) {
            alert('照片必填');
            return false;
          }
        })

        $.ajax({
          type: "post",
          url: "{{url('/api/set_olo')}}",
          data: formdata,
          processData: false,
          contentType: false,
          dataType: "json",
          success: function (response) {
            if(response.success) {
              alert('修改完成');
              location.reload();
            } else {
              alert(response.msg);
            }
          }
        });
      });
      // 送出設計
      $('#btn-send-design').on('click', function () {
        var formdata = new FormData($('#form-design')[0]);
        formdata.append('_token', '{{csrf_token()}}');
        var old_video = [];
        var olo_video = [];
        $('#form-design .yt-video').each(function () {
          if($(this).data('id') != '0') {
            // old_video.push($(this).data('id'));
            formdata.append('old_video[]', $(this).data('id'));
          }
          formdata.append('olo_video_id[]', $(this).data('id'));
          // olo_video.push($(this).data('id'));
        })
        var old_license_img = [];
        var olo_license_img = [];
        $('#form-design .old_license_img').each(function () {
          if($(this).data('id') != '0') {
            // old_video.push($(this).data('id'));
            formdata.append('old_license_img[]', $(this).data('id'));
          }
          // formdata.append('olo_video_id[]', $(this).data('id'));
          // olo_video.push($(this).data('id'));
        })
        var old_img = [];
        var olo_img = [];
        $('#form-design .old_img').each(function () {
          if($(this).data('id') != '0') {
            // old_video.push($(this).data('id'));
            formdata.append('old_img[]', $(this).data('id'));
          }
          // formdata.append('olo_video_id[]', $(this).data('id'));
          // olo_video.push($(this).data('id'));
        })

        $.ajax({
          type: "post",
          url: "{{url('/api/set_olo')}}",
          data: formdata,
          processData: false,
          contentType: false,
          dataType: "json",
          success: function (response) {
            if(response.success) {
              alert('修改完成');
              location.reload();
            } else {
              alert(response.msg);
            }
          }
        });
      });
        // 刪除服務項目
        $(document).on("click", ".ser-edit" , function() {
            event.preventDefault();
            console.log("delete");
            $.ajax({
                type: "post",
                url: "{{url('/api/del_olo')}}",
                data: {
                    id: $(this).data('id'),
                    _token: '{{csrf_token()}}'
                },
                dataType: "json",
                success: function (response) {
                    if(response.success) {
                        alert('刪除成功');
                        location.reload();
                    } else {
                        alert(response.msg);
                    }
                }
            });
        });
      // 編輯服務項目

        $(document).on("click", ".ser-editb" , function() {
          console.log("ser-editb");
        food_index = 0
        $.ajax({
          type: "post",
          url: "{{url('/api/get_olo')}}",
          data: {
            id: $(this).data('id'),
            _token: '{{csrf_token()}}'
          },
          dataType: "json",
          success: function (response) {
            var olo = response.olo;
            // id
            $('.id').val(olo.id);
            $('.offer_title').text(olo.offer_title);
            // 計價方式
            if(olo.price_type == '每件') {
              $('.case').prop('checked', true);
            } else if(olo.price_type == '小時') {
              $('.hour').prop('checked', true);
            }
            // 服務價格
            $('.price').val(olo.price);
            // 服務簡介
            $('.offer_description').text(olo.offer_description);
            // 學歷
            $('.education').val(olo.education);
            // 證照
            $('.olo_license_img').empty()
            $.each(response.olo_license_img, function(k, v) {
              $('.olo_license_img').append('<div class="col-4 col-sm-3 p-2"><span class="b-close"><i class="fa fa-times-circle" aria-hidden="true"></i></span> <img src="{{URL::to("/")}}/license_img/small/'+v.img+'" class="img-fluid old_license_img" data-id="'+v.id+'"> </div>');
            })
            // 作品圖
            $('.olo_img').empty();
            $.each(response.olo_img, function (k, v) {
              $('.olo_img').append('<div class="col-4 col-sm-3 p-2"><span class="b-close"><i class="fa fa-times-circle" aria-hidden="true"></i></span> <img src="{{URL::to("/")}}/img/small/'+v.img+'" class="img-fluid old_img" data-id="'+v.id+'"> </div>');
            })
            // yt影片
            $('.video-add-box').remove();
            $.each(response.olo_video, function (k, v) {
              $('.olo_video').append('<div class="add-box video-add-box"> <div class="b-close"><i class="fa fa-times-circle" aria-hidden="true"></i></div> <input type="text" class="form-control mt-2 yt-video" name="olo_video[]" value="' + v.url + '" placeholder="輸入影片網址" data-id="'+v.id+'"> </div>');
            })
            // 菜單
            $('#menu-list').empty();
            $.each(response.olo_food, function (k, v) {
              $('#menu-list').append('<div class="row mb-2 old_food" data-id="'+v.id+'" data-index="'+food_index+'"> <div class="b-close"><i class="fa fa-times-circle" aria-hidden="true"></i></div> <div class="form-group col-8"> <input type="text" class="form-control food_title" name="food_title['+food_index+']" value="'+v.title+'" placeholder="料理名稱" required> </div> <div class="form-group col-4"> <input type="text" class="form-control food_price" name="food_price['+food_index+']" value="'+v.price+'" placeholder="售價" required> </div> <div class="col-3"><span class="b-close"><i class="fa fa-times-circle" aria-hidden="true"></i></span> <img src="{{URL::to("/")}}/img/small/'+v.img+'" class="img-fluid old_img"> </div> <div class="col-9 form-inline"><span class="text-success mr-2">料理照片 </span><span><img id="preview_menu_food_img'+food_index+'" src=""/><input type="file" onchange="readURL(this,\'preview_menu_food_img'+food_index+'\')" name="food_img['+food_index+']" class="form-control-file food_img" required></span></div> </div>')
              food_index++;
            })
          }
        });
      })
      // 新增服務項目子類別只能單選
      $('.service_type_sub').on('click', function () {
        var chk = 0;
        $('.service_type_sub').each(function () {
          if($(this).prop('checked')) {
            chk++;
          }
        })
        if(chk > 1) {
          alert('子類別只能選擇一個');
          return false;
        }
      })
      // 新增服務項目
      $('#btn-add-olo').on('click', function () {
        var formdata = new FormData($('#form-add-olo')[0]);
        formdata.append('address', $('#address').val());
        formdata.append('_token', '{{csrf_token()}}');
        $.ajax({
          type: "post",
          url: "{{url('/api/add_olo')}}",
          data: formdata,
          processData: false,
          contentType: false,
          dataType: "json",
          success: function (response) {
            var str = '';
            if(response.class_flag == 0) {
              str = '<div class="ser-list">' + response.offer_title + '<a href="#" class="ser-editb" data-toggle="modal" data-target="#editModal" data-id="'+ response.id +'">編輯 <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a href="#" class="ser-edit" data-id="'+ response.id +'" >刪除<i class="fa fa-times-circle" aria-hidden="true"></i></a> </div>';
            } else if(response.class_flag == 1) {
              str = '<div class="ser-list">' + response.offer_title + '<a href="#" class="ser-editb" data-toggle="modal" data-target="#editfood" data-id="'+ response.id +'">編輯 <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a href="#" class="ser-edit" data-id="'+ response.id +'" >刪除<i class="fa fa-times-circle" aria-hidden="true"></i></a> </div>';
            } else {
              str = '<div class="ser-list">' + response.offer_title + '<a href="#" class="ser-editb" data-toggle="modal" data-target="#editdesign" data-id="'+ response.id +'">編輯 <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a href="#" class="ser-edit" data-id="'+ response.id +'" >刪除<i class="fa fa-times-circle" aria-hidden="true"></i></a> </div>';
            }
            $('.set-ser').after(str);
            location.reload();
          }
        });
      })
      // 修改
      $('#btn-send').on('click', function (e) {
        e.preventDefault();
        var formdata = new FormData($('#form-helper')[0]);
        formdata.append('_token', '{{csrf_token()}}');
        $.ajax({
          type: "post",
          url: "{{url('/api/set_helper')}}",
          data: formdata,
          processData: false,
          contentType: false,
          dataType: "json",
          success: function (response) {
            alert(response.msg);
            location.reload();
          }
        });
      })

      $('.header,.search-frame').addClass('fixed') ;
      if ($(window).width() < 991) {$('.header,.search-frame').removeClass('fixed')  }

      $( ".add-more" ).on('click', function() {
        $('.yotube').append('<div class="add-box"><div class="b-close"><i class="fa fa-times-circle" aria-hidden="true"></i></div><input type="text" class="form-control mt-2 yt-video" name="olo_video[]" placeholder="輸入影片網址" data-id="0" /></div>')
      });
      $( ".add-menu" ).on('click', function() {
        $('#menu-list').append('<div class="row mb-2 old_food" data-id="0" data-index="'+food_index+'"><div class="b-close"><i class="fa fa-times-circle" aria-hidden="true"></i></div><div class="form-group col-8"><input type="text" class="form-control food_title" name="food_title['+food_index+']" placeholder="料理名稱" required></div><div class="form-group col-4"><input type="text" class="form-control food_price" name="food_price['+food_index+']" placeholder="售價" required></div><div class="col-9 form-inline"><span class="text-success mr-2">料理照片 </span><span><img id="preview_menu_food_img'+food_index+'" src=""/><input type="file" onchange="readURL(this,\'preview_menu_food_img'+food_index+'\')" class="form-control-file food_img" name="food_img['+food_index+']" required></span></div></div>');
        food_index++;
      });
    });

    $("#address").change(function () {
        $.ajax({
            type: "get",
            url: "{{url('/api/get_office_list')}}",
            data: {
                id: $(this).val()
            },
            dataType: "json",
            success: function (responses) {
                $('.ser-list').remove();
                responses.offerlist.forEach(function (response, index) {
                    if(response.class_flag == 0) {
                        str = '<div class="ser-list">' + response.offer_title + '<a href="#" class="ser-editb" data-toggle="modal" data-target="#editModal" data-id="'+ response.id +'">編輯 <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a href="#" class="ser-edit" data-id="'+ response.id +'" >刪除<i class="fa fa-times-circle" aria-hidden="true"></i></a> </div>';
                    } else if(response.class_flag == 1) {
                        str = '<div class="ser-list">' + response.offer_title + '<a href="#" class="ser-editb" data-toggle="modal" data-target="#editfood" data-id="'+ response.id +'">編輯 <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a href="#" class="ser-edit" data-id="'+ response.id +'" >刪除<i class="fa fa-times-circle" aria-hidden="true"></i></a> </div>';
                    } else {
                        str = '<div class="ser-list">' + response.offer_title + '<a href="#" class="ser-editb" data-toggle="modal" data-target="#editdesign" data-id="'+ response.id +'">編輯 <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a href="#" class="ser-edit" data-id="'+ response.id +'" >刪除<i class="fa fa-times-circle" aria-hidden="true"></i></a> </div>';
                    }
                    $('.set-ser').after(str);
                });
            }
        });
        $usrId= "{{session()->get('usrID')}}"
        $url = "{{URL::to('/')}}"
        $member_addr_id = $(this).val();
        $("#preview").attr("href",$url+"/web/helper_detail/"+$usrId+'/0/'+$member_addr_id);
    });

    $(function () {
      $("#toggle-select").change(function () {
        var $this = $(this).find(':selected');
        var $hideElements = $($this.attr("data-toggle-select-hide"));
        var $showElements = $($this.attr("data-toggle-select-show"));

        $hideElements.slideUp();
        $showElements.slideDown();

      });
    });


    $(document).on('click', '.b-close', function() {
        $(this).parent().remove();
    });
    $('#quote').on('change', function () {
      $('#price').hide();
    });
    $('#case').on('change', function () {
      $('#price').show();
    });

    function readURL(input,element) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#'+element).attr('src', e.target.result)
                    .width(120).height(100);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
  </script>

<!-- <script>
  var filechooser = document.getElementById("choose");

  //    用于压缩图片的canvas
  var canvas = document.createElement("canvas");
  var ctx = canvas.getContext('2d');

  //    瓦片canvas
  var tCanvas = document.createElement("canvas");
  var tctx = tCanvas.getContext("2d");

  var maxsize = 100 * 1024;

  $("#upload").on("click", function() {
        filechooser.click();
      })
      .on("touchstart", function() {
        $(this).addClass("touch")
      })
      .on("touchend", function() {
        $(this).removeClass("touch")
      });

  filechooser.onchange = function() {
    if (!this.files.length) return;

    var files = Array.prototype.slice.call(this.files);

    if (files.length > 9) {
      alert("最多同时只可上传9张图片");
      return;
    }

    files.forEach(function(file, i) {
      if (!/\/(?:jpeg|png|gif)/i.test(file.type)) return;

      var reader = new FileReader();

      var li = document.createElement("li");

  //          获取图片大小
        var size = file.size / 1024 > 1024 ? (~~(10 * file.size / 1024 / 1024)) / 10 + "MB" : ~~(file.size / 1024) + "KB";
        li.innerHTML = '<div class="progress"><span></span></div><div class="size">' + size + '</div>';
        $(".img-list").append($(li));

        reader.onload = function() {
          var result = this.result;
          var img = new Image();
          img.src = result;

          $(li).css("background-image", "url(" + result + ")");

          //如果图片大小小于100kb，则直接上传
          if (result.length <= maxsize) {
            img = null;

            upload(result, file.type, $(li));

            return;
          }

  //      图片加载完毕之后进行压缩，然后上传
        if (img.complete) {
          callback();
        } else {
          img.onload = callback;
        }

        function callback() {
          var data = compress(img);

          upload(data, file.type, $(li));

          img = null;
        }

      };

      reader.readAsDataURL(file);
    })
  };

  //    使用canvas对大图片进行压缩
  function compress(img) {
    var initSize = img.src.length;
    var width = img.width;
    var height = img.height;

    //如果图片大于四百万像素，计算压缩比并将大小压至400万以下
    var ratio;
    if ((ratio = width * height / 4000000) > 1) {
      ratio = Math.sqrt(ratio);
      width /= ratio;
      height /= ratio;
    } else {
      ratio = 1;
    }

    canvas.width = width;
    canvas.height = height;

  //        铺底色
    ctx.fillStyle = "#fff";
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    //如果图片像素大于100万则使用瓦片绘制
    var count;
    if ((count = width * height / 1000000) > 1) {
      count = ~~(Math.sqrt(count) + 1); //计算要分成多少块瓦片

  //            计算每块瓦片的宽和高
      var nw = ~~(width / count);
      var nh = ~~(height / count);

      tCanvas.width = nw;
      tCanvas.height = nh;

      for (var i = 0; i < count; i++) {
        for (var j = 0; j < count; j++) {
          tctx.drawImage(img, i * nw * ratio, j * nh * ratio, nw * ratio, nh * ratio, 0, 0, nw, nh);

          ctx.drawImage(tCanvas, i * nw, j * nh, nw, nh);
        }
      }
    } else {
      ctx.drawImage(img, 0, 0, width, height);
    }

    //进行最小压缩
    var ndata = canvas.toDataURL('image/jpeg', 0.1);

    console.log('压缩前：' + initSize);
    console.log('压缩后：' + ndata.length);
    console.log('压缩率：' + ~~(100 * (initSize - ndata.length) / initSize) + "%");

    tCanvas.width = tCanvas.height = canvas.width = canvas.height = 0;

    return ndata;
  }

  //    图片上传，将base64的图片转成二进制对象，塞进formdata上传
  function upload(basestr, type, $li) {
    var text = window.atob(basestr.split(",")[1]);
    var buffer = new Uint8Array(text.length);
    var pecent = 0, loop = null;

    for (var i = 0; i < text.length; i++) {
      buffer[i] = text.charCodeAt(i);
    }

    var blob = getBlob([buffer], type);

    var xhr = new XMLHttpRequest();

    var formdata = getFormData();

    formdata.append('imagefile', blob);

    // xhr.open('post', '/cupload');

    // xhr.onreadystatechange = function() {
    //   if (xhr.readyState == 4 && xhr.status == 200) {
    //     var jsonData = JSON.parse(xhr.responseText);
    //     var imagedata = jsonData[0] || {};
    //     var text = imagedata.path ? '上传成功' : '上传失败';

    //     console.log(text + '：' + imagedata.path);

    //     clearInterval(loop);

    //     //当收到该消息时上传完毕
    //     $li.find(".progress span").animate({'width': "100%"}, pecent < 95 ? 200 : 0, function() {
    //       $(this).html(text);
    //     });

    //     if (!imagedata.path) return;

    //     $(".pic-list").append('<a href="' + imagedata.path + '">' + imagedata.name + '（' + imagedata.size + '）<img src="' + imagedata.path + '" /></a>');
    //   }
    // };

    // //数据发送进度，前50%展示该进度
    // xhr.upload.addEventListener('progress', function(e) {
    //   if (loop) return;

    //   pecent = ~~(100 * e.loaded / e.total) / 2;
    //   $li.find(".progress span").css('width', pecent + "%");

    //   if (pecent == 50) {
    //     mockProgress();
    //   }
    // }, false);

    //数据后50%用模拟进度
    function mockProgress() {
      if (loop) return;

      loop = setInterval(function() {
        pecent++;
        $li.find(".progress span").css('width', pecent + "%");

        if (pecent == 99) {
          clearInterval(loop);
        }
      }, 100)
    }

    // xhr.send(formdata);
  }

  /**
   * 获取blob对象的兼容性写法
   * @param buffer
   * @param format
   * @returns {*}
   */
  function getBlob(buffer, format) {
    try {
      return new Blob(buffer, {type: format});
    } catch (e) {
      var bb = new (window.BlobBuilder || window.WebKitBlobBuilder || window.MSBlobBuilder);
      buffer.forEach(function(buf) {
        bb.append(buf);
      });
      return bb.getBlob(format);
    }
  }

  /**
   * 获取formdata
   */
  function getFormData() {
    var isNeedShim = ~navigator.userAgent.indexOf('Android')
        && ~navigator.vendor.indexOf('Google')
        && !~navigator.userAgent.indexOf('Chrome')
        && navigator.userAgent.match(/AppleWebKit\/(\d+)/).pop() <= 534;

    return isNeedShim ? new FormDataShim() : new FormData()
  }

  /**
   * formdata 补丁, 给不支持formdata上传blob的android机打补丁
   * @constructor
   */
  function FormDataShim() {
    console.warn('using formdata shim');

    var o = this,
        parts = [],
        boundary = Array(21).join('-') + (+new Date() * (1e16 * Math.random())).toString(36),
        oldSend = XMLHttpRequest.prototype.send;

    this.append = function(name, value, filename) {
      parts.push('--' + boundary + '\r\nContent-Disposition: form-data; name="' + name + '"');

      if (value instanceof Blob) {
        parts.push('; filename="' + (filename || 'blob') + '"\r\nContent-Type: ' + value.type + '\r\n\r\n');
        parts.push(value);
      }
      else {
        parts.push('\r\n\r\n' + value);
      }
      parts.push('\r\n');
    };

    // Override XHR send()
    XMLHttpRequest.prototype.send = function(val) {
      var fr,
          data,
          oXHR = this;

      if (val === o) {
        // Append the final boundary string
        parts.push('--' + boundary + '--\r\n');

        // Create the blob
        data = getBlob(parts);

        // Set up and read the blob into an array to be sent
        fr = new FileReader();
        fr.onload = function() {
          oldSend.call(oXHR, fr.result);
        };
        fr.onerror = function(err) {
          throw err;
        };
        fr.readAsArrayBuffer(data);

        // Set the multipart content type and boudary
        this.setRequestHeader('Content-Type', 'multipart/form-data; boundary=' + boundary);
        XMLHttpRequest.prototype.send = oldSend;
      }
      else {
        oldSend.call(this, val);
      }
    };
  }
</script> -->
  @endsection
  @stop
