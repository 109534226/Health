<?php
session_start();
include "db.php";

// 獲取 SESSION 資料
$帳號 = $_SESSION["帳號"];

// 獲取 POST 資料
$姓名 = $_POST["username"] ?? '';
$出生年月日 = $_POST["userdate"] ?? '';
$身分證字號 = $_POST["useridcard"] ?? '';
$電話 = $_POST["userphone"] ?? '';
$電子郵件 = $_POST["useremail"] ?? '';
$隸屬醫院 = $_POST["hospital"] ?? '';
$科別 = $_POST["department"] ?? '';

// 資料驗證
if (empty($姓名) || empty($出生年月日) || empty($身分證字號) || empty($電話) || empty($電子郵件) || empty($隸屬醫院) || empty($科別)) {
    echo "<script>alert('所有欄位皆為必填，請完整填寫！'); window.location.href = 'd_profile.php';</script>";
    exit;
}

// 驗證電話格式（09 開頭，8 位數字）
if (!preg_match('/^09\d{8}$/', $電話)) {
    echo "<script>alert('聯絡電話格式錯誤！'); window.location.href = 'd_profile.php';</script>";
    exit;
}

// 驗證電子郵件格式
if (!filter_var($電子郵件, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('電子郵件格式錯誤！'); window.location.href = 'd_profile.php';</script>";
    exit;
}

// 更新資料庫
$SQL更新 = "
    UPDATE profession 
    SET 
        username = '$姓名', 
        birthday = '$出生年月日', 
        idcard = '$身分證字號', 
        phone = '$電話', 
        email = '$電子郵件', 
        hospital = '$隸屬醫院', 
        department = '$科別' 
    WHERE account = '$帳號'
";

if (!mysqli_query($link, $SQL更新)) {
    echo "<script>alert('修改失敗：" . mysqli_error($link) . "'); window.location.href = 'd_profile.php';</script>";
    exit;
}

// 成功提示
echo "<script>alert('修改成功！'); window.location.href = 'd_profile.php';</script>";
?>
