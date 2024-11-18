<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 根據表單的提交狀態獲取數據
    $username = isset($_POST['anonymous']) ? '匿名用戶' : $_POST['nickname']; // 若為匿名則設為"匿名用戶"
    $comment = $_POST['comment'];
    $share_option = $_POST['share_option'];

    // 儲存故事邏輯（根據你的資料庫設計）
    $stmt = mysqli_prepare($link, "INSERT INTO story (username, comment, share) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sss', $username, $comment, $share_option);
    
    // 執行準備好的語句並檢查執行結果
    if (mysqli_stmt_execute($stmt)) {
        // 設定成功訊息到 session
        $_SESSION['message'] = "新增成功！";
    } else {
        // 設定錯誤訊息到 session
        $_SESSION['message'] = "新增失敗！請再試一次。";
    }

    mysqli_stmt_close($stmt); // 在執行後關閉語句

    // 重定向回 story.html
    header("Location: story.php");
    exit(); // 確保不再執行後續代碼
}

// 關閉資料庫連接
mysqli_close($link);
?>
