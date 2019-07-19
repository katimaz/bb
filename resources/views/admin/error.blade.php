<!doctype html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="/css/app.css" rel="stylesheet">
    <title>錯誤頁面</title>
  </head>
  <body>
    <main role="main" class="container">
      <div class="jumbotron w-50 mx-auto pl-4 mt-5" style="min-width:380px">
        <h2>喔喔 錯誤了 !!</h2>
        <p class="lead">{{ ((isset($message))?$message:'') }}</p>
        <a class="btn btn-lg btn-primary mt-5" href="/adm_login" role="button">重新登入</a>
      </div>
    </main>
  </body>
</html>