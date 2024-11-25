<?php
session_start();
include 'db.php'; // 引入資料庫連線檔案

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 從表單接收資料
    $name = $_POST["name"]; // 帳號
    $username = $_POST["name"]; // 姓名
    $password = $_POST["password"]; // 明文密碼
    $grade_id = intval($_POST["grade_id"]); // 等級 ID

    // 更新 `user` 表中的資料
    $sql = "UPDATE user SET `name` = ?, `password` = ?, `grade_id` = ? WHERE `name` = ?";
    $stmt = mysqli_prepare($link, $sql); // 準備 SQL 語句
    mysqli_stmt_bind_param($stmt, "ssis", $username, $password, $grade_id, $name); // 綁定參數

    // 執行查詢並檢查結果
    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "<script>
                    alert('用戶資料更新成功！');
                    window.location.href = 'c_user.php'; // 返回列表頁面
                  </script>";
        } else {
            echo "<script>
                    alert('資料未更新（可能資料未更動或用戶不存在）。');
                    window.location.href = '用戶列表.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('更新失敗：" . mysqli_stmt_error($stmt) . "');
                window.location.href = '用戶列表.php';
              </script>";
    }

    mysqli_stmt_close($stmt); // 關閉查詢語句
}

mysqli_close($link); // 關閉資料庫連線
?>
