<?php
// 数据库连接设置
session_start();
include 'db.php';
// 获取表单数据，并检查是否定义了键
$title = isset($_POST['title']) ? $_POST['title'] : '';
$subtitle = isset($_POST['subtitle']) ? $_POST['subtitle'] : '';
$content = isset($_POST['content']) ? $_POST['content'] : '';
$source = isset($_POST['source']) ? $_POST['source'] : '';
$url = isset($_POST['url']) ? $_POST['url'] : '';

// 插入数据到 `article` 表（去掉了 image 字段）
$sql = "INSERT INTO article (title, subtitle, source, url)
VALUES ('$title', '$subtitle', '$source', '$url')";

if ($link->query($sql) === TRUE) {
    echo "文章保存成功！";
} else {
    echo "错误: " . $sql . "<br>" . $link->error;
}