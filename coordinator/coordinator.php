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

function callApi($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($err || $httpcode != 200) {
        return false;
    }
    return $response;
}

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'getFullData') {
        $fullData = [];

        foreach ($nodes as $nodeName => $nodeUrl) {
            $data = callApi($nodeUrl);
            if ($data === false) {
                $backupUrl = $backups[$nodeName];
                $data = callApi($backupUrl);
                if ($data === false) {
                    $fullData[$nodeName] = null;
                    continue;
                }
            }
            $decoded = json_decode($data, true);
            $fullData[$nodeName] = $decoded !== null ? $decoded : $data;
        }

        header('Content-Type: application/json');
        echo json_encode(['fullData' => $fullData], JSON_PRETTY_PRINT);
        exit;
    } else {
        // Có thể thêm xử lý các action khác ở đây
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        exit;
    }
}

// Nếu không phải GET
http_response_code(405);
header('Content-Type: application/json');
echo json_encode(['error' => 'Method not allowed. Use GET request.']);
?>