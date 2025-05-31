<?php
setlocale(LC_TIME, 'vi_VN.UTF-8');

$action = $_GET['tag'];
$coordinatorUrl = "http://localhost:8004/api.php?action=$action";
$allNews = [];
$response = @file_get_contents($coordinatorUrl);

if ($response !== false) {
    $result = json_decode($response, true);
    // Khi các tag khác trả về mảng thẳng
    if (isset($result[$action]) && is_array($result[$action])) {
        $allNews = $result[$action];
    } else if (is_array($result)) {
        $allNews = $result;
    }
}

usort($allNews, fn($a, $b) => strtotime($b['time_up']) - strtotime($a['time_up']));

// Gán id = index
foreach ($allNews as $key => &$item) {
    $item['id'] = $key;
}
unset($item);

function formatDate($datetime) {
    return strftime('%A, %d %B %Y %H:%M', strtotime($datetime));
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Trang tin tức theo tag</title>
    <link rel="stylesheet" href="css/tags.css"/>
</head>
<body>

    <header class="header">
        <div class="top-bar">
            <span><?php echo strftime('%A, %d %B, %Y'); ?></span>
            <span class="menu-right">The Menu</span>
        </div>
        <h1 class="logo">The <span class="highlight">NEWS*</span></h1>
        <nav class="nav-bar">
            <a href="index.php" class="<?= $action === 'getFullData' ? 'active' : '' ?>">Trang chủ</a>
            <a href="tags.php?tag=getKinhDoanh" class="<?= $action === 'getKinhDoanh' ? 'active' : '' ?>">Kinh doanh</a>
            <a href="tags.php?tag=getChinhTri" class="<?= $action === 'getChinhTri' ? 'active' : '' ?>">Chính trị</a>
            <a href="tags.php?tag=getSucKhoe" class="<?= $action === 'getSucKhoe' ? 'active' : '' ?>">Sức khỏe</a>
            <a href="tags.php?tag=getTheThao" class="<?= $action === 'getTheThao' ? 'active' : '' ?>">Thể thao</a>
            <a href="tags.php?tag=getVHGT" class="<?= $action === 'getVHGT' ? 'active' : '' ?>">Văn hóa - Giải trí</a>
            <a href="tags.php?tag=getDuLich" class="<?= $action === 'getDuLich' ? 'active' : '' ?>">Du lịch</a>
        </nav>
    </header>
    <div class="list-news">
        <?php if (count($allNews) === 0): ?>
            <p>Chưa có bài viết nào.</p>
        <?php else: ?>
            <?php foreach ($allNews as $news): ?>
                <div class="news-item">
                    <div class="news-thumb">
                        <img src="<?= htmlspecialchars($news['image'] ?? 'default.jpg') ?>" alt="Thumbnail">
                    </div>
                    <div class="news-content">
                        <a href="detail.php?id=<?= urlencode($news['id']) ?>" class="news-title">
                            <?= htmlspecialchars($news['title'] ?? 'Không có tiêu đề') ?>
                        </a>
                        <div class="news-sumary">
                            <?= htmlspecialchars($news['sumary'] ?? '') ?>
                        </div>
                        <div class="news-meta">
                            <?= htmlspecialchars($news['category'] ?? 'Chính trị') ?> —
                            <?= formatDate($news['time_up'] ?? '') ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>
