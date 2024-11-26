<?php
// 引入資料庫連接檔案
include 'db.php';

// 啟動會話（用於管理登入等狀態）
session_start();

// 如果請求方式是 POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 從表單獲取數據
    $id = $_POST['id']; // 用戶 ID
    $name = $_POST['username']; // 帳號名稱
    $account = $_POST['name']; // 帳號名稱
    $password = $_POST['password']; // 密碼
    $grade_id = $_POST['grade_id']; // 用戶權限等級
    $state = $_POST['state']; // 用戶狀態

    // 檢查是否已經存在該帳號
    $SQL檢查 = "SELECT COUNT(*) as cnt FROM `user` WHERE `name` = '$name'";
    $result = mysqli_query($link, $SQL檢查); // 執行檢查帳號的 SQL 語句
    $row = mysqli_fetch_assoc($result); // 獲取查詢結果
    
    // 如果查詢結果顯示帳號已存在
    if ($row['cnt'] > 0) {
        echo "<script>
                alert('帳號已存在，請選擇其他帳號。'); // 提示用戶帳號已存在
                window.location.href = '新增用戶.php'; // 返回新增用戶頁面
            </script>";
        exit(); // 終止執行
    }

    // 防止 SQL 注入的措施，轉義特殊字符
    $grade_id = mysqli_real_escape_string($link, $grade_id); // 轉義權限等級
    $name = mysqli_real_escape_string($link, $name); // 轉義帳號名稱

    $account = mysqli_real_escape_string($link, $account); 
    $password = mysqli_real_escape_string($link, $password); // 轉義密碼

    // SQL 插入語句，將用戶數據插入資料庫
    $sql = "INSERT INTO `user` (`user_id`,`name`,`password`,`account`, `grade_id`) VALUES (null,'$name','$password', '$account', '$grade_id')";

    // 使用 mysqli_query 執行 SQL 插入語句
    if (mysqli_query($link, $sql)) {
        echo ""; // 不顯示其他訊息
        echo "<script>
                alert('新用戶新增成功'); // 提示新增成功
                window.location.href = '新增用戶.php'; // 返回新增用戶頁面
              </script>";
    } else {
        // 如果執行失敗，顯示錯誤訊息
        echo "新增失敗: " . mysqli_error($link);
    }

    // 關閉與資料庫的連接
    mysqli_close($link);
}
?>
