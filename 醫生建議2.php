<?php
session_start();
include "db.php";

$日期 = trim($_POST['appointment_date']);
$病例號 = trim($_POST['clinic_number']); 
$患者姓名 = trim($_POST['patient_name']);
$出生日期 = trim($_POST['birth_date']); 
$性別 = trim($_POST['gender']);
$看診醫生 = trim($_POST['doctor_name']);
$醫生建議 = trim($_POST['doctor_advice']); 
$是否回診 = isset($_POST['follow_up']) ? trim($_POST['follow_up']) : '';

// 防止 SQL 注入
$日期 = mysqli_real_escape_string($link, $日期);
$病例號 = mysqli_real_escape_string($link, $病例號);
$患者姓名 = mysqli_real_escape_string($link, $患者姓名);
$出生日期 = mysqli_real_escape_string($link, $出生日期);
$性別 = mysqli_real_escape_string($link, $性別);
$看診醫生 = mysqli_real_escape_string($link, $看診醫生);
$醫生建議 = mysqli_real_escape_string($link, $醫生建議); 
$是否回診 = mysqli_real_escape_string($link, $是否回診);

// 檢查是否有空值
$errors = [];
if (empty($日期)) $errors[] = '日期未輸入';
if (empty($病例號)) $errors[] = '病例號未輸入';
if (empty($患者姓名)) $errors[] = '患者姓名未輸入';
if (empty($出生日期)) $errors[] = '出生日期未輸入';
if (empty($性別)) $errors[] = '性別未選擇';
if (empty($看診醫生)) $errors[] = '看診醫生未輸入';
if (empty($醫生建議)) $errors[] = '醫生建議未輸入';
if (empty($是否回診)) $errors[] = '是否回診未選擇';  

if (!empty($errors)) {
    echo "<script>
            alert('" . implode("\\n", $errors) . "');
            window.location.href = 'n_advice.php';
          </script>";
    exit;
}

// 準備 SQL 指令
$SQL指令 = "INSERT INTO `medicaladvice` 
    (`dateday`, `clinicnumber`, `patientname`, `birthdaydate`, `gender`, `doctorname`, `doctoradvice`, `followup`, `created_at`) 
VALUES 
    ('$日期', '$病例號', '$患者姓名', '$出生日期', '$性別', '$看診醫生', '$醫生建議', '$是否回診', CURRENT_TIMESTAMP);";

// 執行 SQL 指令
if (mysqli_query($link, $SQL指令)) {
    echo "<script>
            alert('新增成功！');
            window.location.href = 'n_advice.php';
          </script>";
} else {
    $error_message = mysqli_error($link);
    echo "<script>
            alert('新增失敗，錯誤訊息: $error_message');
            window.location.href = 'n_advice.php';
          </script>";
}

// 關閉資料庫連線
mysqli_close($link);
?>
