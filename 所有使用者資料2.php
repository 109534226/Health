<?php
session_start(); // 開啟 session
include "db.php"; // 包含資料庫連接文件

// 獲取 POST 資料
$帳號 = $_SESSION["帳號"]; // 從 session 中獲取帳號
$姓名 = $_POST["username"]; // 從表單中獲取使用者名稱
$出生年月日 = $_POST["userdate"]; // 從表單中獲取使用者生日
$身分證字號 = $_POST["useridcard"]; // 從表單中獲取使用者身分證字號
$電話 = $_POST["userphone"]; // 從表單中獲取使用者電話
$電子郵件 = $_POST["useremail"]; // 從表單中獲取使用者電子郵件
$隸屬醫院 = $_POST["hospital"]; // 從表單中獲取隸屬醫院
$科別 = $_POST["department"]; // 從表單中獲取科別
$profilePicture = $_FILES['profilePicture']; // 從表單中獲取使用者上傳的個人圖片

// 資料驗證
if (empty($出生年月日) || empty($身分證字號) || empty($電話) || empty($隸屬醫院) || empty($科別)) {
    // 如果必要欄位為空，則顯示錯誤訊息並重新導向到個人資料頁面
    echo "<script>alert('必要資料為空，請填寫所有欄位！'); window.location.href = 'd_profile.php';</script>";
    exit;
}

// 檢查資料庫中是否已有該使用者的資料
$SQL檢查 = "SELECT * FROM profession WHERE name = '$帳號'"; // 查詢profession資料表中是否有對應的帳號
$result = mysqli_query($link, $SQL檢查); // 執行查詢
$userData = mysqli_fetch_assoc($result); // 獲取查詢結果

if ($userData) {
    // 如果資料已存在，則執行更新
    $SQL指令 = "UPDATE profession SET username='$姓名', birthday='$出生年月日', idcard='$身分證字號', 
                phone='$電話', email='$電子郵件', hospital='$隸屬醫院', department='$科別'";

    // 如果有上傳新圖片，更新 image 欄位
    if (!empty($profilePicture['tmp_name']) && $profilePicture['error'] == 0) {
        $imageData = addslashes(file_get_contents($profilePicture['tmp_name'])); // 讀取圖片內容並轉換為字串
        $SQL指令 .= ", image='$imageData'"; // 更新 image 欄位
    }

    $SQL指令 .= " WHERE name='$帳號'"; // 指定更新的使用者條件

    // 更新 user 資料表中的 username 欄位
    $SQL更新User = "UPDATE user SET username='$姓名' WHERE email='$電子郵件'"; // 更新 user 資料表中的使用者名稱
} else {
    // 如果資料不存在，則插入新資料
    $SQL指令 = "INSERT INTO profession (name, username, birthday, idcard, phone, email, hospital,department";

    if (!empty($profilePicture['tmp_name']) && $profilePicture['error'] == 0) {
        $imageData = addslashes(file_get_contents($profilePicture['tmp_name'])); // 讀取圖片內容並轉換為字串
        $SQL指令 .= ", image) VALUES ('$帳號', '$姓名', '$出生年月日', '$身分證字號', '$電話', '$電子郵件', '$隸屬醫院', '$科別', '$imageData')"; // 插入新資料，包含圖片
    } else {
        $SQL指令 .= ") VALUES ('$帳號', '$姓名', '$出生年月日', '$身分證字號', '$電話', '$電子郵件', '$隸屬醫院', '$科別')"; // 插入新資料，不包含圖片
    }

    // 插入新記錄後，也在 user 表中插入或更新對應的 username
    $SQL更新User = "UPDATE user SET username='$姓名' WHERE email='$電子郵件'"; // 更新 user 資料表中的使用者名稱
}

// 執行資料庫操作
if (mysqli_query($link, $SQL指令) && mysqli_query($link, $SQL更新User)) {
    // 如果執行成功，重新導向到個人資料頁面，並顯示成功訊息
    header("Location: d_profile.php?帳號=$帳號&success=資料已成功修改");
} else {
    // 如果執行失敗，記錄錯誤並重新導向到個人資料頁面，顯示錯誤訊息
    error_log("SQL Error: " . mysqli_error($link));
    header("Location: d_profile.php?帳號=$帳號&error=修改失敗，請稍後再試");
}

mysqli_close($link); // 關閉資料庫連接
?>