<?php
session_start();
include "db.php"; // 資料庫連線

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 驗證登入狀態
    if (!isset($_SESSION["登入狀態"]) || $_SESSION["登入狀態"] !== true) {
        echo "<script>alert('請先登入'); window.location.href = 'login.php';</script>";
        exit();
    }

    $sender_id = $_SESSION['user_id']; // 發送者 ID
    $receiver_account = mysqli_real_escape_string($link, $_POST['receiver_account']);
    $message = mysqli_real_escape_string($link, trim($_POST['message']));

    // 驗證訊息是否為空
    if (empty($message)) {
        echo "<script>alert('訊息不可為空'); history.go(-1);</script>";
        exit();
    }

    // 查找接收者 ID
    $查詢接收者 = "
        SELECT user_id FROM user WHERE account = '$receiver_account'
    ";
    $接收者結果 = mysqli_query($link, $查詢接收者);

    if ($接收者結果 && $row = mysqli_fetch_assoc($接收者結果)) {
        $receiver_id = $row['user_id'];

        // 插入留言
        $插入留言 = "
            INSERT INTO messenger (medicalS_id, medicalP_id, messenger) 
            VALUES ($sender_id, $receiver_id, '$message')
        ";
        if (mysqli_query($link, $插入留言)) {
            echo "<script>alert('留言成功'); window.location.href = '留言頁面d.php';</script>";
        } else {
            echo "<script>alert('留言失敗'); history.go(-1);</script>";
        }
    } else {
        echo "<script>alert('接收者不存在'); history.go(-1);</script>";
    }
}
?>
