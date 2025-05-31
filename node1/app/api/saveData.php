<?php
// Đường dẫn file lưu dữ liệu chính và backup
$dataFile = __DIR__ . '/../data/news.data.json';
$backupFile = __DIR__ . '/../data_backup/node3.data.json';
$nodeBackup = 'http://localhost:8002/saveData.php?action=newDataBackup';

function sendBackup($data, $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    if ($response === false) {
        error_log('Failed to send backup to node 2: ' . curl_error($ch));
    }
    curl_close($ch);
}

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $postData = json_decode($input, true);

    if ($postData === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        exit;
    }

    if ($action === 'newData') {
        if (file_put_contents($dataFile, json_encode($postData, JSON_PRETTY_PRINT))) {
            echo json_encode(['message' => 'Data saved successfully']);
            sendBackup($postData, $nodeBackup);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save data']);
        }
        exit;

    } elseif ($action === 'newDataBackup') {
        if (file_put_contents($backupFile, json_encode($postData, JSON_PRETTY_PRINT))) {
            echo json_encode(['message' => 'Backup data saved successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save backup data']);
        }
        exit;

    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        exit;
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
?>
