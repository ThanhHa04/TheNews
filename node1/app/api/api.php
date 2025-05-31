<?php
// node 1 Chính trị và Thể thao
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';


if ($action === 'getOriginal') {
    $dataFile = __DIR__ . '/../data/news.data.json';
} elseif ($action === 'getBackup') {
    $dataFile = __DIR__ . '/../data_backup/node3.data.json';
} elseif (in_array($action, ['getChinhTri', 'getTheThao'])) {
    $dataFile = __DIR__ . '/../data/news.data.json';
} elseif (in_array($action, ['getDuLich', 'getVHGT'])) {
    $dataFile = __DIR__ . '/../data_backup/node3.data.json';
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
} elseif ($action === 'getChinhTri') {
    $filtered = filterByTag($data, 'chinh tri');
    echo json_encode(array_values($filtered), JSON_PRETTY_PRINT);
} elseif ($action === 'getTheThao') {
    $filtered = filterByTag($data, 'the thao');
    echo json_encode(array_values($filtered), JSON_PRETTY_PRINT);
} elseif ($action === 'getVHGT') {
    $filtered = filterByTag($data, 'VH - GT');
    echo json_encode(array_values($filtered), JSON_PRETTY_PRINT);
} elseif ($action === 'getDuLich') {
    $filtered = filterByTag($data, 'du lich');
    echo json_encode(array_values($filtered), JSON_PRETTY_PRINT);
}
?>
