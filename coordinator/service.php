<?php
header('Content-Type: application/json');
$request = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($request)) {
    $request = $_POST;
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

function fetchFromNode($url, $data) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    return [$response, $httpCode, $error];
}

if (isset($request['action'])) {
    if ($request['action'] === 'remove' && isset($request['id'])) {
        $studentId = $request['id'];
        $prefix = substr($studentId, 0, 2);
        if (!isset($prefixNodeMap[$prefix])) {
            echo json_encode(['status' => 'fail', 'message' => 'Không xác định được node từ ID']);
            exit;
        }
        $url = $prefixNodeMap[$prefix] . 'api.php';
        list($response, $httpCode, $error) = fetchFromNode($url, ['action' => 'remove', 'id' => $studentId]);
        echo $response;
        exit;
    } elseif ($request['action'] === 'add' && isset($request['student']['class'])) {
        $class = $request['student']['class'];
        if (!isset($nodeMap[$class])) {
            echo json_encode(['status' => 'fail', 'message' => 'Không xác định được node từ lớp học']);
            exit;
        }
        $url = $nodeMap[$class] . 'api.php';
        list($response, $httpCode, $error) = fetchFromNode($url, $request);
        echo $response;
        exit;
    } elseif ($request['action'] === 'update' && isset($request['student']) && is_array($request['student']) && isset($request['student']['id'])) {
        $student = $request['student'];
        $id = $student['id'];
        $prefix = substr($id, 0, 2);
        if (!isset($prefixNodeMap[$prefix])) {
            echo json_encode(['status' => 'fail', 'message' => 'Không xác định được node từ ID']);
            exit;
        }
        $url = $prefixNodeMap[$prefix] . 'api.php';
        list($response, $httpCode, $error) = fetchFromNode($url, $request);
        echo $response;
        exit;
    }

}

http_response_code(400);
echo json_encode(['status' => 'fail', 'message' => 'Invalid request']);
