<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/like_post.php';

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>作者</title>

   <!-- font awesome cdn 链接 -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- 自定义 css 文件链接 -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- 头部部分开始 -->
<?php include 'components/user_header.php'; ?>
<!-- 头部部分结束 -->

<section class="authors">

   <h1 class="heading">作者</h1>

   <div class="box-container">

   <?php
      $select_author = $conn->prepare("SELECT * FROM `admin`");
      $select_author->execute();
      if($select_author->rowCount() > 0){
         while($fetch_authors = $select_author->fetch(PDO::FETCH_ASSOC)){ 

            $count_admin_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? AND status = ?");
            $count_admin_posts->execute([$fetch_authors['id'], 'active']);
            $total_admin_posts = $count_admin_posts->rowCount();

            $count_admin_likes = $conn->prepare("SELECT * FROM `likes` WHERE admin_id = ?");
            $count_admin_likes->execute([$fetch_authors['id']]);
            $total_admin_likes = $count_admin_likes->rowCount();

            $count_admin_comments = $conn->prepare("SELECT * FROM `comments` WHERE admin_id = ?");
            $count_admin_comments->execute([$fetch_authors['id']]);
            $total_admin_comments = $count_admin_comments->rowCount();

   ?>
   <div class="box">
      <p>作者 : <span><?= $fetch_authors['name']; ?></span></p>
      <p>帖子总数 : <span><?= $total_admin_posts; ?></span></p>
      <p>帖子点赞数 : <span><?= $total_admin_likes; ?></span></p>
      <p>帖子评论数 : <span><?= $total_admin_comments; ?></span></p>
      <a href="author_posts.php?author=<?= $fetch_authors['name']; ?>" class="btn">查看帖子</a>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">未找到作者</p>';
   }
   ?>

   </div>
      
</section>

<?php include 'components/footer.php'; ?>

<!-- 自定义 js 文件链接 -->
<script src="js/script.js"></script>

</body>
</html>