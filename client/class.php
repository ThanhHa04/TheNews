<?php
$students = [];

if (isset($_GET['class'])) {
    $selectedClass = $_GET['class'];

    $data = json_encode(['class' => $selectedClass]);

    $ch = curl_init('http://localhost:8004/coordinator.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true); // gửi POST để lấy theo lớp
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    $students = json_decode($response, true);

}
?>



<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách lớp <?= htmlspecialchars($_GET['class'] ?? '') ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php include 'sidebar.php'; ?>

        <div class="main">
            <div class="top-bar">
                <h2 id="selectedClass">Danh sách lớp <?= htmlspecialchars($_GET['class'] ?? '') ?></h2>
                <button id="addStudentBtn" onclick="showAddForm()">➕ Thêm học sinh</button>
                <div id="addStudentForm" class="form-overlay" style="display: none;">
                    <div class="form-box">
                        <h3>➕ Thêm học sinh mới</h3>
                        <form method="post" action="add.php">
                            <label>Họ và tên: <input type="text" name="name" required></label>
                            <label>Lớp: <input type="text" name="class" value="<?= htmlspecialchars($_GET['class'] ?? '') ?>" required></label>
                            <label>Ngày sinh: <input type="text" name="birth" required></label>
                            <label>Địa chỉ: <input type="text" name="address" required></label>
                            <label>SĐT: <input type="text" name="phone" required></label>
                            <div class="form-actions">
                                <button type="submit">💾 Lưu</button>
                                <button type="button" onclick="hideAddForm()">❌ Hủy</button>
                            </div>
                        </form>
                    </div>
                </div>
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
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($students as $s) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($s['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($s['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($s['class'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($s['birth']) . "</td>";
                        echo "<td>" . htmlspecialchars($s['address']) . "</td>";
                        echo "<td>" . htmlspecialchars($s['phone']) . "</td>";
                        echo "<td>
                            <form method='get' action='edit.php' style='display:inline'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($s['id']) . "'>
                                <button type='submit'>✏️ Sửa</button>
                            </form>
                            <form method='post' action='remove.php' style='display:inline' onsubmit='return confirm(\"Bạn có chắc muốn xóa học sinh này?\")'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($s['id']) . "'>
                                <button type='submit'>🗑️ Xóa</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

<script>
    function showAddForm() {
        document.getElementById('addStudentForm').style.display = 'flex';
    }

    function hideAddForm() {
        document.getElementById('addStudentForm').style.display = 'none';
    }
</script>

</body>
</html>
