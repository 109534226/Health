<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST["id"]);
    $username = $_POST["username"];
    $name = $_POST["name"];
    $password =$_POST["password"];
    $email = $_POST["email"];
    $grade = intval($_POST["grade"]);

    // 檢查目前的資料
    $sql_check = "SELECT username, name, password, email, grade FROM user WHERE id = ?";
    $stmt_check = mysqli_prepare($link, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $id);
    mysqli_stmt_execute($stmt_check);
    $result = mysqli_stmt_get_result($stmt_check);
    $current_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt_check);

    // 更新資料
    $sql = "UPDATE user SET username = ?, name = ?, password = ?, email = ?, grade = ? WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ssssii", $username, $name, $password, $email, $grade, $id);

    if (mysqli_stmt_execute($stmt)) {
        $affected_rows = mysqli_stmt_affected_rows($stmt);
        if ($affected_rows > 0) {
            // 顯示成功提示並跳轉回編輯頁面
            echo "<script>
                    alert('編輯用戶成功！');
                    window.location.href = '編輯用戶.php';
                  </script>";
        } else {
            // 資料未更新
            echo "<script>
                    alert('資料未更新（資料可能相同或條件不匹配）。');
                    window.location.href = '編輯用戶.php';
                  </script>";
        }
    } else {
        // 更新失敗
        echo "<script>
                alert('更新失敗：" . mysqli_stmt_error($stmt) . "');
                window.location.href = '編輯用戶.php';
              </script>";
    }

    mysqli_stmt_close($stmt);
}

// 關閉資料庫連線
mysqli_close($link);
?>
