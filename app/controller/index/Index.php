<?php
// app/controller/index/Index.php

namespace app\controller\index;

use app\BaseController;
use app\model\Post;
use app\model\Category;
use app\model\Tag;
use app\model\Config as ConfigModel;
use think\facade\View;
use think\facade\Request;

class Index extends BaseController
{
    protected $config = [];

    public function initialize()
    {
        parent::initialize();
        // 加载系统配置
        $configs = ConfigModel::where('key', 'in', ['blog_name', 'blog_subtitle', 'blog_logo', 'blog_keywords', 'blog_description', 'posts_per_page'])->select();
        $this->config = [];
        foreach ($configs as $config) {
            $this->config[$config['key']] = $config['value'];
        }
        View::assign('config', $this->config);
    }

    public function index()
    {
        // 前台首页 - 展示文章列表
        $page = Request::param('page', 1);
        $postsPerPage = (int)($this->config['posts_per_page'] ?? 10);
        $posts = Post::with('category')->where('status', 'published')->order('published_at', 'desc')->paginate($postsPerPage, false, ['page' => $page]);

        $title = $this->config['blog_name'] . ' - ' . $this->config['blog_subtitle'];
        $keywords = $this->config['blog_keywords'];
        $description = $this->config['blog_description'];

//        View::assign([
//            'title' => $title,
//            'keywords' => $keywords,
//            'description' => $description,
//            'posts' => $posts,
//        ]);
        View::assign('title', $title ?? '');
        View::assign('keywords', $keywords ?? '');
        View::assign('description', $description ?? '');
        View::assign('posts', $posts);

        return View::fetch('/index');
    }

    public function post($slug)
    {
        // 文章详情页
        $post = Post::with(['category', 'tags'])->where('slug', $slug)->where('status', 'published')->find();
        if (!$post) {
            // 可以返回 404 页面
            abort(404, '文章未找到');
        }

        // 增加浏览次数 (可选)
        // $post->setInc('view_count');

        $title = $post->title . ' - ' . $this->config['blog_name'];
        $keywords = $post->seo_keywords ?? $this->config['blog_keywords'];
        $description = $post->seo_description ?? $this->config['blog_description'];

        View::assign([
            'title' => $title,
            'keywords' => $keywords,
            'description' => $description,
            'post' => $post,
        ]);

        return View::fetch('post');
    }

    public function category($slug)
    {
        // 分类列表页
        $category = Category::where('slug', $slug)->find();
        if (!$category) {
            abort(404, '分类未找到');
        }

        $page = Request::param('page', 1);
        $postsPerPage = (int)($this->config['posts_per_page'] ?? 10);
        $posts = Post::with('category')->where('status', 'published')->where('category_id', $category->id)->order('published_at', 'desc')->paginate($postsPerPage, false, ['page' => $page]);

        $title = $category->name . ' - 分类 - ' . $this->config['blog_name'];
        $keywords = $category->name . ',' . $this->config['blog_keywords'];
        $description = $category->description ?? $this->config['blog_description'];

        View::assign([
            'title' => $title,
            'keywords' => $keywords,
            'description' => $description,
            'category' => $category,
            'posts' => $posts,
        ]);

        return View::fetch('category');
    }

    public function tag($slug)
    {
        // 标签列表页
        $tag = Tag::where('slug', $slug)->find();
        if (!$tag) {
            abort(404, '标签未找到');
        }

        $page = Request::param('page', 1);
        $postsPerPage = (int)($this->config['posts_per_page'] ?? 10);
        // 通过多对多关联查询文章
        $posts = $tag->posts()->with('category')->where('status', 'published')->order('published_at', 'desc')->paginate($postsPerPage, false, ['page' => $page]);

        $title = $tag->name . ' - 标签 - ' . $this->config['blog_name'];
        $keywords = $tag->name . ',' . $this->config['blog_keywords'];
        $description = '包含标签 ' . $tag->name . ' 的文章列表 - ' . $this->config['blog_description'];

        View::assign([
            'title' => $title,
            'keywords' => $keywords,
            'description' => $description,
            'tag' => $tag,
            'posts' => $posts,
        ]);

        return View::fetch('tag');
    }

    public function archive()
    {
        // 归档页面 - 按年月归档
        $posts = Post::where('status', 'published')->order('published_at', 'desc')->select();

        $archiveData = [];
        foreach ($posts as $post) {
            $yearMonth = date('Y-m', strtotime($post->published_at));
            $archiveData[$yearMonth][] = $post;
        }

        $title = '文章归档 - ' . $this->config['blog_name'];
        $keywords = '归档,' . $this->config['blog_keywords'];
        $description = '按时间归档的文章列表 - ' . $this->config['blog_description'];

        View::assign([
            'title' => $title,
            'keywords' => $keywords,
            'description' => $description,
            'archiveData' => $archiveData,
        ]);

        return View::fetch('archive');
    }
}