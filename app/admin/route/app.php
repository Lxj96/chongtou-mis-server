<?php
/**
 * Description: 后端路由
 * File: app.php
 * User: Lxj
 * DateTime: 2022-03-25 18:22
 */

use think\facade\Route;

// admin/admin/Login/login
// 登录
Route::group('admin/Login', function () {
    Route::get('setting', 'setting');
    Route::post('login', 'login');
    Route::post('logout', 'logout');
    Route::get('refresh', 'refresh');
})->prefix('admin.Login/');

Route::group('admin', function () {
    // 菜单管理
    Route::group('menu', function () {
        Route::rest('delete', ['DELETE', '/', 'delete']);
        Route::get('role', 'admin.Menu/role');
        Route::post('roleRemove', 'admin.Menu/roleRemove');
        Route::get('user', 'admin.Menu/user');
        Route::post('userRemove', 'admin.Menu/userRemove');
        Route::resource('/', 'admin.Menu');
        Route::patch('pid', 'admin.Menu/pid');
        Route::patch('unlogin', 'admin.Menu/unlogin');
        Route::patch('unauth', 'admin.Menu/unauth');
        Route::patch('disable', 'admin.Menu/disable');
    });
    // 角色管理
    Route::group('role', function () {
        Route::rest('delete', ['DELETE', '/', 'delete']);
        Route::get('menu', 'admin.Role/menu');
        Route::get('user', 'admin.Role/user');
        Route::post('userRemove', 'admin.Role/userRemove');
        Route::resource('/', 'admin.Role');
        Route::patch('disable', 'admin.Role/disable');
    });
    // 用户管理
    Route::group('user', function () {
        Route::get('rule', 'admin.User/rule');
        Route::rest('delete', ['DELETE', '/', 'delete']);
        Route::resource('/', 'admin.User');
        Route::patch('super', 'admin.User/super');
        Route::patch('disable', 'admin.User/disable');
        Route::patch('pwd', 'admin.User/pwd');
        Route::patch('rule', 'admin.User/rule');
    });
    // 用户日志
    Route::group('userLog', function () {
        Route::get('/', 'admin.UserLog/index');
        Route::get('/:id', 'admin.UserLog/read');
        Route::delete('/', 'admin.UserLog/delete');
        Route::post('/clear', 'admin.UserLog/clear');
        Route::post('/stat', 'admin.UserLog/stat');
    });
    // 个人中心
    Route::group('userCenter', function () {
        Route::get('/', 'admin.UserCenter/index');
        Route::put('/', 'admin.UserCenter/update');
        Route::put('/pwd', 'admin.UserCenter/pwd');
        Route::get('/log', 'admin.UserCenter/log');
    });

});

Route::group('file', function () {
    // 文件列表
    Route::group('file', function () {
        Route::get('group', 'file.File/group');
        Route::rest('delete', ['DELETE', '/', 'delete']);
        Route::resource('/', 'file.File');
        Route::patch('disable', 'file.File/disable');
    });
    // 文件分组
    Route::group('group', function () {
        Route::rest('delete', ['DELETE', '/', 'delete']);
        Route::resource('/', 'file.Group');
        Route::patch('disable', 'file.Group/disable');
    });
    // 文件设置
    Route::group('setting', function () {
        Route::get('/', 'file.Setting/read');
        Route::put('/', 'file.Setting/update');
    });
});