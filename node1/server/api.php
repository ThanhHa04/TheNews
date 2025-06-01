<?php
require_once('../src/dataConfig.php');
require_once __DIR__.'/../vendor/autoload.php';

use Lazer\Classes\Database as Lazer;

header('Content-Type: application/json');

// ✅ XỬ LÝ POST trước
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);
    $action = $data['action'] ?? null;

    if (isset($data['action']) && $data['action'] === 'remove') {
        $id = (int) $data['id'];
        try {
            $record = Lazer::table('students')->find($id);
            $record->delete();
            echo json_encode(['status' => 'success', 'message' => "Đã xóa học sinh ID $id"]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'fail', 'message' => 'Không thể xóa: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($data['action'] === 'add') {
        if (!isset($data['student']) || !is_array($data['student'])) {
            echo json_encode(['status' => 'fail', 'message' => 'Dữ liệu học sinh không hợp lệ']);
            exit;
        }
        $student = $data['student'];
        try {
            $table = Lazer::table('students');
            $table->name = $student['name'] ?? '';
            $table->class = $student['class'] ?? '';
            $table->birth = $student['birth'] ?? '';
            $table->address = $student['address'] ?? '';
            $table->phone = $student['phone'] ?? '';
            $table->insert();
            echo json_encode(['status' => 'success', 'message' => 'Thêm học sinh thành công']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'fail', 'message' => 'Không thể thêm học sinh: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'update') {
        if (!isset($data['student']) || !is_array($data['student']) || !isset($data['student']['id'])) {
            echo json_encode(['status' => 'fail', 'message' => 'Dữ liệu học sinh không hợp lệ hoặc thiếu ID']);
            exit;
        }
        $student = $data['student'];
        $id = (int) $student['id'];

        try {
            $table = Lazer::table('students');
            $record = $table->find($id);

            if (!$record) {
                echo json_encode(['status' => 'fail', 'message' => 'Không tìm thấy học sinh với ID ' . $id]);
                exit;
            }

            // Cập nhật các trường nếu có
            if (isset($student['name'])) {
                $record->setField('name', $student['name']);
            }
            if (isset($student['class'])) {
                $record->setField('class', $student['class']);
            }
            if (isset($student['birth'])) {
                $record->setField('birth', $student['birth']);
            }
            if (isset($student['address'])) {
                $record->setField('address', $student['address']);
            }
            if (isset($student['phone'])) {
                $record->setField('phone', $student['phone']);
            }

            $record->save();

            echo json_encode(['status' => 'success', 'message' => 'Cập nhật học sinh thành công']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'fail', 'message' => 'Không thể cập nhật học sinh: ' . $e->getMessage()]);
        }
        exit;
    }

    
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        $student = Lazer::table('students')->where('id', '=', $id)->findAll()->asArray();
        echo json_encode($student);
    } elseif (isset($_GET['class'])) {
        $class = $_GET['class'];
        $students = Lazer::table('students')->where('class', '=', $class)->findAll()->asArray();
        echo json_encode($students);
    } else {
        $students = Lazer::table('students')->findAll()->asArray();
        echo json_encode($students);
    }
    exit;
}

// ❌ Nếu không phải GET hoặc POST
http_response_code(405);
echo json_encode(['status' => 'fail', 'message' => 'Method Not Allowed']);
exit;
