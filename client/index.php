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

function sortByTimeDesc(&$arr) {
    usort($arr, fn($a, $b) => strtotime($b['time_up']) - strtotime($a['time_up']));
}
sortByTimeDesc($allNews);

// Gán id = index cho mỗi phần tử trong $allNews, dùng để làm key duy nhất
foreach ($allNews as $key => &$item) {
    $item['id'] = $key;
}
unset($item);

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
        case 'vh - gt':
            return 'Văn hóa - Giải trí';
        case 'du lich':
            return 'Du lịch';
        case 'the thao':
            return 'Thể thao';
        default:
            return ucfirst($tag);
    }
}

// Lọc theo tag (chú ý chuyển về lowercase để so sánh)
$newsChinhTri = array_filter($allNews, fn($n) => strtolower($n['tags']) === 'chinh tri');
$newsSucKhoe = array_filter($allNews, fn($n) => strtolower($n['tags']) === 'suc khoe');
$newsKinhDoanh = array_filter($allNews, fn($n) => strtolower($n['tags']) === 'kinh doanh');
$newsVHGiaiTri = array_filter($allNews, fn($n) => strtolower($n['tags']) === 'vh - gt');
$newsDuLich = array_filter($allNews, fn($n) => strtolower($n['tags']) === 'du lich');
$newsTheThao = array_filter($allNews, fn($n) => strtolower($n['tags']) === 'the thao');

// reset lại chỉ số mảng đã lọc
$newsChinhTri = array_values($newsChinhTri);
$newsSucKhoe = array_values($newsSucKhoe);
$newsKinhDoanh = array_values($newsKinhDoanh);
$newsVHGiaiTri = array_values($newsVHGiaiTri);
$newsDuLich = array_values($newsDuLich);
$newsTheThao = array_values($newsTheThao);

// Lấy 15 tin mới nhất (đã sắp xếp trước)
$latestNews = array_slice($allNews, 0, 15);

// Lấy 7 tin random
$shuffledNews = $allNews;
shuffle($shuffledNews);
$randomNews = array_slice($shuffledNews, 0, 7);

// Tin lớn là phần tử đầu tiên trong $randomNews
$largeNews = $randomNews[0];

// 5 tin nhỏ là phần tử từ index 1 đến 5
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
        <span class="menu-right">
            <span class="login" onclick="showLoginForm()">Đăng nhập</span>
            <span class="register" onclick="showRegisterForm()">Đăng ký</span>
        </span>
    </div>

    <div id="login-box" class="login-overlay" style="display: none;">
    <div class="login-form">
        <span class="close-btn" onclick="hideForms()">×</span>
        <form method="post" action="login.php">
        <h2>Đăng nhập</h2>
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng nhập</button>
        </form>
    </div>
    </div>

    <div id="register-box" class="login-overlay" style="display: none;">
    <div class="login-form">
        <span class="close-btn" onclick="hideForms()">×</span>
        <form method="post" action="register.php">
        <h2>Đăng ký</h2>
        <input type="text" name="email" placeholder="Email" required>
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng ký</button>
        </form>
    </div>
    </div>


    <h1 class="logo">The <span class="highlight">NEWS*</span></h1>
        <nav class="nav-bar">
            <a href="index.php">Trang chủ</a>
            <a href="tags.php?tag=getKinhDoanh">Kinh doanh</a>
            <a href="tags.php?tag=getChinhTri">Chính trị</a>
            <a href="tags.php?tag=getSucKhoe">Sức khỏe</a>
            <a href="tags.php?tag=getTheThao">Thể thao</a>
            <a href="tags.php?tag=getVHGT">Văn hóa - Giải trí</a>
            <a href="tags.php?tag=getDuLich">Du lịch</a>
        </nav>
    </header>
  <main>
    <section class="featured">
        <?php if (!empty($randomNews[0]['image'])): ?>
            <a href="detail.php?id=<?= urlencode($randomNews[0]['id']) ?>">
                <img src="<?= htmlspecialchars($randomNews[0]['image']) ?>" alt="Main News" />
            </a>
        <?php endif; ?>
        <div class="featured-content">
            <h1>
                <a href="detail.php?id=<?= urlencode($randomNews[0]['id']) ?>">
                    <?= htmlspecialchars($randomNews[0]['title']) ?>
                </a>
            </h1>
            <p>
                <span class="tag"><?= formatTag($randomNews[0]['tags']) ?></span> • 
                <?= htmlspecialchars($randomNews[0]['author'] ?? 'Không rõ') ?> • 
                <?= date('M d, Y', strtotime($randomNews[0]['time_up'])) ?>
            </p>
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
                            <h3>
                                <a href="detail.php?id=<?= $latestNews[10]['id'] ?>">
                                    <?= htmlspecialchars($latestNews[10]['title']) ?>
                                </a>
                            </h3>
                            <p><?= formatTag($latestNews[10]['tags']) ?> — <?= date('M d, Y', strtotime($latestNews[10]['time_up'])) ?></p>
                        </div>
                    </article>
                <?php endif; ?>
            </div>

            <div class="latest-news-right">
                <?php for ($i = 2; $i <= 3; $i++): ?>
                    <?php if (!empty($latestNews[$i])): ?>
                        <article class="news-item-small">
                            <div class="small-news-content">
                                <h4>
                                    <a href="detail.php?id=<?= $latestNews[$i]['id'] ?>">
                                        <?= htmlspecialchars($latestNews[$i]['title']) ?>
                                    </a>
                                </h4>
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
                <a href="detail.php?id=<?= urlencode($news['id']) ?>" style="text-decoration: none; color: black;">
                    <img src="<?= htmlspecialchars($news['image'] ?? 'default-image.jpg') ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                    <h3><?= htmlspecialchars($news['title']) ?></h3>
                    <p><?= formatTag($news['tags']) ?> — <?= date('M d, Y', strtotime($news['time_up'])) ?></p>
                </a>
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
                        <h3>
                            <a href="detail.php?id=<?= urlencode($news['id']) ?>" style="color: black; text-decoration: none;">
                                <?= htmlspecialchars($news['title']) ?>
                            </a>
                        </h3>
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
                        <h3>
                            <a href="detail.php?id=<?= urlencode($news['id']) ?>" style="color: black; text-decoration: none;">
                                <?= htmlspecialchars($news['title']) ?>
                            </a>
                        </h3>
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
                        <h3>
                            <a href="detail.php?id=<?= urlencode($news['id']) ?>" style="color: black; text-decoration: none;">
                                <?= htmlspecialchars($news['title']) ?>
                            </a>
                        </h3>
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
                <h3>
                    <a href="detail.php?id=<?= urlencode($news['id']) ?>" style="color: black; text-decoration: none;">
                        <?= htmlspecialchars($news['title']) ?>
                    </a>
                </h3>
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
                    <h3>
                        <a href="detail.php?id=<?= urlencode($randomNews[1]['id']) ?>" style="color: black; text-decoration: none;">
                            <?= htmlspecialchars($randomNews[1]['title']) ?>
                        </a>
                    </h3>
                    <p class="summary"><?= htmlspecialchars($randomNews[1]['summary'] ?? substr($randomNews[1]['content'] ?? '', 0, 150) . '...') ?></p>
                    <p class="meta"><?= formatTag($randomNews[1]['tags']) ?> — <?= date('M d, Y', strtotime($randomNews[1]['time_up'])) ?></p>
                </article>

                <div class="small-news-list">
                    <?php foreach(array_slice($randomNews, 2, 6) as $news): ?>
                    <article class="small-news">
                        <img src="<?= htmlspecialchars($news['image'] ?? 'default-image.jpg') ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                        <div class="small-news-content">
                            <h4>
                                <a href="detail.php?id=<?= urlencode($news['id']) ?>" style="color: black; text-decoration: none;">
                                    <?= htmlspecialchars($news['title']) ?>
                                </a>
                            </h4>
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
                <li><a href="index.php" data-count="<?= count($allNews) ?>">Tất cả</a></li>
                <li><a href="tags.php?tag=getChinhTri" data-count="<?= count($newsChinhTri) ?>">Chính trị</a></li>
                <li><a href="tags.php?tag=getKinhDoanh" data-count="<?= count($newsKinhDoanh) ?>">Kinh doanh</a></li>
                <li><a href="tags.php?tag=getSucKhoe" data-count="<?= count($newsSucKhoe) ?>">Sức khỏe</a></li>
                <li><a href="tags.php?tag=getTheThao" data-count="<?= count($newsTheThao) ?>">Thể thao</a></li>
                <li><a href="tags.php?tag=getVHGT" data-count="<?= count($newsVHGiaiTri) ?>">Văn hóa - Giải trí</a></li>
                <li><a href="tags.php?tag=getDuLich" data-count="<?= count($newsDuLich) ?>">Du lịch</a></li>
            </ul>
        </aside>
    </section>

  <footer>
    <p>© 2024 The News. All rights reserved.</p>
  </footer>
</body>
<script>
function showLoginForm() {
  document.getElementById("login-box").style.display = "flex";
  document.getElementById("register-box").style.display = "none";
}

function showRegisterForm() {
  document.getElementById("register-box").style.display = "flex";
  document.getElementById("login-box").style.display = "none";
}

function hideForms() {
  document.getElementById("login-box").style.display = "none";
  document.getElementById("register-box").style.display = "none";
}
</script>


</html>
