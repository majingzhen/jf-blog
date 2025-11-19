<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

// 前台默认路由
Route::get('think', function () {
    return 'hello,ThinkPHP8!';
});

Route::get('hello/:name', 'index/hello');

// 前台路由
Route::get('/', 'index.Index/index');
Route::get('/post/:slug', 'index.Index/post');
Route::get('/category/:slug', 'index.Index/category');
Route::get('/tag/:slug', 'index.Index/tag');
Route::get('/archive', 'index.Index/archive');

// 后台路由组，带 /admin 前缀
Route::group('admin', function () {
    // 登录相关路由
    Route::get('/login', 'admin.Login/index');
    Route::post('/login', 'admin.Login/doLogin');
    Route::get('/logout', 'admin.Login/logout');

    // 需要登录验证的路由组 (继承 BaseAdminController 的控制器)
    Route::group(function () {
        // 后台首页
        Route::get('/', 'admin.Index/index');
        Route::get('/index', 'admin.Index/index');

        // 后台管理路由
        Route::get('/posts', 'admin.Post/index');
        Route::get('/posts/create', 'admin.Post/create');
        Route::post('/posts', 'admin.Post/save');
        Route::get('/posts/:id/edit', 'admin.Post/edit');
        Route::post('/posts/:id', 'admin.Post/save'); // 更新
        Route::delete('/posts/:id', 'admin.Post/delete'); // 删除

        Route::get('/categories', 'admin.Category/index');
        Route::get('/categories/create', 'admin.Category/create');
        Route::post('/categories', 'admin.Category/save');
        Route::get('/categories/:id/edit', 'admin.Category/edit');
        Route::post('/categories/:id', 'admin.Category/save'); // 更新
        Route::delete('/categories/:id', 'admin.Category/delete'); // 删除

        Route::get('/tags', 'admin.Tag/index');
        Route::get('/tags/create', 'admin.Tag/create');
        Route::post('/tags', 'admin.Tag/save');
        Route::get('/tags/:id/edit', 'admin.Tag/edit');
        Route::post('/tags/:id', 'admin.Tag/save'); // 更新
        Route::delete('/tags/:id', 'admin.Tag/delete'); // 删除

        Route::get('/config', 'admin.Config/index');
        Route::post('/config', 'admin.Config/save');

        // 上传接口
        Route::post('/upload/image', 'admin.Upload/image');
    }); // 登录验证由 BaseAdminController 处理
});
