<?php
require 'vendor/autoload.php';

use Lazer\Classes\Database as Lazer;

define('LAZER_DATA_PATH', realpath(__DIR__) . '/app/data/');

// Lazer::create('news', array(
//     'id'      => 'integer',
//     'title'   => 'string',
//     'sumary'  => 'string',
//     'image'   => 'string',
//     'content' => 'string',
//     'url'     => 'string',
//     'author'  => 'string',
//     'tags'    => 'string',
//     'time_up' => 'string',
// ));

// Đọc dữ liệu từ file JSON (file nằm cùng thư mục với import.php)
$jsonData = file_get_contents(__DIR__ . '/dulich.json');
$newsArray = json_decode($jsonData, true);

if (is_array($newsArray)) {
    $maxRecords = 25;
    $count = 0;
    $id = 76;

    foreach ($newsArray as $item) {
        if ($count >= $maxRecords) break;

        $row = Lazer::table('news');
        $row->id = $id++;
        $row->title = $item['title'] ?? '';
        $row->sumary = $item['sumary'] ?? '';
        $row->image = $item['image'] ?? '';
        $row->content = $item['content'] ?? '';
        $row->url = $item['url'] ?? '';
        $row->author = $item['author'] ?? '';
        $row->tags = $item['tags'] ?? '';
        $row->time_up = $item['time_up'] ?? '';
        $row->save();

        $count++;
    }
    echo "✅ Đã import thành công " . ($id - 1) . " bài viết!";
} else {
    echo "❌ Dữ liệu JSON không hợp lệ!";
}
?>