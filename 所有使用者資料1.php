
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
if (empty($出生年月日) || empty($身分證字號) || empty($電話)) {
    echo "<script>alert('必要資料為空，請填寫所有欄位！'); window.location.href = 'n_profile.php';</script>";
    exit;
}

// 更新 profession 表
$SQL檢查 = "SELECT * FROM profession WHERE name = '$帳號'";
$result = mysqli_query($link, $SQL檢查);
$userData = mysqli_fetch_assoc($result);

if ($userData) {
    // profession 表更新
    $SQL指令 = "UPDATE profession SET username='$姓名', birthday='$出生年月日', idcard='$身分證字號',
                phone='$電話', email='$電子郵件', hospital='$隸屬醫院', department='$科別'";
    if (!empty($profilePicture['tmp_name']) && $profilePicture['error'] == 0) {
        $imageData = addslashes(file_get_contents($profilePicture['tmp_name']));
        $SQL指令 .= ", image='$imageData'";
    }
    $SQL指令 .= " WHERE name='$帳號'";
} else {
    // profession 表插入
    $SQL指令 = "INSERT INTO profession (name, username, birthday, idcard, phone, email, hospital, department";
    if (!empty($profilePicture['tmp_name']) && $profilePicture['error'] == 0) {
        $imageData = addslashes(file_get_contents($profilePicture['tmp_name']));
        $SQL指令 .= ", image) VALUES ('$帳號', '$姓名', '$出生年月日', '$身分證字號', '$電話', '$電子郵件', '$隸屬醫院', '$科別', '$imageData')";
    } else {
        $SQL指令 .= ") VALUES ('$帳號', '$姓名', '$出生年月日', '$身分證字號', '$電話', '$電子郵件', '$隸屬醫院', '$科別')";
    }
}

// 執行 profession 表更新
if (mysqli_query($link, $SQL指令)) {
    // 更新 user 表
    $SQL更新User = "UPDATE user SET username='$姓名', email='$電子郵件' WHERE name = '$帳號'";
    if (!mysqli_query($link, $SQL更新User)) {
        error_log("User table update error: " . mysqli_error($link) . " | SQL: " . $SQL更新User);
        echo "<script>alert('資料已部分更新，但 user 表未同步。請檢查資料！'); window.location.href = 'n_profile.php';</script>";
        exit;
    }

    // 重新查詢 profession 資料，返回最新資料
    $SQL重新查詢 = "SELECT * FROM profession WHERE name = '$帳號'";
    $result = mysqli_query($link, $SQL重新查詢);
    $updatedData = mysqli_fetch_assoc($result);

    // 渲染最新資料到網頁
    echo json_encode([
        'success' => true,
        'message' => '資料更新成功',
        'data' => $updatedData
    ]);
} else {
    error_log("Profession table update error: " . mysqli_error($link) . " | SQL: " . $SQL指令);
    echo json_encode([
        'success' => false,
        'message' => '資料更新失敗，請稍後再試'
    ]);
}

mysqli_close($link);
?>
