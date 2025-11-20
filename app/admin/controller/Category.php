<?php
// app/admin/controller/Category.php

namespace app\admin\controller;

use app\model\Category as CategoryModel;
use think\facade\View;
use think\facade\Request;

class Category extends BaseAdminController
{
    public function index()
    {
        // 分类列表页
        $categories = CategoryModel::order('created_at', 'desc')->select();
        $title = '分类管理 - JF-Blog 后台';

        View::assign([
            'title' => $title,
            'categories' => $categories
        ]);

        return View::fetch('admin/category/index');
    }

    public function create()
    {
        // 创建分类页
        $title = '新建分类 - JF-Blog 后台';
        View::assign('title', $title);
        return View::fetch('admin/category/create');
    }

    public function edit($id)
    {
        // 编辑分类页
        $category = CategoryModel::find($id);
        if (!$category) {
            return redirect(url('/admin/category/index'))->with('error', '分类不存在');
        }

        $title = '编辑分类 - JF-Blog 后台';
        View::assign([
            'title' => $title,
            'category' => $category
        ]);

        return View::fetch('/admin/category/edit');
    }

    public function save()
    {
        // 保存分类 (创建或更新)
        $data = Request::post();
        $categoryId = $data['id'] ?? null;

        // 验证规则
        $validateRules = [
            'name|分类名称' => 'require|max:100',
            'slug|分类别名' => 'require|alphaDash|max:100|unique:categories,slug,' . ($categoryId ? $categoryId : '0'),
            'description|分类描述' => 'max:500',
        ];

        $validate = new \think\Validate($validateRules);
        if (!$validate->check($data)) {
            return redirect(url('/admin/category/index'))->with('error', $validate->getError());
        }

        if ($categoryId) {
            // 更新
            $category = CategoryModel::find($categoryId);
            if (!$category) {
                return redirect(url('/admin/category/index'))->with('error', '分类不存在');
            }
            $category->save([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description']
            ]);
            return redirect(url('/admin/category/index'));
        } else {
            // 创建
            $category = new CategoryModel();
            $category->save([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description']
            ]);
            return redirect(url('/admin/category/index'));
        }
    }

    public function delete($id)
    {
        // 删除分类
        $category = CategoryModel::find($id);
        if (!$category) {
            return redirect(url('/admin/category/index'))->with('error', '分类不存在');
        }

        // 检查该分类下是否有文章，如果有，不允许删除或先处理文章
        $postCount = \app\model\Post::where('category_id', $id)->count();
        if ($postCount > 0) {
            return redirect(url('/admin/category/index'))->with('error', '该分类下有文章，无法删除。请先将文章移至其他分类或删除文章。');
        }

        $category->delete();
        return redirect(url('/admin/category/index'));
    }
}