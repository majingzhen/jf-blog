<?php
// app/admin/controller/BaseAdminController.php

namespace app\admin\controller;

use app\admin\BaseController;
use think\facade\Session;
use think\facade\View;

class BaseAdminController extends BaseController
{
    protected $adminUser = null;

    public function initialize()
    {
        parent::initialize();
        // 检查用户是否已登录
        $this->checkLogin();
        // 可以在这里添加其他后台通用逻辑
    }

    protected function checkLogin()
    {
        $adminUserId = Session::get('admin_user_id');
        dump($adminUserId);
        if (!$adminUserId) {
            // 如果未登录，重定向到登录页
            redirect(url('/admin/login'))->send();
            exit;
        }

        // 获取用户信息并存储
        $this->adminUser = \app\model\User::find($adminUserId);
        if (!$this->adminUser) {
            // 如果Session中的用户ID无效，也重定向到登录页
            Session::delete('admin_user_id');
            redirect(url('/admin/login'))->send();
            exit;
        }

        // 将用户信息传递给视图
        View::assign('adminUser', $this->adminUser);
    }
}