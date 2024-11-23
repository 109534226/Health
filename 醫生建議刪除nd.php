<?php
session_start();
include "db.php";

$id = trim($_POST['id']);
$searchTerm = isset($_POST['searchTerm']) ? trim($_POST['searchTerm']) : ''; // 取得搜尋條件

// 檢查是否存在該ID的患者資料
$查詢指令 = "SELECT * FROM patients WHERE id = ?";
$查詢準備 = mysqli_prepare($link, $查詢指令);
mysqli_stmt_bind_param($查詢準備, "i", $id);
mysqli_stmt_execute($查詢準備);
$查詢結果 = mysqli_stmt_get_result($查詢準備);

if (mysqli_num_rows($查詢結果) > 0) {
    $刪除指令 = "DELETE FROM patients WHERE id = ?";
    $刪除準備 = mysqli_prepare($link, $刪除指令);
    mysqli_stmt_bind_param($刪除準備, "i", $id);
    if (mysqli_stmt_execute($刪除準備)) {
        echo "<script>
                alert('刪除成功');
                window.location.href = 'n_advicefind.php?deleted=true&search=" . urlencode($searchTerm) . "';
              </script>";
    } else {
        $錯誤訊息 = mysqli_error($link);
        echo "<script>
                alert('刪除失敗: $錯誤訊息');
                window.location.href = 'n_advicefind.php?search=" . urlencode($searchTerm) . "';
              </script>";
    }
} else {
    echo "<script>
            alert('查無此筆資料');
            window.location.href = 'n_advicefind.php?search=" . urlencode($searchTerm) . "';
          </script>";
}

// 關閉資料庫連線
mysqli_close($link);
?>
