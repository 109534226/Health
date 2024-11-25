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
$defaultAvatarPath = "img/300.jpg"; // 預設頭像的路徑

// 查詢 `user` 表中獲取 `user_id`
$SQL查詢 = "SELECT user_id FROM user WHERE account = '$帳號'";
$result = mysqli_query($link, $SQL查詢);

if ($result && mysqli_num_rows($result) > 0) {
    $userData = mysqli_fetch_assoc($result);
    $user_id = $userData['user_id'];

    // 查詢目前使用者的頭像資料
    $SQL查詢頭像 = "SELECT image FROM people WHERE user_id = '$user_id'";
    $resultAvatar = mysqli_query($link, $SQL查詢頭像);
    
    if ($resultAvatar && mysqli_num_rows($resultAvatar) > 0) {
        $row = mysqli_fetch_assoc($resultAvatar);
        $currentAvatarData = $row['image'];

        // 如果存在頭像資料，將 `image` 欄位設為 NULL 表示刪除
        if ($currentAvatarData) {
            $SQL更新 = "UPDATE people SET image = NULL WHERE user_id = '$user_id'";
            if (mysqli_query($link, $SQL更新)) {
                // 返回刪除結果給前端，顯示預設頭像
                echo json_encode(array('success' => true, 'imageUrl' => $defaultAvatarPath));
            } else {
                // 如果更新失敗，返回錯誤訊息
                echo json_encode(array('success' => false, 'error' => '資料庫更新失敗'));
            }
        } else {
            // 如果沒有頭像資料，返回預設頭像
            echo json_encode(array('success' => true, 'imageUrl' => $defaultAvatarPath));
        }
    } else {
        // 如果沒有找到使用者頭像資料，返回預設頭像
        echo json_encode(array('success' => true, 'imageUrl' => $defaultAvatarPath));
    }
} else {
    // 如果查詢 `user` 表失敗，返回錯誤訊息
    echo json_encode(array('success' => false, 'error' => '使用者不存在'));
}

// 關閉資料庫連線
mysqli_close($link);
?>
