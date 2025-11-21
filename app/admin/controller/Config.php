<?php
// app/admin/controller/Config.php

namespace app\admin\controller;

use app\model\Config as ConfigModel;
use think\facade\View;
use think\facade\Request;

class Config extends BaseAdminController
{
    public function index()
    {
        // 系统配置页
        $configs = ConfigModel::where('key', 'in', ['blog_name', 'blog_subtitle', 'blog_logo', 'blog_keywords', 'blog_description', 'posts_per_page'])->select();
        $configMap = [];
        foreach ($configs as $config) {
            $configMap[$config['key']] = $config['value'];
        }

        $title = '系统配置 - JF-Blog 后台';

        return View::fetch('config/index', ['title' => $title, 'config' => $configMap]);
    }

    public function save()
    {
        // 保存系统配置
        $data = Request::post();
        $file = Request::file('blog_logo');

        $allowedKeys = ['blog_name', 'blog_subtitle', 'blog_keywords', 'blog_description', 'posts_per_page'];
        $errors = [];

        // 处理文件上传
        $logoPath = $data['current_blog_logo'] ?? ''; // 默认使用当前路径
        if ($file && $file->isValid()) {
            // 验证文件类型和大小
            if (!$file->checkMime(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                $errors[] = '上传的文件类型不正确，仅支持 JPG、PNG、GIF、WebP 格式。';
            } elseif ($file->getSize() > 2 * 1024 * 1024) { // 2MB 限制
                $errors[] = '上传的文件大小不能超过 2MB。';
            } else {
                // 移动文件到指定目录
                $saveName = \think\facade\Filesystem::putFile('logo', $file, 'uniqid');
                if ($saveName) {
                    $logoPath = '/upload/' . $saveName; // 假设文件系统配置了正确的 public 访问路径
                    // 如果有旧的 Logo 文件，可以考虑删除（可选）
                    $oldLogoPath = $data['current_blog_logo'] ?? '';
                    if ($oldLogoPath && file_exists('.' . $oldLogoPath)) {
                        unlink('.' . $oldLogoPath);
                    }
                } else {
                    $errors[] = 'Logo 文件上传失败。';
                }
            }
        }

        if (empty($errors)) {
            // 保存其他配置项
            foreach ($allowedKeys as $key) {
                if (isset($data[$key])) {
                    $value = is_array($data[$key]) ? json_encode($data[$key]) : trim($data[$key]);
                    $this->updateOrCreateConfig($key, $value);
                }
            }

            // 保存 Logo 路径
            if ($logoPath !== null) {
                $this->updateOrCreateConfig('blog_logo', $logoPath);
            }

            return redirect(url('/admin/config/index'));
        } else {
            return redirect(url('/admin/config/index'))->with('error', implode('<br>', $errors));
        }
    }

    private function updateOrCreateConfig($key, $value)
    {
        $existingConfig = ConfigModel::where('key', $key)->find();
        if ($existingConfig) {
            $existingConfig->value = $value;
            $existingConfig->save();
        } else {
            ConfigModel::create([
                'key' => $key,
                'value' => $value,
                'description' => $this->getDescriptionForConfigKey($key)
            ]);
        }
    }

    private function getDescriptionForConfigKey($key)
    {
        $descriptions = [
            'blog_name' => '博客名称',
            'blog_subtitle' => '博客副标题',
            'blog_logo' => '博客 LOGO 路径',
            'blog_keywords' => '博客关键词',
            'blog_description' => '博客描述',
            'posts_per_page' => '每页显示文章数量',
        ];
        return $descriptions[$key] ?? $key;
    }
}