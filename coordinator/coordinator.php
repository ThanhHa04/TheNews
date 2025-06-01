<?php
header('Content-Type: application/json');
$request = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($request)) {
    http_response_code(400);
    echo json_encode(['status' => 'fail', 'message' => 'Invalid JSON input']);
    exit;
}

$nodeMap = [
    '10A' => 'http://node1/server/', '10B' => 'http://node1/server/', '10C' => 'http://node1/server/',
    '11A' => 'http://node2/server/', '11B' => 'http://node2/server/', '11C' => 'http://node2/server/',
    '12A' => 'http://node3/server/', '12B' => 'http://node3/server/', '12C' => 'http://node3/server/',
];

$prefixNodeMap = [
    '10' => 'http://node1/server/',
    '11' => 'http://node2/server/',
    '12' => 'http://node3/server/',
];

function fetchFromNode($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    return [$response, $httpCode, $error];
}

if (isset($request['action']) && $request['action'] === 'getAll') {
    $allStudents = [];
    foreach (array_unique($nodeMap) as $baseUrl) {
        [$response, $httpCode] = fetchFromNode($baseUrl . 'api.php');
        if ($httpCode === 200 && ($data = json_decode($response, true)) && is_array($data)) {
            $allStudents = array_merge($allStudents, $data);
        }
    }
    echo json_encode(['status' => 'success', 'students' => array_values($allStudents)]);
    exit;
}

if (isset($request['class'])) {
    $class = $request['class'];
    if (!isset($nodeMap[$class])) {
        http_response_code(404);
        echo json_encode(['status' => 'fail', 'message' => 'Class not found in node map']);
        exit;
    }
    [$response, $httpCode, $error] = fetchFromNode($nodeMap[$class] . 'api.php?class=' . urlencode($class));
    echo $httpCode === 200 && $response !== false ? $response :
        json_encode(['status' => 'fail', 'message' => "Node không phản hồi hoặc lỗi: $error"]);
    exit;
}

if (isset($request['id'])) {
    $id = $request['id'];
    $prefix = substr($id, 0, 2);
    if (!isset($prefixNodeMap[$prefix])) {
        http_response_code(404);
        echo json_encode(['status' => 'fail', 'message' => 'ID không thuộc node nào']);
        exit;
    }
    [$response, $httpCode, $error] = fetchFromNode($prefixNodeMap[$prefix] . 'api.php?id=' . urlencode($id));
    echo $httpCode === 200 && $response !== false ? $response :
        json_encode(['status' => 'fail', 'message' => "Lỗi gọi node: $error"]);
    exit;
}

http_response_code(400);
echo json_encode(['status' => 'fail', 'message' => 'Invalid request']);
