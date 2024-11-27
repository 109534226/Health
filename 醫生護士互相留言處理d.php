<?php
session_start();
include "db.php"; // 資料庫連線

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 驗證登入狀態
    if (!isset($_SESSION["登入狀態"]) || $_SESSION["登入狀態"] !== true) {
        echo "<script>alert('請先登入'); window.location.href = 'login.php';</script>";
        exit();
    }

    // 接收表單資料
    $sender_account = $_SESSION['帳號']; // 傳送者帳號
    $receiver_account = trim($_POST['receiver_name']); // 接收者帳號
    echo $receiver_account;
    $message = trim($_POST['message']); // 訊息內容
    // 防止 SQL 注入
    $receiver_account = mysqli_real_escape_string($link, $receiver_account);
    $message = mysqli_real_escape_string($link, $message);

    // 檢查是否有空值
    $errors = [];
    if ($receiver_account === "" || $receiver_account === null) {
        $errors[] = '請選擇接收者帳號';
    }
    if ($message === "" || $message === null) {
        $errors[] = '訊息不可為空';
    }

    if (!empty($errors)) {
        echo "<script>
                alert('" . implode("\\n", $errors) . "');
                window.history.back();
              </script>";
        exit();
    }

    // 查找傳送者 ID
    $查詢傳送者 = "SELECT medical_id FROM medical WHERE user_id = (SELECT user_id FROM user WHERE account = '$sender_account')";
    $傳送者結果 = mysqli_query($link, $查詢傳送者);

    if ($傳送者結果 && mysqli_num_rows($傳送者結果) > 0) {
        $傳送者行 = mysqli_fetch_assoc($傳送者結果);
        $sender_medical_id = $傳送者行['medical_id'];

        // 查找接收者 ID
        $查詢接收者 = "SELECT medical_id FROM medical WHERE user_id = (SELECT user_id FROM user WHERE account = '$receiver_account')";
        $接收者結果 = mysqli_query($link, $查詢接收者);
    }
    if ($接收者結果 && mysqli_num_rows($接收者結果) > 0) {
        $接收者行 = mysqli_fetch_assoc($接收者結果);
        $receiver_medical_id = $接收者行['medical_id'];

        // 插入留言
        $插入留言 = "INSERT INTO messenger (medicalS_id, medicalP_id, messenger) VALUES ($sender_medical_id, $receiver_medical_id, '$message')";

        if (mysqli_query($link, $插入留言)) {
            echo "<script>
                        alert('留言成功');
                        window.location.href = '留言頁面d.php';
                      </script>";
        } else {
            $error_message = mysqli_error($link);
            echo "<script>
                        alert('留言失敗，錯誤訊息: $error_message');
                        window.history.back();
                      </script>";
        }
    } else {
        echo "<script>
                    alert('接收者不存在，請檢查接收者帳號。');
                    window.history.back();
                  </script>";
    }
} else {
    echo "<script>
                alert('傳送者不存在，請檢查傳送者帳號。');
                window.history.back();
              </script>";
}

// 關閉資料庫連線
mysqli_close($link);
?>