<?php

// Initialize message array
$message = [];

if (isset($_POST['like_post'])) {
    if ($user_id != '') {
        $post_id = filter_var($_POST['post_id'], FILTER_VALIDATE_INT);
        $admin_id = filter_var($_POST['admin_id'], FILTER_VALIDATE_INT);

        // Validate that post_id and admin_id are positive integers
        if ($post_id === false || $post_id <= 0 || $admin_id === false || $admin_id <= 0) {
            $message[] = '无效的文章或管理员ID！';
        } else {
            $select_post_like = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ? AND user_id = ?");
            $select_post_like->execute([$post_id, $user_id]);

            if ($select_post_like->rowCount() > 0) {
                $remove_like = $conn->prepare("DELETE FROM `likes` WHERE post_id = ? AND user_id = ?");
                $remove_like->execute([$post_id, $user_id]);
                $message[] = '已从点赞中移除';
            } else {
                $add_like = $conn->prepare("INSERT INTO `likes`(user_id, post_id, admin_id) VALUES(?,?,?)");
                $add_like->execute([$user_id, $post_id, $admin_id]);
                $message[] = '已添加到点赞';
            }
        }
    } else {
        $message[] = '请先登录！';
    }
}

?>