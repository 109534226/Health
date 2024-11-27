<?php
session_start();
include "db.php";

// 檢查 ID 是否提供且為有效的數字
if (!isset($_POST['id']) || !ctype_digit(trim($_POST['id']))) {
    echo "<script>
            alert('無效的患者 ID');
            window.location.href = 'd_advicesee.php';
          </script>";
    exit;
}

$id = trim($_POST['id']);

// 使用預處理語句來防止 SQL 注入攻擊
$查詢指令 = "SELECT * FROM patients WHERE id = ?";
$查詢準備 = mysqli_prepare($link, $查詢指令);
mysqli_stmt_bind_param($查詢準備, "i", $id);
mysqli_stmt_execute($查詢準備);
$查詢結果 = mysqli_stmt_get_result($查詢準備);

if ($查詢結果 && mysqli_num_rows($查詢結果) > 0) {
    $刪除指令 = "DELETE FROM patients WHERE id = ?";
    $刪除準備 = mysqli_prepare($link, $刪除指令);
    mysqli_stmt_bind_param($刪除準備, "i", $id);
    
    if (mysqli_stmt_execute($刪除準備)) {
        echo "<script>
                alert('刪除成功');
                window.location.href = 'd_advicesee.php?search=" . urlencode($id) . "';
              </script>";
    } else {
        error_log("刪除患者資料時發生錯誤: " . mysqli_error($link)); // 記錄錯誤到伺服器日誌
        echo "<script>
                alert('刪除失敗，請稍後再試');
                window.location.href = 'd_advicesee.php';
              </script>";
    }
} else {
    echo "<script>
            alert('查無此筆資料');
            window.location.href = 'd_advicesee.php';
          </script>";
}

// 關閉資料庫連線
mysqli_close($link);
?>
