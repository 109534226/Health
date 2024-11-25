<?php
session_start();
include "db.php"; // 引入資料庫連線檔案

// 確認是否已登入
if (!isset($_SESSION["登入狀態"]) || $_SESSION["登入狀態"] !== true) {
    header("Location: login.php"); // 如果未登入，跳轉到登入頁面
    exit();
}

// 獲取使用者帳號
$帳號 = $_SESSION["帳號"];

// 檢查是否有上傳的頭像文件
if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
    // 獲取圖片數據
    $imageData = file_get_contents($_FILES['profilePicture']['tmp_name']);
    $encodedImage = mysqli_real_escape_string($link, $imageData);

    // 查詢 `user` 表中獲取 `user_id`
    $SQL查詢 = "SELECT user_id FROM user WHERE account = '$帳號'";
    $result = mysqli_query($link, $SQL查詢);

    if ($result && mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_assoc($result);
        $user_id = $userData['user_id'];

        // 檢查 `people` 表中是否已經有該使用者的資料
        $SQL檢查People = "SELECT * FROM people WHERE user_id = '$user_id'";
        $resultPeople = mysqli_query($link, $SQL檢查People);

        if ($resultPeople && mysqli_num_rows($resultPeople) > 0) {
            // 如果資料已存在，則更新 `people` 表中的頭像
            $SQL更新 = "UPDATE people SET image='$encodedImage' WHERE user_id='$user_id'";
        } else {
            // 如果資料不存在，則插入新資料到 `people` 表
            $SQL更新 = "INSERT INTO people (user_id, image) VALUES ('$user_id', '$encodedImage')";
        }

        // 執行資料庫操作
        if (mysqli_query($link, $SQL更新)) {
            // 返回 Base64 編碼的圖像數據
            $imageBase64 = 'data:image/jpeg;base64,' . base64_encode($imageData);
            echo json_encode(array('success' => true, 'imageUrl' => $imageBase64));
        } else {
            // 如果更新或插入失敗，返回錯誤
            echo json_encode(array('success' => false, 'error' => '資料庫更新失敗'));
        }
    } else {
        // 如果查詢 `user` 表失敗，返回錯誤
        echo json_encode(array('success' => false, 'error' => '使用者不存在'));
    }
} else {
    // 如果文件上傳失敗，返回錯誤
    echo json_encode(array('success' => false, 'error' => '文件上傳失敗'));
}

// 關閉資料庫連接
mysqli_close($link);
?>
