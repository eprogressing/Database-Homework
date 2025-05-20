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
   <title>分类</title>

   <!-- font awesome cdn 链接 -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- 自定义 css 文件链接 -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- 头部部分开始 -->
<?php include 'components/user_header.php'; ?>
<!-- 头部部分结束 -->

<section class="categories">

   <h1 class="heading">帖子分类</h1>

   <div class="box-container">
      <div class="box"><span>01</span><a href="category.php?category=nature">自然</a></div>
      <div class="box"><span>02</span><a href="category.php?category=eduction">教育</a></div>
      <div class="box"><span>03</span><a href="category.php?category=pets and animals">宠物与动物</a></div>
      <div class="box"><span>04</span><a href="category.php?category=technology">科技</a></div>
      <div class="box"><span>05</span><a href="category.php?category=fashion">时尚</a></div>
      <div class="box"><span>06</span><a href="category.php?category=entertainment">娱乐</a></div>
      <div class="box"><span>07</span><a href="category.php?category=movies">电影</a></div>
      <div class="box"><span>08</span><a href="category.php?category=gaming">游戏</a></div>
      <div class="box"><span>09</span><a href="category.php?category=music">音乐</a></div>
      <div class="box"><span>10</span><a href="category.php?category=sports">体育</a></div>
      <div class="box"><span>11</span><a href="category.php?category=news">新闻</a></div>
      <div class="box"><span>12</span><a href="category.php?category=travel">旅行</a></div>
      <div class="box"><span>13</span><a href="category.php?category=comedy">喜剧</a></div>
      <div class="box"><span>14</span><a href="category.php?category=design and development">设计与开发</a></div>
      <div class="box"><span>15</span><a href="category.php?category=food and drinks">美食与饮品</a></div>
      <div class="box"><span>16</span><a href="category.php?category=lifestyle">生活方式</a></div>
      <div class="box"><span>17</span><a href="category.php?category=health and fitness">健康与健身</a></div>
      <div class="box"><span>18</span><a href="category.php?category=business">商业</a></div>
      <div class="box"><span>19</span><a href="category.php?category=shopping">购物</a></div>
      <div class="box"><span>20</span><a href="category.php?category=animations">动画</a></div>
   </div>

</section>

<?php include 'components/footer.php'; ?>

<!-- 自定义 js 文件链接 -->
<script src="js/script.js"></script>

</body>
</html>