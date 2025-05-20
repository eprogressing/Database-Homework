<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
   $select_user->execute([$email, $pass]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($select_user->rowCount() > 0){
      $_SESSION['user_id'] = $row['id'];
      header('location:home.php');
   }else{
      $message[] = '用户名或密码错误！';
   }

}

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>登录</title>

   <!-- font awesome cdn 链接 -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- 自定义 css 文件链接 -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- 头部部分开始 -->
<?php include 'components/user_header.php'; ?>
<!-- 头部部分结束 -->

<section class="form-container">

   <form action="" method="post">
      <h3>立即登录</h3>
      <input type="email" name="email" required placeholder="请输入您的邮箱" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="请输入您的密码" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="立即登录" name="submit" class="btn">
      <p>还没有账户？ <a href="register.php">立即注册</a></p>
   </form>

</section>

<?php include 'components/footer.php'; ?>

<!-- 自定义 js 文件链接 -->
<script src="js/script.js"></script>

</body>
</html>