<?php
session_start(); // 啟動 Session

include "db.php"; // 引入資料庫連線檔案

// 從表單中接收用戶輸入的電子郵件
$電子郵件 = $_POST["email"];

// 驗證是否有輸入電子郵件
if ($電子郵件 == "") {
    echo "<script>
            alert('電子郵件未輸入'); // 提示用戶未輸入電子郵件
            window.location.href = 'forget.php'; // 導向回忘記密碼頁面
          </script>";
    exit; // 停止後續程式執行
}

// 防止SQL注入，對輸入的電子郵件進行過濾
$電子郵件 = mysqli_real_escape_string($link, $電子郵件);

// 使用 JOIN 查詢，用於關聯 user 表和 people 表，根據電子郵件查詢相關資料
$SQL指令 = "
    SELECT 
        people.people_id,
        people.user_id,
        people.email,
        user.name AS user_name,
        user.account
    FROM people
    JOIN user
    ON people.user_id = user.user_id
    WHERE people.email = '$電子郵件';
";

// 執行查詢
$ret = mysqli_query($link, $SQL指令) or die(mysqli_error($link));

// 檢查查詢結果
if (mysqli_num_rows($ret) > 0) {
    // 如果找到匹配的記錄，則導向下一步並傳遞電子郵件
    echo "<script>
            alert('電子郵件驗證成功，請完成下一步操作。');
            window.location.href = 'forget2.php?email=" . urlencode($電子郵件) . "';
          </script>";
} else {
    // 如果未找到匹配的記錄，提示用戶
    echo "<script>
            alert('該電子郵件未註冊，請檢查後重新輸入。');
            window.location.href = 'forget.php'; // 導向回忘記密碼頁面
          </script>";
}

// 關閉資料庫連接
mysqli_close($link);
?>