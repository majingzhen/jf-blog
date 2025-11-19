<?php
// app/controller/admin/Login.php

namespace app\controller\admin;

use app\BaseController;
use app\model\User;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;

class Login extends BaseController
{
    public function index()
    {
        // 如果已登录，直接跳转到后台首页
        if (Session::get('admin_user_id')) {
            return redirect(url('admin.Index/index'));
        }

        // 显示登录表单
        return View::fetch('admin/login');
    }

    public function doLogin()
    {
        $username = Request::param('username');
        $password = Request::param('password');


        if (!$username || !$password) {
            // 传递错误信息到视图
            View::assign('error', '用户名和密码不能为空');
            return View::fetch('login');
        }

        // 查找用户
        $user = User::where('username', $username)->find();
        if (!$user || !password_verify($password, $user->password)) {
            View::assign('error', '用户名或密码错误');
            return View::fetch('admin/login');
        }

        // 登录成功，保存用户ID到Session
        Session::set('admin_user_id', $user->id);

        return redirect(url('/admin'));
    }

    public function logout()
    {
        Session::delete('admin_user_id');
        return redirect(url('admin.Login/index'));
    }
}