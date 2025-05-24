<?php
$nodes = [
    'http://localhost:8001/api.php',
    'http://localhost:8002/api.php',
    'http://localhost:8003/api.php'
];

$allNews = [];

function getDataFromNode($url) {
    $json = @file_get_contents($url);
    if ($json === false) {
        return [];
    }
    return json_decode($json, true);
}

// Lấy dữ liệu từ các node
foreach ($nodes as $nodeApi) {
    $data = getDataFromNode($nodeApi);
    if (is_array($data)) {
        $allNews = array_merge($allNews, $data);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Trang Tin Tức PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 10px; background: #f5f5f5;
            color: #333;
        }
        header, footer { text-align: center; padding: 10px; background: #ddd; border-radius: 8px; }
        article { margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 5px; }
        h2 { color: #0077cc; }
    </style>
</head>
<body>
    <header>
        <h1>Trang Tin Tức Tổng Hợp</h1>
    </header>

    <main>
        <?php if (count($allNews) === 0): ?>
            <p>Không có tin tức nào.</p>
        <?php else: ?>
            <?php foreach ($allNews as $news): ?>
                <article>
                    <h2><?= htmlspecialchars($news['title']) ?></h2>
                    <p><?= htmlspecialchars($news['sumary']) ?></p>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <footer>
        <p>© 2025 Baby News</p>
    </footer>
</body>
</html>
