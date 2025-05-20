<?php

$db_name = 'mysql:host=127.0.0.1;port=3306;dbname=blog_db';
$user_name = 'root';
$user_password = 'Ljm89821798';

try {
    $conn = new PDO($db_name, $user_name, $user_password);
    // 设置 PDO 错误模式为异常，便于调试
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

?>
