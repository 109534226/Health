<?php
session_start();
include "db.php";

$id = trim($_POST['id']);

// 檢查是否存在該ID的患者資料
$查詢指令 = "SELECT * FROM  patients WHERE id = $id";
$查詢結果 = mysqli_query($link, $查詢指令);

if (mysqli_num_rows($查詢結果) > 0) {
    $刪除指令 = "DELETE FROM  patients WHERE id = $id";
    if (mysqli_query($link, $刪除指令)) {
        echo "<script>
                alert('刪除成功');
                window.location.href = 'd_advicesee.php?search=$id';
              </script>";
    } else {
        $錯誤訊息 = mysqli_error($link);
        echo "<script>
                alert('刪除失敗: $錯誤訊息');
                window.location.href = 'd_advicesee.php';
              </script>";
    }
}

// 關閉資料庫連線
mysqli_close($link);
?>
