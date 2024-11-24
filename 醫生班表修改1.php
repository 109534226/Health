<?php
session_start();
include "db.php";

$id = trim($_POST['id']);
$日期 = trim($_POST['appointment_date']);
$診間號 = trim($_POST['clinic_number']);
$醫生姓名 = trim($_POST['doctor_name']);
$看診時段 = trim($_POST['consultation_period']);

// 防止 SQL 注入
$日期 = mysqli_real_escape_string($link, $日期);
$診間號 = mysqli_real_escape_string($link, $診間號);
$醫生姓名 = mysqli_real_escape_string($link, $醫生姓名);
$看診時段 = mysqli_real_escape_string($link, $看診時段);

// 檢查是否有空值
$errors = [];
if (empty($日期))
  $errors[] = '日期未輸入';
if (empty($診間號))
  $errors[] = '診間號未輸入';
if (empty($醫生姓名))
  $errors[] = '醫生姓名未輸入';
if (empty($看診時段))
  $errors[] = '看診時段未輸入';

if (!empty($errors)) {
  echo "<script>
            alert('" . implode("\\n", $errors) . "');
            window.location.href = 'd_timesee.php';
          </script>";
  exit;
}

// 準備 SQL 更新語句並更新創建時間為當前時間
$SQL指令 = "UPDATE `doctorshift` SET 
                `dateday` = '$日期', 
                `clinicnumber` = '$診間號', 
                `doctorname` = '$醫生姓名', 
                `consultationperiod` = '$看診時段',
                `created_at` = NOW()
            WHERE `id` = $id";

// 執行 SQL 指令
if (mysqli_query($link, $SQL指令)) {
  echo "<script>
            alert('資料已成功更新！');
            window.location.href = 'd_timesee.php';
          </script>";
} else {
  $error_message = mysqli_error($link);
  echo "<script>
            alert('更新失敗，錯誤訊息: $error_message');
            window.location.href = 'd_timesee.php?id=$id';
          </script>";
}

// 關閉資料庫連線
mysqli_close($link);
?>