<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 從表單接收資料
    $name = $_POST["name"]; // 帳號
    $username = $_POST["username"]; // 姓名
    $password = $_POST["password"]; // 明文密碼
    $email = $_POST["email"]; // 電子郵件
    $grade = intval($_POST["grade"]); // 等級

    // 執行更新
    $sql = "UPDATE user SET `username` = ?, `password` = ?, `email` = ?, `grade` = ? WHERE `name` = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "sssis", $username, $password, $email, $grade, $name);

    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "<script>
                    alert('用戶資料更新成功！');
                    window.location.href = 'c_user.php'; // 返回列表頁面
                  </script>";
        } else {
            echo "<script>
                    alert('資料未更新（可能資料未更動或用戶不存在）。');
                    window.location.href = 'c_user.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('更新失敗：" . mysqli_stmt_error($stmt) . "');
                window.location.href = 'c_user.php';
              </script>";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($link);
?>
