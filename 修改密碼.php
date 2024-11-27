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
    $stmt = $link->prepare("UPDATE user SET password = ? WHERE `name` = ? AND grade_id = ?");
    // 綁定參數：新密碼、帳號和等級
    $stmt->bind_param("ssi", $新密碼, $帳號, $等級);

    // 執行更新操作
    if ($stmt->execute()) {
        // 如果更新成功，顯示成功訊息並跳轉到一般登入頁面
        echo "<script>
                alert('修改成功，請重新登入');
                window.location.href = 'login.php';
            </script>";
    } else {
        // 如果更新失敗，顯示失敗訊息並根據等級跳轉到不同頁面
        echo "<script>
                alert('修改失敗，請重試。');
            </script>";

        switch ($等級) {
            case 1:
                // 醫生修改頁面
                echo "<script>window.location.href = 'd_change.php';</script>";
                break;
            case 2:
                // 護士修改頁面
                echo "<script>window.location.href = 'n_change.php';</script>";
                break;
            case 3:
                // 使用者修改頁面
                echo "<script>window.location.href = 'u_change.php';</script>";
                break;
            case 4:
                // 管理者修改頁面
                echo "<script>window.location.href = 'c_change.php';</script>";
                break;
            case 5:
                // 醫院修改頁面
                echo "<script>window.location.href = 'h_change.php';</script>";
                break;
        }
    }
} else {
    // 舊密碼不匹配，顯示錯誤訊息並根據等級跳轉到不同頁面
    echo "<script>
            alert('修改失敗，請檢查帳號或密碼是否正確。');
        </script>";

    switch ($等級) {
        case 1:
            // 醫生修改頁面
            echo "<script>window.location.href = 'd_change.php';</script>";
            break;
        case 2:
            // 護士修改頁面
            echo "<script>window.location.href = 'n_change.php';</script>";
            break;
        case 3:
            // 使用者修改頁面
            echo "<script>window.location.href = 'u_change.php';</script>";
            break;
        case 4:
            // 管理者修改頁面
            echo "<script>window.location.href = 'c_change.php';</script>";
            break;
        case 5:
            // 醫院修改頁面
            echo "<script>window.location.href = 'h_change.php';</script>";
            break;

    }
}

?>