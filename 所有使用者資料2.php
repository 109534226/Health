<?php
session_start();
include "db.php"; // 資料庫連線檔案

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

// 檢查資料庫中是否已有該使用者的資料
$SQL檢查 = "SELECT * FROM profession WHERE name = ?";
$stmt = $link->prepare($SQL檢查);
$stmt->bind_param("s", $帳號);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// 如果資料已存在，則執行更新
if ($userData) {
    $SQL指令 = "UPDATE profession SET 
        username = ?, 
        birthday = ?, 
        idcard = ?, 
        phone = ?, 
        email = ?, 
        hospital = ?";
    
    // 如果有上傳新圖片，更新 image 欄位
    if ($imageData !== null) {
        $SQL指令 .= ", image = ?";
        $stmt = $link->prepare($SQL指令 . " WHERE name = ?");
        $stmt->bind_param(
            "ssssssss",
            $姓名,
            $出生年月日,
            $身分證字號,
            $電話,
            $電子郵件,
            $隸屬醫院,
            $imageData,
            $帳號
        );
    } else {
        $stmt = $link->prepare($SQL指令 . " WHERE name = ?");
        $stmt->bind_param(
            "sssssss",
            $姓名,
            $出生年月日,
            $身分證字號,
            $電話,
            $電子郵件,
            $隸屬醫院,
            $帳號
        );
    }
} else {
    // 如果資料不存在，則插入新資料
    $SQL指令 = "INSERT INTO profession 
        (name, username, birthday, idcard, phone, email, hospital";
    if ($imageData !== null) {
        $SQL指令 .= ", image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $link->prepare($SQL指令);
        $stmt->bind_param(
            "ssssssss",
            $帳號,
            $姓名,
            $出生年月日,
            $身分證字號,
            $電話,
            $電子郵件,
            $隸屬醫院,
            $imageData
        );
    } else {
        $SQL指令 .= ") VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $link->prepare($SQL指令);
        $stmt->bind_param(
            "sssssss",
            $帳號,
            $姓名,
            $出生年月日,
            $身分證字號,
            $電話,
            $電子郵件,
            $隸屬醫院
        );
    }
}

// 執行資料庫操作
if ($stmt->execute()) {
    header("Location: d_profile.php?帳號=$帳號&success=資料已成功修改");
} else {
    error_log("SQL Error: " . $stmt->error);
    header("Location: d_profile.php?帳號=$帳號&error=修改失敗，請稍後再試");
}

$stmt->close();
$link->close();
?>
