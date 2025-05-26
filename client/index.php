<?php
$nodes = [
    'node1' => 'http://localhost:8001/app/api/api.php?action=getOriginal',
    'node2' => 'http://localhost:8002/app/api/api.php?action=getOriginal',
    'node3' => 'http://localhost:8003/app/api/api.php?action=getOriginal',
];

$backups = [
    'node1' => 'http://localhost:8002/app/api/api.php?action=getBackup',
    'node2' => 'http://localhost:8003/app/api/api.php?action=getBackup',
    'node3' => 'http://localhost:8001/app/api/api.php?action=getBackup',
];

$allNews = [];

foreach ($nodes as $nodeName => $url) {
    $response = @file_get_contents($url);

    // Nếu lỗi, thử backup
    if ($response === false && isset($backups[$nodeName])) {
        $response = @file_get_contents($backups[$nodeName]);
    }

    if ($response !== false) {
        $data = json_decode($response, true);
        
        // Bỏ qua nếu không phải array đúng chuẩn
        if (is_array($data)) {
            foreach ($data as $item) {
                if (is_array($item) && isset($item['time_up'])) {
                    $allNews[] = $item;
                }
            }
        }
    }
}

// Sắp xếp giảm dần theo thời gian
usort($allNews, function ($a, $b) {
    return strtotime($b['time_up']) - strtotime($a['time_up']);
});

// Format tag
function formatTag($tag) {
    $tag = strtolower(trim($tag)); // chuyển về chữ thường và bỏ khoảng trắng thừa
    switch ($tag) {
        case 'chinh tri':
            return 'Chính trị';
        case 'suc khoe':
            return 'Sức khỏe' ;
        case 'giao duc':
            return 'Giáo dục';
        case 'VH - GT':
            return 'Văn hóa - Giải trí';
        case 'du lich':
            return 'Du lịch';
        case 'the thao':
            return 'Thể thao';
        default:
            return ucfirst($tag); // Viết hoa chữ cái đầu nếu không khớp
    }
}
// Lấy một tin random
$randomNews = $allNews[array_rand($allNews)];

// Lấy 3 tin mới nhất
$latestNews = array_slice($allNews, 0, 15);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>The News</title>
  <link rel="stylesheet" href="css/index.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
</head>
<body>
  <header class="header">
    <div class="top-bar">
      <span>Tuesday, October 29, 2024</span>
      <span class="menu-right">The Menu ▸</span>
    </div>
    <h1 class="logo">The <span class="highlight">NEWS*</span></h1>
    <nav class="nav-bar">
      <a href="#">World News</a><a href="#">Politics</a><a href="#">Business</a>
      <a href="#">Technology</a><a href="#">Health</a><a href="#">Sports</a>
      <a href="#">Culture</a><a href="#">Podcast</a>
    </nav>
  </header>

  <main>
    <!-- Big featured banner -->
    <section class="featured">
        <?php if (!empty($randomNews['image'])): ?>
            <img src="<?= htmlspecialchars($randomNews['image']) ?>" alt="Main News" />
        <?php endif; ?>
        <div class="featured-content">
            <h2><?= htmlspecialchars($randomNews['title']) ?></h2>
            <p><span class="tag"><?= formatTag($randomNews['tags']) ?></span> • <?= htmlspecialchars($randomNews['author'] ?? 'Không rõ') ?> • <?= date('M d, Y', strtotime($randomNews['time_up'])) ?></p>
        </div>
    </section>

    <!-- Latest News -->
    <section class="latest-news-custom">
        <h2 class="latest-news-heading">Latest News</h2>
        <div class="latest-news-layout">
            <div class="latest-news-left">
                <?php if (!empty($latestNews[10])): ?>
                    <article class="news-item-large">
                        <?php if (!empty($latestNews[10]['image'])): ?>
                            <img src="<?= htmlspecialchars($latestNews[10]['image']) ?>" alt="">
                        <?php endif; ?>
                        <div class="news-content">
                            <h3><?= htmlspecialchars($latestNews[10]['title']) ?></h3>
                            <p><?= formatTag($latestNews[10]['tags']) ?> — <?= date('M d, Y', strtotime($latestNews[0]['time_up'])) ?></p>
                        </div>
                    </article>
                <?php endif; ?>
            </div>
            <div class="latest-news-right">
                <?php for ($i = 2; $i <= 3; $i++): ?>
                    <?php if (!empty($latestNews[$i])): ?>
                        <article class="news-item-small">
                            <div class="small-news-content">
                                <h4><?= htmlspecialchars($latestNews[$i]['title']) ?></h4>
                                <p><?= formatTag($latestNews[$i]['tags']) ?> — <?= date('M d, Y', strtotime($latestNews[$i]['time_up'])) ?></p>
                            </div>
                            <?php if (!empty($latestNews[$i]['image'])): ?>
                                <div class="small-news-thumb">
                                    <img src="<?= htmlspecialchars($latestNews[$i]['image']) ?>" alt="">
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- Technology News -->
    <section class="section-news">
      <h2>Technology News</h2>
      <div class="news-grid">
        <article><img src="https://yourdomain.com/images/car.jpg"><h3>Latest Innovations Pave the Way</h3></article>
        <article><img src="https://yourdomain.com/images/gpt.jpg"><h3>Understanding big data in driving tech</h3></article>
        <article><img src="https://yourdomain.com/images/ai.jpg"><h3>Exploring developments in AI</h3></article>
        <article><img src="https://yourdomain.com/images/keyboard.jpg"><h3>Future of computing and its role</h3></article>
      </div>
    </section>

    <!-- Podcast Section -->
    <section class="section-news">
      <h2>Podcasts</h2>
      <div class="news-grid podcast-grid">
        <article><h3>Where ideas come alive</h3><p>Guy Hawkins • 6 min</p></article>
        <article><h3>On the air capturing the change</h3><p>Guy Hawkins • 8 min</p></article>
      </div>
    </section>
  </main>

  <footer>
    <p>© 2024 The News. All rights reserved.</p>
  </footer>
</body>
</html>
