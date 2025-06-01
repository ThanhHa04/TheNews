<?php
define('LAZER_DATA_PATH', realpath(__DIR__) . '/../data/'); // Thư mục lưu dữ liệu bảng

use Lazer\Classes\Database as Lazer;
use Lazer\Classes\Helpers\Validate;

require_once __DIR__ . '/../vendor/autoload.php';

if (!file_exists(LAZER_DATA_PATH)) {
    mkdir(LAZER_DATA_PATH, 0777, true);
}

try {
    Validate::table('students')->exists();
} catch (\Lazer\Classes\LazerException $e) {
    // Tạo bảng students với trường id kiểu int
    Lazer::create('students', array(
        'id' => 'int',
        'name' => 'string',
        'class' => 'string',
        'birth' => 'string',
        'address' => 'string',
        'phone' => 'string',
    ));
}
?>
