<?php
$coordinatorUrl = 'http://localhost:8004/coordinator.php?action=getFullData';

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

// Hàm sắp xếp giảm dần theo thời gian
function sortByTimeDesc(&$arr) {
    usort($arr, fn($a, $b) => strtotime($b['time_up']) - strtotime($a['time_up']));
}

// Hàm format tag
function formatTag($tag) {
    $tag = strtolower(trim($tag));
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
            return ucfirst($tag);
    }
}

// Lọc theo tag
$newsChinhTri = array_filter($allNews, fn($n) => strtolower($n['tags']) === 'chinh tri');
$newsSucKhoe = array_filter($allNews, fn($n) => strtolower($n['tags']) === 'suc khoe');
$newsKinhDoanh = array_filter($allNews, fn($n) => strtolower($n['tags']) === 'kinh doanh');
$newsVHGiaiTri = array_filter($allNews, fn($n) => strtolower($n['tags']) === 'vh - gt');
$newsDuLich = array_filter($allNews, fn($n) => strtolower($n['tags']) === 'du lich');
$newsTheThao = array_filter($allNews, fn($n) => strtolower($n['tags']) === 'the thao');

// reset lại id của mảng đã lọc 
$newsChinhTri = array_values($newsChinhTri);
$newsSucKhoe = array_values($newsSucKhoe);
$newsKinhDoanh = array_values($newsKinhDoanh);
$newsVHGiaiTri = array_values($newsVHGiaiTri);
$newsDuLich = array_values($newsDuLich);
$newsTheThao = array_values($newsTheThao);

// Sắp xếp từng mảng
sortByTimeDesc($newsChinhTri);
sortByTimeDesc($newsSucKhoe);
sortByTimeDesc($newsKinhDoanh);
sortByTimeDesc($newsVHGiaiTri);
sortByTimeDesc($newsDuLich);
sortByTimeDesc($newsTheThao);
sortByTimeDesc($allNews);

// Lấy 3 tin mới nhất
$latestNews = array_slice($allNews, 0, 15);

// Lấy 7 tin random
$shuffledNews = $allNews;
shuffle($shuffledNews);
$randomNews = array_slice($shuffledNews, 0, 7);

// Tin lớn là phần tử đầu tiên
$largeNews = $randomNews[0];

// 5 tin nhỏ là phần tử từ index 1 đến 5 (tức id 2 đến 6 trong mảng)
$smallNews = array_slice($randomNews, 1, 5);

setlocale(LC_TIME, 'vi_VN.UTF-8');
date_default_timezone_set('Asia/Ho_Chi_Minh');

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>The News</title>
  <link rel="stylesheet" href="css/index.css"/>
</head>
<body>
  <header class="header">
    <div class="top-bar">
      <span><?php echo strftime('%A, %d %B, %Y'); ?></span>
      <span class="menu-right">The Menu ▸</span>
    </div>
    <h1 class="logo">The <span class="highlight">NEWS*</span></h1>
    <nav class="nav-bar">
      <a href="#"></a><a href="#">Mới nhất</a><a href="#">Kinh doanh</a>
      <a href="#">Chính trị</a><a href="#">Sức khỏe</a><a href="#">Thể thao</a>
      <a href="#">Văn hóa - Giải trí</a><a href="#">Du lịch</a>
    </nav>
  </header>

  <main>
    <section class="featured">
        <?php if (!empty($randomNews[0]['image'])): ?>
            <img src="<?= htmlspecialchars($randomNews[0]['image']) ?>" alt="Main News" />
        <?php endif; ?>
        <div class="featured-content">
            <h1><?= htmlspecialchars($randomNews[0]['title']) ?></h1>
            <p><span class="tag"><?= formatTag($randomNews[0]['tags']) ?></span> • <?= htmlspecialchars($randomNews[0]['author'] ?? 'Không rõ') ?> • <?= date('M d, Y', strtotime($randomNews[0]['time_up'])) ?></p>
        </div>
    </section>

    <!-- Latest News -->
    <section class="latest-news-custom">
        <h1 class="latest-news-heading">Tin mới nhất</h1>
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

    <!-- Health News -->
    <section class="section-news">
        <h1>Sức khỏe</h1>
        <div class="news-grid">
            <?php foreach (array_slice($newsSucKhoe, 0, 4) as $news) : ?>
            <article>
                <img src="<?= htmlspecialchars($news['image'] ?? 'default-image.jpg') ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                <h3><?= htmlspecialchars($news['title']) ?></h3>
                <p><?= formatTag($news['tags']) ?> — <?= date('M d, Y', strtotime($news['time_up'])) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

        <section class="section-news">
        <div class="three-columns-grid">
            <!-- Cột Chính trị -->
            <div class="news-column">
                <h2>Chính trị</h2>
                <?php foreach (array_slice($newsChinhTri, 0, 4) as $news) : ?>
                <article>
                    <div class="news-image">
                        <img src="<?= htmlspecialchars($news['image'] ?? 'default-image.jpg') ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                    </div>
                    <div class="news-content">
                        <h3><?= htmlspecialchars($news['title']) ?></h3>
                        <p class="meta"><?= formatTag($news['tags']) ?> — <?= date('M d, Y', strtotime($news['time_up'])) ?></p>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Cột Thể thao -->
            <div class="news-column">
                <h2>Thể thao</h2>
                <?php foreach (array_slice($newsTheThao, 0, 4) as $news) : ?>
                <article>
                    <div class="news-image">
                        <img src="<?= htmlspecialchars($news['image'] ?? 'default-image.jpg') ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                    </div>
                    <div class="news-content">
                        <h3><?= htmlspecialchars($news['title']) ?></h3>
                        <p class="meta"><?= formatTag($news['tags']) ?> — <?= date('M d, Y', strtotime($news['time_up'])) ?></p>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Cột Kinh doanh -->
            <div class="news-column">
                <h2>Kinh doanh</h2>
                <?php foreach (array_slice($newsKinhDoanh, 0, 4) as $news) : ?>
                <article>
                    <div class="news-image">
                        <img src="<?= htmlspecialchars($news['image'] ?? 'default-image.jpg') ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                    </div>
                    <div class="news-content">
                        <h3><?= htmlspecialchars($news['title']) ?></h3>
                        <p class="meta"><?= formatTag($news['tags']) ?> — <?= date('M d, Y', strtotime($news['time_up'])) ?></p>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Travel News -->
    <section class="section-news">
        <h1>Du lịch</h1>
        <div class="news-grid">
            <?php foreach (array_slice($newsDuLich, 0, 4) as $news) : ?>
            <article>
                <img src="<?= htmlspecialchars($news['image'] ?? 'default-image.jpg') ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                <h3><?= htmlspecialchars($news['title']) ?></h3>
                <p><?= formatTag($news['tags']) ?> — <?= date('M d, Y', strtotime($news['time_up'])) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section-random-tags">
        <div class="random-news">
            <h2>Đừng bỏ lỡ</h2>
            <div class="random-news-grid">
                <article class="large-news">
                    <img src="<?= htmlspecialchars($randomNews[1]['image'] ?? 'default-image.jpg') ?>" alt="<?= htmlspecialchars($randomNews[1]['title']) ?>">
                    <h3><?= htmlspecialchars($randomNews[1]['title']) ?></h3>
                    <p class="summary"><?= htmlspecialchars($randomNews[1]['summary'] ?? substr($randomNews[1]['content'] ?? '', 0, 150) . '...') ?></p>
                    <p class="meta"><?= formatTag($randomNews[1]['tags']) ?> — <?= date('M d, Y', strtotime($randomNews[1]['time_up'])) ?></p>
                </article>

                <div class="small-news-list">
                    <?php foreach(array_slice($randomNews, 2, 6) as $news): ?>
                    <article class="small-news">
                        <img src="<?= htmlspecialchars($news['image'] ?? 'default-image.jpg') ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                        <div class="small-news-content">
                            <h4><?= htmlspecialchars($news['title']) ?></h4>
                            <p class="meta"><?= formatTag($news['tags']) ?> — <?= date('M d, Y', strtotime($news['time_up'])) ?></p>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <aside class="tag-sidebar">
            <h2>Thể loại</h2>
            <ul class="tag-list">
                <li><a href="#" data-count="<?= count($allNews) ?>">Tất cả</a></li>
                <li><a href="#" data-count="<?= count($newsChinhTri) ?>">Chính trị</a></li>
                <li><a href="#" data-count="<?= count($newsKinhDoanh) ?>">Kinh doanh</a></li>
                <li><a href="#" data-count="<?= count($newsSucKhoe) ?>">Sức khỏe</a></li>
                <li><a href="#" data-count="<?= count($newsTheThao) ?>">Thể thao</a></li>
                <li><a href="#" data-count="<?= count($newsVHGiaiTri) ?>">Văn hóa - Giải trí</a></li>
                <li><a href="#" data-count="<?= count($newsDuLich) ?>">Du lịch</a></li>
            </ul>
        </aside>
    </section>

  <footer>
    <p>© 2024 The News. All rights reserved.</p>
  </footer>
</body>
</html>
