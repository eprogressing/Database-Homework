<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    header('location:admin_login.php');
    exit();
}

// 初始化消息数组
$message = [];

// 获取管理员信息
$select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function handleImageUpload($file, $admin_id, $conn) {
    global $message;
    
    if (empty($file['name'])) return null;
    
    // 生成唯一文件名防止重复
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $image = uniqid('img_') . '.' . $file_extension;
    $image_size = $file['size'];
    $image_tmp_name = $file['tmp_name'];
    
    if ($image_size > 2000000) {
        $message[] = '图片大小不能超过2MB！';
        return false;
    }
    
    $upload_dir = '../uploaded_img/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $target_path = $upload_dir . $image;
    if (move_uploaded_file($image_tmp_name, $target_path)) {
        return $image;
    }
    
    $message[] = '文件上传失败，请检查目录权限';
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 清理输入数据
    $name = sanitizeInput($fetch_profile['name'] ?? '');
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = $_POST['content'] ?? ''; // 保留HTML标签用于富文本内容
    $category = sanitizeInput($_POST['category'] ?? '');
    $status = isset($_POST['publish']) ? 'active' : 'deactive';

    // 处理图片上传
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $image = handleImageUpload($_FILES['image'], $admin_id, $conn);
    }

    // 验证必填字段
    $required_fields = [
        '标题' => $title,
        '内容' => $content,
        '分类' => $category
    ];

    foreach ($required_fields as $field => $value) {
        if (empty($value)) {
            $message[] = "请填写{$field}";
        }
    }

    // 无错误时提交数据
    if (empty($message)) {
        try {
            $insert_post = $conn->prepare("INSERT INTO `posts` 
                (admin_id, name, title, content, category, image, status) 
                VALUES(?, ?, ?, ?, ?, ?, ?)");
                
            $success = $insert_post->execute([
                $admin_id,
                $name,
                $title,
                $content,
                $category,
                $image,
                $status
            ]);

            if ($success) {
                $message[] = $status === 'active' ? '文章已发布！' : '草稿已保存！';
                // 清空表单数据
                $_POST = [];
                $title = $content = $category = '';
            } else {
                $message[] = '操作失败，请重试';
            }
            
        } catch (PDOException $e) {
            $message[] = '数据库错误：' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章发布</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<?php include '../components/admin_header.php' ?>

<section class="post-editor">
    <h1 class="heading">添加新文章</h1>

    <?php if (!empty($message) && is_array($message)) : ?>
        <?php foreach ($message as $msg) : ?>
            <div class="message">
                <span><?= htmlspecialchars($msg) ?></span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <?php if (!is_array($message)) : ?>
            <div class="message">
                <span>系统错误：消息格式无效</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <p>文章标题 <span>*</span></p>
        <input type="text" name="title" required class="box" 
               placeholder="输入文章标题" maxlength="100"
               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
        
        <p>文章内容 <span>*</span></p>
        <textarea name="content" class="box" required cols="30" rows="10"
                  placeholder="撰写文章内容..."><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
        
        <p>文章分类 <span>*</span></p>
        <select name="category" class="box" required>
            <option value="">-- 请选择分类 --</option>
            <?php
            $categories = [
                'nature' => '自然',
                'education' => '教育',
                'pets and animals' => '宠物与动物',
                'technology' => '科技',
                'fashion' => '时尚',
                'entertainment' => '娱乐',
                'movies and animations' => '影视动画',
                'gaming' => '游戏',
                'music' => '音乐',
                'sports' => '体育',
                'news' => '新闻',
                'travel' => '旅行',
                'comedy' => '喜剧',
                'design and development' => '设计与开发',
                'food and drinks' => '美食',
                'lifestyle' => '生活方式',
                'personal' => '个人',
                'health and fitness' => '健康与健身',
                'business' => '商业',
                'shopping' => '购物',
                'animations' => '动画'
            ];
            
            foreach ($categories as $value => $label) :
                $selected = ($_POST['category'] ?? '') === $value ? 'selected' : '';
            ?>
                <option value="<?= $value ?>" <?= $selected ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>

        <p>文章图片（可选）</p>
        <input type="file" name="image" class="box" accept="image/*">
        <small>支持格式: JPEG, PNG | 最大尺寸: 2MB</small>
        
        <div class="flex-btn">
            <button type="submit" name="publish" class="btn">立即发布</button>
            <button type="submit" name="draft" class="option-btn">保存草稿</button>
        </div>
    </form>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>