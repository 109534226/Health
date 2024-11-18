<?php
session_start();
include "db.php";

$id = trim($_POST['id']);
$日期 = trim($_POST['appointment_date']);
$病例號 = trim($_POST['medical_record_number']);
$患者姓名 = trim($_POST['patient_name']);
$性別 = trim($_POST['gender']);
$科別 = trim($_POST['department']);
$看診醫生 = trim($_POST['doctor_name']);

// 防止 SQL 注入攻擊
$日期 = mysqli_real_escape_string($link, $日期);
$病例號 = mysqli_real_escape_string($link, $病例號);
$患者姓名 = mysqli_real_escape_string($link, $患者姓名);
$性別 = mysqli_real_escape_string($link, $性別);
$科別 = mysqli_real_escape_string($link, $科別);
$看診醫生 = mysqli_real_escape_string($link, $看診醫生);

$errors = [];
if (empty($日期)) $errors[] = '日期未輸入';
if (empty($病例號)) $errors[] = '病例號未輸入';
if (empty($患者姓名)) $errors[] = '患者姓名未輸入';
if (empty($性別)) $errors[] = '性別未輸入';
if (empty($科別)) $errors[] = '科別未輸入';
if (empty($看診醫生)) $errors[] = '看診醫生未輸入';

if (!empty($errors)) {
    echo "<script>
            alert('" . implode("\\n", $errors) . "');
            window.location.href = 'n_recordssee.php';
          </script>";
    exit;
}

// 準備 SQL 更新語句，將紀錄創建時間更新為當下時間
$SQL指令 = "UPDATE `medicalrecords` SET 
                `dateday` = '$日期', 
                `medicalnumber` = '$病例號', 
                `patientname` = '$患者姓名', 
                `gender` = '$性別', 
                `department` = '$科別', 
                `doctorname` = '$看診醫生',
                `created_at` = NOW() -- 更新為當下的時間
            WHERE `id` = $id";

// 執行 SQL 指令
if (mysqli_query($link, $SQL指令)) {
    echo "<script>
            alert('資料已成功更新！');
            window.location.href = 'n_recordssee.php';
          </script>";
} else {
    $error_message = mysqli_error($link);
    echo "<script>
            alert('更新失敗，錯誤訊息: $error_message');
            window.location.href = 'n_recordssee.php';
          </script>";
}

// 關閉資料庫連線
mysqli_close($link);
?>
