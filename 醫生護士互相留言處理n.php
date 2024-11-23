<?php
session_start();
include "db.php";  // 包含資料庫連接

$sender = isset($_POST['sender']) ? trim($_POST['sender']) : null;
$message = trim($_POST['message']);

// 檢查是否填寫了留言
if (!$message) {
    echo "<script>alert('請輸入留言內容'); window.location.href = '留言頁面n.php';</script>";
    exit();
}

// 檢查 sender 是否正確，確保是 doctor 或 nurse
if (!$sender || !in_array($sender, ['醫生', '護士'])) {
    echo "<script>console.log('Invalid sender value: $sender');</script>";
    echo "<script>alert('發送者身份不正確，請重新登入。'); window.location.href = 'login.php';</script>";
    exit();
}

// 插入留言到 chatmessages 資料表
$插入指令 = "INSERT INTO chatmessages (sender, message, timestamp) VALUES ('$sender', '$message', NOW())";
// if (mysqli_query($link, $插入指令)) {
//     echo "<script>alert('留言已送出'); window.location.href = '留言頁面n.php';</script>";
// } else {
//     echo "<script>alert('留言失敗：" . mysqli_error($link) . "'); window.location.href = '留言頁面n.php';</script>";
// }

// 關閉資料庫連線
mysqli_close($link);
?>
