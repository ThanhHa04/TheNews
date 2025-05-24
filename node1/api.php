<?php
header('Content-Type: application/json');

$dataFile = __DIR__ . '/data/news.data.json';

if (!file_exists($dataFile)) {
    echo json_encode(['error' => 'Data file not found']);
    exit;
}

// Đọc dữ liệu JSON
$jsonData = file_get_contents($dataFile);
echo $jsonData;
?>