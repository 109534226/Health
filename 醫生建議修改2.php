<?php
session_start();
include "db.php";

// 獲取並修剪 POST 請求的資料
$id = trim($_POST['id']);
$appointment_date = trim($_POST['appointment_date']);
$consultationt = trim($_POST['consultationt']);
$clinic_number = trim($_POST['clinic_number']);
$patient_name = trim($_POST['patient_name']);
$birth_date = trim($_POST['birth_date']);
$gender = trim($_POST['gender']);
$department = trim($_POST['department']);
$doctor_name = trim($_POST['doctor_name']);
$doctor_advice = trim($_POST['doctor_advice']);

// 驗證輸入是否為空
$errors = [];
if (empty($appointment_date)) $errors[] = '看診日期未輸入';
if (empty($consultationt)) $errors[] = '看診時段未輸入';
if (empty($clinic_number)) $errors[] = '病例號未輸入';
if (empty($patient_name)) $errors[] = '患者姓名未輸入';
if (empty($birth_date)) $errors[] = '出生日期未輸入';
if (empty($gender)) $errors[] = '性別未輸入';
if (empty($department)) $errors[] = '看診科別未輸入';
if (empty($doctor_name)) $errors[] = '看診醫生未輸入';
if (empty($doctor_advice)) $errors[] = '醫生建議未輸入';

if (!empty($errors)) {
    echo "<script>
            alert('" . implode("\\n", $errors) . "');
            window.location.href = 'd_advicefind.php';
          </script>";
    exit;
}

// 使用 Prepared Statements 防止 SQL 注入
$query = "
    UPDATE patient SET 
        dateday = ?, 
        consultationT_id = ?, 
        medicalnumber = ?, 
        patientname = ?, 
        birthdaydate = ?, 
        gender_id = (SELECT gender_id FROM gender WHERE gender = ?), 
        department_id = (SELECT department_id FROM department WHERE department = ?), 
        doctorShift_id = ?, 
        doctoradvice = ?, 
        created_at = NOW()
    WHERE patient_id = ?
";

$stmt = $link->prepare($query);
if (!$stmt) {
    echo "<script>
            alert('SQL 語句準備失敗: " . $link->error . "');
            window.location.href = 'd_advicefind.php';
          </script>";
    exit;
}

// 查詢看診醫生的 `doctorShift_id`
$doctorShiftQuery = "SELECT doctorShift_id FROM doctorShift WHERE user_id = ?";
$doctorStmt = $link->prepare($doctorShiftQuery);
$doctorStmt->bind_param('s', $doctor_name);
$doctorStmt->execute();
$doctorResult = $doctorStmt->get_result();
$doctorShift = $doctorResult->fetch_assoc();
$doctorShift_id = $doctorShift['doctorShift_id'] ?? null;

if (!$doctorShift_id) {
    echo "<script>
            alert('找不到對應的醫生班表！');
            window.location.href = 'd_advicefind.php';
          </script>";
    exit;
}

// 綁定參數到更新語句
$stmt->bind_param(
    'sssssssisi', 
    $appointment_date, 
    $consultationt, 
    $clinic_number, 
    $patient_name, 
    $birth_date, 
    $gender, 
    $department, 
    $doctorShift_id, 
    $doctor_advice, 
    $id
);

// 執行語句並處理結果
if ($stmt->execute()) {
    echo "<script>
            alert('資料已成功更新！');
            window.location.href = 'd_advicefind.php?search=" . urlencode($patient_name) . "';
          </script>";
} else {
    echo "<script>
            alert('更新失敗: " . $stmt->error . "');
            window.location.href = 'd_advicefind.php?id=$id';
          </script>";
}

// 關閉資料庫連接
$stmt->close();
$link->close();
?>
