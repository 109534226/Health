<?php
session_start();
include "db.php";

// 獲取並清理 POST 請求的資料
$id = trim($_POST['id']);
$看診日期 = trim($_POST['appointment_date']);
$看診時段 =trim($_POST['consultationt']);
$病例號 = trim($_POST['clinic_number']);
$患者姓名 = trim($_POST['patient_name']);
$出生日期 = trim($_POST['birth_date']);
$性別 = trim($_POST['gender']);
$看診科別 trim($_POST['department']);
$看診醫生 = trim($_POST['doctor_name']);
$醫生建議 = trim($_POST['doctor_advice']);

// 防止 SQL 注入
$看診日期 = mysqli_real_escape_string($link, $看診日期);
$看診時段 = mysqli_real_escape_string($link, $看診時段);
$病例號 = mysqli_real_escape_string($link, $病例號);
$患者姓名 = mysqli_real_escape_string($link, $患者姓名);
$出生日期 = mysqli_real_escape_string($link, $出生日期);
$性別 = mysqli_real_escape_string($link, $性別);
$看診科別 = mysqli_real_escape_string($link, $看診科別);
$看診醫生 = mysqli_real_escape_string($link, $看診醫生);
$醫生建議 = mysqli_real_escape_string($link, $醫生建議);

// 檢查是否有空值
$errors = [];
if (empty($看診日期)) $errors[] = '看診日期未輸入';
if (empty($看診時段)) $errors[] = '看診時段未輸入';
if (empty($病例號)) $errors[] = '病例號未輸入';
if (empty($患者姓名)) $errors[] = '患者姓名未輸入';
if (empty($出生日期)) $errors[] = '出生日期未輸入';
if (empty($性別)) $errors[] = '性別未輸入';
if (empty($看診科別)) $errors[] = '看診科別未輸入';
if (empty($看診醫生)) $errors[] = '看診醫生未輸入';
if (empty($醫生建議)) $errors[] = '醫生建議未輸入';

if (!empty($errors)) {
    echo "<script>
            alert('" . implode("\\n", $errors) . "');
            window.location.href = 'd_advicesee.php';
          </script>";
    exit;
}

// 更新紀錄，包括當前時間的 `created_at`
$SQL指令 = "UPDATE patients SET 
                dateday = '$看診日期', 
                consultationt = '$看診時段', 
                medicalnumber = '$病例號', 
                patientname = '$患者姓名', 
                birthday = '$出生日期', 
                gender = '$性別', 
                department = '$看診科別', 
                doctorname = '$看診醫生', 
                doctoradvice = '$醫生建議',
                created_at = NOW() -- 更新為當下的時間
            WHERE id = $id";

if (mysqli_query($link, $SQL指令)) {
    echo "<script>
            alert('資料已成功更新！');
            window.location.href = 'd_advicesee.php';
          </script>";
} else {
    $error_message = mysqli_error($link);
    echo "<script>
            alert('更新失敗，錯誤訊息: $error_message');
            window.location.href = '醫生建議修改000.php?id=$id';
          </script>";
}

// 關閉資料庫連線
mysqli_close($link);
?>
