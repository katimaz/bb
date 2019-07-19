@extends('web.base')
@section('content')

<main role="main" class="container" style="max-width:500px; margin-top:200px;">
  <div class="jumbotron pl-5">
    <h2>喔喔 錯誤了 !!</h2>
    <hr />
    <p class="lead">{{ $message }}</p>
    @if(isset($goUrl) && $goUrl)
    <a class="btn btn-lg btn-primary mt-5" href="{{ url($goUrl.((isset($mode))?'?mode='.$mode:'').((isset($id))?'&id='.$id:'')) }}" role="button">返回前頁</a>
    @endif
  </div>
</main>
    
@stop