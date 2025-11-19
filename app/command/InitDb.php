<?php
// app/command/InitDb.php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Db;

class InitDb extends Command
{
    protected function configure()
    {
        $this->setName('init:db')
             ->setDescription('Initialize database and tables for JF-Blog');
    }

    protected function execute(Input $input, Output $output)
    {
        try {
            // 1. 创建数据库 (jf_blog)
            $output->writeln("Creating database 'jf_blog'...");
            Db::query('CREATE DATABASE IF NOT EXISTS jf_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
            $output->writeln("Database 'jf_blog' created or already exists.");

            // 2. 选择数据库并执行建表语句 (USE 语句可能不适用于所有连接，直接在表名前加库名)
            $sqlStatements = [
                "CREATE TABLE IF NOT EXISTS `jf_blog`.`users` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `username` VARCHAR(50) NOT NULL UNIQUE,
                    `password` VARCHAR(255) NOT NULL,
                    `email` VARCHAR(100) NULL UNIQUE,
                    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                "CREATE TABLE IF NOT EXISTS `jf_blog`.`categories` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(100) NOT NULL UNIQUE,
                    `slug` VARCHAR(100) NOT NULL UNIQUE,
                    `description` TEXT NULL,
                    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                "CREATE TABLE IF NOT EXISTS `jf_blog`.`tags` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(100) NOT NULL UNIQUE,
                    `slug` VARCHAR(100) NOT NULL UNIQUE,
                    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                "CREATE TABLE IF NOT EXISTS `jf_blog`.`posts` (
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
                    FOREIGN KEY (`category_id`) REFERENCES `jf_blog`.`categories`(`id`) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                "CREATE TABLE IF NOT EXISTS `jf_blog`.`post_tag` (
                    `post_id` INT NOT NULL,
                    `tag_id` INT NOT NULL,
                    PRIMARY KEY (`post_id`, `tag_id`),
                    FOREIGN KEY (`post_id`) REFERENCES `jf_blog`.`posts`(`id`) ON DELETE CASCADE,
                    FOREIGN KEY (`tag_id`) REFERENCES `jf_blog`.`tags`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                "CREATE TABLE IF NOT EXISTS `jf_blog`.`configs` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `key` VARCHAR(100) NOT NULL UNIQUE,
                    `value` TEXT NOT NULL,
                    `description` TEXT NULL,
                    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
            ];

            $output->writeln("Creating tables...");
            foreach ($sqlStatements as $sql) {
                Db::query($sql);
                $output->writeln("Executed: " . substr($sql, 0, 50) . "...");
            }
            $output->writeln("Tables created successfully.");

            // 3. 插入初始管理员用户 (用户名: admin, 密码: 123456 - 需要加密)
            $username = 'admin';
            $password = password_hash('123456', PASSWORD_DEFAULT); // 使用 PHP 内置加密
            $email = 'admin@example.com';

            $existingUser = Db::name('jf_blog.users')->where('username', $username)->find();
            if (!$existingUser) {
                Db::name('jf_blog.users')->insert([
                    'username' => $username,
                    'password' => $password,
                    'email' => $email,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $output->writeln("Initial admin user 'admin' (password: 123456) created.");
            } else {
                $output->writeln("Admin user 'admin' already exists.");
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
                $existingConfig = Db::name('jf_blog.configs')->where('key', $config['key'])->find();
                if (!$existingConfig) {
                    Db::name('jf_blog.configs')->insert($config);
                }
            }
            $output->writeln("Initial configurations set.");

            $output->writeln("\nDatabase setup completed successfully!");
            $output->writeln("Admin login credentials:");
            $output->writeln("Username: admin");
            $output->writeln("Password: 123456");

        } catch (Exception $e) {
            $output->writeln("Error: " . $e->getMessage());
        }
    }
}