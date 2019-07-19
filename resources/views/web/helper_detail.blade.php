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
        <div class="add-cellect" title="加入首選">
          <i class="fa fa-heart" aria-hidden="true"></i>
        </div>
        <div class="added">
          已加入首選
        </div>
        <div class="helper-box">
          <div class="h-face">
            <img src="/images/{{$user->usr_photo}}'">
          </div>
          <div class="helper-info">
            <span class="user-name">{{$user->last_name}}{{$user->first_name}}</span>
            <span class="start"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> </span>
            <span class="avg">4.9</span>
            <div class="income">
              地點：{{$olo[0]->mem_addr}}
            </div>
            <div class="feedback">距離： <span class="text-danger">{{$distance}}</span>公尺 </div>
            <div class="feedback">受雇次數：<span class="text-danger">150  </span>次 </div>
            <div class="feedback">總工作時數：<span class="text-danger">240 </span>小時 </div>
            <div class="feedback">身份認證已完成 <span class="text-success"><i class="fa fa-check-circle" aria-hidden="true"></i></span> </div>
            <div class="intro"><p>簡介：{{$user->personal_brief}}</p> </div>

            <div class="helper-jobs">
              <div class="jobs-tit">服務專區 <i class="fa fa-check-square-o" aria-hidden="true"></i> </div>
              <div class="form-group" id="toggle-select">
                <select class="form-control" id="select-tab">
                  <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type0" selected>全部</option>
                  @foreach($olo as $value)
                  <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type1">{{$value->offer_title}}</option>
                  @endforeach
                  <!-- <option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type2">學習</option><option data-toggle-select-hide=".type-arr" data-toggle-select-show="#type3">專業設計</option> -->
                </select>
              </div>
              <div class="type-arr active" id="type0">
                <div class="job-list">
                  @foreach($olo as $value)
                  <span>{{$value->offer_title}}</span>
                  @endforeach
                  <!-- <span>電腦教學</span><span>室內設計</span> -->
                </div>
                <div class="jobs-info">
                  受雇次數：<span class="text-danger">150</span>次
                  總工作時數：<span class="text-danger">240 </span> 小時
                </div>
                @foreach($olo as $value)
                <div class="jobs-item"> {{$value->offer_title}}：<span class="text-danger">{{$value->price}}</span>元 / {{$value->price_type}}起 </div>
                <div class="col text-center btnarr">
                  <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#example-food">雇用</a>
                  <a href="#" class=" btn btn-success" data-toggle="modal" data-target="#example-food">詢問</a>
                  <a href="re-helper.html" class=" btn btn-info">推薦</a>
                </div>
                <div class="intro">
                  <p>簡介：{{$value->offer_description}}</p>
                </div>
                <div class="row justify-content-center">
                  <div class="col-md-12 mt-2">
                    <div class="row fix">
                      <a href="images/food.jpg" data-toggle="lightbox" data-gallery="food" class="col-4 col-sm-3  mt-2  p-2">
                          <img src="images/food.jpg" class="img-fluid">
                          <div class="pic-dis">西湖醋魚<br><span class="text-danger">500</span>元 / 件 </div>
                      </a>
                    </div>
                  </div>
                </div>
                @endforeach
                <!-- <div class="jobs-item"> 電腦教學：<span class="text-danger">500</span>元 / 小時 </div>
                <div class="col text-center btnarr">
                  <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#exampleModalLong">雇用</a>
                  <a href="#" class=" btn btn-success" data-toggle="modal" data-target="#exampleModalLong">詢問</a>
                  <a href="re-helper.html" class=" btn btn btn-info">推薦</a>
                </div>
                <div class="intro"><p>簡介：用心經營、責任施工、品質保證 假日、夜間（下午五點後）施工因需提前備料務必提前預約。</p> </div>
                <div class="jobs-item">最高學歷：台北科技大學資工系 </div>
                <div class="row justify-content-center">
                  <div class="col-md-12 mt-2">
                    <div class="row fix">
                      <a href="images/works.jpg" data-toggle="lightbox" data-gallery="example-gallery" class="col-4 col-sm-3  mt-2  p-2">
                          <img src="images/works.jpg" class="img-fluid">
                          <div class="pic-dis">作品 </div>
                      </a>
                      <a href="images/20161005_R030.jpg" data-toggle="lightbox" data-gallery="example-gallery" class="col-4 col-sm-3  mt-2  p-2">
                          <img src="images/20161005_R030.jpg" class="img-fluid">
                            <div class="pic-dis">作品 </div>
                      </a>
                    </div>
                  </div>
                </div>
                <div class="jobs-item"> 室內設計：<span class="text-danger">15000</span>元/件起 </div>
                <div class="col text-center btnarr">
                  <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#example-design">雇用</a>
                  <a href="#" class=" btn btn-success" data-toggle="modal" data-target="#example-design">詢問</a>
                  <a href="re-helper.html" class=" btn btn btn-info">推薦</a>
                </div>
                <div class="intro"><p>簡介：用心經營、責任施工、品質保證 假日、夜間（下午五點後）施工因需提前備料務必提前預約。</p> </div>
                <div class="row justify-content-center">
                  <div class="col-md-12 mt-2">
                    <div class="row fix">
                      <a href="images/works.jpg" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">
                        <img src="images/works.jpg" class="img-fluid">
                        <div class="pic-dis">證照 </div>
                      </a>
                      <a href="http://youtu.be/pmW2af_BaRk" data-toggle="lightbox" data-gallery="design" class="col-4 col-sm-3  mt-2  p-2">
                        <img src="http://i1.ytimg.com/vi/pmW2af_BaRk/mqdefault.jpg" class="img-fluid">
                        <div class="pic-dis">影片 </div>
                      </a>
                    </div>
                  </div>
                </div> -->

                <div class="jobs-tit">服務評分 <i class="fa fa-star-o" aria-hidden="true"></i> </div>
                <div class="jobs-all"> 總體服務評分
                  <span class="start-all">
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <span class="avg">4.9</span>
                  </span>
                </div>
                <div class="start-box">
                  <div class="box-satrt">
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                  </div>
                  <div class="box-pros">
                    <div class="progress">
                      <div class="progress-bar bg-warning" role="progressbar" style="width: 95%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"> </div>
                    </div>
                  </div>
                  <div class="box-pres">95% </div>
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
                  <div class="box-pres">5% </div>
                </div>
                <div class="start-box">
                  <div class="box-satrt">
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                  </div>
                  <div class="box-pros">
                    <div class="progress">
                      <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"> </div>
                    </div>
                  </div>
                <div class="box-pres">0% </div>
              </div>
              <div class="start-box">
                <div class="box-satrt">
                  <i class="fa fa-star" aria-hidden="true"></i>
                  <i class="fa fa-star" aria-hidden="true"></i>
                </div>
                <div class="box-pros">
                  <div class="progress">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"> </div>
                  </div>
                </div>
              <div class="box-pres">0% </div>
            </div>
            <div class="start-box">
              <div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i> </div>
              <div class="box-pros">
                <div class="progress">
                  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"> </div>
                </div>
              </div>
            <div class="box-pres">0% </div>
          </div>

          <div class="comm">共有13則評價 <i class="fa fa-commenting-o" aria-hidden="true"></i> </div>
          <div class="comm-list">
            <div class="comm-face">
              <img src="images/face.jpg">
            </div>
            <div class="comm-info">
              <div class="comm-name">吳大偉</div >
              <div class="comm-date">2019/03/25 </div>
              <div class="comm-re">大推~~很專業很細心的服務 </div>
            </div>
          </div>
          <div class="comm-list">
            <div class="comm-face">
              <img src="images/face.jpg">
            </div>
            <div class="comm-info">
              <div class="comm-name">吳大偉</div >
              <div class="comm-date">2019/03/25 </div>
              <div class="comm-re">大推~~很專業很細心的服務 </div>
            </div>
          </div>
          <div class="more"> <button class="btn btn-sm btn-light">更多評價</button> </div>
        </div>

    <div class="type-arr" id="type1">
      <div class="job-list"> <span> 家常菜</span> </div>
      <div class="jobs-info">受雇次數：<span class="text-danger">115</span>次 </div>
      <div class="jobs-item"> 家常菜：<span class="text-danger">300</span>元 / 件起 </div>
      <div class="col text-center btnarr">
        <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#example-food">雇用</a>
        <a href="#" class=" btn btn-success" data-toggle="modal" data-target="#exampleModalLong">詢問</a>
        <a href="re-helper.html" class=" btn btn btn-info">推薦</a>
      </div>
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



  <div class="jobs-tit">服務評分 <i class="fa fa-star-o" aria-hidden="true"></i> </div>
  <div class="jobs-all"> 居家服務評分<span class="start-all"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> <span class="avg">4.9</span></span> </div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 95%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">95%

</div>


</div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 5%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">5%

</div>


</div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">0%

</div>


</div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">0%

</div>


</div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">0%

</div>


</div>

<div class="comm">共有6則評價 <i class="fa fa-commenting-o" aria-hidden="true"></i>

</div>
<div class="comm-list">
  <div class="comm-face"><img src="images/face.jpg">

  </div><div class="comm-info"><div class="comm-name">吳大偉</div ><div class="comm-date">2019/03/25

  </div><div class="comm-re">大推~~很專業很細心的服務

  </div>

  </div>

  </div>
  <div class="comm-list">
  <div class="comm-face"><img src="images/face.jpg">

  </div><div class="comm-info"><div class="comm-name">吳大偉</div ><div class="comm-date">2019/03/25

  </div><div class="comm-re">大推~~很專業很細心的服務

  </div>

  </div>

  </div>
 <div class="more"> <button class="btn btn-sm btn-light">更多評價</button>

 </div>


</div>

<div class="type-arr" id="type2">
            <div class="job-list"><span>電腦教學</span>

            </div>
            <div class="jobs-info">受雇次數：<span class="text-danger">20</span>次  工作時數：<span class="text-danger">30 </span> 小時

            </div>

              <div class="jobs-item"> 電腦教學：<span class="text-danger">500</span>元 / 小時

              </div>
              <div class="col text-center btnarr">
         <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#exampleModalLong">雇用</a>
         <a href="#" class=" btn btn-success" data-toggle="modal" data-target="#exampleModalLong">詢問</a>
         <a href="re-helper.html" class=" btn btn btn-info">推薦</a>


          </div>
              <div class="intro"><p>簡介：用心經營、責任施工、品質保證
假日、夜間（下午五點後）施工因需提前備料務必提前預約。</p>

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

      <div class="jobs-tit">服務評分 <i class="fa fa-star-o" aria-hidden="true"></i>

      </div>
      <div class="jobs-all"> 學習服務評分<span class="start-all"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> <span class="avg">4.9</span></span>

      </div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 95%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">95%

</div>


</div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 5%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">5%

</div>


</div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">0%

</div>


</div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">0%

</div>


</div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">0%

</div>


</div>

<div class="comm">共有7則評價 <i class="fa fa-commenting-o" aria-hidden="true"></i>

</div>
<div class="comm-list">
  <div class="comm-face"><img src="images/face.jpg">

  </div><div class="comm-info"><div class="comm-name">吳大偉</div ><div class="comm-date">2019/03/25

  </div><div class="comm-re">大推~~很專業很細心的服務

  </div>

  </div>

  </div>
  <div class="comm-list">
  <div class="comm-face"><img src="images/face.jpg">

  </div><div class="comm-info"><div class="comm-name">吳大偉</div ><div class="comm-date">2019/03/25

  </div><div class="comm-re">大推~~很專業很細心的服務

  </div>

  </div>

  </div>
 <div class="more"> <button class="btn btn-sm btn-light">更多評價</button>

 </div>


</div>
<div class="type-arr" id="type3">
            <div class="jobs-item"> 室內設計：<span class="text-danger">15000</span>元/件起

            </div>
    <div class="col text-center btnarr">
         <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#example-design">雇用</a>
         <a href="#" class=" btn btn-success" data-toggle="modal" data-target="#example-design">詢問</a>
         <a href="re-helper.html" class=" btn btn btn-info">推薦</a>


          </div>
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

      <div class="jobs-tit">服務評分 <i class="fa fa-star-o" aria-hidden="true"></i>

      </div>
      <div class="jobs-all"> 學習服務評分<span class="start-all"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i> <span class="avg">4.9</span></span>

      </div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 95%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">95%

</div>


</div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 5%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">5%

</div>


</div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">0%

</div>


</div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">0%

</div>


</div>
<div class="start-box">
<div class="box-satrt"><i class="fa fa-star" aria-hidden="true"></i>

</div><div class="box-pros"><div class="progress">
  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">

  </div>


</div>

</div><div class="box-pres">0%

</div>


</div>

<div class="comm">共有7則評價 <i class="fa fa-commenting-o" aria-hidden="true"></i>

</div>
<div class="comm-list">
  <div class="comm-face"><img src="images/face.jpg">

  </div><div class="comm-info"><div class="comm-name">吳大偉</div ><div class="comm-date">2019/03/25

  </div><div class="comm-re">大推~~很專業很細心的服務

  </div>

  </div>

  </div>
  <div class="comm-list">
  <div class="comm-face"><img src="images/face.jpg">

  </div><div class="comm-info"><div class="comm-name">吳大偉</div ><div class="comm-date">2019/03/25

  </div><div class="comm-re">大推~~很專業很細心的服務

  </div>

  </div>

  </div>
 <div class="more"> <button class="btn btn-sm btn-light">更多評價</button>

 </div>


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
          <p><span class="text-success"><i class="fa fa-check-square-o" aria-hidden="true"></i> 服務類別：</span>家常菜</p><p><span class="text-success"><i class="fa fa-adjust" aria-hidden="true"></i> 搜尋半徑：</span>10 公里</p>
          <div class="form-group" id="toggle-select">
            <label><i class="fa fa-map-marker" aria-hidden="true"></i> 服務地點：</label>
            <select class="form-control">
              <option  selected>台中市大里區大智路567號12樓</option>
             </select>


             </div>
    <p><span class="text-success">菜單：</p>
    <div class="f-menu">
    <div class="col">
    <h5>西湖醋魚 <span class="text-danger"> 500</span>元 / 件</h5>


      </div>
   <div style="width:120px">
        <input type="number" class="f-pic" value="0" min="0" max="10" />


      </div>

      </div>
       <div class="f-menu">
    <div class="col">
    <h5>宮堡雞丁 <span class="text-danger"> 500</span>元 / 件</h5>


      </div>
      <div style="width:120px">
        <input type="number" class="f-pic" value="0" min="0" max="10" />


      </div>

      </div>
       <div class="f-menu">
    <div class="col">
    <h5>開陽白菜 <span class="text-danger"> 500</span>元 / 件</h5>


      </div>
     <div style="width:120px">
        <input type="number" class="f-pic" value="0" min="0" max="10" />


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

          <p><span class="text-success"><i class="fa fa-check-square-o" aria-hidden="true"></i> 服務類別：</span>水電工程</p><p><span class="text-success"><i class="fa fa-adjust" aria-hidden="true"></i> 搜尋半徑：</span>10 公里</p>
     <div class="form-group" id="toggle-select">
            <label><i class="fa fa-map-marker" aria-hidden="true"></i> 服務地點：</label>
            <select class="form-control">
              <option  selected>台中市大里區大智路567號12樓</option>
             </select>


             </div>
            <div class="form-group">
              <label for="location"><i class="fa fa-usd" aria-hidden="true"></i> 預算</label>
              <input type="text" class="form-control" id="location" value="500" disabled>
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
          <p><span class="text-success"><i class="fa fa-check-square-o" aria-hidden="true"></i> 服務類別：</span>室內設計</p><p><span class="text-success"><i class="fa fa-adjust" aria-hidden="true"></i> 搜尋半徑：</span>10 公里</p>

          <div class="form-group" id="toggle-select">
            <label><i class="fa fa-map-marker" aria-hidden="true"></i> 服務地點：</label>
            <select class="form-control">
              <option  selected>台中市大里區大智路567號12樓</option>
             </select>


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

    $('#select-tab').on('change', function () {
      var $this = $('option:selected', this);
      var $hideElements = $($this.attr("data-toggle-select-hide"));
      var $showElements = $($this.attr("data-toggle-select-show"));

      $hideElements.slideUp();
      $showElements.slideDown();

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
 $("#all-type1,#all-type2,#all-type3").click(function () {
   $(this).siblings('input:checkbox').not(this).prop('checked', this.checked);
 }); });
</script>
@endsection
@stop