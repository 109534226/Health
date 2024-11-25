<?php
session_start(); // 啟動 Session
include "db.php"; // 引入資料庫連線檔案

// 接收用戶輸入的電子郵件和新密碼
$電子郵件 = $_POST["email"];
$新密碼 = $_POST["newpsd"];
$新密碼2 = $_POST["newpsd2"];

// 檢查新密碼是否輸入
if ($新密碼 == "") {
    echo "<script>
            alert('新密碼未輸入'); // 提示新密碼未輸入
            window.location.href = 'u_change.php'; // 跳轉回修改密碼頁面
          </script>";
    exit; // 停止後續程式執行
}

// 檢查再次輸入的新密碼是否輸入
if ($新密碼2 == "") {
    echo "<script>
            alert('再次輸入的新密碼未輸入'); // 提示再次輸入的新密碼未輸入
            window.location.href = 'u_change.php'; // 跳轉回修改密碼頁面
          </script>";
    exit; // 停止後續程式執行
}

// 檢查新密碼是否一致
if ($新密碼 !== $新密碼2) {
    echo "<script>
            alert('密碼不一致'); // 提示密碼不一致
            window.location.href = 'u_change.php'; // 跳轉回修改密碼頁面
          </script>";
    exit; // 停止後續程式執行
}

// 防止 SQL 注入，對電子郵件進行過濾
$電子郵件 = mysqli_real_escape_string($link, $電子郵件);

// 查詢電子郵件是否存在於 `people` 表中
$SQL指令 = "
    SELECT 
        people.people_id, 
        people.email, 
        user.user_id 
    FROM people 
    JOIN user 
    ON people.user_id = user.user_id 
    WHERE people.email = '$電子郵件';
";
$ret = mysqli_query($link, $SQL指令) or die(mysqli_error($link));

// 檢查是否找到符合條件的記錄
if (mysqli_num_rows($ret) > 0) {
    // 如果電子郵件存在，更新用戶密碼
    $row = mysqli_fetch_assoc($ret); // 獲取查詢結果
    $user_id = $row["user_id"]; // 獲取 `user_id`

    // 使用預處理語句更新用戶密碼
    $updateSQL = "UPDATE user SET password = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($link, $updateSQL); // 預處理 SQL 語句
    mysqli_stmt_bind_param($stmt, "si", $新密碼, $user_id); // 綁定參數（新密碼 和 user_id）

    // 執行更新操作並檢查結果
    if (mysqli_stmt_execute($stmt)) {
        // 密碼更新成功
        echo "<script>
                alert('修改成功，請重新登入'); // 提示密碼修改成功
                window.location.href = 'login.php'; // 跳轉到登入頁面
              </script>";
    } else {
        // 密碼更新失敗
        echo "<script>
                alert('修改失敗，請重試。'); // 提示修改失敗
                window.location.href = 'forget.php'; // 跳轉回修改密碼頁面
              </script>";
    }
    mysqli_stmt_close($stmt); // 關閉預處理語句
} else {
    // 如果未找到對應的電子郵件，提示用戶
    echo "<script>
            alert('該電子郵件未註冊，請檢查後重新輸入。');
            window.location.href = 'register.php'; // 跳轉回修改密碼頁面
          </script>";
}

// 關閉資料庫連線
mysqli_close($link);
?>
