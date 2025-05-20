<?php

include '../components/connect.php';

session_start();

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $pass = sha1($_POST['pass']);

   $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
   $select_admin->execute([$name, $pass]);
   
   if($select_admin->rowCount() > 0){
      $fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
      $_SESSION['admin_id'] = $fetch_admin_id['id'];
      header('location:dashboard.php');
   }else{
      $message[] = '用户名或密码错误！';
   }

}

?>

<!DOCTYPE html>
<html lang="zh">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>登录</title>

   <!-- Font Awesome CDN 链接 -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- 自定义 CSS 文件 链接 -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body style="padding-left: 0 !important;">

<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<!-- 管理员登录表单开始 -->

<section class="form-container">

   <form action="" method="POST">
      <h3>立即登录</h3>
      <p>默认用户名 = <span>admin</span> & 密码 = <span>123456</span></p>
      <input type="text" name="name" maxlength="20" required placeholder="请输入用户名" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" maxlength="20" required placeholder="请输入密码" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="立即登录" name="submit" class="btn">
   </form>

</section>

<!-- 管理员登录表单结束 -->
</body>
</html>
