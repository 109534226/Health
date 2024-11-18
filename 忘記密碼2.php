<?php
session_start();
include "db.php";
$電子郵件 = $_POST["email"];
$新密碼 = $_POST["newpsd"];
$新密碼2 = $_POST["newpsd2"];

if ($新密碼 == "") {
    echo "<script>
            alert('新密碼未輸入');
            window.location.href = 'u_change.php';
        </script>";
    exit;
}

if ($新密碼2 == "") {
    echo "<script>
            alert('再次輸入的新密碼未輸入');
            window.location.href = 'u_change.php';
        </script>";
    exit;
}

if ($新密碼 !== $新密碼2) {
    echo "<script>
            alert('密碼不一致');
            window.location.href = 'u_change.php';
        </script>";
    exit;
}

$電子郵件 = mysqli_real_escape_string($link, $電子郵件);

// 查詢帳號的密碼
$stmt = $link->prepare("SELECT password FROM user WHERE `email` = ?");
$stmt->bind_param("s", $電子郵件);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc(); // 取得查詢結果
// 更新為新密碼
$stmt = $link->prepare("UPDATE user SET password = ? WHERE `email` = ?");
$stmt->bind_param("ss", $新密碼, $電子郵件);
if ($stmt->execute()) {
    echo "<script>
                alert('修改成功，請重新登入');
                window.location.href = 'login.php';
            </script>";
} else {
    echo "<script>
                alert('修改失敗，請重試。');
                window.location.href = 'u_change.php';
            </script>";
}
?>