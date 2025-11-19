<?php
// create_tables.php
// Run this script to create the database and tables

require_once __DIR__ . '/vendor/autoload.php';

use think\facade\Db;

try {
    // 1. 创建数据库 (jf_blog)
    echo "Creating database 'jf_blog'...\n";
    Db::query('CREATE DATABASE IF NOT EXISTS jf_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
    echo "Database 'jf_blog' created or already exists.\n";

    // 2. 选择数据库并执行建表语句
    $sqlStatements = [
        "USE jf_blog;",

        "CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `email` VARCHAR(100) NULL UNIQUE,
            `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `categories` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL UNIQUE,
            `slug` VARCHAR(100) NOT NULL UNIQUE,
            `description` TEXT NULL,
            `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `tags` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL UNIQUE,
            `slug` VARCHAR(100) NOT NULL UNIQUE,
            `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `posts` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `slug` VARCHAR(255) NULL UNIQUE,
            `summary` TEXT NULL,
            `content` LONGTEXT NOT NULL,
            `status` ENUM('draft', 'published') DEFAULT 'draft',
            `category_id` INT NULL,
            `view_count` INT DEFAULT 0,
            `seo_keywords` TEXT NULL,
            `seo_description` TEXT NULL,
            `published_at` TIMESTAMP NULL,
            `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `post_tag` (
            `post_id` INT NOT NULL,
            `tag_id` INT NOT NULL,
            PRIMARY KEY (`post_id`, `tag_id`),
            FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE IF NOT EXISTS `configs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `key` VARCHAR(100) NOT NULL UNIQUE,
            `value` TEXT NOT NULL,
            `description` TEXT NULL,
            `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    ];

    echo "Creating tables...\n";
    foreach ($sqlStatements as $sql) {
        Db::query($sql);
        echo "Executed: " . substr($sql, 0, 50) . "...\n";
    }
    echo "Tables created successfully.\n";

    // 3. 插入初始管理员用户 (用户名: admin, 密码: 123456 - 需要加密)
    $username = 'admin';
    $password = password_hash('123456', PASSWORD_DEFAULT); // 使用 PHP 内置加密
    $email = 'admin@example.com';

    $existingUser = Db::name('users')->where('username', $username)->find();
    if (!$existingUser) {
        Db::name('users')->insert([
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        echo "Initial admin user 'admin' (password: 123456) created.\n";
    } else {
        echo "Admin user 'admin' already exists.\n";
    }

    // 4. 插入初始配置项
    $initialConfigs = [
        ['key' => 'blog_name', 'value' => 'JF-Blog', 'description' => '博客名称'],
        ['key' => 'blog_subtitle', 'value' => '一个简洁的个人博客', 'description' => '博客副标题'],
        ['key' => 'blog_logo', 'value' => '', 'description' => '博客 LOGO 路径'],
        ['key' => 'blog_keywords', 'value' => '博客, JF-Blog, 技术', 'description' => '博客关键词'],
        ['key' => 'blog_description', 'value' => '欢迎来到我的个人博客 JF-Blog！', 'description' => '博客描述'],
        ['key' => 'posts_per_page', 'value' => '10', 'description' => '每页显示文章数量'],
    ];

    foreach ($initialConfigs as $config) {
        $existingConfig = Db::name('configs')->where('key', $config['key'])->find();
        if (!$existingConfig) {
            Db::name('configs')->insert($config);
        }
    }
    echo "Initial configurations set.\n";

    echo "\nDatabase setup completed successfully!\n";
    echo "Admin login credentials:\n";
    echo "Username: admin\n";
    echo "Password: 123456\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}