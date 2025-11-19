# JF-Blog 设计文档

## 项目概述

JF-Blog 是一个基于 ThinkPHP 框架构建的个人博客系统，旨在为用户提供一个简洁、高效且功能丰富的博客平台。项目支持 Markdown 格式的文章撰写、分类与标签管理、SEO 优化、系统配置以及使用 AI Editor 作为富文本编辑器。

## 技术栈

- **后端框架**: ThinkPHP 8.x
- **数据库**: MySQL 8.0+
- **前端编辑器**: [AI Editor](https://www.aieditor.com.cn/docs/zh/what-is-ai-editor.html) (支持 Markdown 和富文本)
- **前端样式**: Bootstrap 5.x 或 Tailwind CSS (前端展示可选)
- **构建工具**: (可选，如需要复杂构建流程)
- **其他**: Composer (PHP 包管理)

## 核心功能模块

### 1. 用户与权限模块 (Admin)

- **用户认证**: 登录、登出、密码修改
- **后台管理**: 访问控制，权限验证 (目前仅管理员)

### 2. 文章管理模块 (Admin)

- **文章列表**: 分页展示所有文章，支持搜索、按状态/分类/标签筛选
- **文章创建/编辑**:
  - 标题
  - 摘要
  - 内容 (使用 AI Editor)
  - 发布状态 (草稿/发布)
  - 分类 (单选)
  - 标签 (多选)
  - 发布时间
  - SEO 关键字 (keywords)
  - SEO 描述 (description)
  - 自定义 URL slug (可选)
- **文章删除/恢复** (软删除)
- **文章预览** (在后台或新窗口中)

### 3. 分类模块 (Admin)

- **分类列表**: 显示所有分类
- **添加/编辑/删除分类**:
  - 分类名称
  - 分类描述
  - 分类别名/URL slug (用于生成 URL)

### 4. 标签模块 (Admin)

- **标签列表**: 显示所有标签
- **添加/编辑/删除标签**:
  - 标签名称
  - 标签别名/URL slug (用于生成 URL)

### 5. 系统配置模块 (Admin)

- **基础配置**:
  - 博客名称
  - 博客副标题/描述
  - 博客 LOGO (文件上传)
  - 博客关键词 (keywords)
  - 博客描述 (description)
- **SEO 配置**:
  - 默认 SEO 标题格式
  - 默认 SEO 描述格式
  - 默认 SEO 关键词格式
- **其他配置**:
  - 每页显示文章数量
  - 评论开关 (可扩展)

### 6. 前端展示模块 (Frontend)

- **首页**: 分页展示文章列表，支持按分类/标签筛选
- **分类页面**: 展示特定分类下的文章列表
- **标签页面**: 展示特定标签下的文章列表
- **文章详情页**: 显示文章标题、摘要、内容、发布时间、分类、标签、SEO 信息
- **归档页面**: 按年/月归档文章列表
- **RSS/Atom 订阅**: (可选) 提供文章订阅功能
- **搜索功能**: (可选) 搜索文章标题和内容

## 数据库设计

### `users` (用户表)

- `id` (INT, PK, AI)
- `username` (VARCHAR(50), NOT NULL, UNIQUE) - 用户名
- `password` (VARCHAR(255), NOT NULL) - 加密密码
- `email` (VARCHAR(100), UNIQUE) - 邮箱 (可选)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### `categories` (分类表)

- `id` (INT, PK, AI)
- `name` (VARCHAR(100), NOT NULL, UNIQUE) - 分类名称
- `slug` (VARCHAR(100), NOT NULL, UNIQUE) - 分类 URL 别名
- `description` (TEXT, NULL) - 分类描述
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### `tags` (标签表)

- `id` (INT, PK, AI)
- `name` (VARCHAR(100), NOT NULL, UNIQUE) - 标签名称
- `slug` (VARCHAR(100), NOT NULL, UNIQUE) - 标签 URL 别名
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### `posts` (文章表)

- `id` (INT, PK, AI)
- `title` (VARCHAR(255), NOT NULL) - 文章标题
- `slug` (VARCHAR(255), UNIQUE) - 文章 URL 别名 (可选，若未提供则根据标题生成)
- `summary` (TEXT, NULL) - 文章摘要
- `content` (LONGTEXT, NOT NULL) - 文章内容 (Markdown 或 HTML，由编辑器决定)
- `status` (ENUM('draft', 'published'), DEFAULT 'draft') - 文章状态
- `category_id` (INT, FK) - 关联分类
- `view_count` (INT, DEFAULT 0) - 浏览次数 (可选，后期统计)
- `seo_keywords` (TEXT, NULL) - SEO 关键词
- `seo_description` (TEXT, NULL) - SEO 描述
- `published_at` (TIMESTAMP, NULL) - 发布时间
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### `post_tag` (文章-标签关联表)

- `post_id` (INT, FK, PK)
- `tag_id` (INT, FK, PK)

### `configs` (系统配置表)

- `id` (INT, PK, AI)
- `key` (VARCHAR(100), NOT NULL, UNIQUE) - 配置键，如 'blog_name', 'blog_logo'
- `value` (TEXT, NOT NULL) - 配置值 (JSON 格式存储复杂配置)
- `description` (TEXT, NULL) - 配置描述
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

## 前端路由设计 (参考)

- **后端 (Admin)**
  - `admin/login` - 登录
  - `admin/logout` - 登出
  - `admin/dashboard` - 仪表盘
  - `admin/posts` - 文章列表
  - `admin/posts/create` - 创建文章
  - `admin/posts/edit/{id}` - 编辑文章
  - `admin/categories` - 分类列表
  - `admin/categories/create` - 创建分类
  - `admin/categories/edit/{id}` - 编辑分类
  - `admin/tags` - 标签列表
  - `admin/tags/create` - 创建标签
  - `admin/tags/edit/{id}` - 编辑标签
  - `admin/config` - 系统配置

- **前端 (Frontend)**
  - `/` - 首页
  - `/post/{slug}` - 文章详情页
  - `/category/{slug}` - 分类页面
  - `/tag/{slug}` - 标签页面
  - `/archive` - 归档页面

## SEO 优化策略

- **Meta 标签**: 为首页、分类页、标签页、文章页生成合适的 `title`, `meta keywords`, `meta description`。
- **URL 结构**: 使用友好的 URL 结构，如 `/post/my-first-post`, `/category/tech`, `/tag/php`。
- **Open Graph / Twitter Cards**: 为分享时提供预览信息。
- **Sitemap**: (可选) 生成 XML 网站地图提交给搜索引擎。
- **Robots.txt**: 控制搜索引擎爬虫行为。

## 文件上传与管理

- **Logo**: 限制文件类型和大小，存储到 `public/upload/logo/` 目录。
- **文章内容图片**: AI Editor 上传的图片应有独立的路径，如 `public/upload/articles/`，并考虑 CDN 配置。

## 安全考虑

- **输入验证**: 严格验证所有用户输入 (标题、内容、配置等)，防止 XSS。
- **密码安全**: 使用 ThinkPHP 内置的加密方法存储密码。
- **SQL 注入**: 使用 ORM 的查询方法，避免直接拼接 SQL。
- **CSRF 保护**: 在表单中加入 CSRF Token。

## AI Editor 集成

- 在文章编辑页面引入 AI Editor JavaScript 库。
- 配置编辑器以默认使用 Markdown 模式，但允许切换到富文本。
- 将编辑器内容提交到后端时，应能正确接收 Markdown 或 HTML 格式 (根据后端处理逻辑决定)。

## 依赖管理

- 使用 `composer.json` 管理 PHP 依赖。
- 安装 ThinkPHP 框架核心包。
- 可能需要安装一些辅助包，如 `guzzlehttp/guzzle` (HTTP 请求), `league/commonmark` (Markdown 解析，如果需要自解析)。

## 部署

- 配置 `.env` 文件管理环境变量 (数据库连接、应用密钥等)。
- 设置 `public/index.php` 为 Web 服务器入口。
- 配置 URL 重写规则 (`.htaccess` for Apache, Nginx config for Nginx)。
- 遵循 ThinkPHP 部署最佳实践。

---