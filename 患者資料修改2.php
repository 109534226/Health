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

// 準備 SQL 更新語句，並更新創建時間為當前時間
$SQL指令 = "UPDATE `patients` SET 
                `medicalnumber` = '$病歷號', 
                `patientname` = '$患者姓名', 
                `gender` = '$性別', 
                `birthday` = '$出生日期', 
                `consultationT` = '$看診時段',
                `department` = '$看診科別',
                `doctorname` = '$看診醫生',
                `currentsymptoms` = '$當前狀況', 
                `allergies` = '$過敏藥物', 
                `medicalhistory` = '$歷史重大疾病',
                `created_at` = NOW()
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
