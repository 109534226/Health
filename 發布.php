<?php
session_start();
include 'db.php';

// 获取表单数据
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$subtitle = isset($_POST['subtitle']) ? trim($_POST['subtitle']) : '';
$source = isset($_POST['source']) ? trim($_POST['source']) : '';
$url = isset($_POST['url']) ? trim($_POST['url']) : '';
$image = isset($_POST['image']) ? trim($_POST['image']) : '';

// 添加一个默认的 type 值 (假设 type 为 1)
$type = 1;  // 如果 type 字段需要根据需求来设置，可以从表单或其他来源获取

// 使用预处理语句插入数据
$stmt = $link->prepare("INSERT INTO article (title, subtitle, source, url, image, type) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssi", $title, $subtitle, $source, $url, $image, $type);
    
// 执行插入操作
if ($stmt->execute()) {
    echo "<script>
            alert('文章保存成功！');
            window.location.href = 'c_content.php';  // 重定向到 c_content.php
          </script>";
} else {
    // 插入失败，输出错误信息
    echo "<script>alert('保存失败，请稍后再试。'); window.history.back();</script>";
    error_log("数据库错误: " . $stmt->error);  // 输出详细错误到 PHP 错误日志
}

// 关闭语句和连接
$stmt->close();
$link->close();
?>
