<?php
session_start(); // 啟動 Session
include "db.php"; // 引入資料庫連線檔案

// 確認是否已登入
if (!isset($_SESSION["登入狀態"]) || $_SESSION["登入狀態"] !== true) {
    header("Location: login.php"); // 如果未登入，跳轉到登入頁面
    exit();
}

// 獲取表單中的資料
$account = $_SESSION["帳號"]; // 從 Session 中取得目前登入的使用者帳號
$name = isset($_POST["username"]) && !empty($_POST["username"]) ? $_POST["username"] : null; // 使用者名稱
$birthday = isset($_POST["userdate"]) && !empty($_POST["userdate"]) ? $_POST["userdate"] : null; // 出生年月日
$idcard = isset($_POST["useridcard"]) && !empty($_POST["useridcard"]) ? $_POST["useridcard"] : null; // 身分證字號
$phone = isset($_POST["userphone"]) && !empty($_POST["userphone"]) ? $_POST["userphone"] : null; // 電話號碼
$email = isset($_POST["useremail"]) && !empty($_POST["useremail"]) ? $_POST["useremail"] : null; // 電子郵件
$ecname = isset($_POST["useremergencycontact"]) && !empty($_POST["useremergencycontact"]) ? $_POST["useremergencycontact"] : null; // 緊急聯絡人姓名
$ecphone = isset($_POST["useremergencycontactphone"]) && !empty($_POST["useremergencycontactphone"]) ? $_POST["useremergencycontactphone"] : null; // 緊急聯絡人電話

// 資料完整性檢查
if (empty($name) || empty($birthday) || empty($idcard) || empty($phone) || empty($ecname) || empty($ecphone)) {
    header("Location: u_profile.php?error=必要資料為空，請填寫所有欄位！"); // 如果必填欄位為空，重新導向並顯示錯誤訊息
    exit();
}

// 查詢 `user` 表中取得目前使用者的 `user_id` 和目前的 `name`
$SQL檢查 = "
    SELECT user_id, name 
    FROM user 
    WHERE account = '$account'
";
$result = mysqli_query($link, $SQL檢查); // 執行查詢

if (!$result) {
    error_log("SQL Error (User Check Query): " . mysqli_error($link)); // 記錄 SQL 錯誤
    header("Location: login.php?error=無法獲取使用者資訊，請重新登入！");
    exit();
}

$userData = mysqli_fetch_assoc($result); // 獲取查詢結果

if (!$userData) {
    // 如果找不到對應的使用者，重新導向並顯示錯誤訊息
    header("Location: login.php?error=使用者不存在，請重新登入！");
    exit();
}

$user_id = $userData['user_id'];
$originalName = $userData['name']; // 原本的使用者名稱

// 查詢 `people` 表中是否已有該使用者的資料
$SQL檢查People = "
    SELECT * 
    FROM people 
    WHERE user_id = '$user_id'
";
$resultPeople = mysqli_query($link, $SQL檢查People); // 執行查詢

if (!$resultPeople) {
    error_log("SQL Error (People Check Query): " . mysqli_error($link)); // 記錄 SQL 錯誤
    header("Location: u_profile.php?帳號=$account&error=無法檢查個人資料！");
    exit();
}

$peopleData = mysqli_fetch_assoc($resultPeople); // 獲取查詢結果

// 準備 SQL 語句
if ($peopleData) {
    // 如果資料已存在，執行更新操作
    $SQL指令 = "
        UPDATE people 
        SET birthday = '$birthday',
            idcard = '$idcard',
            phone = '$phone',
            email = '$email',
            ecname = '$ecname',
            ecphone = '$ecphone'
        WHERE user_id = '$user_id'"; // 限定更新的使用者
} else {
    // 如果資料不存在，插入新資料
    $SQL指令 = "
        INSERT INTO people (user_id, name, birthday, idcard, phone, email, ecname, ecphone) 
        VALUES ('$user_id', '$name', '$birthday', '$idcard', '$phone', '$email', '$ecname', '$ecphone')";
}

// 執行資料庫操作
if (!mysqli_query($link, $SQL指令)) {
    error_log("SQL Error (People Table Update/Insert): " . mysqli_error($link)); // 將錯誤記錄到伺服器日誌
    header("Location: u_profile.php?帳號=$account&error=修改失敗，請稍後再試");
    exit();
}

// 更新 `user` 表中的 name（如果新名稱和原名稱不一致且新名稱非空）
if (!empty($name) && $name !== $originalName) {
    $SQL更新User = "
        UPDATE user 
        SET name = '$name' 
        WHERE account = '$account'
    ";
    // 執行更新 `user` 表的操作
    if (!mysqli_query($link, $SQL更新User)) {
        // 如果操作失敗，記錄錯誤並顯示錯誤提示
        error_log("SQL Error (Update User Name): " . mysqli_error($link)); // 將錯誤記錄到伺服器日誌
        header("Location: u_profile.php?帳號=$account&error=修改失敗，請稍後再試");
        exit();
    }
}

// 如果所有操作成功，跳轉到個人資料頁面並顯示成功訊息
header("Location: u_profile.php?帳號=$account&success=資料已成功修改");
exit();

// 關閉資料庫連接
mysqli_close($link);
?>
