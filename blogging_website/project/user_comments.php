<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};

if(isset($_POST['edit_comment'])){

   $edit_comment_id = $_POST['edit_comment_id'];
   $edit_comment_id = filter_var($edit_comment_id, FILTER_SANITIZE_STRING);
   $comment_edit_box = $_POST['comment_edit_box'];
   $comment_edit_box = filter_var($comment_edit_box, FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE comment = ? AND id = ?");
   $verify_comment->execute([$comment_edit_box, $edit_comment_id]);

   if($verify_comment->rowCount() > 0){
      $message[] = '评论已存在！';
   }else{
      $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
      $update_comment->execute([$comment_edit_box, $edit_comment_id]);
      $message[] = '评论修改成功！';
   }
   
}

if(isset($_POST['delete_comment'])){
   $delete_comment_id = $_POST['comment_id'];
   $delete_comment_id = filter_var($delete_comment_id, FILTER_SANITIZE_STRING);
   $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
   $delete_comment->execute([$delete_comment_id]);
   $message[] = '评论删除成功！';
}

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>评论管理</title>

   <!-- 字体awesome CDN链接 -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- 自定义CSS文件链接 -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<!-- 头部开始 -->
<?php include 'components/user_header.php'; ?>
<!-- 头部结束 -->

<?php
   if(isset($_POST['open_edit_box'])){
   $comment_id = $_POST['comment_id'];
   $comment_id = filter_var($comment_id, FILTER_SANITIZE_STRING);
?>
   <section class="comment-edit-form">
   <p>编辑您的评论</p>
   <?php
      $select_edit_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
      $select_edit_comment->execute([$comment_id]);
      $fetch_edit_comment = $select_edit_comment->fetch(PDO::FETCH_ASSOC);
   ?>
   <form action="" method="POST">
      <input type="hidden" name="edit_comment_id" value="<?= $comment_id; ?>">
      <textarea name="comment_edit_box" required cols="30" rows="10" placeholder="请输入您的评论"><?= $fetch_edit_comment['comment']; ?></textarea>
      <button type="submit" class="inline-btn" name="edit_comment">保存修改</button>
      <div class="inline-option-btn" onclick="window.location.href = 'user_comments.php';">取消编辑</div>
   </form>
   </section>
<?php
   }
?>

<section class="comments-container">

   <h1 class="heading">您的评论</h1>

   <p class="comment-title">您在文章中的评论</p>
   <div class="user-comments-container">
      <?php
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
         $select_comments->execute([$user_id]);
         if($select_comments->rowCount() > 0){
            while($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="show-comments">
         <?php
            $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
            $select_posts->execute([$fetch_comments['post_id']]);
            while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
         ?>
         <div class="post-title"> 来自： <span><?= $fetch_posts['title']; ?></span> <a href="view_post.php?post_id=<?= $fetch_posts['id']; ?>" >查看文章</a></div>
         <?php
            }
         ?>
         <div class="comment-box"><?= $fetch_comments['comment']; ?></div>
         <form action="" method="POST">
            <input type="hidden" name="comment_id" value="<?= $fetch_comments['id']; ?>">
            <button type="submit" class="inline-option-btn" name="open_edit_box">编辑评论</button>
            <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('确认删除此评论？');">删除评论</button>
         </form>
      </div>
      <?php
            }
         }else{
            echo '<p class="empty">暂无评论！</p>';
         }
      ?>
   </div>

</section>

<?php include 'components/footer.php'; ?>

<!-- 自定义JS文件链接 -->
<script src="js/script.js"></script>

</body>
</html>