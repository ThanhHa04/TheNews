<?php
$nodes = [
    'node1' => 'http://localhost:8001/app/api/api.php',
    'node2' => 'http://localhost:8002/app/api/api.php',
    'node3' => 'http://localhost:8003/app/api/api.php',
];

function callApiPost($url, $postData) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $postData = json_decode($input, true);
    if ($postData === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        exit;
    }

    if ($action === 'newData') {
        // Xác định node chính dựa trên tags
        $tags = $postData['tags'] ?? [];
        $nodeMain = '';
        if (array_intersect($tags, ['chinh tri', 'the thao'])) {
            $nodeMain = 'node1';
        } elseif (array_intersect($tags, ['suc khoe', 'kinh doanh'])) {
            $nodeMain = 'node2';
        } elseif (array_intersect($tags, ['VH - GT', 'du lich'])) {
            $nodeMain = 'node3';
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Tags do not match any node']);
            exit;
        }
        $mainUrl = $nodes[$nodeMain] . '?action=newData';
        $resultMain = callApiPost($mainUrl, $postData);
        if ($resultMain === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save data on main node']);
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action for POST']);
        exit;
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
?>
