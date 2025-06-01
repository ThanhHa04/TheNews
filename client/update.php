<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $data = [
        'action' => 'update',
        'student' => [
            'id' => $id,
            'name' => $_POST['name'] ?? '',
            'class' => $_POST['class'] ?? '',
            'birth' => $_POST['birth'] ?? '',
            'address' => $_POST['address'] ?? '',
            'phone' => $_POST['phone'] ?? '',
        ],
    ];
    $jsonData = json_encode($data);
    $ch = curl_init('http://localhost:8004/service.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "Lỗi CURL: $error";
        exit;
    }
    $result = json_decode($response, true);
    if ($result && isset($result['status']) && $result['status'] === 'success') {
        header('Location: index.php' . urlencode($class));
        exit;
    } else {
        echo "Không thể cập nhật học sinh. Phản hồi từ coordinator: " . $response;
    }
} else {
    echo "Phương thức không hợp lệ.";
}
?>
