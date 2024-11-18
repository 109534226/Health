
<?php
session_start();
include "db.php";

// 檢查是否已登入
if (!isset($_SESSION["帳號"]) || !isset($_SESSION["姓名"])) {
    echo "<script>
            alert('你還沒有登入，請先登入。');
            window.location.href = 'login.html';
          </script>";
    exit();
}

// 獲取帳號和姓名
$帳號 = $_SESSION["帳號"];
$姓名 = $_SESSION["姓名"];

// 準備刪除語句
$stmt = mysqli_prepare($link, "DELETE FROM user WHERE `name` = ? AND `username` = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $帳號, $姓名);
    mysqli_stmt_execute($stmt);

    // 驗證是否刪除成功
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // 成功刪除帳號，清除 session 並跳轉
        session_destroy();
        echo "<script>
                alert('帳號刪除成功。');
                window.location.href = 'index.php';
              </script>";
    } else {
        echo "<script>
                alert('帳號刪除失敗，請稍後再試。');
                window.location.href = 'u_index.php';
              </script>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<script>
            alert('系統錯誤，請稍後再試。');
            window.location.href = 'u_index.php';
          </script>";
}

// 關閉資料庫連接
mysqli_close($link);
?>
