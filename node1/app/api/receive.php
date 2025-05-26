<?php
header('Content-Type: application/json');

// Đọc dữ liệu được gửi đến từ replication
$data = file_get_contents("php://input");
if (!$data) {
    echo json_encode(["error" => "No data received"]);
    exit;
}

// Đường dẫn đến file lưu bản sao
$dataFile = __DIR__ . '/../data_backup/node3.data.json'; 

// Đọc dữ liệu cũ nếu có
$oldData = file_exists($dataFile) ? file_get_contents($dataFile) : '';

// Nếu dữ liệu mới khác dữ liệu cũ, thì ghi lại
if ($oldData !== $data) {
    file_put_contents($dataFile, $data);
    echo json_encode(["status" => "Updated with new data"]);
} else {
    // Nếu giống nhau thì chỉ trả về nội dung hiện tại
    echo json_encode([
        "status" => "No changes",
        "data" => json_decode($oldData, true) // Trả về JSON đã decode
    ]);
}
?>
