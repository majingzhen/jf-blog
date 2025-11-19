<?php
// app/controller/admin/Index.php

namespace app\controller\admin;

use app\model\Post as PostModel;
use think\facade\Request;
use think\facade\View;

class Index extends BaseAdminController
{
    public function index()
    {
        // 后台首页逻辑
        $title = '仪表盘 - JF-Blog 后台';
        View::assign('title', $title);
        // 文章列表页
        $page = Request::param('page', 1);
        $posts = PostModel::with('category')->order('created_at', 'desc')->paginate(10, false, ['page' => $page]);
        return view('admin/index', ['posts' => $posts, 'title' => $title]);
    }
}