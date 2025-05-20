<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_POST['delete'])){
   $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
   $delete_image->execute([$admin_id]);
   while($fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC)){
      unlink('../uploaded_img/'.$fetch_delete_image['image']);
   }
   $delete_posts = $conn->prepare("DELETE FROM `posts` WHERE admin_id = ?");
   $delete_posts->execute([$admin_id]);
   $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE admin_id = ?");
   $delete_likes->execute([$admin_id]);
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE admin_id = ?");
   $delete_comments->execute([$admin_id]);
   $delete_admin = $conn->prepare("DELETE FROM `admin` WHERE id = ?");
   $delete_admin->execute([$admin_id]);
   header('location:../components/admin_logout.php');
}

?>

<!DOCTYPE html>
<html lang="zh">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>管理员账户</title>

   <!-- Font Awesome CDN 链接 -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- 自定义 CSS 文件 链接 -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- 管理员账户部分开始 -->

<section class="accounts">

   <h1 class="heading">管理员账户</h1>

   <div class="box-container">

      <div class="box" style="order: -2;">
         <p>注册新管理员</p>
         <a href="register_admin.php" class="option-btn" style="margin-bottom: .5rem;">注册</a>
      </div>

      <?php
         $select_account = $conn->prepare("SELECT * FROM `admin`");
         $select_account->execute();
         if($select_account->rowCount() > 0){
            while($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)){ 

               $count_admin_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
               $count_admin_posts->execute([$fetch_accounts['id']]);
               $total_admin_posts = $count_admin_posts->rowCount();

      ?>
      <div class="box" style="order: <?php if($fetch_accounts['id'] == $admin_id){ echo '-1'; } ?>;">
         <p>管理员ID：<span><?= $fetch_accounts['id']; ?></span></p>
         <p>用户名：<span><?= $fetch_accounts['name']; ?></span></p>
         <p>总文章数：<span><?= $total_admin_posts; ?></span></p>
         <div class="flex-btn">
            <?php
               if($fetch_accounts['id'] == $admin_id){
            ?>
               <a href="update_profile.php" class="option-btn" style="margin-bottom: .5rem;">更新</a>
               <form action="" method="POST">
                  <input type="hidden" name="post_id" value="<?= $fetch_accounts['id']; ?>">
                  <button type="submit" name="delete" onclick="return confirm('确定要删除账户吗？');" class="delete-btn" style="margin-bottom: .5rem;">删除</button>
               </form>
            <?php
               }
            ?>
         </div>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">暂无管理员账户</p>';
      }
      ?>

   </div>

</section>

<!-- 管理员账户部分结束 -->







<!-- 自定义 JS 文件 链接 -->
<script src="../js/admin_script.js"></script>

</body>
</html>