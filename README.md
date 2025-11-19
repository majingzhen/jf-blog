# JF-Blog

一个基于 ThinkPHP 8.x 构建的个人博客系统，支持 Markdown、分类、标签、SEO、系统配置等功能，并集成了 AI Editor 富文本编辑器。

## 功能特性

- **用户管理**: 后台管理员登录、登出
- **文章管理**: 创建、编辑、发布/草稿文章，支持 Markdown/富文本编辑 (AI Editor)
- **分类与标签**: 文章分类和多标签管理
- **SEO 优化**: 页面标题、关键词、描述，Open Graph 和 Twitter Card 支持
- **系统配置**: 博客名称、LOGO、关键词、描述等全局设置
- **前端展示**: 首页、文章详情、分类页、标签页、归档页

## 环境要求

- PHP >= 8.0
- MySQL >= 5.7
- Composer

## 安装步骤

1.  **克隆或下载项目**:
    ```bash
    git clone <your_repo_url> jf-blog
    cd jf-blog
    ```

2.  **安装依赖**:
    ```bash

    composer install
    ```

3.  **配置环境**:
    - 复制 `.example.env` 为 `.env`
    - 编辑 `.env` 文件，配置数据库连接信息 (`DB_NAME`, `DB_USER`, `DB_PASS` 等)

4.  **初始化数据库**:
    - **重要**: 确保 MySQL 服务已启动，且 `.env` 中的数据库凭据正确。
    - 运行数据库初始化命令（需要先解决数据库连接问题）：
      ```bash
      php think init:db
      ```
      这将创建 `jf_blog` 数据库、数据表，并插入初始管理员用户（用户名: `admin`, 密码: `123456`）和基本配置。

5.  **配置 Web 服务器**:
    - 将 Web 服务器的文档根目录指向 `public` 目录
    - 配置 URL 重写规则 (如 Apache 的 `.htaccess` 已提供，Nginx 需自行配置)

6.  **访问**:
    - 访问 `http://<your-domain>/` 查看博客前台
    - 访问 `http://<your-domain>/admin/login` 进入后台管理

## AI Editor 集成

- AI Editor 的静态文件 (CSS, JS) 需要手动下载并放置在 `public/static/aieditor/` 目录下。
- 请参考 `public/static/aieditor/README.md` 获取文件来源信息。

## 安全注意事项

- 初始管理员密码为 `123456`，请登录后立即修改。
- 生产环境请确保 `.env` 文件不被 Web 访问。
- 定期更新 ThinkPHP 框架和依赖库。

## 许可证

[请在此处添加您的许可证信息，例如 Apache 2.0 或 MIT]