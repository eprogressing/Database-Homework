<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($select_user->rowCount() > 0){
      $message[] = '邮箱已存在！';
   }else{
      if($pass != $cpass){
         $message[] = '密码确认不匹配！';
      }else{
         $insert_user = $conn->prepare("INSERT INTO `users`(name, email, password) VALUES(?,?,?)");
         $insert_user->execute([$name, $email, $cpass]);
         $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
         $select_user->execute([$email, $pass]);
         $row = $select_user->fetch(PDO::FETCH_ASSOC);
         if($select_user->rowCount() > 0){
            $_SESSION['user_id'] = $row['id'];
            header('location:home.php');
         }
      }
   }

}

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>用户注册</title>

   <!-- font awesome cdn链接 -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- 自定义css文件链接 -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<!-- 头部开始 -->
<?php include 'components/user_header.php'; ?>
<!-- 头部结束 -->

<section class="form-container">

   <form action="" method="post">
      <h3>立即注册</h3>
      <input type="text" name="name" required placeholder="输入用户名" class="box" maxlength="50">
      <input type="email" name="email" required placeholder="输入电子邮箱" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="设置密码" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="确认密码" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="立即注册" name="submit" class="btn">
      <p>已有账号？ <a href="login.php">立即登录</a></p>
   </form>

</section>

<?php include 'components/footer.php'; ?>

<!-- 自定义js文件链接 -->
<script src="js/script.js"></script>

</body>
</html>