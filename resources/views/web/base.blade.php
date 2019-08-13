<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="">
    <meta property=og:type content="website" />
    <meta property=fb:app_id content="">
    <meta property=og:image content="">
    <meta property=og:image:url content="">
    <meta property=og:image:secure_url content="">
    <meta property=og:url content="">
    <meta property=og:site_name content="">
    <meta property=og:title content="">
    <meta property=og:description content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel=canonical href="">
    <link rel="icon" href="{{asset("/favicon.ico")}}">
    <link rel="stylesheet" href="{{asset("/css/bootstrap.min.css")}}">
    <link rel="stylesheet" href="{{asset("/css/font-awesome.min.css")}}">
    <link rel="stylesheet" href="{{asset("/css/swiper.min.css")}}">
    <link rel="stylesheet" href="{{asset("/css/style.css")}}">
    <link rel="stylesheet" href="{{asset("/css/slick-theme.css")}}">
    <link rel="stylesheet" href="{{asset("/css/slick.css")}}">
    <title>幫棒 您的好幫手</title>
    <!--<link href="/css/app.css" rel="stylesheet">-->
    <script src="{{asset("/js/app.js")}}"></script>
    @yield('myStyle')
</head>

<body>
    @include('layout.header')

    @yield('content')

    @include('layout.footer')
</body>

<script>
    $(window).on('load', function () {
        $(".se-pre-con").fadeOut("slow");
    });
</script>
<script src="{{asset("/js/popper.min.js")}}"></script>
<script src="{{asset("/js/bootstrap.min.js")}}"></script>
<script src="{{asset("/js/jquery.tinyMap.min.js")}}"></script>
<script src="{{asset("/js/position.js")}}"></script>
<script src="{{asset("/js/main.js")}}"></script>

<script>
    $('#head-change').on('click', function () {
        $.ajax({
            type: "post",
            url: "{{url('/api/change')}}",
            dataType: "json",
            data: {
                _token: '{{csrf_token()}}'
            },
            success: function (response) {
                location.reload();
            }
        });
    })
</script>

@yield('myScript')

</html>

