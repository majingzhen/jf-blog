<?php
// app/admin/controller/Tag.php

namespace app\admin\controller;

use app\model\Tag as TagModel;
use think\facade\View;
use think\facade\Request;

class Tag extends BaseAdminController
{
    public function index()
    {
        // 标签列表页
        $tags = TagModel::order('created_at', 'desc')->select();
        $title = '标签管理 - JF-Blog 后台';

        View::assign([
            'title' => $title,
            'tags' => $tags
        ]);

        return View::fetch('index');
    }

    public function create()
    {
        // 创建标签页
        $title = '新建标签 - JF-Blog 后台';
        View::assign('title', $title);
        return View::fetch('create');
    }

    public function edit($id)
    {
        // 编辑标签页
        $tag = TagModel::find($id);
        if (!$tag) {
            return redirect(url('admin.Tag/index'))->with('error', '标签不存在');
        }

        $title = '编辑标签 - JF-Blog 后台';
        View::assign([
            'title' => $title,
            'tag' => $tag
        ]);

        return View::fetch('edit');
    }

    public function save()
    {
        // 保存标签 (创建或更新)
        $data = Request::post();
        $tagId = $data['id'] ?? null;

        // 验证规则
        $validateRules = [
            'name|标签名称' => 'require|max:100',
            'slug|标签别名' => 'require|alphaDash|max:100|unique:tags,slug,' . ($tagId ? $tagId : '0'),
        ];

        $validate = new \think\Validate($validateRules);
        if (!$validate->check($data)) {
            return redirect(url('admin.Tag/index'))->with('error', $validate->getError());
        }

        if ($tagId) {
            // 更新
            $tag = TagModel::find($tagId);
            if (!$tag) {
                return redirect(url('admin.Tag/index'))->with('error', '标签不存在');
            }
            $tag->save([
                'name' => $data['name'],
                'slug' => $data['slug']
            ]);
            return redirect(url('admin.Tag/index'));
        } else {
            // 创建
            $tag = new TagModel();
            $tag->save([
                'name' => $data['name'],
                'slug' => $data['slug']
            ]);
            return redirect(url('admin.Tag/index'));
        }
    }

    public function delete($id)
    {
        // 删除标签
        $tag = TagModel::find($id);
        if (!$tag) {
            View::assign('error', '标签不存在');
            return View::fetch('error');
        }

        // 删除关联关系，再删除标签本身
        // 在 post_tag 表中删除所有关联此标签的记录
        \app\model\Post::whereHas('tags', function ($query) use ($id) {
            $query->where('tag_id', $id);
        })->detach($id);
        // 或者直接删除关联表记录
        // \think\facade\Db::name('post_tag')->where('tag_id', $id)->delete();

        $tag->delete();
        return redirect(url('admin.Tag/index'));
    }
}