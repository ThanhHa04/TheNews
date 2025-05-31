<?php
$coordinatorUrl = 'http://localhost:8004/api.php?action=getFullData';

$allNews = [];

$response = @file_get_contents($coordinatorUrl);

if ($response !== false) {
    $result = json_decode($response, true);
    if (isset($result['fullData']) && is_array($result['fullData'])) {
        foreach ($result['fullData'] as $nodeName => $nodeData) {
            if (is_array($nodeData)) {
                foreach ($nodeData as $item) {
                    if (is_array($item) && isset($item['time_up'])) {
                        $allNews[] = $item;
                    }
                }
            }
        }
    }
}

// Lấy ID từ URL
$id = $_GET['id'] ?? -1;
$newsDetail = $allNews[$id];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($newsDetail['title']) ?></title>
    <link rel="stylesheet" href="css/detail.css"/>
</head>
<body>
<header>
    <div class="top-bar">
        <span><?php echo strftime('%A, %d %B, %Y'); ?></span>
        <a href="index.php">Quay lại ▸</a>
    </div>
    <h1><?= htmlspecialchars($newsDetail['title']) ?></h1>
    <?php if (!empty($newsDetail['author']) || !empty($newsDetail['time_up'])): ?>
    <div class="meta-info">
        <?= !empty($newsDetail['author']) ? 'Tác giả: ' . htmlspecialchars($newsDetail['author']) : '' ?>
        <?= (!empty($newsDetail['author']) && !empty($newsDetail['time_up'])) ? ' | ' : '' ?>
        <?= !empty($newsDetail['time_up']) ? 'Ngày đăng: ' . date('d/m/Y H:i', strtotime($newsDetail['time_up'])) : '' ?>
    </div>
    <?php endif; ?>
</header>

<?php if (!empty($newsDetail['image'])): ?>
    <div class="news-image">
        <img src="<?= htmlspecialchars($newsDetail['image']) ?>" alt="<?= htmlspecialchars($newsDetail['title']) ?>" />
    </div>
<?php endif; ?>

<div class="content">
    <div class="summary"><?= nl2br(htmlspecialchars($newsDetail['sumary'] ?? '')) ?></div>
    <hr>
    <div class="article-content"><?= nl2br(htmlspecialchars($newsDetail['content'] ?? $newsDetail['sumary'] ?? '')) ?></div>
</div>

<footer>
    <a href="index.php" class="back-link">&laquo; Quay lại trang chủ</a>
</footer>
</body>
</html>
