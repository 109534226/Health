<?php
session_start();
include "db.php"; // 連接資料庫

// 假設從 session 獲取目前登入的帳號
$帳號 = $_SESSION["帳號"];
if (!$帳號) {
    die("用戶未登入，請重新登入！");
}

// 獲取 POST 資料
$姓名 = $_POST["username"] ?? null;
$出生年月日 = $_POST["userdate"] ?? null;
$身分證字號 = $_POST["useridcard"] ?? null;
$電話 = $_POST["userphone"] ?? null;
$電子郵件 = $_POST["useremail"] ?? null;
$隸屬醫院 = $_POST["hospital"] ?? null;
$profilePicture = $_FILES['profilePicture'] ?? null;

// 資料驗證
if (empty($出生年月日) || empty($身分證字號) || empty($電話)) {
    die("必要資料為空，請檢查後重新提交！");
}

// 處理圖片檔案
$imageData = null;
if (!empty($profilePicture['tmp_name']) && $profilePicture['error'] === 0) {
    $imageData = addslashes(file_get_contents($profilePicture['tmp_name']));
}

// 插入或更新資料
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

// 執行 SQL 指令並檢查錯誤
if (mysqli_query($link, $sql)) {
    header("Location: d_profile.php?帳號=$帳號&success=資料已成功修改");
} else {
    echo "更新失敗，SQL 錯誤：" . mysqli_error($link);
    exit;
}

mysqli_close($link);
?>
