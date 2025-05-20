<?php
include '../components/connect.php';

session_start();

$admin_id = filter_var($_SESSION['admin_id'] ?? null, FILTER_VALIDATE_INT);

if (!$admin_id || $admin_id <= 0) {
    header('location:admin_login.php');
    exit;
}

// Initialize message array
$message = [];

if (isset($_POST['delete'])) {
    $p_id = filter_var($_POST['post_id'], FILTER_VALIDATE_INT);

    // Validate that post_id is a positive integer
    if ($p_id === false || $p_id <= 0) {
        $message[] = '无效的帖子ID！';
    } else {
        $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
        $delete_image->execute([$p_id]);
        $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
        if ($fetch_delete_image && $fetch_delete_image['image'] != '') {
            $image_path = '../uploaded_img/' . $fetch_delete_image['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            } else {
                $message[] = '警告：图片文件 ' . htmlspecialchars($fetch_delete_image['image'], ENT_QUOTES, 'UTF-8') . ' 不存在！';
            }
        }
        $delete_post = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
        $delete_post->execute([$p_id]);
        $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
        $delete_comments->execute([$p_id]);
        $message[] = '帖子删除成功！';
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>帖子</title>

    <!-- font awesome cdn 链接 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- 自定义 css 文件链接 -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="show-posts">
    <h1 class="heading">您的帖子</h1>

    <?php
    // Display messages if any
    if (!empty($message) && is_array($message)) {
        foreach ($message as $msg) {
            echo '<div class="message"><span>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</span><i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
        }
    } elseif (is_string($message) && !empty($message)) {
        echo '<div class="message"><span>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</span><i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
    }
    ?>

    <div class="box-container">
        <?php
        $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
        $select_posts->execute([$admin_id]);
        if ($select_posts->rowCount() > 0) {
            while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
                $post_id = $fetch_posts['id'];

                $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
                $count_post_comments->execute([$post_id]);
                $total_post_comments = $count_post_comments->rowCount();

                $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
                $count_post_likes->execute([$post_id]);
                $total_post_likes = $count_post_likes->rowCount();
        ?>
        <form method="post" class="box">
            <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id, ENT_QUOTES, 'UTF-8'); ?>">
            <?php if ($fetch_posts['image'] != '') { ?>
                <img src="../uploaded_img/<?= htmlspecialchars($fetch_posts['image'], ENT_QUOTES, 'UTF-8'); ?>" class="image" alt="">
            <?php } ?>
            <div class="status" style="background-color:<?php if ($fetch_posts['status'] == 'active') { echo 'limegreen'; } else { echo 'coral'; }; ?>;">
                <?= $fetch_posts['status'] == 'active' ? '活跃' : '非活跃'; ?>
            </div>
            <div class="title"><?= htmlspecialchars($fetch_posts['title'], ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="posts-content"><?= htmlspecialchars($fetch_posts['content'], ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="icons">
                <div class="likes"><i class="fas fa-heart"></i><span><?= $total_post_likes; ?></span></div>
                <div class="comments"><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></div>
            </div>
            <div class="flex-btn">
                <a href="edit_post.php?id=<?= htmlspecialchars($post_id, ENT_QUOTES, 'UTF-8'); ?>" class="option-btn">编辑</a>
                <button type="submit" name="delete" class="delete-btn" onclick="return confirm('删除此帖子？');">删除</button>
            </div>
            <a href="read_post.php?post_id=<?= htmlspecialchars($post_id, ENT_QUOTES, 'UTF-8'); ?>" class="btn">查看帖子</a>
        </form>
        <?php
            }
        } else {
            echo '<p class="empty">尚未添加帖子！ <a href="add_posts.php" class="btn" style="margin-top:1.5rem;">添加帖子</a></p>';
        }
        ?>
    </div>
</section>

<!-- 自定义 js 文件链接 -->
<script src="../js/admin_script.js"></script>

</body>
</html>