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
        if (!$file->checkMime(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            return json(['success' => false, 'message' => '上传的文件类型不正确']);
        }
        if ($file->getSize() > 5 * 1024 * 1024) { // 5MB 限制
            return json(['success' => false, 'message' => '上传的文件大小不能超过 5MB']);
        }

        try {
            // 保存文件到指定目录
            $savename = Filesystem::putFile('articles', $file, 'uniqid'); // 保存到 public/upload/articles/
            if ($savename) {
                $filepath = '/upload/' . $savename;
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
}