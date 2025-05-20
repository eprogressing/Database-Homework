<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>用户账户</title>

   <!-- font awesome cdn 链接 -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- 自定义 css 文件链接 -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- 用户账户部分开始 -->

<section class="accounts">

   <h1 class="heading">用户账户</h1>

   <div class="box-container">

   <?php
      $select_account = $conn->prepare("SELECT * FROM `users`");
      $select_account->execute();
      if($select_account->rowCount() > 0){
         while($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)){ 
            $user_id = $fetch_accounts['id']; 
            $count_user_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
            $count_user_comments->execute([$user_id]);
            $total_user_comments = $count_user_comments->rowCount();
            $count_user_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
            $count_user_likes->execute([$user_id]);
            $total_user_likes = $count_user_likes->rowCount();
   ?>
   <div class="box">
      <p> 用户 ID : <span><?= $user_id; ?></span> </p>
      <p> 用户名 : <span><?= $fetch_accounts['name']; ?></span> </p>
      <p> 总评论数 : <span><?= $total_user_comments; ?></span> </p>
      <p> 总点赞数 : <span><?= $total_user_likes; ?></span> </p>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">暂无可用账户</p>';
   }
   ?>

   </div>

</section>

<!-- 用户账户部分结束 -->

<!-- 自定义 js 文件链接 -->
<script src="../js/admin_script.js"></script>

</body>
</html>