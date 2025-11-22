<?php
// app/admin/controller/Upload.php

namespace app\admin\controller;

use think\facade\Request;
use think\facade\Filesystem;

class Upload extends BaseAdminController
{
    public function image()
    {
        // 处理图片上传 (例如 AI Editor 的图片上传)
        $file = Request::file('image'); // 假设 AI Editor 发送的字段名是 'image'

        if (!$file || !$file->isValid()) {
            return json(['success' => false, 'message' => '文件上传失败或无效']);
        }

        // 验证文件类型和大小
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $originalMime = $file->getOriginalMime();
        $actualMime = $file->getMime(); // 获取真实 MIME 类型

        // 检查原始 MIME 类型和实际 MIME 类型是否都在允许范围内
        if (!in_array($originalMime, $allowedMimes) || !in_array($actualMime, $allowedMimes)) {
            return json(['success' => false, 'message' => '上传的文件类型不正确']);
        }

        // 也可以额外检查文件扩展名
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower($file->extension());
        if (!in_array($extension, $allowedExtensions)) {
            return json(['success' => false, 'message' => '上传的文件类型不正确']);
        }

        if ($file->getSize() > 5 * 1024 * 1024) { // 5MB 限制
            return json(['success' => false, 'message' => '上传的文件大小不能超过 5MB']);
        }

        try {
            // 移动文件到 public/upload/articles 目录 directly
            $fileName = 'articles/' . date('Y-m') . '/' . uniqid() . '.' . $file->extension();
            $uploadDir = app()->getRootPath() . 'public/upload/articles/' . date('Y-m');

            // Create directory if not exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if ($file->move($uploadDir, $fileName)) {
                $filepath = '/upload/' . $fileName;
                // 返回 AI Editor 期望的格式
                // 注意：具体返回格式取决于 AI Editor 的文档
                return json([
                    'success' => true,
                    'url' => $filepath,
                    'message' => '上传成功'
                ]);
            } else {
                return json(['success' => false, 'message' => '文件保存失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '上传过程中发生错误: ' . $e->getMessage()]);
        }
    }

    public function logo()
    {
        // 处理 Logo 上传 (用于系统配置中的 Logo 上传)
        $file = Request::file('logo'); // Logo 上传的字段名

        if (!$file || !$file->isValid()) {
            return json(['success' => false, 'message' => '文件上传失败或无效']);
        }

        // 验证文件类型和大小 (Logo 通常有更严格的限制)
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $originalMime = $file->getOriginalMime();
        $actualMime = $file->getMime(); // 获取真实 MIME 类型

        // 检查原始 MIME 类型和实际 MIME 类型是否都在允许范围内
        if (!in_array($originalMime, $allowedMimes) || !in_array($actualMime, $allowedMimes)) {
            return json(['success' => false, 'message' => '上传的文件类型不正确']);
        }

        // 也可以额外检查文件扩展名
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower($file->extension());
        if (!in_array($extension, $allowedExtensions)) {
            return json(['success' => false, 'message' => '上传的文件类型不正确']);
        }

        if ($file->getSize() > 2 * 1024 * 1024) { // 2MB 限制 for logos
            return json(['success' => false, 'message' => '上传的文件大小不能超过 2MB']);
        }

        try {
            // 移动文件到 public/upload/logo 目录 directly
            $fileName = 'logo/' . date('Y-m') . '/' . uniqid() . '.' . $file->extension();
            $uploadDir = app()->getRootPath() . 'public/upload/logo/' . date('Y-m');

            // Create directory if not exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if ($file->move($uploadDir, $fileName)) {
                $filepath = '/upload/' . $fileName;
                return json([
                    'success' => true,
                    'url' => $filepath,
                    'message' => '上传成功'
                ]);
            } else {
                return json(['success' => false, 'message' => '文件保存失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '上传过程中发生错误: ' . $e->getMessage()]);
        }
    }
}