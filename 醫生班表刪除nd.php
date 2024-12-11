<?php
session_start();
include "db.php";

// 檢查是否提供了有效的 ID
if (!isset($_POST['id']) || !ctype_digit(trim($_POST['id']))) {
    echo "<script>
            alert('無效的排班 ID');
            window.location.href = 'd_timefind.php';
          </script>";
    exit;
}

$id = trim($_POST['id']);
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// 使用預處理語句來防止 SQL 注入
$查詢指令 = "SELECT * FROM doctorshift WHERE id = ?";
$查詢準備 = mysqli_prepare($link, $查詢指令);
mysqli_stmt_bind_param($查詢準備, "i", $id);
mysqli_stmt_execute($查詢準備);
$查詢結果 = mysqli_stmt_get_result($查詢準備);

if ($查詢結果 && mysqli_num_rows($查詢結果) > 0) {
    $刪除指令 = "DELETE FROM doctorshift WHERE id = ?";
    $刪除準備 = mysqli_prepare($link, $刪除指令);
    mysqli_stmt_bind_param($刪除準備, "i", $id);

    if (mysqli_stmt_execute($刪除準備)) {
        echo "<script>
                alert('刪除成功');
                window.location.href = 'd_timefind.php?deleted=true&search=" . urlencode($searchTerm) . "';
              </script>";
    } else {
        error_log("刪除醫生排班時發生錯誤: " . mysqli_error($link)); // 將錯誤訊息記錄到伺服器日誌
        echo "<script>
                alert('刪除失敗，請稍後再試');
                window.location.href = 'd_timefind.php?search=" . urlencode($searchTerm) . "';
              </script>";
    }
} else {
    echo "<script>
            alert('查無此筆資料');
            window.location.href = 'd_timefind.php?search=" . urlencode($searchTerm) . "';
          </script>";
}

// 關閉資料庫連線
mysqli_close($link);
?>
