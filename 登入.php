<?php
session_start(); // 啟動 Session，讓伺服器可以儲存使用者登入資訊
include "db.php"; // 引入資料庫連線檔案

// 從表單中接收用戶輸入的帳號和密碼
$帳號 = $_POST["account"]; // 修改欄位為 account
$密碼 = $_POST["password"]; // 修改欄位為 password

// 移除用戶輸入的帳號和密碼前後的多餘空格
$帳號 = trim($帳號);
$密碼 = trim($密碼);

// 驗證是否有輸入帳號
if ($帳號 == "") {
    echo "<script>
            alert('帳號未輸入'); // 提示用戶帳號未輸入
            window.location.href = 'login.php'; // 導向回登入頁面
          </script>";
    exit; // 停止後續程式執行
}

// 驗證是否有輸入密碼
if ($密碼 == "") {
    echo "<script>
            alert('密碼未輸入'); // 提示用戶密碼未輸入
            window.location.href = 'login.php'; // 導向回登入頁面
          </script>";
    exit; // 停止後續程式執行
}

// 防止SQL注入，對輸入的帳號和密碼進行過濾
$帳號 = mysqli_real_escape_string($link, $帳號);
$密碼 = mysqli_real_escape_string($link, $密碼);

// 使用 JOIN 查詢，用於關聯 user 表和 grade 表
$SQL指令 = "
    SELECT 
        user.user_id,
        user.name AS user_name, 
        user.account, 
        user.password, 
        user.grade_id, 
        grade.grade AS role_name 
    FROM user 
    JOIN grade 
    ON user.grade_id = grade.grade_id 
    WHERE user.account = '$帳號' AND user.password = '$密碼';
";
$ret = mysqli_query($link, $SQL指令) or die(mysqli_error($link)); // 執行查詢，並處理可能的錯誤

// 檢查查詢是否有返回結果
if ($row = mysqli_fetch_assoc($ret)) {
    // 如果用戶存在，設定Session變數以記錄用戶狀態
    
    $_SESSION["帳號"] = $row["account"]; // 儲存用戶帳號
    $_SESSION["姓名"] = $row["user_name"]; // 儲存用戶姓名
    $_SESSION["登入狀態"] = true; // 紀錄用戶已登入
    $_SESSION["角色"] = $row["role_name"]; // 從 `grade` 表中提取角色名稱

    // 顯示歡迎訊息
    echo "<script>
            alert('~歡迎回來~" . htmlspecialchars($row["user_name"]) . "');
          </script>";

    // 根據角色名稱跳轉至對應頁面
    switch ($row["role_name"]) {
        case "使用者":
            echo "<script>window.location.href = 'u_profile.php?帳號=$帳號';</script>";
            break;
        case "醫生":
            echo "<script>window.location.href = 'd_profile.php?帳號=$帳號';</script>";
            break;
        case "護士":
            echo "<script>window.location.href = 'n_profile.php?帳號=$帳號';</script>";
            break;
        case "醫院":
            echo "<script>window.location.href = 'h_profile.php?帳號=$帳號';</script>";
            break;
        case "管理者":
            echo "<script>window.location.href = 'c_profile.php?帳號=$帳號';</script>";
            break;
        default:
            echo "<script>
                    alert('無效的使用者角色。');
                    window.location.href = 'register.php';
                  </script>";
    }
    exit; // 確保後續程式碼不執行
} else {
    // 如果查詢結果為空，提示用戶登入失敗
    echo "<script>
            alert('登入失敗，請檢查帳號或密碼。');
            window.location.href = 'login.php'; // 導向回登入頁面
          </script>";
    exit; // 停止後續程式執行
}

// 關閉資料庫連接
mysqli_close($link);
?>