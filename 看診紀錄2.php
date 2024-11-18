<?php
session_start();
include "db.php";

$日期 = trim($_POST['appointment_date']); 
$病例號 = trim($_POST['medical_record_number']);
$患者姓名 = trim($_POST['patient_name']);
$性別= trim($_POST['gender']);
$出生年月日 = trim($_POST['birth_date']);
$看診科別 = trim($_POST['department']);
$看診醫生 = trim($_POST['doctor_name']);

// 檢查是否有空值
$errors = [];
if (empty($日期)) $errors[] = '日期未輸入';
if (empty($病例號)) $errors[] = '病例號未輸入';
if (empty($患者姓名)) $errors[] = '患者姓名未輸入';
if (empty($性別)) $errors[] = '性別未選擇';
if (empty($出生年月日)) $errors[] = '出生年月日未選擇';
if (empty($看診科別)) $errors[] = '看診科別未輸入';
if (empty($看診醫生)) $errors[] = '看診醫生未輸入';


if (!empty($errors)) {
    echo "<script>
            alert('" . implode("\\n", $errors) . "');
            window.location.href = 'n_records.php';
          </script>";
    exit;
}

$SQL指令 = "INSERT INTO medicalrecords (dateday, medicalnumber, patientname, 	gender, birthdaydate , department, doctorname) 
VALUES ('$日期', '$病例號', '$患者姓名', '$性別', '$出生年月日','$看診科別', '$看診醫生');";  


// 執行 SQL 指令
if (mysqli_query($link, $SQL指令)) {
    echo "<script>
            alert('新增成功！');
            window.location.href = 'n_records.php';
          </script>";
} else {
    $error_message = mysqli_stmt_error($stmt);
    echo "<script>
            alert('新增失敗，錯誤訊息: $error_message');
            window.location.href = 'n_records.php';
          </script>";
}

// 關閉資料庫連線
mysqli_stmt_close($stmt);
mysqli_close($link);
?>
