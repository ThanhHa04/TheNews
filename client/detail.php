<?php
// Danh sách API node
$nodes = [
    'http://localhost:8001/app/api/api.php?action=getOriginal',
    'http://localhost:8002/app/api/api.php?action=getOriginal',
    'http://localhost:8003/app/api/api.php?action=getOriginal',
];

// Hàm lấy dữ liệu JSON từ node
function getDataFromNode($url) {
    $json = @file_get_contents($url);
    if ($json === false) {
        return [];
    }
    return json_decode($json, true);
}

// Lấy dữ liệu tổng hợp từ các node
$allNews = [];
foreach ($nodes as $nodeApi) {
    $data = getDataFromNode($nodeApi);
    if (is_array($data)) {
        $allNews = array_merge($allNews, $data);
    }
}

// Lấy id bài viết từ URL
$id = $_GET['id'] ?? null;

if ($id === null) {
    echo "Không có bài viết được chỉ định.";
    exit;
}

// Tìm bài viết theo id
$newsDetail = null;
foreach ($allNews as $news) {
    if (isset($news['id']) && $news['id'] == $id) {
        $newsDetail = $news;
        break;
    }
}

if (!$newsDetail) {
    echo "Không tìm thấy bài viết với ID: " . htmlspecialchars($id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($newsDetail['title']) ?></title>
    <style>
        body { max-width: 800px; margin: 30px auto; font-family: Arial, sans-serif; line-height: 1.6; padding: 10px; background: #f9f9f9; color: #333; }
        header, footer { text-align: center; background: #eee; padding: 10px; border-radius: 6px; margin-bottom: 20px; }
        h1 { color: #0077cc; }
        .content { background: white; padding: 20px; border-radius: 6px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        a.back-link { display: inline-block; margin-top: 20px; color: #0077cc; text-decoration: none; }
        a.back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<header>
    <h1><?= htmlspecialchars($newsDetail['title']) ?></h1>
</header>

<div class="content">
    <p><strong>Tóm tắt:</strong> <?= nl2br(htmlspecialchars($newsDetail['sumary'] ?? '')) ?></p>
    <hr>
    <p><?= nl2br(htmlspecialchars($newsDetail['content'] ?? $newsDetail['sumary'] ?? '')) ?></p>
</div>

<footer>
    <a href="index.php" class="back-link">&laquo; Quay lại trang chủ</a>
</footer>
</body>
</html>
