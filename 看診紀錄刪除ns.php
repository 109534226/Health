<?php
session_start();
include "db.php";

// 檢查 POST 中是否有傳遞 'id' 並且不是空值
if (isset($_POST['id']) && !empty(trim($_POST['id']))) {
    $id = trim($_POST['id']);

    // 使用預處理語句防止 SQL 注入
    $查詢指令 = "SELECT * FROM patients WHERE patient_id = ?";
    $stmt = mysqli_prepare($link, $查詢指令);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $查詢結果 = mysqli_stmt_get_result($stmt);

    // 如果找到該患者資料
    if ($查詢結果 && mysqli_num_rows($查詢結果) > 0) {
        // 刪除患者資料
        $刪除指令 = "DELETE FROM patients WHERE patient_id = ?";
        $stmt_delete = mysqli_prepare($link, $刪除指令);
        mysqli_stmt_bind_param($stmt_delete, "i", $id);
        if (mysqli_stmt_execute($stmt_delete)) {
            echo "<script>
                    alert('刪除成功');
                    window.location.href = 'd_recordssee.php?search=" . urlencode($id) . "';
                  </script>";
        } else {
            $錯誤訊息 = mysqli_error($link);
            echo "<script>
                    alert('刪除失敗: $錯誤訊息');
                    window.location.href = 'd_recordssee.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('找不到對應的患者資料');
                window.location.href = 'd_recordssee.php';
              </script>";
    }
} else {
    echo "<script>
            alert('未提供有效的 ID');
            window.location.href = 'd_recordssee.php';
          </script>";
}

// 關閉資料庫連線
mysqli_close($link);
?>
