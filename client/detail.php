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
    echo "<p style='text-align:center; margin-top: 50px; font-size:1.2em; color: #666;'>Không có bài viết được chỉ định.</p>";
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
    echo "<p style='text-align:center; margin-top: 50px; font-size:1.2em; color: #666;'>Không tìm thấy bài viết với ID: " . htmlspecialchars($id) . "</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($newsDetail['title']) ?></title>
    <style>
        /* Reset cơ bản */
        * {
            box-sizing: border-box;
        }
        body {
            max-width: 1000px;
            margin: 40px auto;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.8;
            color: #2c3e50;
            background-color: #fefefe;
            padding: 0 20px 50px 20px;
        }
        .article-content {
            line-height: 1.8;        /* Tăng khoảng cách dòng */
            text-align: justify;     /* Căn đều 2 bên */
            margin-top: 20px;
            font-size: 1.1em;
            white-space: pre-line;   /* Giữ xuống dòng trong chuỗi */
            color: #2c3e50;
        }

        .news-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: block;
            margin: 0 auto;
        }



        header {
            border-bottom: 2px solidrgb(35, 39, 42);
            padding-bottom: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        header h1 {
            font-size: 2.2em;
            color: black;
            font-weight: 700;
            line-height: 1.2;
        }
        .meta-info {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-top: 8px;
            font-style: italic;
        }
        .content {
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            font-size: 1.1em;
            color: #34495e;
        }
        .summary {
            font-weight: 600;
            font-size: 1.15em;
            color: #2c3e50;
            margin-bottom: 25px;
            border-left: 4px solid #3498db;
            padding-left: 15px;
            background-color: #ecf0f1;
            line-height: 1.6;
            text-align: justify;
            white-space: pre-line;
        }

        p {
            margin-bottom: 1.3em;
        }
        hr {
            border: none;
            border-top: 1px solid #dcdcdc;
            margin: 30px 0;
        }
        a.back-link {
            display: inline-block;
            margin-top: 40px;
            text-decoration: none;
            color: #2980b9;
            font-weight: 600;
            font-size: 1em;
            transition: color 0.3s ease;
        }
        a.back-link:hover {
            color: #1abc9c;
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 600px) {
            body {
                padding: 0 10px 30px 10px;
            }
            header h1 {
                font-size: 1.6em;
            }
            .content {
                padding: 20px 15px;
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
<header>
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
    <div class="news-image" style="text-align:center; margin: 25px 0;">
        <img src="<?= htmlspecialchars($newsDetail['image']) ?>" alt="<?= htmlspecialchars($newsDetail['title']) ?>" style="max-width:100%; height:auto; border-radius:8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);" />
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
