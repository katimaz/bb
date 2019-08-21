@extends('web.base')
@section('content')
<div class="item-title">我的夥伴團隊</div>
<div id="app" class="container">
  <div class="row">
    <div class="col-md-12 mt-5">
      <div class="list-box best-bg">
        <div class="list-left hide-m"></div>
        <div class="list-right">
        	<div class="list-name hide-m"></div>
          	<div  class="list-comm">
            	<a href="#" class="sbtn">評價 <i class="fa fa-long-arrow-down" aria-hidden="true"></i></a>
            </div>
            <div class="list-ds">
            	<a href="#"  class="sbtn">地點 <i class="fa fa-long-arrow-down" aria-hidden="true"></i></a>
            </div>
            <div class="list-dl">
            	<a href="#"  class="sbtn">受雇次數 <i class="fa fa-long-arrow-down" aria-hidden="true"></i></a>
            </div >
            <div class="list-dl">
            	<a href="#"  class="sbtn">工作時數 <i class="fa fa-long-arrow-down" aria-hidden="true"></i></a>
            </div >
          	<div class="list-dl"><a href="#" class="sbtn">服務項目</a></div>
        	<div class="list-types  hide-m"></div >
            <div class="list-hire  hide-m"></div>
        </div>
      </div>

      <div class="list-box" v-for="(partner,index) in partners">
      	<div class="list-left">
        	<span class="b-face">
        		<img :src="((partner.usr_photo)?'{{asset('/avatar/big')}}/'+partner.usr_photo:'{{asset('/images/person-icon.jpg')}}')">
            </span>
        </div>
        <a href="javascript:void(0)" class="list-right">
        	<div class="list-name" v-text="((partner.last_name&&partner.first_name)?partner.last_name+''+partner.first_name:'未設姓名')"></div>
            <!--<div  class="list-comm">
            	<span class="list-start">
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                </span>
                <span class="avg">4.9</span>
            </div>-->
            <div class="list-ds">
            	<span class="show-m">地點：</span>
                @{{partner.city+''+partner.nat}}
            </div>
            <div class="list-dl">
            	<span class="show-m">受雇次數：</span>
                @{{((partner.total_served_case)?partner.total_served_case:0)}}次
            </div >
            <div class="list-dl">
            	<span class="show-m">工作時數：</span>
                @{{((partner.total_served_hours)?partner.total_served_hours:0)}}/小時
            </div >
           	<div class="list-types pl-x">
            	<span class="show-m">服務項目：</span>
                水電工程,居家清理,電腦維修,電腦教學...
            </div>
            <div class="list-bt">
                <a href="#" class="lask" data-toggle="modal" data-target="#exampleModal" >推薦</a>
            </div>
          </a>
      </div>
      <!--<div class="more"><button class="btn ">更多</button></div>-->
    </div>
  </div>
</div>
<script>
new Vue({
	el: "#app",
	data: {
	  partners: ''
	},
	mounted: function () {
		var self = this;
		axios.get('/api/get_partners').then(function (response){
			console.log(response.data);
			if(response.data=='error')
				alert('喔喔!錯誤了喔')
			else
			{
				self.partners = response.data.partners;
			}
		})
	},
	methods: {
		copyline: function(){
			  
		}
	
	}
  
})
</script>
@stop