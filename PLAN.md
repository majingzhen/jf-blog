# JF-Blog 开发计划

## 总体目标
在 ThinkPHP 框架下构建一个功能完善的个人博客系统，支持 Markdown 编辑、分类标签管理、SEO 优化和系统配置。

## 开发阶段

### 阶段一：项目初始化与基础设置 (Day 1)
1.  **环境准备**:
    - 确保本地开发环境 (PHP 8.0+, MySQL, Composer) 齐备。
    - (如果未完成) 使用 Composer 安装 ThinkPHP 8.x 骨架项目。
    - 初始化 Git 仓库。
2.  **项目结构**:
    - 确认并熟悉 ThinkPHP 项目的基本目录结构 (app, config, route, public, etc.)。
    - 创建基本的控制器目录结构 (admin, index)。
    - 创建模型目录结构。
3.  **数据库**:
    - 根据设计文档创建数据库 (`jf_blog`)。
    - 使用 ThinkPHP 迁移工具或手动创建基础数据表 (`users`, `categories`, `tags`, `posts`, `post_tag`, `configs`)。
    - 插入初始管理员用户数据 (密码需加密)。
4.  **配置文件**:
    - 创建 `.env` 文件，配置数据库连接参数。
    - 在 `config/` 目录下创建必要的配置文件 (database, app, route, etc.)。
    - 配置 URL 重写规则 (`.htaccess` 或 Nginx)。

### 阶段二：核心后端逻辑开发 (Day 2-4)
1.  **模型创建**:
    - 创建与数据库表对应的 ThinkPHP 模型 (User, Category, Tag, Post, Config)。
    - 定义模型之间的关联关系 (Post hasOne Category, Post belongsToMany Tag, etc.)。
    - 添加基础的验证规则。
2.  **用户认证模块 (Admin)**:
    - 实现登录控制器 (`app/admin/controller/Login.php`) 和视图。
    - 使用 ThinkPHP 的 Session 或 JWT (较复杂) 实现用户会话管理。
    - 实现中间件进行登录验证 (`app/middleware/AuthMiddleware.php`)。
    - 实现登出功能。
3.  **文章管理模块 (Admin)**:
    - 创建文章控制器 (`app/admin/controller/Post.php`)。
    - 实现文章列表页 (分页、搜索、筛选)。
    - 实现文章创建/编辑页 (暂时使用简单的 textarea，后续集成 AI Editor)。
    - 实现文章保存逻辑 (包括分类、标签的关联)。
    - 实现文章删除/恢复 (软删除)。
4.  **分类与标签管理模块 (Admin)**:
    - 创建分类控制器 (`app/admin/controller/Category.php`) 和标签控制器 (`app/admin/controller/Tag.php`)。
    - 实现列表、创建、编辑、删除功能。
5.  **系统配置模块 (Admin)**:
    - 创建配置控制器 (`app/admin/controller/Config.php`)。
    - 实现配置列表、编辑功能 (表单提交到数据库 `configs` 表)。
    - 创建配置读取服务类 (`app/service/ConfigService.php`) 以便在其他地方方便地获取配置项。

### 阶段三：前端展示与 SEO 优化 (Day 5-6)
1.  **前端控制器**:
    - 创建前端控制器 (如 `app/index/controller/Index.php`, `Post.php`, `Category.php`, `Tag.php`)。
    - 实现首页、文章详情页、分类列表页、标签列表页的路由和基础逻辑。
2.  **前端视图**:
    - 设计并创建前端视图文件 (HTML + CSS/Bootstrap)。
    - 集成基础的前端页面布局 (Header, Footer)。
    - 展示文章列表、分类列表、标签列表、文章详情。
3.  **SEO 优化**:
    - 在控制器中动态设置页面的 `title`, `meta keywords`, `meta description`。
    - 在视图中正确输出这些 Meta 标签。
    - 考虑添加 Open Graph / Twitter Card Meta 标签。

### 阶段四：AI Editor 集成与文件上传 (Day 7)
1.  **AI Editor 集成**:
    - 下载或通过 CDN 引入 AI Editor 的 JavaScript/CSS 文件到 `public/static/`。
    - 在文章创建/编辑页 (`admin/post/add`, `admin/post/edit`) 引入 AI Editor。
    - 配置 AI Editor，使其内容能正确提交到后端。
    - 确认后端如何处理 AI Editor 提交的内容 (Markdown 还是 HTML)。
2.  **文件上传**:
    - 实现 Logo 文件上传功能 (在 `Config` 控制器中)。
    - 实现文章内容图片上传功能 (通常通过编辑器 API)。
    - 添加上传文件的验证 (类型、大小) 和安全检查。
    - 配置上传文件的存储路径。

### 阶段五：完善与测试 (Day 8)
1.  **功能完善**:
    - 实现文章页面浏览次数统计 (可选)。
    - 实现归档页面 (按年月列出文章)。
    - 根据需要添加搜索功能。
2.  **前端优化**:
    - 优化前端页面的响应式设计。
    - 添加简单的 JavaScript 增强交互 (如确认删除对话框)。
3.  **后端优化**:
    - 添加日志记录。
    - 完善输入验证和错误处理。
    - 对关键功能进行单元测试 (可选，但推荐)。
4.  **安全加固**:
    - 检查并修复潜在的安全漏洞 (XSS, CSRF, SQL Injection)。
    - 确保敏感信息 (如数据库密码) 不被硬编码。

### 阶段六：部署准备 (Day 9)
1.  **生产环境配置**:
    - 准备生产环境的 `.env` 配置文件。
    - 检查 `config/` 中的配置是否适合生产环境。
2.  **代码审查与优化**:
    - 通读代码，优化结构和性能。
3.  **文档整理**:
    - 更新 README.md，包含安装、配置、使用说明。
4.  **打包**:
    - 准备部署包或部署脚本。

## 开发工具与资源
- PHP IDE (如 PhpStorm, VSCode with PHP extensions)
- 数据库管理工具 (如 phpMyAdmin, Navicat)
- API 测试工具 (如 Postman) - 如有 API 接口
- ThinkPHP 官方文档: https://doc.thinkphp.cn/
- AI Editor 文档: https://www.aieditor.com.cn/docs/zh/what-is-ai-editor.html

## 风险与注意事项
- **AI Editor 集成**: 仔细阅读文档，确保其与 ThinkPHP 表单提交方式兼容。
- **文件上传安全**: 严格验证上传文件类型，防止恶意文件上传。
- **数据库迁移**: 使用迁移工具管理数据库变更，便于版本控制。
- **性能**: 对文章列表等频繁访问的页面考虑缓存策略 (可选，后期优化)。

---