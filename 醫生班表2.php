<?php
session_start();
include "db.php";  // 確認你的 db.php 文件正確包含資料庫連線


$日期 = $_POST['appointment_date'];
$診間號 = $_POST['clinic_number'];
$醫生姓名 = $_POST['doctor_name'];
$看診時段 = $_POST['consultation_period'];

// 防止 SQL 注入攻擊
$日期 = mysqli_real_escape_string($link, $日期);
$診間號 = mysqli_real_escape_string($link, $診間號);
$醫生姓名 = mysqli_real_escape_string($link, $醫生姓名);
$看診時段 = mysqli_real_escape_string($link, $看診時段);




// 檢查輸入數據的有效性
if (empty($日期) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $日期)) {
    echo "<script>
            alert('請輸入有效的日期格式。');
            window.location.href = 'n_time.php';
          </script>";
    exit;
}

if (empty($看診時段)) {
    echo "<script>
            alert('請選擇看診時段！');
            window.location.href = 'n_time.php';
          </script>";
    exit;
}

// 確保其他欄位檢查也在這裡
if (empty($診間號) || !preg_match("/^\d{1,10}$/", $診間號)) {
    echo "<script>
            alert('診間號必須是1到10位的數字。');
            window.location.href = 'n_time.php';
          </script>";
    exit;
}

if (empty($醫生姓名)) {
    echo "<script>
            alert('醫生姓名未輸入');
            window.location.href = 'n_time.php';
          </script>";
    exit;
}

// 進行資料庫插入的後續步驟


// 使用正確的欄位名稱
$SQL指令 = "INSERT INTO doctorshift (dateday, clinicnumber, doctorname, consultationperiod)
VALUES ('$日期', '$診間號', '$醫生姓名', '$看診時段');";


// 執行 SQL 指令
if (mysqli_query($link, $SQL指令)) {
    echo "<script>
            alert('新增成功！');
            window.location.href = 'n_time.php';
          </script>";
} else {
    $error_message = mysqli_error($link);
    echo "<script>
            alert('新增失敗，錯誤訊息: $error_message');
            window.location.href = 'n_time.php';
          </script>";
}

// 關閉資料庫連線
mysqli_close($link);
?>