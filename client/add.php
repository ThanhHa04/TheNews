<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu học sinh từ POST
    $name = $_POST['name'] ?? '';
    $class = $_POST['class'] ?? '';
    $birth = $_POST['birth'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Kiểm tra bắt buộc
    if (!$name || !$class || !$birth || !$address || !$phone) {
        die('Thiếu dữ liệu bắt buộc');
    }

    // Tạo dữ liệu JSON gửi lên coordinator
    $postData = json_encode([
        'action' => 'add',
        'student' => [
            'name' => $name,
            'class' => $class,
            'birth' => $birth,
            'address' => $address,
            'phone' => $phone
        ]
    ]);

    $coordinatorUrl = 'http://localhost:8004/service.php'; // Chỉnh URL coordinator đúng nha

    $ch = curl_init($coordinatorUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); // Dùng POST request
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); // Gửi JSON body
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postData)
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);
    var_dump($result); // Dòng debug, xóa đi khi chạy ổn

    if ($httpCode === 200 && isset($result['status']) && $result['status'] === 'success') {
        // Thêm thành công thì redirect về trang danh sách lớp
        header('Location: index.php?class=' . urlencode($class));
        exit;
    } else {
        echo "Không thể thêm học sinh. Phản hồi từ coordinator: <pre>" . htmlspecialchars($response) . "</pre>";
    }
} else {
    echo "Phương thức không hợp lệ.";
}
?>