<?php
// app/controller/admin/Post.php

namespace app\controller\admin;

use app\model\Post as PostModel;
use app\model\Category;
use app\model\Tag;
use think\facade\View;
use think\facade\Request;

class Post extends BaseAdminController
{
    public function index()
    {
        // 文章列表页
        $page = Request::param('page', 1);
        $posts = PostModel::with('category')->order('created_at', 'desc')->paginate(10, false, ['page' => $page]);
        $title = '文章管理 - JF-Blog 后台';

        return view('index_post', ['posts' => $posts, 'title' => $title]);
    }

    public function create()
    {
        // 创建文章页
        $title = '新建文章 - JF-Blog 后台';
        $categories = Category::select();
        $tags = Tag::select(); // 简单起见，先加载所有标签

        View::assign([
            'title' => $title,
            'categories' => $categories,
            'tags' => $tags
        ]);

        return View::fetch('create');
    }

    public function edit($id)
    {
        // 编辑文章页
        $post = PostModel::with('tags')->find($id);
        if (!$post) {
            return redirect(url('admin.Post/index'))->with('error', '文章不存在');
        }

        $title = '编辑文章 - JF-Blog 后台';
        $categories = Category::select();
        $allTags = Tag::select();
        $selectedTagIds = $post->tags->column('id'); // 获取当前文章已选的标签ID

        View::assign([
            'title' => $title,
            'post' => $post,
            'categories' => $categories,
            'allTags' => $allTags,
            'selectedTagIds' => $selectedTagIds
        ]);

        return View::fetch('edit');
    }

    public function save()
    {
        // 保存文章 (创建或更新)
        $data = Request::post();
        $postId = $data['id'] ?? null;

        // 验证和处理数据 (简化处理)
        $validateRules = [
            'title|标题' => 'require|max:255',
            'summary|摘要' => 'max:500',
            'content|内容' => 'require',
            'category_id|分类' => 'require|integer',
            'status|状态' => 'in:draft,published',
        ];

        $validate = new \think\Validate($validateRules);
        if (!$validate->check($data)) {
            return redirect(url('admin.Post/index'))->with('error', $validate->getError());
        }

        if ($postId) {
            // 更新
            $post = PostModel::find($postId);
            if (!$post) {
                return redirect(url('admin.Post/index'))->with('error', '文章不存在');
            }
            $post->save([
                'title' => $data['title'],
                'slug' => $data['slug'] ?? null, // 可选
                'summary' => $data['summary'] ?? null,
                'content' => $data['content'],
                'category_id' => $data['category_id'],
                'status' => $data['status'] ?? 'draft',
                'seo_keywords' => $data['seo_keywords'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
                'published_at' => $data['status'] === 'published' && !$post->published_at ? date('Y-m-d H:i:s') : $post->published_at,
            ]);

            // 同步标签
            if (isset($data['tag_ids']) && is_array($data['tag_ids'])) {
                $post->tags()->sync($data['tag_ids']);
            } else {
                $post->tags()->detach(); // 如果未选择标签，则清空关联
            }

            return redirect(url('admin.Post/index'));
        } else {
            // 创建
            $post = new PostModel();
            $post->save([
                'title' => $data['title'],
                'slug' => $data['slug'] ?? null,
                'summary' => $data['summary'] ?? null,
                'content' => $data['content'],
                'category_id' => $data['category_id'],
                'status' => $data['status'] ?? 'draft',
                'seo_keywords' => $data['seo_keywords'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
                'published_at' => $data['status'] === 'published' ? date('Y-m-d H:i:s') : null, // 发布时设置发布时间
            ]);

            // 同步标签
            if (isset($data['tag_ids']) && is_array($data['tag_ids'])) {
                $post->tags()->attach($data['tag_ids']);
            }

            return redirect(url('admin.Post/index'));
        }
    }

    public function delete($id)
    {
        // 删除文章 (软删除或真删除，这里用真删除做示例)
        $post = PostModel::find($id);
        if (!$post) {
            return redirect(url('admin.Post/index'))->with('error', '文章不存在');
        }

        // 删除关联的标签关系
        $post->tags()->detach();

        $post->delete();
        return redirect(url('admin.Post/index'));
    }
}