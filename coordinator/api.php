<?php
$nodes = [
    'node1' => 'http://node1/app/api/api.php?action=getOriginal',
    'node2' => 'http://node2/app/api/api.php?action=getOriginal',
    'node3' => 'http://node3/app/api/api.php?action=getOriginal',
];

$backups = [
    'node1' => 'http://node2/app/api/api.php?action=getBackup',
    'node2' => 'http://node3/app/api/api.php?action=getBackup',
    'node3' => 'http://node1/app/api/api.php?action=getBackup',
];

$singleActions = [
    'getChinhTri' => ['http://node1/app/api/api.php?action=getChinhTri', 'http://node2/app/api/api.php?action=getChinhTri'],
    'getTheThao'  => ['http://node1/app/api/api.php?action=getTheThao', 'http://node2/app/api/api.php?action=getTheThao'],
    'getVHGT'     => ['http://node3/app/api/api.php?action=getVHGT', 'http://node1/app/api/api.php?action=getVHGT'],
    'getDulich'   => ['http://node3/app/api/api.php?action=getDulich', 'http://node1/app/api/api.php?action=getDulich'],
    'getSucKhoe'  => ['http://node2/app/api/api.php?action=getSucKhoe', 'http://node3/app/api/api.php?action=getSucKhoe'],
    'getKinhDoanh'=> ['http://node2/app/api/api.php?action=getKinhDoanh', 'http://node3/app/api/api.php?action=getKinhDoanh'],
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
    } elseif (isset($singleActions[$action])) {
        [$urlPrimary, $urlBackup] = $singleActions[$action];
        $data = callApi($urlPrimary);
        if ($data === false) {
            $data = callApi($urlBackup);
            if ($data === false) {
                http_response_code(500);
                echo json_encode(['error' => "Không thể lấy dữ liệu $action từ cả 2 node"]);
                exit;
            }
        }
        $decoded = json_decode($data, true);
        header('Content-Type: application/json');
        echo json_encode([$action => $decoded !== null ? $decoded : $data], JSON_PRETTY_PRINT);
        exit;
    } else {
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
