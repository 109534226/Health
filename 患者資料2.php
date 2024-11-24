<?php
session_start();
include "db.php";

$病歷號 = trim($_POST['medical_record_number']); 
$患者姓名 = trim($_POST['patient_name']);
$性別 = trim($_POST['gender']);
$出生日期 = trim($_POST['birth_date']);
$當前症狀 = trim($_POST['current_symptoms']);
$過敏藥物 = trim($_POST['allergies']);
$歷史重大疾病 = trim($_POST['medical_history']);

// 檢查是否有空值
$errors = [];
if (empty($病歷號)) $errors[] = '病例號未輸入';
if (empty($患者姓名)) $errors[] = '患者姓名未輸入';
if (empty($性別)) $errors[] = '性別未選擇';
if (empty($出生日期)) $errors[] = '出生日期未輸入';
if (empty($當前症狀)) $errors[] = '當前症狀未輸入';
if (empty($過敏藥物)) $errors[] = '過敏藥物未輸入';
if (empty($歷史重大疾病)) $errors[] = '歷史重大疾病未輸入';


if (!empty($errors)) {
    echo "<script>
            alert('" . implode("\\n", $errors) . "');
            window.location.href = 'n_Basic.php';
          </script>";
    exit;
}

$SQL指令 = "INSERT INTO patients (medicalnumber, patientname, gender, birthdaydate, currentsymptoms, allergies, medicalhistory) 
VALUES ('$病歷號', '$患者姓名', '$性別', '$出生日期', '$當前症狀', '$過敏藥物', '$歷史重大疾病');";  


// 執行 SQL 指令
if (mysqli_query($link, $SQL指令)) {
    echo "<script>
            alert('新增成功！');
            window.location.href = 'd_Basic.php';
          </script>";
} else {
    $error_message = mysqli_stmt_error($stmt);
    echo "<script>
            alert('新增失敗，錯誤訊息: $error_message');
            window.location.href = 'd_Basic.php';
          </script>";
}

// 關閉資料庫連線
mysqli_stmt_close($stmt);
mysqli_close($link);
?>
