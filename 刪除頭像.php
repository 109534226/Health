<?php
session_start();
include "db.php";

// 假設使用者的 ID 為 $userId，可根據實際情況替換
$userId = $_SESSION["帳號"]; // 請替換為實際使用者的 ID
$defaultAvatarPath = "img/300.jpg"; // 預設頭像的路徑

// 查詢目前使用者的頭像資料
$query = "SELECT image FROM people WHERE name = '$userId'";
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$currentAvatarData = $row['image'];

// 如果存在頭像資料，刪除該資料
if ($currentAvatarData) {
    // 更新資料庫中的 image 欄位為 NULL，表示無頭像
    $updateQuery = "UPDATE people SET image = NULL WHERE name = '$userId'";
    mysqli_query($link, $updateQuery);
}

// 返回刪除結果給前端
echo json_encode(['success' => true, 'imageUrl' => $defaultAvatarPath]);

// 關閉資料庫連線
mysqli_close($link);
?>