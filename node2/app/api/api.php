<?php
// node2: Sức khỏe và Kinh doanh
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';


if ($action === 'getOriginal') {
    $dataFile = __DIR__ . '/../data/news.data.json';
} elseif ($action === 'getBackup') {
    $dataFile = __DIR__ . '/../data_backup/node1.data.json';
} elseif (in_array($action, ['getSucKhoe', 'getKinhDoanh'])) {
    $dataFile = __DIR__ . '/../data/news.data.json';
} elseif (in_array($action, ['getChinhTri', 'getTheThao'])) {
    $dataFile = __DIR__ . '/../data_backup/node1.data.json';
} else {
    echo json_encode(['error' => 'Unknown action']);
    exit;
}

function filterByTag(array $data, string $tag): array {
    $tag = mb_strtolower($tag);
    return array_filter($data, function($item) use ($tag) {
        if (!isset($item['tags'])) return false;
        if (is_string($item['tags'])) {
            return mb_strtolower($item['tags']) === $tag;
        }
        return false;
    });
}

if (!file_exists($dataFile)) {
    echo json_encode(['error' => 'Data file not found']);
    exit;
}

$jsonData = file_get_contents($dataFile);
$data = json_decode($jsonData, true);
if ($data === null) {
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

if ($action === 'getOriginal' || $action === 'getBackup') {
    echo json_encode($data, JSON_PRETTY_PRINT);
} elseif ($action === 'getSucKhoe') {
    $filtered = filterByTag($data, 'suc khoe');
    echo json_encode(array_values($filtered), JSON_PRETTY_PRINT);
} elseif ($action === 'getKinhDoanh') {
    $filtered = filterByTag($data, 'kinh doanh');
    echo json_encode(array_values($filtered), JSON_PRETTY_PRINT);
} elseif ($action === 'getChinhTri') {
    $filtered = filterByTag($data, 'chinh tri');
    echo json_encode(array_values($filtered), JSON_PRETTY_PRINT);
} elseif ($action === 'getTheThao') {
    $filtered = filterByTag($data, 'the thao');
    echo json_encode(array_values($filtered), JSON_PRETTY_PRINT);
}
?>
