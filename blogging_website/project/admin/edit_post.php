<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

// Initialize message array
$message = [];

if (isset($_POST['save'])) {
    $post_id = $_GET['id'];
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
    $category = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');
    $status = htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8');

    $update_post = $conn->prepare("UPDATE `posts` SET title = ?, content = ?, category = ?, status = ? WHERE id = ?");
    $update_post->execute([$title, $content, $category, $status, $post_id]);

    $message[] = '文章已更新！';

    $old_image = $_POST['old_image'];
    $image = $_FILES['image']['name'];
    $image = htmlspecialchars($image, ENT_QUOTES, 'UTF-8');
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND admin_id = ?");
    $select_image->execute([$image, $admin_id]);

    if (!empty($image)) {
        if ($image_size > 2000000) {
            $message[] = '图片大小过大！';
        } elseif ($select_image->rowCount() > 0 && $image != '') {
            $message[] = '请重命名您的图片！';
        } else {
            $update_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
            move_uploaded_file($image_tmp_name, $image_folder);
            $update_image->execute([$image, $post_id]);
            if ($old_image != $image && $old_image != '') {
                $old_image_path = '../uploaded_img/' . $old_image;
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
            $message[] = '图片已更新！';
        }
    }
}

if (isset($_POST['delete_post'])) {
    $post_id = htmlspecialchars($_POST['post_id'], ENT_QUOTES, 'UTF-8');
    $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
    $delete_image->execute([$post_id]);
    $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
    if ($fetch_delete_image['image'] != '') {
        $image_path = '../uploaded_img/' . $fetch_delete_image['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    $delete_post = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
    $delete_post->execute([$post_id]);
    $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
    $delete_comments->execute([$post_id]);
    $message[] = '文章删除成功！';
}

if (isset($_POST['delete_image'])) {
    $empty_image = '';
    $post_id = htmlspecialchars($_POST['post_id'], ENT_QUOTES, 'UTF-8');
    $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
    $delete_image->execute([$post_id]);
    $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
    if ($fetch_delete_image['image'] != '') {
        $image_path = '../uploaded_img/' . $fetch_delete_image['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        } else {
            $message[] = '警告：图片文件 ' . htmlspecialchars($fetch_delete_image['image'], ENT_QUOTES, 'UTF-8') . ' 不存在！';
        }
    }
    $unset_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
    $unset_image->execute([$empty_image, $post_id]);
    $message[] = '图片删除成功！';
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑文章</title>

    <!-- Font Awesome CDN 链接 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- 自定义 CSS 文件 链接 -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="post-editor">
    <h1 class="heading">编辑文章</h1>

    <?php
    // Ensure $message is an array before looping
    if (!empty($message) && is_array($message)) {
        foreach ($message as $msg) {
            echo '<div class="message"><span>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</span><i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
        }
    } elseif (is_string($message) && !empty($message)) {
        // Handle case where $message is a string
        echo '<div class="message"><span>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</span><i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
    }
    $post_id = $_GET['id'];
    $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
    $select_posts->execute([$post_id]);
    if ($select_posts->rowCount() > 0) {
        while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="old_image" value="<?= htmlspecialchars($fetch_posts['image'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="post_id" value="<?= htmlspecialchars($fetch_posts['id'], ENT_QUOTES, 'UTF-8'); ?>">
        <p>文章状态 <span>*</span></p>
        <select name="status" class="box" required>
            <option value="<?= htmlspecialchars($fetch_posts['status'], ENT_QUOTES, 'UTF-8'); ?>" selected><?= $fetch_posts['status'] == 'active' ? '已激活' : '未激活'; ?></option>
            <option value="active">已激活</option>
            <option value="deactive">未激活</option>
        </select>
        <p>文章标题 <span>*</span></p>
        <input type="text" name="title" maxlength="100" required placeholder="输入文章标题" class="box" value="<?= htmlspecialchars($fetch_posts['title'], ENT_QUOTES, 'UTF-8'); ?>">
        <p>文章内容 <span>*</span></p>
        <textarea name="content" class="box" required maxlength="10000" placeholder="撰写内容..." cols="30" rows="10"><?= htmlspecialchars($fetch_posts['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        <p>文章分类 <span>*</span></p>
        <select name="category" class="box" required>
            <option value="<?= htmlspecialchars($fetch_posts['category'], ENT_QUOTES, 'UTF-8'); ?>" selected><?= htmlspecialchars($fetch_posts['category'], ENT_QUOTES, 'UTF-8'); ?></option>
            <option value="nature">自然</option>
            <option value="education">教育</option>
            <option value="pets and animals">宠物与动物</option>
            <option value="technology">科技</option>
            <option value="fashion">时尚</option>
            <option value="entertainment">娱乐</option>
            <option value="movies and animations">影视动画</option>
            <option value="gaming">游戏</option>
            <option value="music">音乐</option>
            <option value="sports">体育</option>
            <option value="news">新闻</option>
            <option value="travel">旅行</option>
            <option value="comedy">喜剧</option>
            <option value="design and development">设计与开发</option>
            <option value="food and drinks">美食</option>
            <option value="lifestyle">生活方式</option>
            <option value="personal">个人</option>
            <option value="health and fitness">健康与健身</option>
            <option value="business">商业</option>
            <option value="shopping">购物</option>
            <option value="animations">动画</option>
        </select>
        <p>文章图片</p>
        <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
        <?php if ($fetch_posts['image'] != '') { ?>
            <img src="../uploaded_img/<?= htmlspecialchars($fetch_posts['image'], ENT_QUOTES, 'UTF-8'); ?>" class="image" alt="">
            <input type="submit" value="删除图片" class="inline-delete-btn" name="delete_image">
        <?php } ?>
        <div class="flex-btn">
            <input type="submit" value="保存更改" name="save" class="btn">
            <a href="view_posts.php" class="option-btn">返回</a>
            <input type="submit" value="删除文章" class="delete-btn" name="delete_post" onclick="return confirm('确定要删除此文章吗？');">
        </div>
    </form>
    <?php
        }
    } else {
        echo '<p class="empty">未找到文章！</p>';
    ?>
    <div class="flex-btn">
        <a href="view_posts.php" class="option-btn">查看文章列表</a>
        <a href="add_posts.php" class="option-btn">添加文章</a>
    </div>
    <?php
    }
    ?>
</section>

<!-- 自定义 JS 文件 链接 -->
<script src="../js/admin_script.js"></script>

</body>
</html>