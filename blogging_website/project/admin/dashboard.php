<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

?>

<!DOCTYPE html>
<html lang="zh">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>仪表盘</title>

   <!-- Font Awesome CDN 链接 -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- 自定义 CSS 文件 链接 -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- 管理员仪表盘部分开始 -->

<section class="dashboard">

   <h1 class="heading">仪表盘</h1>

   <div class="box-container">

      <div class="box">
         <h3>欢迎！</h3>
         <p><?= $fetch_profile['name']; ?></p>
         <a href="update_profile.php" class="btn">更新个人资料</a>
      </div>

      <div class="box">
         <?php
            $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
            $select_posts->execute([$admin_id]);
            $numbers_of_posts = $select_posts->rowCount();
         ?>
         <h3><?= $numbers_of_posts; ?></h3>
         <p>已发布文章</p>
         <a href="add_posts.php" class="btn">添加新文章</a>
      </div>

      <div class="box">
         <?php
            $select_active_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? AND status = ?");
            $select_active_posts->execute([$admin_id, 'active']);
            $numbers_of_active_posts = $select_active_posts->rowCount();
         ?>
         <h3><?= $numbers_of_active_posts; ?></h3>
         <p>已激活文章</p>
         <a href="view_posts.php" class="btn">查看文章</a>
      </div>

      <div class="box">
         <?php
            $select_deactive_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? AND status = ?");
            $select_deactive_posts->execute([$admin_id, 'deactive']);
            $numbers_of_deactive_posts = $select_deactive_posts->rowCount();
         ?>
         <h3><?= $numbers_of_deactive_posts; ?></h3>
         <p>未激活文章</p>
         <a href="view_posts.php" class="btn">查看文章</a>
      </div>

      <div class="box">
         <?php
            $select_users = $conn->prepare("SELECT * FROM `users`");
            $select_users->execute();
            $numbers_of_users = $select_users->rowCount();
         ?>
         <h3><?= $numbers_of_users; ?></h3>
         <p>用户账户</p>
         <a href="users_accounts.php" class="btn">查看用户</a>
      </div>

      <div class="box">
         <?php
            $select_admins = $conn->prepare("SELECT * FROM `admin`");
            $select_admins->execute();
            $numbers_of_admins = $select_admins->rowCount();
         ?>
         <h3><?= $numbers_of_admins; ?></h3>
         <p>管理员账户</p>
         <a href="admin_accounts.php" class="btn">查看管理员</a>
      </div>
      
      <div class="box">
         <?php
            $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE admin_id = ?");
            $select_comments->execute([$admin_id]);
            $numbers_of_comments = $select_comments->rowCount();
         ?>
         <h3><?= $numbers_of_comments; ?></h3>
         <p>已添加评论</p>
         <a href="comments.php" class="btn">查看评论</a>
      </div>

      <div class="box">
         <?php
            $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE admin_id = ?");
            $select_likes->execute([$admin_id]);
            $numbers_of_likes = $select_likes->rowCount();
         ?>
         <h3><?= $numbers_of_likes; ?></h3>
         <p>总点赞数</p>
         <a href="view_posts.php" class="btn">查看文章</a>
      </div>

   </div>

</section>

<!-- 管理员仪表盘部分结束 -->







<!-- 自定义 JS 文件 链接 -->
<script src="../js/admin_script.js"></script>

</body>
</html>
