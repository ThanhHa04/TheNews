<?php
require_once('../src/dataConfig.php');
require_once __DIR__.'/../vendor/autoload.php';

use Lazer\Classes\Database as Lazer;

header('Content-Type: application/json');

function replicateToNode2($action, $studentData) {
    $replicationUrl = 'http://node2/server/replicate.php'; 
    $payload = json_encode([
        'action' => $action,
        'student' => $studentData
    ]);

    $ch = curl_init($replicationUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 3
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);
    $action = $data['action'] ?? null;

    if ($action === 'remove') {
        $id = (int) $data['id'];
        try {
            $record = Lazer::table('students')->find($id);
            $record->delete();
            replicateToNode2('remove', ['id' => $id]);

            echo json_encode(['status' => 'success', 'message' => "Đã xóa học sinh ID $id"]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'fail', 'message' => 'Không thể xóa: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'add') {
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
            replicateToNode2('add', $student);

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
            $record->setField('name', $student['name']);
            $record->setField('class', $student['class']);
            $record->setField('birth', $student['birth']);
            $record->setField('address', $student['address']);
            $record->setField('phone', $student['phone']);
            $record->save();
            replicateToNode2('update', $student);

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

http_response_code(405);
echo json_encode(['status' => 'fail', 'message' => 'Method Not Allowed']);
exit;
