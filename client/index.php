<?php
function getAllStudents() {
    $url = 'http://localhost:8004/coordinator.php';

    $data = ['action' => 'getAll'];
    $jsonData = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);

    $result = json_decode($response, true);
    return $result['students'] ?? [];
}

// Lấy toàn bộ học sinh khi load trang
$allStudents = getAllStudents();
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý học sinh</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php include 'sidebar.php'; ?>
        <div class="main">
            <div class="top-bar">
                <h2 id="selectedClass">Tất cả học sinh</h2>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Lớp</th>
                        <th>Ngày sinh</th>
                        <th>Địa chỉ</th>
                        <th>SĐT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($allStudents as $s) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($s['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($s['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($s['class'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($s['birth']) . "</td>";
                        echo "<td>" . htmlspecialchars($s['address']) . "</td>";
                        echo "<td>" . htmlspecialchars($s['phone']) . "</td>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
