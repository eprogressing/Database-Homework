SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE DATABASE IF NOT EXISTS `blog_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `blog_db`;


CREATE TABLE `admin` (
  `id` INT(100) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL,
  `password` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=2;


CREATE TABLE `comments` (
  `id` INT(100) NOT NULL AUTO_INCREMENT,
  `post_id` INT(100) NOT NULL,
  `admin_id` INT(100) NOT NULL,
  `user_id` INT(100) NOT NULL,
  `user_name` VARCHAR(50) NOT NULL,
  `comment` VARCHAR(1000) NOT NULL,
  `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `likes` (
  `id` INT(100) NOT NULL AUTO_INCREMENT,
  `user_id` INT(100) NOT NULL,
  `admin_id` INT(100) NOT NULL,
  `post_id` INT(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `posts` (
  `id` INT(100) NOT NULL AUTO_INCREMENT,
  `admin_id` INT(100) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `content` VARCHAR(10000) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `image` VARCHAR(100) NOT NULL,
  `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `users` (
  `id` INT(100) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `password` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



INSERT INTO `admin` (`id`, `name`, `password`) VALUES
(1, 'admin', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2');

COMMIT;


DELIMITER //

CREATE TRIGGER after_post_insert
AFTER INSERT ON posts
FOR EACH ROW
BEGIN
    -- 检查 admin 表中是否存在 total_posts 字段
    -- 如果不存在，则需要先通过 ALTER TABLE 语句添加该字段
    -- ALTER TABLE admin ADD COLUMN total_posts INT DEFAULT 0;

    UPDATE admin
    SET total_posts = total_posts + 1
    WHERE id = NEW.admin_id;
END //

DELIMITER ;

DELIMITER //

-- 存储过程：删除指定评论
CREATE PROCEDURE delete_comment_transaction(
    IN p_comment_id INT
)
BEGIN
    START TRANSACTION; -- 开启事务

    DELETE FROM comments
    WHERE id = p_comment_id;

    IF ROW_COUNT() > 0 THEN
        COMMIT; -- 提交事务
        SELECT '评论已成功删除' AS message;
    ELSE
        ROLLBACK; -- 回滚事务
        SELECT '未找到评论或删除失败' AS message;
    END IF;

END //

DELIMITER ;

-- 如何调用：
-- CALL delete_comment_transaction(123); -- 假设要删除ID为123的评论

-- SQL 代码：创建视图 `admin_posts_view`
-- 这个视图将文章信息与发布文章的管理员名称连接起来
CREATE VIEW admin_posts_view AS
SELECT
    p.id AS post_id, -- 文章ID
    p.admin_id,      -- 管理员ID
    a.name AS admin_name, -- 管理员名称 (文章作者)
    p.title,         -- 文章标题
    p.content,       -- 文章内容
    p.category,      -- 文章分类
    p.image,         -- 文章图片
    p.date,          -- 发布日期
    p.status         -- 文章状态
FROM
    posts AS p
JOIN
    admin AS a ON p.admin_id = a.id;