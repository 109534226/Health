<?php
session_start();
include "db.php"; // 假設 db.php 是你的資料庫連線檔案

// 獲取 POST 資料
$帳號 = $_SESSION["帳號"];
$姓名 = $_POST["username"];
$出生年月日 = $_POST["userdate"];
$身分證字號 = $_POST["useridcard"];
$電話 = $_POST["userphone"];
$電子郵件 = $_POST["useremail"];
$隸屬醫院 = $_POST["hospital"];
$profilePicture = $_FILES['profilePicture'];

// 資料驗證
if (empty($出生年月日) || empty($身分證字號) || empty($電話)) {
    echo "必要資料為空";
    exit;
}

// 處理圖片檔案
$imageData = null;
if (!empty($profilePicture['tmp_name']) && $profilePicture['error'] == 0) {
    $imageData = addslashes(file_get_contents($profilePicture['tmp_name']));
}

// 使用 MySQL 函數實現新增或更新邏輯
$sql = "
INSERT INTO profession (name, username, birthday, idcard, phone, email, hospital, image)
VALUES ('$帳號', '$姓名', '$出生年月日', '$身分證字號', '$電話', '$電子郵件', '$隸屬醫院', " . ($imageData ? "'$imageData'" : "NULL") . ")
ON DUPLICATE KEY UPDATE 
    username = VALUES(username),
    birthday = VALUES(birthday),
    idcard = VALUES(idcard),
    phone = VALUES(phone),
    email = VALUES(email),
    hospital = VALUES(hospital)" . ($imageData ? ", image = VALUES(image)" : "") . ";";

// 執行 SQL 指令
if (mysqli_query($link, $sql)) {
    header("Location: n_profile.php?帳號=$帳號&success=資料已成功修改");
} else {
    error_log("SQL Error: " . mysqli_error($link));
    header("Location: n_profile.php?帳號=$帳號&error=修改失敗，請稍後再試");
}

mysqli_close($link);
?>
