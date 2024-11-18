<?php
session_start();
include "db.php";

$帳號 = $_POST["name"];
$舊密碼 = $_POST["oldpsd"];
$新密碼 = $_POST["newpsd"];
$新密碼2 = $_POST["newpsd2"];

$帳號 = trim($帳號);

if ($帳號 == "") {
    echo "<script>
            alert('帳號未輸入');
            window.location.href = 'u_change.php';
        </script>";
    exit; // 添加 exit; 確保不繼續執行
}

if ($舊密碼 == "") {
    echo "<script>
            alert('舊密碼未輸入');
            window.location.href = 'u_change.php';
        </script>";
    exit;
}

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

$帳號 = mysqli_real_escape_string($link, $帳號);

// 查詢帳號的密碼
$stmt = $link->prepare("SELECT password FROM user WHERE `name` = ?");
$stmt->bind_param("s", $帳號);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc(); // 取得查詢結果

// 驗證舊密碼是否正確
if ($row && $舊密碼 === $row['password']) {
    // 舊密碼正確，更新為新密碼
    $stmt = $link->prepare("UPDATE user SET password = ? WHERE `name` = ?");
    $stmt->bind_param("ss", $新密碼, $帳號);
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
} else {
    // 舊密碼不匹配
    echo "<script>
            alert('修改失敗，請檢查帳號或密碼是否相同。');
            window.location.href = 'u_change.php';
        </script>";
}
?>
