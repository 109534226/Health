<?php
session_start(); // 啟動 Session
include "db.php"; // 引入資料庫連線檔案

// 接收表單輸入的資料
$姓名 = $_POST["name"];
$帳號 = $_POST["account"];
$密碼 = $_POST["password"];
$電子郵件 = $_POST["email"];

// 驗證用戶輸入是否完整
if ($姓名 == "") {
    echo "<script>
            alert('姓名未輸入');
            window.location.href = 'register.php';
          </script>";
    exit; // 確保不繼續執行
}
if ($帳號 == "") {
    echo "<script>
            alert('帳號未輸入');
            window.location.href = 'register.php';
          </script>";
    exit; // 確保不繼續執行
}
if ($密碼 == "") {
    echo "<script>
            alert('密碼未輸入');
            window.location.href = 'register.php';
          </script>";
    exit; // 確保不繼續執行
}
if ($電子郵件 == "") {
    echo "<script>
            alert('電子郵件未輸入');
            window.location.href = 'register.php';
          </script>";
    exit; // 確保不繼續執行
}

// 檢查是否已經存在該帳號
$SQL檢查 = "SELECT COUNT(*) as cnt FROM `user` WHERE `account` = '$帳號'";
$result = mysqli_query($link, $SQL檢查);
$row = mysqli_fetch_assoc($result);

if ($row['cnt'] > 0) {
    // 如果帳號已存在，提示用戶並跳轉回註冊頁面
    echo "<script>
            alert('帳號已存在，請選擇其他帳號。');
            window.location.href = 'register.php';
          </script>";
    exit();
}

// 預設等級為 "使用者"（grade_id = 1）
$預設等級 = 1;

// 插入新使用者到 `user` 表
$SQL插入使用者 = "
    INSERT INTO `user` (`name`, `account`, `password`, `grade_id`) 
    VALUES ('$姓名', '$帳號', '$密碼', '$預設等級');
";

// 檢查插入是否成功
if ($ret = mysqli_query($link, $SQL插入使用者)) {
    // 獲取剛插入的使用者的 user_id
    $user_id = mysqli_insert_id($link);

    // 插入 `people` 表，對應的詳細個人資料，包含電子郵件
    $SQL插入個人資料 = "
        INSERT INTO `people` (`user_id`, `email`) 
        VALUES ('$user_id', '$電子郵件');
    ";
    if ($retPeople = mysqli_query($link, $SQL插入個人資料)) {
        // 註冊成功，導向使用者個人頁面
        header("Location: u_profile.php?帳號=$帳號");
        exit(); // 確保跳轉後不繼續執行
    } else {
        // 插入個人資料失敗，刪除已插入的 `user` 記錄
        $SQL刪除使用者 = "DELETE FROM `user` WHERE `user_id` = '$user_id'";
        mysqli_query($link, $SQL刪除使用者);
        echo "<script>
                alert('新增個人資料失敗，請稍後再試！');
                window.location.href = 'register.php';
              </script>";
        exit();
    }
} else {
    // 插入 `user` 表失敗
    echo "<script>
            alert('註冊失敗，請稍後再試！');
            window.location.href = 'register.php';
          </script>";
    exit();
}

// 關閉資料庫連接
mysqli_close($link);
?>
