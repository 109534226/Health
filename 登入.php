<?php
session_start();
include "db.php"; // 假設你有一個 db.php 檔案用來建立資料庫連接

// 從表單中獲取帳號和密碼
$帳號 = $_POST["name"];
$密碼 = $_POST["passwd"];

// 移除空白字符
$帳號 = trim($帳號);
$密碼 = trim($密碼);

// 驗證帳號是否輸入
if ($帳號 == "") {
    echo "<script>
            alert('帳號未輸入');
            window.location.href = 'login.php';
        </script>";
    exit;
}

// 驗證密碼是否輸入
if ($密碼 == "") {
    echo "<script>
            alert('密碼未輸入');
            window.location.href = 'login.php';
        </script>";
    exit;
}

// 防止SQL注入
$帳號 = mysqli_real_escape_string($link, $帳號);
$密碼 = mysqli_real_escape_string($link, $密碼);

// 構造SQL查詢語句，根據帳號查找用戶
$SQL指令 = "SELECT * FROM `user` WHERE `name` = '$帳號' AND `password` = '$密碼';";
$ret = mysqli_query($link, $SQL指令) or die(mysqli_error($link));

// 檢查查詢結果
if ($row = mysqli_fetch_assoc($ret)) {
    // 成功登入，設置 session 變數
    $_SESSION["帳號"] = $row["name"]; // 假設資料庫的帳號字段是 `name`
    $_SESSION["姓名"] = $row["username"]; // 假設資料庫的姓名字段是 `username`
    $_SESSION["電子郵件"] = $row["email"]; // 假設資料庫的電子郵件字段是 `email`
    $_SESSION["登入狀態"] = true; // 設定登入狀態
    $_SESSION["user_role"] = $row["grade"] == "0" ? "使用者" :
        ($row["grade"] == "1" ? "醫生" :
            ($row["grade"] == "2" ? "護士" :
                ($row["grade"] == "3" ? "管理者" : "未知角色")));

    // 顯示彈跳視窗並跳轉
    echo "<script>
alert('~歡迎回來~" . htmlspecialchars($row["username"]) . "');
</script>";


    // 根據使用者等級跳轉到不同頁面
    if ($row["grade"] == "0") {
        echo "<script>window.location.href = 'u_profile.php?帳號=$帳號';</script>"; // 使用者
    } elseif ($row["grade"] == "1") {
        echo "<script>window.location.href = 'd_profile.php?帳號=$帳號';</script>"; // 醫生
    } elseif ($row["grade"] == "2") {
        echo "<script>window.location.href = 'n_profile.php?帳號=$帳號';</script>"; // 護士
    } elseif ($row["grade"] == "3") {
        echo "<script>window.location.href = 'c_profile.php?帳號=$帳號';</script>"; // 管理者
    } else {
        echo "<script>
                alert('無效的使用者等級。');
                window.location.href = 'register.php';
              </script>";
    }
    exit; // 確保跳轉後停止執行其他代碼
} else {
    // 登入失敗處理
    echo "<script>
            alert('登入失敗，請檢查帳號或密碼。');
            window.location.href = 'login.php';
          </script>";
    exit;
}

// 關閉資料庫連接
mysqli_close($link);
?>