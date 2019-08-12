<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'WebController@index');
Route::get('/login', function (){ return view('/login'); });
Route::get('/forgot', function (){ return view('/forgot'); });
Route::get('/signup', function (){ return view('/signup'); });
Route::get('/adm_login', function (){ return view('admin/adm_login'); });
Route::get('/error', function (){ return view('admin/error', array('message' => '很抱歉，系統已登出，請重新登入喔!')); });

Route::get('/veri_mail', 'WebController@veri_mail');
Route::get('/set_forgot_passwd', 'WebController@set_forgot_passwd');
Route::get('/term_of_use', 'WebController@term_of_use');
Route::get('/how2help_post_list', 'WebController@how2help_post_list');
Route::any('/newebPay_return_url', 'WebController@newebPay_return_url');
Route::post('/newebPay_notify_url', 'WebController@newebPay_notify_url');
Route::any('/newebPay_customer_url', 'WebController@newebPay_customer_url');
Route::any('/newebPay_back_url', 'WebController@newebPay_back_url');
Route::any('/newebPay_creditCancel_url', 'WebController@newebPay_creditCancel_url');

Route::get('/test', 'WebController@test');

Route::post('/set_latlng', 'ApiController@set_latlng');

Route::prefix('web')->group(function () {

	//Route::get( 'login', 'WebController@login' );
	Route::post( 'signup_pt', 'WebController@signup_pt' );
	Route::get( 'logout', 'WebController@logout' );
	Route::post( 'login_pt', 'WebController@login_pt' );
	Route::post( 'register_pt', 'WebController@register_pt' );
    Route::post( 'set_forgot_passwd_pt', 'WebController@set_forgot_passwd_pt' );
	Route::get( 'profile', 'WebController@profile' );

    Route::get( 'map', 'FrontController@map' );
	Route::get( 'list', 'FrontController@list' );

	Route::get('helper_detail/{u_id}/{distance}', 'FrontController@helper_detail');
	Route::get('einvoice_info', 'FrontController@einvoice_info');
	Route::get('collection_info', 'FrontController@collection_info');
	Route::get('set_notify', 'FrontController@set_notify');

	Route::any('certification', 'FrontController@certification');
	Route::get('job_detail/{u_id}/{distance}', 'FrontController@job_detail');
	Route::get('h_set', 'FrontController@h_set');
});

Route::prefix('admin')->group(function () {

	Route::get( 'owner', 'AdminController@owner' );
	Route::post( 'owner_pt', 'AdminController@owner_pt' );
	Route::get( 'users', 'AdminController@users' );
	Route::get( 'get_users', 'AdminController@get_users' );
	Route::post( 'users_pt', 'AdminController@users_pt' );
	Route::get( 'get_owner', 'AdminController@get_owner' );
	Route::get( 'managers', 'AdminController@managers' );
	Route::get( 'get_managers', 'AdminController@get_managers' );
	Route::post( 'adm_login_pt', 'AdminController@adm_login_pt' );
	Route::get( 'logout', 'AdminController@adm_logout' );
	Route::get( 'chk_account_repeat', 'AdminController@chk_account_repeat' );
	Route::post( 'managers_pt', 'AdminController@managers_pt' );
	Route::get( 'groups', 'AdminController@groups' );
	Route::post( 'groups_pt', 'AdminController@groups_pt' );
	Route::get( 'get_groups', 'AdminController@get_groups' );
	Route::get( 'accountings', 'AdminController@accountings' );
	Route::get( 'get_accountings', 'AdminController@get_accountings' );
	Route::post( 'accountings_pt', 'AdminController@accountings_pt' );
	Route::get( 'settings', 'AdminController@settings' );
	Route::get( 'get_system', 'AdminController@get_system' );
	Route::get( 'get_settings', 'AdminController@get_settings' );
	Route::post( 'settings_pt', 'AdminController@settings_pt' );
	Route::get( 'videos', 'AdminController@videos' );
	Route::post( 'videos_pt', 'AdminController@videos_pt' );
	Route::get( 'get_videos', 'AdminController@get_videos' );
	Route::get( 'marketings', 'AdminController@marketings' );
	Route::post( 'marketings_pt', 'AdminController@marketings_pt' );
	Route::get( 'get_marketings', 'AdminController@get_marketings' );
	Route::get( 'email_get_account', 'AdminController@email_get_account' );
	Route::get( 'transfer_records', 'AdminController@transfer_records' );
	Route::post( 'transfer_records_pt', 'AdminController@transfer_records_pt' );
	Route::get( 'get_transfer_records', 'AdminController@get_transfer_records' );


});

Route::prefix('api')->group(function () {

	Route::get( 'chk_mail', 'ApiController@chk_mail' );
	Route::get( 'get_forgot_passwd', 'ApiController@get_forgot_passwd' );
	Route::get( 'get_profile', 'ApiController@get_profile' );
	Route::get( 'set_veri_mail', 'ApiController@set_veri_mail' );
	Route::get( 'is_existed', 'ApiController@is_existed' );
	Route::get( 'get_index', 'ApiController@get_index' );
	Route::get( 'get_newebPay_info_code', 'ApiController@get_newebPay_info_code' );

	Route::post('search_offer', 'AjaxController@search_offer');
	Route::post('search_offer_list', 'AjaxController@search_offer_list');
	Route::post('set_need', 'AjaxController@set_need');
	Route::post('set_invoice', 'AjaxController@set_invoice');
	Route::post('del_invoice', 'AjaxController@del_invoice');
	Route::post('set_bank', 'AjaxController@set_bank');
	Route::post('del_bank', 'AjaxController@del_bank');
	Route::post('set_notify', 'AjaxController@set_notify');
	Route::post('change', 'AjaxController@change');

	Route::post('set_helper', 'AjaxController@set_helper');
	Route::post('get_olo', 'AjaxController@get_olo');
	Route::post('add_olo', 'AjaxController@add_olo');
	Route::post('set_olo', 'AjaxController@set_olo');
});

Route::get('home/big/{filename}', array(function ($filename)
{
    $path = storage_path('files/pic/home/photoBig/'.$filename);

    if (File::exists($path)) {
        $file = new Symfony\Component\HttpFoundation\File\File($path);
        $type = $file->getMimeType();
        header('Content-Type:'.$type);
        header('Content-Length: ' . filesize($path));
        readfile($path);
    }
}));

Route::get('home/small/{filename}', array(function ($filename)
{
    $path = storage_path('files/pic/home/photoSmall/'.$filename);

    if (File::exists($path)) {
        $file = new Symfony\Component\HttpFoundation\File\File($path);
        $type = $file->getMimeType();
        header('Content-Type:'.$type);
        header('Content-Length: ' . filesize($path));
        readfile($path);
    }
}));


Route::get('avatar/big/{filename}', function ($filename)
{
    $path = storage_path('files/pic/avatar/photoBig/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::get('avatar/small/{filename}', function ($filename)
{
    $path = storage_path('files/pic/avatar/photoSmall/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::get('license_img/big/{filename}', function ($filename)
{
    $path = storage_path('files/pic/license_img/photoBig/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::get('license_img/small/{filename}', function ($filename)
{
    $path = storage_path('files/pic/license_img/photoSmall/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::get('img/big/{filename}', function ($filename)
{
    $path = storage_path('files/pic/img/photoBig/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::get('img/small/{filename}', function ($filename)
{
    $path = storage_path('files/pic/img/photoSmall/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});
