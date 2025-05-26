<?php
function replicateTo($targetUrl, $jsonData) {
    $ch = curl_init($targetUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

$dataFile = __DIR__ . '/../data/news.data.json';
if (!file_exists($dataFile)) {
    echo json_encode(['error' => 'Local data file not found']);
    exit;
}

$jsonData = file_get_contents($dataFile);

$target = 'http://localhost:8003/app/api/receive.php';
$response = replicateTo($target, $jsonData);

echo $response;
?>
