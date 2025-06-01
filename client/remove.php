<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id'])) {
        die('Thiếu ID học sinh cần xóa.');
    }

    $id = (int) $_POST['id'];

    // Dữ liệu sẽ gửi tới coordinator
    $postData = json_encode([
        'action' => 'remove',
        'id' => $id
    ]);

    // URL đến coordinator (cưng chỉnh lại nếu cần)
    $coordinatorUrl = 'http://localhost:8004/service.php'; // hoặc địa chỉ thật

    // Khởi tạo cURL
    $ch = curl_init($coordinatorUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postData)
    ]);

    // Gửi request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Giải mã phản hồi JSON
    $result = json_decode($response, true);
    var_dump($result); // Debugging line, can be removed later
    if ($httpCode === 200 && isset($result['status']) && $result['status'] === 'success') {
        // Xóa thành công
        header('Location: index.php');
        exit;
    } else {
        echo "Không thể xóa học sinh. Phản hồi từ coordinator: <pre>" . htmlspecialchars($response) . "</pre>";
    }
} else {
    echo "Phương thức không hợp lệ.";
}
