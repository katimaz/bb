@extends('web.base')
@section('content')
<div class="item-title">使用者條款</div>
<div class="pro-box mx-auto py-4" style="max-width:1200px; letter-spacing:1px; line-height:140%">
     {!! nl2br($term_of_use) !!} 
</div>        
@if(isset($back))
	<div class="w-100 py-4 text-center"><a href="{{(($back==1)?'/web/profile':'/')}}" class="btn btn-success">閱畢返回</a></div>
@endif
@stop