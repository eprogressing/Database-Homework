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

<header class="header">

   <a href="dashboard.php" class="logo">管理<span>面板</span></a>

   <div class="profile">
      <?php
         $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
         $select_profile->execute([$admin_id]);
         $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
      ?>
      <p><?= $fetch_profile['name']; ?></Liberal>
      <a href="update_profile.php" class="btn">更新个人资料</a>
   </div>

   <nav class="navbar">
      <a href="dashboard.php"><i class="fas fa-home"></i> <span>首页</span></a>
      <a href="add_posts.php"><i class="fas fa-pen"></i> <span>添加帖子</span></a>
      <a href="view_posts.php"><i class="fas fa-eye"></i> <span>查看帖子</span></a>
      <a href="admin_accounts.php"><i class="fas fa-user"></i> <span>账户</span></a>
      <a href="../components/admin_logout.php" style="color:var(--red);" onclick="return confirm('退出网站？');"><i class="fas fa-right-from-bracket"></i><span>退出</span></a>
   </nav>

   <div class="flex-btn">
      <a href="admin_login.php" class="option-btn">登录</a>
      <a href="register_admin.php" class="option-btn">注册</a>
   </div>

</header>

<div id="menu-btn" class="fas fa-bars"></div>