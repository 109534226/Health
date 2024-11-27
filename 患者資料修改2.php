<?php
session_start();
include "db.php";

$id = trim($_POST['id']);
$病歷號 = trim($_POST['medical_record_number']);
$患者姓名 = trim($_POST['patient_name']);
$性別 = trim($_POST['gender']);
$出生日期 = trim($_POST['birth_date']);
$看診時段 = trim($_POST['consultation']);
$看診科別 = trim($_POST['department']);
$看診醫生 = trim($_POST['doctor_name']);
$當前狀況 = trim($_POST['current_symptoms']);
$過敏藥物 = trim($_POST['allergies']);
$歷史重大疾病 = trim($_POST['medical_history']);

// 防止 SQL 注入
$病歷號 = mysqli_real_escape_string($link, $病歷號);
$患者姓名 = mysqli_real_escape_string($link, $患者姓名);
$性別 = mysqli_real_escape_string($link, $性別);
$出生日期 = mysqli_real_escape_string($link, $出生日期);
$看診時段 = mysqli_real_escape_string($link, $看診時段);
$看診科別 = mysqli_real_escape_string($link, $看診科別);
$看診醫生 = mysqli_real_escape_string($link, $看診醫生);
$當前狀況 = mysqli_real_escape_string($link, $當前狀況);
$過敏藥物 = mysqli_real_escape_string($link, $過敏藥物);
$歷史重大疾病 = mysqli_real_escape_string($link, $歷史重大疾病);

// 根據關聯表格更新資料
// 首先獲取對應的性別 ID、科別 ID、看診時段 ID、看診醫生 ID
$性別查詢 = "SELECT gender_id FROM gender WHERE gender = '$性別'";
$性別結果 = mysqli_query($link, $性別查詢);
$性別行 = mysqli_fetch_assoc($性別結果);
$性別_id = $性別行['gender_id'];

$科別查詢 = "SELECT department_id FROM department WHERE department = '$看診科別'";
$科別結果 = mysqli_query($link, $科別查詢);
$科別行 = mysqli_fetch_assoc($科別結果);
$科別_id = $科別行['department_id'];

$時段查詢 = "SELECT consultationT_id FROM consultationt WHERE consultationT = '$看診時段'";
$時段結果 = mysqli_query($link, $時段查詢);
$時段行 = mysqli_fetch_assoc($時段結果);
$時段_id = $時段行['consultationT_id'];

$醫生查詢 = "SELECT user_id FROM `user` WHERE name = '$看診醫生'";
$醫生結果 = mysqli_query($link, $醫生查詢);
$醫生行 = mysqli_fetch_assoc($醫生結果);
$醫生_id = $醫生行['user_id'];

// 準備更新患者資料的 SQL 指令
$SQL指令 = "UPDATE `patient` SET 
                `medicalnumber` = '$病歷號', 
                `patientname` = '$患者姓名', 
                `gender_id` = '$性別_id', 
                `birthday` = '$出生日期', 
                `doctorshift_id` = (
                    SELECT doctorshift_id FROM doctorshift 
                    WHERE consultationT_id = '$時段_id' AND user_id = '$醫生_id' 
                    LIMIT 1
                ),
                `department_id` = '$科別_id',
                `currentsymptoms` = '$當前狀況', 
                `allergies` = '$過敏藥物', 
                `medicalhistory` = '$歷史重大疾病'
            WHERE `patient_id` = $id";

// 執行 SQL 指令
if (mysqli_query($link, $SQL指令)) {
    $searchTerm = urlencode($患者姓名); // 使用患者姓名作為搜尋條件
    echo "<script>
        alert('資料已成功更新！');
        window.location.href = 'd_Basicsee.php?search=" . $searchTerm . "';
    </script>";
} else {
    $error_message = mysqli_error($link);
    echo "<script>
        alert('更新失敗，錯誤訊息: $error_message');
        window.location.href = 'd_Basicsee.php?id=$id';
    </script>";
}

// 關閉資料庫連線
mysqli_close($link);
?>
