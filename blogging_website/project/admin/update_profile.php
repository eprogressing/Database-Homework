<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['submit'])) {

    // 处理用户名更新
    $name = strip_tags($_POST['name']); // 使用 strip_tags 替代 FILTER_SANITIZE_STRING

    if (!empty($name)) {
        $select_name = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
        $select_name->execute([$name]);
        if ($select_name->rowCount() > 0) {
            $message[] = '用户名已被占用！';
        } else {
            $update_name = $conn->prepare("UPDATE `admin` SET name = ? WHERE id = ?");
            $update_name->execute([$name, $admin_id]);
            $message[] = '用户名更新成功！';
        }
    }

    // 处理密码更新
    $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709'; // sha1('')
    $old_pass = sha1($_POST['old_pass']);
    $new_pass = sha1($_POST['new_pass']);
    $confirm_pass = sha1($_POST['confirm_pass']); // 修正确认密码变量

    $select_old_pass = $conn->prepare("SELECT password FROM `admin` WHERE id = ?");
    $select_old_pass->execute([$admin_id]);
    $fetch_prev_pass = $select_old_pass->fetch(PDO::FETCH_ASSOC);
    $prev_pass = $fetch_prev_pass['password'];

    if ($old_pass != $empty_pass) {
        if ($old_pass != $prev_pass) {
            $message[] = '旧密码不匹配！';
        } elseif ($new_pass != $confirm_pass) {
            $message[] = '确认密码不匹配！';
        } else {
            if ($new_pass != $empty_pass) {
                $update_pass = $conn->prepare("UPDATE `admin` SET password = ? WHERE id = ?");
                $update_pass->execute([$confirm_pass, $admin_id]);
                $message[] = '密码更新成功！';
            } else {
                $message[] = '请输入新密码！';
            }
        }
    }
}

// 获取当前管理员信息用于显示
$select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>更新个人资料</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="form-container">
    <form action="" method="POST">
        <h3>更新个人资料</h3>
        <input type="text" name="name" maxlength="20" class="box" 
               pattern="\S{3,}" title="至少3个字符且不含空格"
               placeholder="<?= $fetch_profile['name']; ?>">
        <input type="password" name="old_pass" maxlength="20" 
               placeholder="请输入旧密码" class="box"
               pattern="\S{6,}" title="至少6个字符且不含空格">
        <input type="password" name="new_pass" maxlength="20" 
               placeholder="请输入新密码" class="box"
               pattern="\S{6,}" title="至少6个字符且不含空格">
        <input type="password" name="confirm_pass" maxlength="20" 
               placeholder="确认新密码" class="box"
               pattern="\S{6,}" title="至少6个字符且不含空格">
        <input type="submit" value="立即更新" name="submit" class="btn">
    </form>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>