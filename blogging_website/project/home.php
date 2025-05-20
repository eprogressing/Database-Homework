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
   <title>首页</title>

   <!-- font awesome cdn 链接 -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- 自定义 css 文件链接 -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- 头部部分开始 -->
<?php include 'components/user_header.php'; ?>
<!-- 头部部分结束 -->

<section class="home-grid">

   <div class="box-container">

      <div class="box">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
               $count_user_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
               $count_user_comments->execute([$user_id]);
               $total_user_comments = $count_user_comments->rowCount();
               $count_user_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
               $count_user_likes->execute([$user_id]);
               $total_user_likes = $count_user_likes->rowCount();
         ?>
         <p>欢迎 <span><?= $fetch_profile['name']; ?></span></p>
         <p>评论总数 : <span><?= $total_user_comments; ?></span></p>
         <p>点赞的帖子 : <span><?= $total_user_likes; ?></span></p>
         <a href="update.php" class="btn">更新个人资料</a>
         <div class="flex-btn">
            <a href="user_likes.php" class="option-btn">点赞</a>
            <a href="user_comments.php" class="option-btn">评论</a>
         </div>
         <?php
            }else{
         ?>
            <p class="name">请登录或注册！</p>
            <div class="flex-btn">
               <a href="login.php" class="option-btn">登录</a>
               <a href="register.php" class="option-btn">注册</a>
            </div> 
         <?php
          }
         ?>
      </div>

      <div class="box">
         <p>分类</p>
         <div class="flex-box">
            <a href="category.php?category=nature" class="links">自然</a>
            <a href="category.php?category=education" class="links">教育</a>
            <a href="category.php?category=business - business" class="links">商业</a>
            <a href="category.php?category=travel" class="links">旅行</a>
            <a href="category.php?category=news" class="links">新闻</a>
            <a href="category.php?category=gaming" class="links">游戏</a>
            <a href="category.php?category=sports" class="links">体育</a>
            <a href="category.php?category=design" class="links">设计</a>
            <a href="category.php?category=fashion" class="links">时尚</a>
            <a href="category.php?category=persional" class="links">个人</a>
            <a href="all_category.php" class="btn">查看全部</a>
         </div>
      </div>

      <div class="box">
         <p>作者</p>
         <div class="flex-box">
         <?php
            $select_authors = $conn->prepare("SELECT DISTINCT name FROM `admin` LIMIT 10");
            $select_authors->execute();
            if($select_authors->rowCount() > 0){
               while($fetch_authors = $select_authors->fetch(PDO::FETCH_ASSOC)){ 
         ?>
            <a href="author_posts.php?author=<?= $fetch_authors['name']; ?>" class="links"><?= $fetch_authors['name']; ?></a>
            <?php
            }
         }else{
            echo '<p class="empty">尚未添加帖子！</p>';
         }
         ?>  
         <a href="authors.php" class="btn">查看全部</a>
         </div>
      </div>

   </div>

</section>

<section class="posts-container">

   <h1 class="heading">最新帖子</h1>

   <div class="box-container">

      <?php
         $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE status = ? LIMIT 6 ");
         $select_posts->execute(['active']);
         if($select_posts->rowCount() > 0){
            while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
               
               $post_id = $fetch_posts['id'];

               $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
               $count_post_comments->execute([$post_id]);
               $total_post_comments = $count_post_comments->rowCount(); 

               $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
               $count_post_likes->execute([$post_id]);
               $total_post_likes = $count_post_likes->rowCount();

               $confirm_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND post_id = ?");
               $confirm_likes->execute([$user_id, $post_id]);
      ?>
      <form class="box" method="post">
         <input type="hidden" name="post_id" value="<?= $post_id; ?>">
         <input type="hidden" name="admin_id" value="<?= $fetch_posts['admin_id']; ?>">
         <div class="post-admin">
            <i class="fas fa-user"></i>
            <div>
               <a href="author_posts.php?author=<?= $fetch_posts['name']; ?>"><?= $fetch_posts['name']; ?></a>
               <div><?= $fetch_posts['date']; ?></div>
            </div>
         </div>
         
         <?php
            if($fetch_posts['image'] != ''){  
         ?>
         <img src="uploaded_img/<?= $fetch_posts['image']; ?>" class="post-image" alt="">
         <?php
         }
         ?>
         <div class="post-title"><?= $fetch_posts['title']; ?></div>
         <div class="post-content content-150"><?= $fetch_posts['content']; ?></div>
         <a href="view_post.php?post_id=<?= $post_id; ?>" class="inline-btn">阅读更多</a>
         <a href="category.php?category=<?= $fetch_posts['category']; ?>" class="post-cat"> <i class="fas fa-tag"></i> <span><?= $fetch_posts['category']; ?></span></a>
         <div class="icons">
            <a href="view_post.php?post_id=<?= $post_id; ?>"><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></a>
            <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if($confirm_likes->rowCount() > 0){ echo 'color:var(--red);'; } ?>  "></i><span>(<?= $total_post_likes; ?>)</span></button>
         </div>
      
      </form>
      <?php
         }
      }else{
         echo '<p class="empty">尚未添加帖子！</p>';
      }
      ?>
   </div>

   <div class="more-btn" style="text-align: center; margin-top:1rem;">
      <a href="posts.php" class="inline-btn">查看所有帖子</a>
   </div>

</section>

<?php include 'components/footer.php'; ?>

<!-- 自定义 js 文件链接 -->
<script src="js/script.js"></script>

</body>
</html>