<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
// //chuyển hướng người dùng đến dashboard
// Route::get('dashboard', 'DashboardController@show')->middleware('auth');
// //hệ thống vào admin tự động chuyển hướng vào dashboard
// Route::get('admin', 'DashboardController@show');
// //hiển thị ds quản trị hệ thống
// Route::get('admin/user/list', 'AdminUserController@list');
// //thêm user hệ thống
// Route::get('admin/user/add', 'AdminUserController@add');
// Route::post('admin/user/store', 'AdminUserController@store');

// xử lý lỗi truy cập khi chưa login 
Route::middleware('auth')->group(function () {
    Route::get('dashboard', 'DashboardController@show');
    Route::get('admin', 'DashboardController@show');
    Route::get('admin/user/list', 'AdminUserController@list');
    Route::get('admin/user/add', 'AdminUserController@add');
    Route::post('admin/user/store', 'AdminUserController@store');
    //thiết lập trang xóa user trong ht
    Route::get('admin/user/delete/{id}', 'AdminUserController@delete')->name('delete_user');
    Route::get('admin/user/action', 'AdminUserController@action');
    // cập nhật tt người dùng 
    Route::get('admin/user/edit/{id}', 'AdminUserController@edit')->name('user.edit');
    Route::get('admin/user/update/{id}', 'AdminUserController@update')->name('user.update');
});
