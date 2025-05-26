<?php
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'getOriginal') {
    $dataFile = __DIR__ . '/../data/news.data.json';
} elseif ($action === 'getBackup') {
    $dataFile = __DIR__ . '/../data_backup/node3.data.json';
} else {
    echo json_encode(['error' => 'Unknown action']);
    exit;
}

if (!file_exists($dataFile)) {
    echo json_encode(['error' => 'Data file not found']);
    exit;
}

$jsonData = file_get_contents($dataFile);
echo $jsonData;
?>
