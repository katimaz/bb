@extends('web.base')
@section('content')
  <!--內容開始 -->

  <!--內容結束 -->
  <div class="item-title">
    <a href="javascript:history.back();">
      <i class="fa fa-chevron-left" aria-hidden="true"></i> 回上頁
    </a>
  </div>
  <div class="container">
    <div class=" offset-md-2 col-md-8">
      <div class="pay-box fix">
        <div class="add-cellect {{(count($member_fav) >0) ?'':'active'}}" mem_id ="{{$olo[0]->mem_id}}" fav ="{{count($member_fav) > 0 ? 'TRUE': 'FALSE'}}" title="加入首選">
          <i class="fa fa-heart" aria-hidden="true"></i>
        </div>
        <div class="added">
          已加入首選
        </div>
        <div class="helper-box">
          <div class="h-face">
            <img src="{{url('/avatar/small/' . $user->usr_photo)}}">
          </div>
          <div class="helper-info">
            <span class="user-name">{{$user->last_name}}{{$user->first_name}}</span>
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
            <div class="income">
              地點：{{$olo[0]->mem_addr}}
            </div>
            <div class="feedback">距離： <span class="text-danger">{{$distance}}</span>公尺 </div>
            <div class="feedback">受雇次數：<span class="text-danger">{{is_null($user->total_served_case)?'0':$user->total_served_case}}  </span>次 </div>
            <div class="feedback">總工作時數：<span class="text-danger">{{is_null($user->total_served_hours)?'0':$user->total_served_hours}} </span>小時 </div>
            <div class="feedback">身份認證已完成
                <span class="text-success">
                    @if(is_null($user->kyc_validated) || $user->kyc_validated != '1')
                        <i class="fa fa-times-circle" aria-hidden="true"></i>
                    @else
                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                    @endif
                </span>
            </div>
            <div class="intro"><p>簡介：{{$user->personal_brief}}</p> </div>

            <div class="helper-jobs">
              <div class="jobs-tit">服務專區 <i class="fa fa-check-square-o" aria-hidden="true"></i> </div>
              <div class="form-group" id="toggle-select">
                <select class="form-control" id="select-tab">
                  <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type3" selected>全部</option>
                  @foreach($olo as $value)
                    <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type{{$value->class_flag}}" id="{{$value->id}}">{{$value->offer_title}}</option>
                  @endforeach
                </select>
              </div>
              <div class="type-arr active" id="type3">
                <div class="job-list">
                  @foreach($olo as $value)
                  <span>{{$value->offer_title}}</span>
                  @endforeach
                </div>
                <div class="jobs-info">
                  受雇次數：<span class="text-danger">{{is_null($user->total_served_case)?'0':$user->total_served_case}} </span>次
                  總工作時數：<span class="text-danger">{{is_null($user->total_served_hours)?'0':$user->total_served_hours}} </span> 小時
                </div>
                @foreach($olo as $value)
                    @if($value->class_flag == 0)
                          <div class="jobs-item"> {{$value->offer_title}}：<span class="text-danger">{{is_null($value->price)?'0':$value->price}}</span>元 / {{$value->price_type}} </div>
{{--                            <div class="col text-center btnarr">--}}
{{--                                <a href="#" class="btn btn-warning normal_hire" data-toggle="modal" data-target="#exampleModalLong" olo_id="{{$value->id}}">雇用</a>--}}
{{--                                <a href="#" class=" btn btn-success normal_hire" data-toggle="modal" data-target="#exampleModalLong" olo_id="{{$value->id}}">詢問</a>--}}
{{--                                <a href="re-helper.html" class=" btn btn btn-info">推薦</a>--}}
{{--                            </div>--}}
                                <div class="intro"><p>簡介：{{is_null($value->offer_description)?'':$value->offer_description}}</p> </div>
                                @if($value->offer_title == "小孩讀伴玩" || $value->offer_title == "課業讀伴")
                                    <div class="jobs-item">最高學歷：{{is_null($value->education)?'':$value->education}}</div>
                                @endif
                                <div class="row justify-content-center">
                                    <div class="col-md-12 mt-2">
                                        <div class="row fix">
                                            @foreach($license_imgs as $license_img)
                                                @if($license_img->olo_id == $value->id)
                                                    <a href="{{URL::to("/")}}/license_img/small/{{$license_img->img}}" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">
                                                        <img src="{{URL::to("/")}}/license_img/small/{{$license_img->img}}" class="img-fluid">
                                                        <div class="pic-dis">證照 </div>
                                                    </a>
                                                @endif
                                            @endforeach
                                            @foreach($imgs as $img)
                                                @if($img->olo_id == $value->id)
                                                    <a href="{{URL::to("/")}}/img/small/{{$img->img}}" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">
                                                    <img src="{{URL::to("/")}}/img/small/{{$img->img}}" class="img-fluid">
                                                    <div class="pic-dis">作品 </div>
                                                </a>
                                                @endif
                                            @endforeach
                                            @foreach($videos as $video)
                                                @if($video->olo_id == $value->id)
                                                     <a href="{{$video->url}}" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">
                                                         <img src="http://i1.ytimg.com/vi/pmW2af_BaRk/mqdefault.jpg" class="img-fluid">
                                                         <div class="pic-dis">影片 </div>
                                                     </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                    @elseif($value->class_flag == 1)
                        @foreach($min_food_prices as $min_food_price)
                              @if($min_food_price->olo_id == $value->id)
                                <div class="jobs-item"> {{$value->offer_title}}：<span class="text-danger">{{is_null($min_food_price->min_price)?'0':$min_food_price->min_price}}</span>元 / {{$value->price_type}}起 </div>
                              @endif
                        @endforeach
{{--                          <div class="col text-center btnarr">--}}
{{--                              <a href="#" class="btn btn-warning food_hire" data-toggle="modal" data-target="#example-food" olo_id="{{$value->id}}">雇用</a>--}}
{{--                              <a href="#" class=" btn btn-success food_hire" data-toggle="modal" data-target="#example-food" olo_id="{{$value->id}}">詢問</a>--}}
{{--                              <a href="re-helper.html" class=" btn btn-info">推薦</a>--}}
{{--                          </div>--}}
                          <div class="intro">
                              <p>簡介：{{is_null($value->offer_description)?'':$value->offer_description}}</p>
                          </div>
                          <div class="row justify-content-center">
                              <div class="col-md-12 mt-2">
                                  <div class="row fix">
                                      @foreach($license_imgs as $license_img)
                                          @if($license_img->olo_id == $value->id)
                                              <a href="{{URL::to("/")}}/license_img/small/{{$license_img->img}}" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">
                                                  <img src="{{URL::to("/")}}/license_img/small/{{$license_img->img}}" class="img-fluid">
                                                  <div class="pic-dis">證照 </div>
                                              </a>
                                          @endif
                                      @endforeach
                                      @foreach($foods as $food)
                                          @if($food->olo_id == $value->id)
                                              <a href="{{URL::to("/")}}/img/small/{{$food->img}}" data-toggle="lightbox" data-gallery="food" class="col-4 col-sm-3  mt-2  p-2">
                                                <img src="{{URL::to("/")}}/img/small/{{$food->img}}" class="img-fluid">
                                                <div class="pic-dis">{{$food->title}}<br><span class="text-danger">{{is_null($food->price)?'0':$food->price}}</span>元 / 件 </div>
                                              </a>
                                          @endif
                                      @endforeach

                                  </div>
                              </div>
                          </div>
                    @elseif($value->class_flag == 2)
                        @if($value->price_type == '報價')
                              <div class="jobs-item"> {{$value->offer_title}}：{{'依'.$value->price_type}} </div>
                        @else
                              <div class="jobs-item"> {{$value->offer_title}}：<span class="text-danger">{{is_null($value->price)?'0':$value->price}}</span>元/ {{$value->price_type}}起 </div>
                        @endif
{{--                          <div class="col text-center btnarr">--}}
{{--                              <a href="#" class="btn btn-warning design_hire" data-toggle="modal" data-target="#example-design" olo_id="{{$value->id}}">雇用</a>--}}
{{--                              <a href="#" class=" btn btn-success design_hire" data-toggle="modal" data-target="#example-design" olo_id="{{$value->id}}">詢問</a>--}}
{{--                              <a href="re-helper.html" class=" btn btn btn-info">推薦</a>--}}
{{--                          </div>--}}
                          <div class="intro"><p>簡介：{{is_null($value->offer_description)?'':$value->offer_description}}</p> </div>
                          <div class="row justify-content-center">
                              <div class="col-md-12 mt-2">
                                  <div class="row fix">
                                      @foreach($license_imgs as $license_img)
                                          @if($license_img->olo_id == $value->id)
                                              <a href="{{URL::to("/")}}/license_img/small/{{$license_img->img}}" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">
                                                        <img src="{{URL::to("/")}}/license_img/small/{{$license_img->img}}" class="img-fluid">
                                                        <div class="pic-dis">證照 </div>
                                                    </a>
                                          @endif
                                      @endforeach
                                      @foreach($imgs as $img)
                                          @if($img->olo_id == $value->id)
                                              <a href="{{URL::to("/")}}/img/small/{{$img->img}}" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">
                                                    <img src="{{URL::to("/")}}/img/small/{{$img->img}}" class="img-fluid">
                                                    <div class="pic-dis">作品 </div>
                                                </a>
                                          @endif
                                      @endforeach
                                      @foreach($videos as $video)
                                          @if($video->olo_id == $value->id)
                                              <a href="{{$video->url}}" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">
                                                         <img src="http://i1.ytimg.com/vi/pmW2af_BaRk/mqdefault.jpg" class="img-fluid">
                                                         <div class="pic-dis">影片 </div>
                                                     </a>
                                          @endif
                                      @endforeach
                                  </div>
                              </div>
                          </div>
                    @endif
                @endforeach
                <div class="jobs-tit">服務評分 <i class="fa fa-star-o" aria-hidden="true"></i> </div>
                <div class="jobs-all"> 總體服務評分
                  <span class="start-all">
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
                    <span class="avg">{{is_null($user->customer_avg_rate)?'0':$user->customer_avg_rate}}</span>
                  </span>
                </div>
{{--                <div class="start-box">--}}
{{--                  <div class="box-satrt">--}}
{{--                    <i class="fa fa-star" aria-hidden="true"></i>--}}
{{--                    <i class="fa fa-star" aria-hidden="true"></i>--}}
{{--                    <i class="fa fa-star" aria-hidden="true"></i>--}}
{{--                    <i class="fa fa-star" aria-hidden="true"></i>--}}
{{--                    <i class="fa fa-star" aria-hidden="true"></i>--}}
{{--                  </div>--}}
{{--                  <div class="box-pros">--}}
{{--                    <div class="progress">--}}
{{--                      <div class="progress-bar bg-warning" role="progressbar" style="width: 95%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"> </div>--}}
{{--                    </div>--}}
{{--                  </div>--}}
{{--                  <div class="box-pres">95% </div>--}}
{{--                </div>--}}
{{--                <div class="start-box">--}}
{{--                  <div class="box-satrt">--}}
{{--                    <i class="fa fa-star" aria-hidden="true"></i>--}}
{{--                    <i class="fa fa-star" aria-hidden="true"></i>--}}
{{--                    <i class="fa fa-star" aria-hidden="true"></i>--}}
{{--                    <i class="fa fa-star" aria-hidden="true"></i>--}}
{{--                  </div>--}}
{{--                  <div class="box-pros">--}}
{{--                    <div class="progress">--}}
{{--                      <div class="progress-bar bg-warning" role="progressbar" style="width: 5%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>--}}
{{--                    </div>--}}
{{--                  </div>--}}
{{--                  <div class="box-pres">5% </div>--}}
{{--                </div>--}}
{{--                <div class="start-box">--}}
{{--                  <div class="box-satrt">--}}
{{--                    <i class="fa fa-star" aria-hidden="true"></i>--}}
{{--                    <i class="fa fa-star" aria-hidden="true"></i>--}}
{{--                    <i class="fa fa-star" aria-hidden="true"></i>--}}
{{--                  </div>--}}
{{--                  <div class="box-pros">--}}
{{--                    <div class="progress">--}}
{{--                      <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"> </div>--}}
{{--                    </div>--}}
{{--                  </div>--}}
{{--                <div class="box-pres">0% </div>--}}
{{--              </div>--}}
{{--              <div class="start-box">--}}
{{--                <div class="box-satrt">--}}
{{--                  <i class="fa fa-star" aria-hidden="true"></i>--}}
{{--                  <i class="fa fa-star" aria-hidden="true"></i>--}}
{{--                </div>--}}
{{--                <div class="box-pros">--}}
{{--                  <div class="progress">--}}
{{--                    <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"> </div>--}}
{{--                  </div>--}}
{{--                </div>--}}
{{--              <div class="box-pres">0% </div>--}}
{{--            </div>--}}
{{--            <div class="start-box">--}}
{{--              <div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i> </div>--}}
{{--              <div class="box-pros">--}}
{{--                <div class="progress">--}}
{{--                  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"> </div>--}}
{{--                </div>--}}
{{--              </div>--}}
{{--            <div class="box-pres">0% </div>--}}
{{--          </div>--}}

{{--          <div class="comm">共有13則評價 <i class="fa fa-commenting-o" aria-hidden="true"></i> </div>--}}
{{--          <div class="comm-list">--}}
{{--            <div class="comm-face">--}}
{{--              <img src="images/face.jpg">--}}
{{--            </div>--}}
{{--            <div class="comm-info">--}}
{{--              <div class="comm-name">吳大偉</div >--}}
{{--              <div class="comm-date">2019/03/25 </div>--}}
{{--              <div class="comm-re">大推~~很專業很細心的服務 </div>--}}
{{--            </div>--}}
{{--          </div>--}}
{{--          <div class="comm-list">--}}
{{--            <div class="comm-face">--}}
{{--              <img src="images/face.jpg">--}}
{{--            </div>--}}
{{--            <div class="comm-info">--}}
{{--              <div class="comm-name">吳大偉</div >--}}
{{--              <div class="comm-date">2019/03/25 </div>--}}
{{--              <div class="comm-re">大推~~很專業很細心的服務 </div>--}}
{{--            </div>--}}
{{--          </div>--}}
{{--          <div class="more"> <button class="btn btn-sm btn-light">更多評價</button> </div>--}}
        </div>

    <div class="type-arr" id="type1">
      <div class="job-list"> <span> 家常菜123</span> </div>
      <div class="jobs-info">受雇次數：<span class="text-danger">115</span>次 </div>
      <div class="jobs-item"> 家常菜：<span class="text-danger">300</span>元 / 件起 </div>
{{--      <div class="col text-center btnarr">--}}
{{--        <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#example-food">雇用</a>--}}
{{--        <a href="#" class=" btn btn-success" data-toggle="modal" data-target="#example-food">詢問</a>--}}
{{--        <a href="re-helper.html" class=" btn btn btn-info">推薦</a>--}}
{{--      </div>--}}
      <div class="intro"><p>簡介：用心經營、責任施工、品質保證 假日、夜間（下午五點後）施工因需提前備料務必提前預約。</p>
        </div>
    <div class="row justify-content-center">
      <div class="col-md-12 mt-2">
        <div class="row fix">
          <a href="images/food.jpg" data-toggle="lightbox" data-gallery="food" class="col-4 col-sm-3  mt-2  p-2">
              <img src="images/food.jpg" class="img-fluid">
              <div class="pic-dis">西湖醋魚<br><span class="text-danger">500</span>元 / 件

              </div>
          </a>
          <a href="images/food.jpg" data-toggle="lightbox" data-gallery="food" class="col-4 col-sm-3  mt-2  p-2">
              <img src="images/food.jpg" class="img-fluid">
              <div class="pic-dis">宮堡雞丁<br><span class="text-danger">500</span>元 / 件
              </div>
          </a>
          <a href="images/food.jpg" data-toggle="lightbox" data-gallery="food" class="col-4 col-sm-3  mt-2  p-2">
              <img src="images/food.jpg" class="img-fluid">
              <div class="pic-dis">西湖醋魚<br><span class="text-danger">500</span>元 / 件

              </div>
          </a>
          <a href="images/food.jpg" data-toggle="lightbox" data-gallery="food" class="col-4 col-sm-3  mt-2  p-2">
              <img src="images/food.jpg" class="img-fluid">
              <div class="pic-dis">宮堡雞丁<br><span class="text-danger">500</span>元 / 件

              </div>
          </a>
          <a href="images/food.jpg" data-toggle="lightbox" data-gallery="food" class="col-4 col-sm-3  mt-2  p-2">
              <img src="images/food.jpg" class="img-fluid">
              <div class="pic-dis">西湖醋魚<br><span class="text-danger">500</span>元 / 件

              </div>
          </a>
          <a href="images/food.jpg" data-toggle="lightbox" data-gallery="food" class="col-4 col-sm-3  mt-2  p-2">
              <img src="images/food.jpg" class="img-fluid">
              <div class="pic-dis">宮堡雞丁<br><span class="text-danger">500</span>元 / 件 </div>
          </a>
        </div>
      </div>
    </div>



{{--  <div class="jobs-tit">服務評分 <i class="fa fa-star-o" aria-hidden="true"></i> </div>--}}
{{--  <div class="jobs-all"> 居家服務評分<span class="start-all"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> <span class="avg">4.9</span></span> </div>--}}
{{--<div class="start-box">--}}
{{--<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--</div><div class="box-pros"><div class="progress">--}}
{{--  <div class="progress-bar bg-warning" role="progressbar" style="width: 95%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--  </div>--}}


{{--</div>--}}

{{--</div>--}}
{{--<div class="box-pres">95%--}}

{{--</div>--}}


{{--</div>--}}
{{--<div class="start-box">--}}
{{--<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--</div><div class="box-pros"><div class="progress">--}}
{{--  <div class="progress-bar bg-warning" role="progressbar" style="width: 5%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--  </div>--}}
{{--</div>--}}

{{--</div><div class="box-pres">5%--}}

{{--</div>--}}


{{--</div>--}}
{{--<div class="start-box">--}}
{{--<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--</div><div class="box-pros"><div class="progress">--}}
{{--  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--  </div>--}}


{{--</div>--}}

{{--</div><div class="box-pres">0%--}}

{{--</div>--}}


{{--</div>--}}
{{--<div class="start-box">--}}
{{--<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--</div><div class="box-pros"><div class="progress">--}}
{{--  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--  </div>--}}


{{--</div>--}}

{{--</div><div class="box-pres">0%--}}

{{--</div>--}}


{{--</div>--}}
{{--<div class="start-box">--}}
{{--<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--</div><div class="box-pros"><div class="progress">--}}
{{--  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--  </div>--}}


{{--</div>--}}

{{--</div><div class="box-pres">0%--}}

{{--</div>--}}


{{--</div>--}}

{{--<div class="comm">共有6則評價 <i class="fa fa-commenting-o" aria-hidden="true"></i>--}}

{{--</div>--}}
{{--<div class="comm-list">--}}
{{--  <div class="comm-face"><img src="images/face.jpg">--}}

{{--  </div><div class="comm-info"><div class="comm-name">吳大偉</div ><div class="comm-date">2019/03/25--}}

{{--  </div><div class="comm-re">大推~~很專業很細心的服務--}}

{{--  </div>--}}

{{--  </div>--}}

{{--  </div>--}}
{{--  <div class="comm-list">--}}
{{--  <div class="comm-face"><img src="images/face.jpg">--}}

{{--  </div><div class="comm-info"><div class="comm-name">吳大偉</div ><div class="comm-date">2019/03/25--}}

{{--  </div><div class="comm-re">大推~~很專業很細心的服務--}}

{{--  </div>--}}

{{--  </div>--}}

{{--  </div>--}}
{{-- <div class="more"> <button class="btn btn-sm btn-light">更多評價</button>--}}

{{-- </div>--}}


</div>

<div class="type-arr" id="type0">
    <div class="job-list"><span>電腦教學</span></div>
    <div class="jobs-info">受雇次數：<span class="text-danger">20 </span>次 工作時數：<span class="text-danger">30 </span> 小時</div>
    <div class="jobs-item"> 電腦教學：<span class="text-danger">500</span>元 / 小時</div>
{{--    <div class="col text-center btnarr">--}}
{{--        <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#exampleModalLong">雇用</a>--}}
{{--        <a href="#" class=" btn btn-success" data-toggle="modal" data-target="#exampleModalLong">詢問</a>--}}
{{--        <a href="re-helper.html" class=" btn btn btn-info">推薦</a>--}}
{{--    </div>--}}
    <div class="intro">
        <p>簡介：用心經營、責任施工、品質保證 假日、夜間（下午五點後）施工因需提前備料務必提前預約。
        </p>
    </div>
    <div class="jobs-item">最高學歷：台北科技大學資工系

    </div>
    <div class="row justify-content-center">
        <div class="col-md-12 mt-2">
            <div class="row fix">

                <a href="images/works.jpg" data-toggle="lightbox" data-gallery="example-gallery" class="col-4 col-sm-3  mt-2  p-2">
                    <img src="images/works.jpg" class="img-fluid">
                    <div class="pic-dis">作品

                    </div>
                </a>
                <a href="images/20161005_R030.jpg" data-toggle="lightbox" data-gallery="example-gallery" class="col-4 col-sm-3  mt-2  p-2">
                    <img src="images/20161005_R030.jpg" class="img-fluid">
                    <div class="pic-dis">作品

                    </div>
                </a>

            </div>

        </div>

    </div>

{{--    <div class="jobs-tit">服務評分 <i class="fa fa-star-o" aria-hidden="true"></i>--}}

{{--    </div>--}}
{{--    <div class="jobs-all"> 學習服務評分<span class="start-all"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> <span class="avg">4.9</span></span>--}}

{{--    </div>--}}
{{--    <div class="start-box">--}}
{{--        <div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--        </div>--}}
{{--        <div class="box-pros">--}}
{{--            <div class="progress">--}}
{{--                <div class="progress-bar bg-warning" role="progressbar" style="width: 95%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--                </div>--}}

{{--            </div>--}}

{{--        </div>--}}
{{--        <div class="box-pres">95%--}}

{{--        </div>--}}

{{--    </div>--}}
{{--    <div class="start-box">--}}
{{--        <div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--        </div>--}}
{{--        <div class="box-pros">--}}
{{--            <div class="progress">--}}
{{--                <div class="progress-bar bg-warning" role="progressbar" style="width: 5%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--                </div>--}}

{{--            </div>--}}

{{--        </div>--}}
{{--        <div class="box-pres">5%--}}

{{--        </div>--}}

{{--    </div>--}}
{{--    <div class="start-box">--}}
{{--        <div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--        </div>--}}
{{--        <div class="box-pros">--}}
{{--            <div class="progress">--}}
{{--                <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--                </div>--}}

{{--            </div>--}}

{{--        </div>--}}
{{--        <div class="box-pres">0%--}}

{{--        </div>--}}

{{--    </div>--}}
{{--    <div class="start-box">--}}
{{--        <div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--        </div>--}}
{{--        <div class="box-pros">--}}
{{--            <div class="progress">--}}
{{--                <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--                </div>--}}

{{--            </div>--}}

{{--        </div>--}}
{{--        <div class="box-pres">0%--}}

{{--        </div>--}}

{{--    </div>--}}
{{--    <div class="start-box">--}}
{{--        <div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--        </div>--}}
{{--        <div class="box-pros">--}}
{{--            <div class="progress">--}}
{{--                <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--                </div>--}}

{{--            </div>--}}

{{--        </div>--}}
{{--        <div class="box-pres">0%--}}

{{--        </div>--}}

{{--    </div>--}}

{{--    <div class="comm">共有7則評價 <i class="fa fa-commenting-o" aria-hidden="true"></i>--}}

{{--    </div>--}}
{{--    <div class="comm-list">--}}
{{--        <div class="comm-face"><img src="images/face.jpg">--}}

{{--        </div>--}}
{{--        <div class="comm-info">--}}
{{--            <div class="comm-name">吳大偉</div>--}}
{{--            <div class="comm-date">2019/03/25--}}

{{--            </div>--}}
{{--            <div class="comm-re">大推~~很專業很細心的服務--}}

{{--            </div>--}}

{{--        </div>--}}

{{--    </div>--}}
{{--    <div class="comm-list">--}}
{{--        <div class="comm-face"><img src="images/face.jpg">--}}

{{--        </div>--}}
{{--        <div class="comm-info">--}}
{{--            <div class="comm-name">吳大偉</div>--}}
{{--            <div class="comm-date">2019/03/25--}}

{{--            </div>--}}
{{--            <div class="comm-re">大推~~很專業很細心的服務--}}

{{--            </div>--}}

{{--        </div>--}}

{{--    </div>--}}
{{--    <div class="more">--}}
{{--        <button class="btn btn-sm btn-light">更多評價</button>--}}

{{--    </div>--}}

</div>
<div class="type-arr" id="type2">
            <div class="jobs-item"> 室內設計：<span class="text-danger">15000</span>元/件起</div>
{{--    <div class="col text-center btnarr">--}}
{{--         <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#example-design">雇用</a>--}}
{{--         <a href="#" class=" btn btn-success" data-toggle="modal" data-target="#example-design">詢問</a>--}}
{{--         <a href="re-helper.html" class=" btn btn btn-info">推薦</a>--}}
{{--    </div>--}}
           <div class="intro"><p>簡介：用心經營、責任施工、品質保證
假日、夜間（下午五點後）施工因需提前備料務必提前預約。</p>

</div>
<div class="row justify-content-center">
    <div class="col-md-12 mt-2">
        <div class="row fix">

            <a href="images/works.jpg" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">
                <img src="images/works.jpg" class="img-fluid">
                <div class="pic-dis">證照

                </div>
            </a>
            <a href="http://youtu.be/pmW2af_BaRk" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">
    <img src="http://i1.ytimg.com/vi/pmW2af_BaRk/mqdefault.jpg" class="img-fluid">
    <div class="pic-dis">影片

    </div>
</a>


        </div>


    </div>


</div>

{{--      <div class="jobs-tit">服務評分 <i class="fa fa-star-o" aria-hidden="true"></i>--}}

{{--      </div>--}}
{{--      <div class="jobs-all"> 學習服務評分<span class="start-all"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> <span class="avg">4.9</span></span>--}}

{{--      </div>--}}
{{--<div class="start-box">--}}
{{--<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--</div><div class="box-pros"><div class="progress">--}}
{{--  <div class="progress-bar bg-warning" role="progressbar" style="width: 95%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--  </div>--}}


{{--</div>--}}

{{--</div><div class="box-pres">95%--}}

{{--</div>--}}


{{--</div>--}}
{{--<div class="start-box">--}}
{{--<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--</div><div class="box-pros"><div class="progress">--}}
{{--  <div class="progress-bar bg-warning" role="progressbar" style="width: 5%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--  </div>--}}


{{--</div>--}}

{{--</div><div class="box-pres">5%--}}

{{--</div>--}}


{{--</div>--}}
{{--<div class="start-box">--}}
{{--<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--</div><div class="box-pros"><div class="progress">--}}
{{--  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--  </div>--}}


{{--</div>--}}

{{--</div><div class="box-pres">0%--}}

{{--</div>--}}


{{--</div>--}}
{{--<div class="start-box">--}}
{{--<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--</div><div class="box-pros"><div class="progress">--}}
{{--  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--  </div>--}}


{{--</div>--}}

{{--</div><div class="box-pres">0%--}}

{{--</div>--}}


{{--</div>--}}
{{--<div class="start-box">--}}
{{--<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i>--}}

{{--</div><div class="box-pros"><div class="progress">--}}
{{--  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">--}}

{{--  </div>--}}


{{--</div>--}}

{{--</div><div class="box-pres">0%--}}

{{--</div>--}}


{{--</div>--}}

{{--<div class="comm">共有7則評價 <i class="fa fa-commenting-o" aria-hidden="true"></i>--}}

{{--</div>--}}
{{--<div class="comm-list">--}}
{{--  <div class="comm-face"><img src="images/face.jpg">--}}

{{--  </div><div class="comm-info"><div class="comm-name">吳大偉</div ><div class="comm-date">2019/03/25--}}

{{--  </div><div class="comm-re">大推~~很專業很細心的服務--}}

{{--  </div>--}}

{{--  </div>--}}

{{--  </div>--}}
{{--  <div class="comm-list">--}}
{{--  <div class="comm-face"><img src="images/face.jpg">--}}

{{--  </div><div class="comm-info"><div class="comm-name">吳大偉</div ><div class="comm-date">2019/03/25--}}

{{--  </div><div class="comm-re">大推~~很專業很細心的服務--}}

{{--  </div>--}}

{{--  </div>--}}

{{--  </div>--}}
{{-- <div class="more"> <button class="btn btn-sm btn-light">更多評價</button>--}}

{{-- </div>--}}


</div>


</div>


</div>

</div>


</div>


</div>


</div>

<!--家常菜雇用 -->
  <div class="modal fade" id="example-food" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><img src="images/wbell.svg"> 家常菜雇用(詢問)設定</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>


        </div>
        <div class="modal-body">
          <p><span class="text-success">關鍵字：</span>加壓馬逹安裝</p>
          <p><span class="text-success"><i class="fa fa-check-square-o" aria-hidden="true"></i> 服務類別：</span>家常菜</p><p><span class="text-success"><i class="fa fa-adjust" aria-hidden="true"></i> 搜尋半徑：</span>{{$distance}} 公尺</p>
          <div class="form-group" id="toggle-select">
            <label><i class="fa fa-map-marker" aria-hidden="true"></i> 服務地點：</label>
              {{$olo[0]->mem_addr}}
            {{--<select class="form-control">--}}
              {{--<option  selected>台中市大里區大智路567號12樓</option>--}}
             {{--</select>--}}


             </div>
            <p ><span class="text-success">菜單：</span></p>
            <div id="food_menu">
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 1</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 2</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 3</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 4</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>
                <div class="f-menu">
                    <div class="col">
                        <h5>西湖醋魚 <span class="text-danger"> 5</span>元 / 件</h5>
                    </div>
                    <div style="width:120px">
                        <input type="number" class="f-pic" value="0" min="0" max="10" />
                    </div>
                </div>

            </div>

        <div class="form-group mt-2">
              <label for="location"><i class="fa fa-calendar" aria-hidden="true"></i> 週期</label>
              <div class="cy-arrb">
                <input id='onceb' type="radio" name='group-3' data-toggle-select-hide=".cycle" data-toggle-select-show="#cy-e" checked />
                <label for="onceb">一次</label>
                <input id='dailyb' type="radio" name='group-3' data-toggle-select-hide=".cycle" data-toggle-select-show="#cy-f" />
                <label for="dailyb">每日</label>
                <input id='weeklyb' type="radio" name='group-3' data-toggle-select-hide=".cycle" data-toggle-select-show="#cy-g" />
                <label for="weeklyb">每週</label>
                <input id='monthb' type="radio" name='group-3' data-toggle-select-hide=".cycle" data-toggle-select-show="#cy-h" />
                <label for="monthb">每月</label>


              </div>
              <div class="cycle active" id="cy-e">
                <label>預約日期：</label><input type="date" class="form-control">
                <label>預約時間：</label><input type="time" class="form-control">



              </div>
              <div class="cycle" id="cy-f">
                <label>預約日期：</label><input type="date" id="start" class="form-control">
                <label>結束日期：</label><input type="date" id="end" class="form-control">
                <label>預約時間：</label><input type="time" class="form-control">



               </div>
              <div class="cycle" id="cy-g">
                <label>預約日期：</label><input type="date" id="start" class="form-control">
                <label>結束日期：</label><input type="date" id="end" class="form-control">
                <label>星期：</label>
                <div>
                  <input id='week1' type="checkbox" name='weekb' />
                  <label for="week1">星期一</label>
                  <input id='week2' type="checkbox" name='weekb' />
                  <label for="week2">星期二</label>
                  <input id='week3' type="checkbox" name='weekb' />
                  <label for="week3">星期三</label>
                  <input id='week4' type="checkbox" name='weekb' />
                  <label for="week4">星期四</label>
                  <input id='week5' type="checkbox" name='weekb' />
                  <label for="week5">星期五</label>
                  <input id='week6' type="checkbox" name='weekb' />
                  <label for="week6">星期六</label>
                  <input id='week7' type="checkbox" name='weekb' />
                  <label for="week7">星期日</label>


                </div>
                <label>預約時間：</label><input type="time" class="form-control">




              </div>
              <div class="cycle" id="cy-h">
                <label>預約日期：</label><input type="date" id="start" class="form-control">
                <label>結束日期：</label><input type="date" id="end" class="form-control">
                <label>每月幾號：</label><input type="text" class="form-control" placeholder="多個日期以＇，＇分隔">
                <label>預約時間：</label><input type="time" class="form-control">



              </div>


            </div>
          <textarea name="" 　class="form-control" placeholder="需求描述"></textarea>
          <div class="summary">
            <p>時間週期：2019/05/01 PM:06:30</p>
            <p> 西湖醋魚 2 件 100元 </p>
            <p> 宮堡雞丁 1 件 500元 </p>
            <p> 總金額：1500元 </p>


          </div>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
          <button type="button" class="btn btn-success">送出雇用</button>


        </div>


      </div>


    </div>


  </div>
<!--一般  -->
<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">電腦教學雇用(詢問)設定</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>


        </div>
        <div class="modal-body">
        <p><span class="text-success">關鍵字：</span>加壓馬逹安裝</p>

          <p><span class="text-success"><i class="fa fa-check-square-o" aria-hidden="true"></i> 服務類別：</span>水電工程</p><p><span class="text-success"><i class="fa fa-adjust" aria-hidden="true"></i> 搜尋半徑：</span>{{$distance}} 公尺</p>
     <div class="form-group" id="toggle-select">
            <label><i class="fa fa-map-marker" aria-hidden="true"></i> 服務地點：</label>
         {{$olo[0]->mem_addr}}
            {{--<select class="form-control">--}}
              {{--<option  selected>台中市大里區大智路567號12樓</option>--}}
             {{--</select>--}}


             </div>
            <div class="form-group">
              <label for="location"><i class="fa fa-usd" aria-hidden="true"></i> 預算</label>
              <input type="text" class="form-control" id="location" value="0" >
              <span>元</span>


            </div>
            <div class="form-group">
              <input id='dayb' type="radio" name='group-0'/>
              <label for="dayb">每件</label>
              <input id='hourb' type="radio" name='group-0'  checked='checked' />
              <label for="hourb">每小時</label>


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
                  <input type="number" value="0" min="0" max="10">


                </div>


              </div>
              <div class="cycle" id="cy-b">
                <label>預約日期：</label><input type="date" id="start" class="form-control">
                <label>結束日期：</label><input type="date" id="end" class="form-control">
                <label>預約時間：</label><input type="time" class="form-control">
                <label id="number">工作時間/小時：</label>
                <div class="input-group" id="number-picker">
                 <input type="number" value="0" min="0" max="10" />


              </div>


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
                   <input type="number" value="0" min="0" max="10" />


                </div>



              </div>
              <div class="cycle" id="cy-d">
                <label>預約日期：</label><input type="date" id="start" class="form-control">
                <label>結束日期：</label><input type="date" id="end" class="form-control">
                <label>每月幾號：</label><input type="text" class="form-control" placeholder="多個日期以＇，＇分隔">
                <label>預約時間：</label><input type="time" class="form-control">
                <label id="number">工作時間/小時：</label>
                <div class="input-group" id="number-picker">
                   <input type="number" value="0" min="0" max="10" />


                </div>


              </div>


            </div>
            <label for="file-1">照片上傳：</label><input  id="file-1" type="file" multiple>
            <textarea name="" 　class="form-control" placeholder="需求描述"></textarea>
            <div class="summary">
              <p> 預算：500元/小時 </p>
              <p>時間週期：每週三、五，2019/05/01 至 20/05/10，下午1-5點</p>
              <p>2019/05/01 下午1-5點4小時、2019/05/01 下午1-5點4小時、2019/05/01 下午1-5點4小時、2019/05/01 下午1-5點4小時</p>
              <p> 總金額：8000元 </p>


            </div>


          </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
          <button type="button" class="btn btn-success">確認配對</button>


        </div>


      </div>


    </div>


  </div>
<!-- 設計類  -->
 <div class="modal fade" id="example-design" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">室內設計雇用(詢問)設定</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <p><span class="text-success">關鍵字：</span>加壓馬逹安裝</p>
            <p><span class="text-success"><i class="fa fa-check-square-o" aria-hidden="true"></i> 服務類別：</span>室內設計</p><p><span class="text-success"><i class="fa fa-adjust" aria-hidden="true"></i> 搜尋半徑：</span>{{$distance}} 公尺</p>

          <div class="form-group" id="toggle-select">
            <label><i class="fa fa-map-marker" aria-hidden="true"></i> 服務地點：</label>
              {{$olo[0]->mem_addr}}
            {{--<select class="form-control">--}}
              {{--<option  selected>台中市大里區大智路567號12樓</option>--}}
             {{--</select>--}}


             </div>
    <div class="form-group">
      <label for="location"><i class="fa fa-usd" aria-hidden="true"></i> 預算</label>
      <input type="text" class="form-control" id="location" placeholder="請輸入金額">
      <span>元</span>


      </div>
 <div class="form-group">
      <input id='twice' type="radio" name='group-1' checked='checked' />
      <label for="twice">二階段付款</label>
      <input id='third' type="radio" name='group-1' />
      <label for="third">三階段付款</label>


      </div>
      <div class="twice">
        <label>第一次付款日期:</label><input type="date" id="start" class="form-control">
        <label>付款比例(輸入百分比)%:</label><input type="text" id="starta" class="form-control" value="50">
        <label>結案付款日期:</label><input type="date" id="end" class="form-control">
        <label>付款比例(輸入百分比)%:</label><input type="text" id="enda" class="form-control" value="50">


        </div>
         <div class="third">
        <label>第一次付款日期:</label><input type="date" id="start" class="form-control">
        <label>付款比例(輸入百分比)%:</label><input type="text" id="startb" class="form-control" value="40">
        <label>第二次付款日期:</label><input type="date" id="second" class="form-control">
        <label>付款比例(輸入百分比)%:</label><input type="text" id="secondb" class="form-control" value="30">
        <label>結案付款日期:</label><input type="date" id="end" class="form-control">
        <label>付款比例(輸入百分比)%:</label><input type="text" id="endb" class="form-control"  value="30">


        </div>
        <label for="file-1">照片上傳：</label><input   type="file" multiple>
          <textarea name="" 　class="form-control" placeholder="需求描述"></textarea>
          <div class="summary">
            <p>第一階段付款：2019/05/01 比例：30% 金額：15000元</p>
            <p>第一階段付款：2019/05/15 比例：30% 金額：15000元</p>
            <p>第一階段付款：2019/05/30 比例：40% 金額：20000元</p>
            <p>時間週期：2019/05/01 至 20/05/10</p>
            <p> 總金額：50000元 </p>


          </div>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
          <button type="button" class="btn btn-success">確認配對</button>


        </div>


      </div>


    </div>


  </div>
@section('myScript')
<script src="{{asset('/js/ekko-lightbox.min.js')}}"></script>
<script src="{{asset('/js/bootstrap-input-spinner.js')}}"></script>
<script>
  $("input[type='number']").inputSpinner();
	$(function(){
	  $('.header,.search-frame').addClass('fixed') ;
	  if ($(window).width() < 991) {$('.header,.search-frame').removeClass('fixed')  }
	});
	$(document).on('click', '[data-toggle="lightbox"]', function(event) {
    event.preventDefault();
    $(this).ekkoLightbox();
  });
  $(function () {
    $('.cy-arr input').on('change', function () {
      var $this = $('.cy-arr input:checked')
      var $hideElements = $($this.attr("data-toggle-select-hide"));
      var $showElements = $($this.attr("data-toggle-select-show"));

      $hideElements.slideUp();
      $showElements.slideDown();

    });

    $(document).on("click", ".normal_hire" , function() {
        $.ajax({
            type: "get",
            url: "{{url('/api/get_office_details')}}",
            data: {
                id: $(this).attr('olo_id')
            },
            dataType: "json",
            success: function (responses) {
                $('#exampleModalLong').find('.modal-title').html(responses['olo'][0]['offer_title']+'雇用(詢問)設定');
                $('#exampleModalLong').find('.modal-body').find('p:nth-child(1)').html("<span class=\"text-success\">關鍵字：</span>"+responses['olo'][0]['service_type'])
                $('#exampleModalLong').find('.modal-body').find('p:nth-child(2)').html("<span class=\"text-success\"><i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i> 服務類別：</span>"+responses['olo'][0]['offer_title'])
                //$('#exampleModalLong').find('#location').val(responses['olo'][0]['price']);
            }
        });
    });

    $(document).on("click", ".food_hire" , function() {
        $.ajax({
            type: "get",
            url: "{{url('/api/get_office_details')}}",
            data: {
                id: $(this).attr('olo_id')
            },
            dataType: "json",
            success: function (responses) {
                console.log(responses);
                $('#example-food').find('.modal-title').html('<img src="images/wbell.svg">'+responses['olo'][0]['offer_title']+'雇用(詢問)設定');
                $('#example-food').find('.modal-body').find('p:nth-child(1)').html("<span class=\"text-success\">關鍵字：</span>"+responses['olo'][0]['service_type'])
                $('#example-food').find('.modal-body').find('p:nth-child(2)').html("<span class=\"text-success\"><i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i> 服務類別：</span>"+responses['olo'][0]['offer_title'])
//                $clone = $('#example-food').find('#food_menu').find('.f-menu').clone();
//                console.log($clone);
//                $('#example-food').find('#food_menu').html($clone);
                $('#example-food').find('#food_menu').find('.f-menu:nth-child(1) .col h5').html(''+responses['foods'][0]['title']+'<span class=\"text-danger\"> '+responses['foods'][0]['price']+'</span> 元 / 件');
//                var str = '';
                var child = 0;
                responses['foods'].forEach(function (response, index) {
                    child = index+1;
                    $('#example-food').find('#food_menu').find('.f-menu:nth-child('+child+') .col h5').html(''+responses['foods'][index]['title']+'<span class=\"text-danger\"> '+responses['foods'][index]['price']+'</span> 元 / 件');
                });
                console.log(child);
                $('#food_menu > div:gt('+(child-1)+')').remove();
//                for(child;child<=20;child++){
//                    child = index+1;
//                    console.log(child);
//                    $('#example-food').find('#food_menu').find('.f-menu:nth-child('+child+')').remove();
//                }
                //$('#example-food').find('#food_menu').html(str);
                //$('#exampleModalLong').find('#location').val(responses['olo'][0]['price']);
            }
        });
    });

    $(document).on("click", ".design_hire" , function() {
        $.ajax({
            type: "get",
            url: "{{url('/api/get_office_details')}}",
            data: {
                id: $(this).attr('olo_id')
            },
            dataType: "json",
            success: function (responses) {
                $('#example-design').find('.modal-title').html(responses['olo'][0]['offer_title']+'雇用(詢問)設定');
                $('#example-design').find('.modal-body').find('p:nth-child(1)').html("<span class=\"text-success\">關鍵字：</span>"+responses['olo'][0]['service_type'])
                $('#example-design').find('.modal-body').find('p:nth-child(2)').html("<span class=\"text-success\"><i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i> 服務類別：</span>"+responses['olo'][0]['offer_title'])
                //$('#exampleModalLong').find('#location').val(responses['olo'][0]['price']);
            }
        });
    });

    $('.add-cellect').on('click',function(){
        $fav = $(this).attr('fav');
        $.ajax({
            type: "get",
            url: "{{url('/api/add_favorites')}}",
            data: {
                mem_id: $(this).attr('mem_id'),
                fav:$fav
            },
            dataType: "json",
            success: function (responses) {
                if($fav == 'TRUE'){
                    $('.add-cellect ').addClass('active');
                    $('.add-cellect').attr('fav',"FALSE");
                }else{
                    $('.add-cellect ').removeClass('active')
                    $('.add-cellect').attr('fav',"TRUE");
                }
            }
        });
      });

    $('#select-tab').on('change', function () {
      var $this = $('option:selected', this);
      var $hideElements = $($this.attr("data-toggle-select-hide"));
      var $showElements = $($this.attr("data-toggle-select-show"));

      $hideElements.slideUp();
      $showElements.slideDown();

        $.ajax({
            type: "get",
            url: "{{url('/api/get_office_details')}}",
            data: {
                id: $this.attr('id')
            },
            dataType: "json",
            success: function (responses) {
                if(responses['olo'][0]['class_flag'] == 0){
                    console.log(responses['olo'][0]);
                    $('#type0').find('.job-list').find('span').text(responses['olo'][0]['offer_title']);
                    $('#type0').find('.jobs-info').find('span').first().text((responses['user']['total_served_case'] == null?'0':responses['user']['total_served_case']));
                    $('#type0').find('.jobs-info').find('span').last().text((responses['user']['total_served_hours'] == null?'0':responses['user']['total_served_hours']));
                    $('#type0').find('.jobs-info').next('div').html(responses['olo'][0]['offer_title']+': <span class="text-danger">'+(responses['olo'][0]['price'] == null ?'0':responses['olo'][0]['price'])+'</span>元 / ' +(responses['olo'][0]['price_type'] == null ?'':responses['olo'][0]['price_type']));
                    $('#type0').find('.intro').text((responses['olo'][0]['offer_description']==null?'簡介：':'簡介：'+responses['olo'][0]['offer_description']));
                    if(responses['olo'][0]['offer_title'] == "小孩讀伴玩" || responses['olo'][0]['offer_title'] =="課業讀伴"){
                        $('#type0').find('.intro').next('div').show();
                        $('#type0').find('.intro').next('div').text((responses['olo'][0]['education'] == null? '最高學歷：':'最高學歷：'+responses['olo'][0]['education']));
                    }else{
                        $('#type0').find('.intro').next('div').hide();
                    }

                    $('#type0').find('.btnarr').find('a.btn-warning').attr("olo_id", $this.attr('id'));
                    $('#type0').find('.btnarr').find('a.btn-warning').addClass("normal_hire");
                    $('#type0').find('.btnarr').find('a.btn-success').attr("olo_id", $this.attr('id'));
                    $('#type0').find('.btnarr').find('a.btn-success').addClass("normal_hire");

                    var str ='';
                    if(responses['license_imgs'].length > 0){
                        responses['license_imgs'].forEach(function(element) {
                            str+= '<a href="{{URL::to("/")}}/license_img/small/'+element.img+'" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">\n' +
                            '            <img src="{{URL::to("/")}}/license_img/small/'+element.img+'" class="img-fluid">\n' +
                            '            <div class="pic-dis">證照 </div>\n' +
                            '        </a>'
                        });
                    }
                    if(responses['imgs'].length > 0){
                        responses['imgs'].forEach(function(element) {
                            str+= '<a href="{{URL::to("/")}}/img/small/'+element.img+'" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">\n' +
                                '            <img src="{{URL::to("/")}}/img/small/'+element.img+'" class="img-fluid">\n' +
                                '            <div class="pic-dis">作品 </div>\n' +
                                '        </a>'
                        });
                    }
                    if(responses['videos'].length > 0){
                        responses['videos'].forEach(function(element) {
                            str+= '<a href="'+element.url+'" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">\n' +
                                '            <img src="http://i1.ytimg.com/vi/pmW2af_BaRk/mqdefault.jpg" class="img-fluid">\n' +
                                '            <div class="pic-dis">影片 </div>\n' +
                                '        </a>'
                        });
                    }
                    $('#type0').find('.fix').html(str);

                }else if(responses['olo'][0]['class_flag'] == 1){
                    $('#type1').find('.job-list').find('span').text(responses['olo'][0]['offer_title']);
                    $('#type1').find('.jobs-info').find('span').first().text((responses['user']['total_served_case'] == null?'0':responses['user']['total_served_case']) );
                    $('#type1').find('.jobs-info').next('div').html(responses['olo'][0]['offer_title']+': <span class="text-danger">'+(responses['min_food_price'][0]['min_price'] == null ?'0':responses['min_food_price'][0]['min_price'])+'</span>元 / 起');
                    $('#type1').find('.intro').text((responses['olo'][0]['offer_description'] == null) ?'簡介：':'簡介：'+responses['olo'][0]['offer_description']);
                    $('#type1').find('.btnarr').find('a.btn-warning').attr("olo_id", $this.attr('id'));
                    $('#type1').find('.btnarr').find('a.btn-warning').addClass("food_hire");
                    $('#type1').find('.btnarr').find('a.btn-success').attr("olo_id", $this.attr('id'));
                    $('#type1').find('.btnarr').find('a.btn-success').addClass("food_hire");

                    var str ='';
                    if(responses['license_imgs'].length > 0){
                        responses['license_imgs'].forEach(function(element) {
                            str+= '<a href="{{URL::to("/")}}/license_img/small/'+element.img+'" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">\n' +
                                '            <img src="{{URL::to("/")}}/license_img/small/'+element.img+'" class="img-fluid">\n' +
                                '            <div class="pic-dis">證照 </div>\n' +
                                '        </a>'
                        });
                    }
                    if(responses['foods'].length > 0){
                        responses['foods'].forEach(function(element) {
                            str+= '<a href="{{URL::to("/")}}/img/small/'+element.img+'" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">\n' +
                                '            <img src="{{URL::to("/")}}/img/small/'+element.img+'" class="img-fluid">\n' +
                                '            <div class="pic-dis">'+element.title+'<br><span class="text-danger">'+element.price+'</span>元 / 件</div>\n' +
                                '        </a>'
                        });
                    }
                    $('#type1').find('.fix').html(str);
                }else if(responses['olo'][0]['class_flag'] == 2){
                    if(responses['olo'][0]['price_type'] == '報價'){
                        $('#type2').find('.jobs-item').html(responses['olo'][0]['offer_title']+':'+(responses['olo'][0]['price_type'] == null ? '' : ' 依'+responses['olo'][0]['price_type']));
                    }else{
                        $('#type2').find('.jobs-item').html(responses['olo'][0]['offer_title']+': <span class="text-danger">'+(responses['olo'][0]['price'] == null ? '0':responses['olo'][0]['price'])+'</span>元 / '+(responses['olo'][0]['price_type'] == null ? '' : responses['olo'][0]['price_type']));
                    }

                    $('#type2').find('.intro').text((responses['olo'][0]['offer_description'] == null) ? '簡介：':'簡介：'+responses['olo'][0]['offer_description']);
                    $('#type2').find('.btnarr').find('a.btn-warning').attr("olo_id", $this.attr('id'));
                    $('#type2').find('.btnarr').find('a.btn-warning').addClass("design_hire");
                    $('#type2').find('.btnarr').find('a.btn-success').attr("olo_id", $this.attr('id'));
                    $('#type2').find('.btnarr').find('a.btn-success').addClass("design_hire");
                    var str ='';
                    if(responses['license_imgs'].length > 0){
                        responses['license_imgs'].forEach(function(element) {
                            str+= '<a href="{{URL::to("/")}}/license_img/small/'+element.img+'" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">\n' +
                                '            <img src="{{URL::to("/")}}/license_img/small/'+element.img+'" class="img-fluid">\n' +
                                '            <div class="pic-dis">證照 </div>\n' +
                                '        </a>'
                        });
                    }
                    if(responses['imgs'].length > 0){
                        responses['imgs'].forEach(function(element) {
                            str+= '<a href="{{URL::to("/")}}/img/small/'+element.img+'" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">\n' +
                                '            <img src="{{URL::to("/")}}/img/small/'+element.img+'" class="img-fluid">\n' +
                                '            <div class="pic-dis">作品 </div>\n' +
                                '        </a>'
                        });
                    }
                    if(responses['videos'].length > 0){
                        responses['videos'].forEach(function(element) {
                            str+= '<a href="'+element.url+'" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">\n' +
                                '            <img src="http://i1.ytimg.com/vi/pmW2af_BaRk/mqdefault.jpg" class="img-fluid">\n' +
                                '            <div class="pic-dis">影片 </div>\n' +
                                '        </a>'
                        });
                    }
                    $('#type2').find('.fix').html(str);
                }
            }
        });
    });
  });
	 $(function () {
      $('.cy-arrb input').on('change', function () {
        var $this = $('.cy-arrb input:checked')
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
    $("a.close-side,a.hmbt").on('click', function () {
      $('.map-left').removeClass('active');
    });
    $(function () {$('#number-picker,#number').hide();
	if ($('input#hour,input#hourb,input#hourc').is(':checked')) { $('#number-picker,#number').show();}
    $('#hour,#hourb,#hourc').on('change', function () {
      $('#number-picker,#number').show();
    });
    $('#day,#dayb,#dayc').on('change', function () {
      $('#number-picker,#number').hide();
    });});
    $('.third').hide();
    $('#third').on('change', function () {
      $('.third').show();
      $('.twice').hide();
    });
    $('#twice').on('change', function () {
      $('.twice').show();
      $('.third').hide();
    });
     $('#starta').on('change', function () {
	var sta = $('#starta').val();
   $('#enda').val(100-sta);
   });
    $('#startb,#secondb').on('change', function () {
	var stb = parseInt($('#startb').val());
	var secb = parseInt($('#secondb').val());
	var ttb = stb+secb;
   $('#endb').val(100-ttb);
   });
   $(function(){
 $("#all-type0,#all-type1,#all-type2").click(function () {
   $(this).siblings('input:checkbox').not(this).prop('checked', this.checked);
 }); });
</script>
@endsection
@stop
