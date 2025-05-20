<?php
include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = filter_var($_SESSION['user_id'], FILTER_VALIDATE_INT);
    if ($user_id === false || $user_id <= 0) {
        $user_id = '';
    }
} else {
    $user_id = '';
}

include 'components/like_post.php';

// Check if post_id is provided in the URL
$get_id = isset($_GET['post_id']) ? filter_var($_GET['post_id'], FILTER_VALIDATE_INT) : false;

if ($get_id === false || $get_id <= 0) {
    $message[] = '无效的文章ID！';
} else {
    if (isset($_POST['add_comment'])) {
        $admin_id = filter_var($_POST['admin_id'], FILTER_VALIDATE_INT);
        $user_name = htmlspecialchars($_POST['user_name'], ENT_QUOTES, 'UTF-8');
        $comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');

        if ($admin_id === false || $admin_id <= 0) {
            $message[] = '无效的管理员ID！';
        } elseif (empty($user_name) || empty($comment)) {
            $message[] = '用户名或评论不能为空！';
        } else {
            $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ? AND admin_id = ? AND user_id = ? AND user_name = ? AND comment = ?");
            $verify_comment->execute([$get_id, $admin_id, $user_id, $user_name, $comment]);

            if ($verify_comment->rowCount() > 0) {
                $message[] = '评论已存在！';
            } else {
                $insert_comment = $conn->prepare("INSERT INTO `comments`(post_id, admin_id, user_id, user_name, comment) VALUES(?,?,?,?,?)");
                $insert_comment->execute([$get_id, $admin_id, $user_id, $user_name, $comment]);
                $message[] = '评论添加成功！';
            }
        }
    }

    if (isset($_POST['edit_comment'])) {
        $edit_comment_id = filter_var($_POST['edit_comment_id'], FILTER_VALIDATE_INT);
        $comment_edit_box = htmlspecialchars($_POST['comment_edit_box'], ENT_QUOTES, 'UTF-8');

        if ($edit_comment_id === false || $edit_comment_id <= 0) {
            $message[] = '无效的评论ID！';
        } elseif (empty($comment_edit_box)) {
            $message[] = '评论内容不能为空！';
        } else {
            $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE comment = ? AND id = ?");
            $verify_comment->execute([$comment_edit_box, $edit_comment_id]);

            if ($verify_comment->rowCount() > 0) {
                $message[] = '评论已存在！';
            } else {
                $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
                $update_comment->execute([$comment_edit_box, $edit_comment_id]);
                $message[] = '评论修改成功！';
            }
        }
    }

    if (isset($_POST['delete_comment'])) {
        $delete_comment_id = filter_var($_POST['comment_id'], FILTER_VALIDATE_INT);

        if ($delete_comment_id === false || $delete_comment_id <= 0) {
            $message[] = '无效的评论ID！';
        } else {
            $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
            $delete_comment->execute([$delete_comment_id]);
            $message[] = '评论删除成功！';
        }
    }
}

// Initialize message array
$message = $message ?? [];
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看文章</title>

    <!-- 字体awesome CDN链接 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- 自定义CSS文件链接 -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- 头部开始 -->
<?php include 'components/user_header.php'; ?>
<!-- 头部结束 -->

<section class="posts-container" style="padding-bottom: 0;">
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

    <?php
    if ($get_id !== false && $get_id > 0) {
        if (isset($_POST['open_edit_box'])) {
            $comment_id = filter_var($_POST['comment_id'], FILTER_VALIDATE_INT);
            if ($comment_id === false || $comment_id <= 0) {
                $message[] = '无效的评论ID！';
            } else {
    ?>
    <section class="comment-edit-form">
        <p>编辑您的评论</p>
        <?php
        $select_edit_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
        $select_edit_comment->execute([$comment_id]);
        $fetch_edit_comment = $select_edit_comment->fetch(PDO::FETCH_ASSOC);
        if ($fetch_edit_comment) {
        ?>
        <form action="" method="POST">
            <input type="hidden" name="edit_comment_id" value="<?= htmlspecialchars($comment_id, ENT_QUOTES, 'UTF-8'); ?>">
            <textarea name="comment_edit_box" required cols="30" rows="10" placeholder="请输入您的评论"><?= htmlspecialchars($fetch_edit_comment['comment'], ENT_QUOTES, 'UTF-8'); ?></textarea>
            <button type="submit" class="inline-btn" name="edit_comment">保存修改</button>
            <div class="inline-option-btn" onclick="window.location.href = 'view_post.php?post_id=<?= htmlspecialchars($get_id, ENT_QUOTES, 'UTF-8'); ?>';">取消编辑</div>
        </form>
        <?php
        } else {
            $message[] = '评论不存在！';
        }
            }
        }
    ?>
    <div class="box-container">
        <?php
        $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE status = ? AND id = ?");
        $select_posts->execute(['active', $get_id]);
        if ($select_posts->rowCount() > 0) {
            while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
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
            <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="admin_id" value="<?= htmlspecialchars($fetch_posts['admin_id'], ENT_QUOTES, 'UTF-8'); ?>">
            <div class="post-admin">
                <i class="fas fa-user"></i>
                <div>
                    <a href="author_posts.php?author=<?= htmlspecialchars($fetch_posts['name'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($fetch_posts['name'], ENT_QUOTES, 'UTF-8'); ?></a>
                    <div><?= htmlspecialchars($fetch_posts['date'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
            <?php if ($fetch_posts['image'] != '') { ?>
                <img src="uploaded_img/<?= htmlspecialchars($fetch_posts['image'], ENT_QUOTES, 'UTF-8'); ?>" class="post-image" alt="">
            <?php } ?>
            <div class="post-title"><?= htmlspecialchars($fetch_posts['title'], ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="post-content"><?= htmlspecialchars($fetch_posts['content'], ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="icons">
                <div><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></div>
                <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if ($confirm_likes->rowCount() > 0) { echo 'color:var(--red);'; } ?>"></i><span>(<?= $total_post_likes; ?>)</span></button>
            </div>
        </form>
        <?php
            }
        } else {
            echo '<p class="empty">未找到文章！</p>';
        }
        ?>
    </div>
    <?php
    } else {
        echo '<p class="empty">未提供文章ID！<a href="home.php" class="inline-btn">返回主页</a></p>';
    }
    ?>
</section>

<section class="comments-container">
    <?php if ($get_id !== false && $get_id > 0) { ?>
    <p class="comment-title">发表评论</p>
    <?php
    if ($user_id != '') {
        $select_admin_id = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
        $select_admin_id->execute([$get_id]);
        $fetch_admin_id = $select_admin_id->fetch(PDO::FETCH_ASSOC);
        if ($fetch_admin_id) {
    ?>
    <form action="" method="post" class="add-comment">
        <input type="hidden" name="admin_id" value="<?= htmlspecialchars($fetch_admin_id['admin_id'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="user_name" value="<?= htmlspecialchars($fetch_profile['name'], ENT_QUOTES, 'UTF-8'); ?>">
        <p class="user"><i class="fas fa-user"></i><a href="update.php"><?= htmlspecialchars($fetch_profile['name'], ENT_QUOTES, 'UTF-8'); ?></a></p>
        <textarea name="comment" maxlength="1000" class="comment-box" cols="30" rows="10" placeholder="输入您的评论" required></textarea>
        <input type="submit" value="提交评论" class="inline-btn" name="add_comment">
    </form>
    <?php
        } else {
            echo '<p class="empty">文章不存在！</p>';
        }
    } else {
    ?>
    <div class="add-comment">
        <p>请登录后添加或编辑评论</p>
        <a href="login.php" class="inline-btn">立即登录</a>
    </div>
    <?php
    }
    ?>
    <p class="comment-title">所有评论</p>
    <div class="user-comments-container">
        <?php
        $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
        $select_comments->execute([$get_id]);
        if ($select_comments->rowCount() > 0) {
            while ($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <div class="show-comments" style="<?php if ($fetch_comments['user_id'] == $user_id) { echo 'order:-1;'; } ?>">
            <div class="comment-user">
                <i class="fas fa-user"></i>
                <div>
                    <span><?= htmlspecialchars($fetch_comments['user_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <div><?= htmlspecialchars($fetch_comments['date'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
            <div class="comment-box" style="<?php if ($fetch_comments['user_id'] == $user_id) { echo 'color:var(--white); background:var(--black);'; } ?>"><?= htmlspecialchars($fetch_comments['comment'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php if ($fetch_comments['user_id'] == $user_id) { ?>
            <form action="" method="POST">
                <input type="hidden" name="comment_id" value="<?= htmlspecialchars($fetch_comments['id'], ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="inline-option-btn" name="open_edit_box">编辑评论</button>
                <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('确认删除此评论？');">删除评论</button>
            </form>
            <?php } ?>
        </div>
        <?php
            }
        } else {
            echo '<p class="empty">暂无评论！</p>';
        }
        ?>
    </div>
    <?php } else { ?>
    <p class="empty">无法加载评论，请提供有效的文章ID！<a href="home.php" class="inline-btn">返回主页</a></p>
    <?php } ?>
</section>

<?php include 'components/footer.php'; ?>

<!-- 自定义JS文件链接 -->
<script src="js/script.js"></script>

</body>
</html>