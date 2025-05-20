<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

// Initialize message array
$message = [];

if (isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $pass = sha1($_POST['pass']);
    $cpass = sha1($_POST['cpass']);

    // Validate username (alphanumeric, no spaces, max 20 characters)
    if (!preg_match('/^[a-zA-Z0-9]{1,20}$/', $_POST['name'])) {
        $message[] = '用户名只能包含字母和数字，最多20个字符！';
    } else {
        $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
        $select_admin->execute([$name]);

        if ($select_admin->rowCount() > 0) {
            $message[] = '用户名已存在！';
        } else {
            if ($pass != $cpass) {
                $message[] = '两次密码不匹配！';
            } else {
                $insert_admin = $conn->prepare("INSERT INTO `admin`(name, password) VALUES(?,?)");
                $insert_admin->execute([$name, $cpass]);
                $message[] = '新管理员注册成功！';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册管理员</title>

    <!-- Font Awesome CDN 链接 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- 自定义 CSS 文件 链接 -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- 注册管理员表单开始 -->

<section class="form-container">
    <?php
    if (!empty($message) && is_array($message)) {
        foreach ($message as $msg) {
            echo '<div class="message"><span>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</span><i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
        }
    } elseif (is_string($message) && !empty($message)) {
        echo '<div class="message"><span>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</span><i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
    }
    ?>

    <form action="" method="POST">
        <h3>注册新管理员</h3>
        <input type="text" name="name" maxlength="20" required placeholder="请输入用户名" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="pass" maxlength="20" required placeholder="请输入密码" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="cpass" maxlength="20" required placeholder="确认密码" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="submit" value="立即注册" name="submit" class="btn">
    </form>
</section>

<!-- 注册管理员表单结束 -->

<!-- 自定义 JS 文件 链接 -->
<script src="../js/admin_script.js"></script>

</body>
</html>