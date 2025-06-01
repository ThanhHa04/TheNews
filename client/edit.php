<?php
$student = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $data = json_encode(['id' => $id]);

    $ch = curl_init('http://localhost:8004/coordinator.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if ($error) {
        echo "Curl error: $error";
        exit;
    }

    $decoded = json_decode($response);

    if (is_array($decoded) && count($decoded) > 0) {
        $student = $decoded[0];
    } elseif (is_object($decoded)) {
        $student = $decoded;
    }

    if (!$student) {
        echo "Không tìm thấy học sinh hoặc dữ liệu không hợp lệ";
        exit;
    }
} else {
    echo "Chưa nhận được ID";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Sửa thông tin học sinh</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .content {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .content h2 {
            margin-bottom: 25px;
            color: #333;
            font-weight: 700;
            text-align: center;
        }

        form label {
            display: block;
            width:90%;
            margin-bottom: 15px;
            font-weight: 600;
            color: #555;
        }

        form input[type="text"] {
            width: 100%;
            padding: 8px 12px;
            margin-top: 5px;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        form input[type="text"]:focus {
            border-color:rgb(0, 0, 0);
            outline: none;
        }

        button[id="save"] {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        button[id="save"]:hover {
            background-color:rgb(0, 0, 0);
        }

        a {
            display: block;
            margin-top: 20px;
            text-align: center;
            color:rgb(0, 0, 0);
            font-weight: 600;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>

</head>
<body>
    <div class="container">
        <?php include "sidebar.php"; ?>

        <div class="content">
            <h2>Sửa thông tin học sinh ID <?= htmlspecialchars($student->id) ?></h2>

            <form method="post" action="update.php">
                <input type="hidden" name="id" value="<?= htmlspecialchars($student->id) ?>">
                <label>Họ và tên:
                    <input type="text" name="name" value="<?= htmlspecialchars($student->name) ?>" required>
                </label>
                <label>Lớp:
                    <input type="text" name="class" value="<?= htmlspecialchars($student->class) ?>" required>
                </label>
                <label>Ngày sinh:
                    <input type="text" name="birth" value="<?= htmlspecialchars($student->birth) ?>" required>
                </label>
                <label>Địa chỉ:
                    <input type="text" name="address" value="<?= htmlspecialchars($student->address) ?>" required>
                </label>
                <label>SĐT:
                    <input type="text" name="phone" value="<?= htmlspecialchars($student->phone) ?>" required>
                </label>
                <button id="save" type="submit" name="save">💾 Lưu</button>
            </form>

            <a href="index.php?class=<?= urlencode($student->class) ?>">← Quay lại danh sách</a>
        </div>
    </div>
</body>
</html>
